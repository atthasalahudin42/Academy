from sqlalchemy.orm import Session
from sqlalchemy import func
from backend.models.models import PracticeSession, PracticeAnswer, PracticeQuestion, PerformanceMetric
from typing import Dict, Any, Optional
from datetime import date, timedelta, datetime

class AnalyticsService:
    def calculate_streak(self, db: Session, user_id: int) -> int:
        """
        Calculate how many consecutive days the user has had at least one practice session,
        counting backwards from today.
        """
        sessions = db.query(
            func.date(PracticeSession.created_at).label("day")
        ).filter(
            PracticeSession.user_id == user_id
        ).distinct().order_by(
            func.date(PracticeSession.created_at).desc()
        ).all()

        if not sessions:
            return 0

        active_days = sorted({row.day for row in sessions}, reverse=True)
        today = date.today()
        streak = 0

        for i, d in enumerate(active_days):
            # Allow today or yesterday as start of streak
            expected = today - timedelta(days=i)
            if d == expected:
                streak += 1
            else:
                break

        return streak

    def calculate_xp(self, db: Session, user_id: int) -> int:
        """
        XP formula:
          +10 XP per correct answer
          +5  XP per completed session
        """
        total_sessions = db.query(PracticeSession).filter(
            PracticeSession.user_id == user_id
        ).count()

        correct_answers = db.query(PracticeAnswer).filter(
            PracticeAnswer.user_id == user_id,
            PracticeAnswer.is_correct == True
        ).count()

        return (correct_answers * 10) + (total_sessions * 5)

    def get_user_stats(self, db: Session, user_id: int, days: Optional[int] = None) -> Dict[str, Any]:
        """
        Calculate and retrieve per-user performance statistics including
        study streak and total XP. Optional `days` filters sessions to a window.
        """
        since = None
        if days:
            since = datetime.utcnow() - timedelta(days=days)

        session_q = db.query(PracticeSession).filter(PracticeSession.user_id == user_id)
        if since:
            session_q = session_q.filter(PracticeSession.created_at >= since)

        # 1. Basic Stats
        total_sessions = session_q.count()

        avg_score_q = db.query(func.avg(PracticeSession.score)).filter(
            PracticeSession.user_id == user_id
        )
        if since:
            avg_score_q = avg_score_q.filter(PracticeSession.created_at >= since)
        avg_score = avg_score_q.scalar() or 0.0

        # 2. Questions & Accuracy (all questions in user's sessions + submitted answers)
        total_questions = (
            db.query(PracticeQuestion)
            .join(PracticeSession, PracticeQuestion.session_id == PracticeSession.id)
            .filter(PracticeSession.user_id == user_id)
        )
        if since:
            total_questions = total_questions.filter(PracticeSession.created_at >= since)
        total_questions_count = total_questions.count()

        answer_base = db.query(PracticeAnswer).filter(PracticeAnswer.user_id == user_id)
        if since:
            answer_base = answer_base.filter(PracticeAnswer.created_at >= since)
        total_answers = answer_base.count()
        correct_answers = answer_base.filter(PracticeAnswer.is_correct == True).count()

        accuracy = (correct_answers / total_answers * 100) if total_answers > 0 else 0.0

        # 3. Subject-wise performance
        subject_performance = db.query(
            PracticeSession.topic,
            func.avg(PracticeSession.score).label("avg_score")
        ).filter(
            PracticeSession.user_id == user_id
        ).group_by(PracticeSession.topic).all()

        subjects = [
            {"topic": s.topic, "score": round(float(s.avg_score), 2)}
            for s in subject_performance
        ]

        # 4. Progress Trends (last 7 sessions in range)
        trends_q = db.query(
            PracticeSession.created_at,
            PracticeSession.score
        ).filter(PracticeSession.user_id == user_id)
        if since:
            trends_q = trends_q.filter(PracticeSession.created_at >= since)
        trends = trends_q.order_by(PracticeSession.created_at.desc()).limit(7).all()
        trends = list(reversed(trends))

        progress_data = [
            {"date": str(t.created_at.date()), "score": t.score}
            for t in trends
        ]

        # 5. Streak & XP (per-user)
        study_streak = self.calculate_streak(db, user_id)
        total_xp = self.calculate_xp(db, user_id)

        # 6. Persist latest values to PerformanceMetric
        metric = db.query(PerformanceMetric).filter(
            PerformanceMetric.user_id == user_id
        ).first()

        if metric:
            metric.total_sessions = total_sessions
            metric.total_questions = total_questions_count
            metric.correct_answers = correct_answers
            metric.average_accuracy = round(accuracy, 2)
            metric.study_time_minutes = total_sessions * 15
            metric.study_streak = study_streak
            metric.total_xp = total_xp
        else:
            metric = PerformanceMetric(
                user_id=user_id,
                total_sessions=total_sessions,
                total_questions=total_questions_count,
                correct_answers=correct_answers,
                average_accuracy=round(accuracy, 2),
                study_time_minutes=total_sessions * 15,
                study_streak=study_streak,
                total_xp=total_xp,
            )
            db.add(metric)

        db.commit()

        return {
            "total_sessions": total_sessions,
            "average_score": round(avg_score, 2),
            "accuracy": round(accuracy, 2),
            "total_questions": total_questions_count,
            "subjects": subjects,
            "progress_trends": progress_data,
            "study_time": total_sessions * 15,  # minutes (15 min per session estimate)
            "study_streak": study_streak,
            "total_xp": total_xp,
        }

analytics_service = AnalyticsService()
