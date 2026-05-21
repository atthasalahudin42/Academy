from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session
from backend.database.database import get_db
from backend.models.models import User
from backend.services.analytics_service import analytics_service
from backend.auth.dependencies import get_current_user
from typing import Optional

router = APIRouter(prefix="/performance", tags=["performance"])

@router.get("/stats")
async def get_performance_stats(
    days: Optional[int] = Query(None, description="Filter stats to last N days (7 or 30)"),
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Full performance stats including streak and XP for this user."""
    stats = analytics_service.get_user_stats(db, user.id, days=days)
    return stats

@router.get("/dashboard-summary")
async def get_dashboard_summary(
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Lightweight summary for the dashboard: streak, XP, accuracy, sessions."""
    stats = analytics_service.get_user_stats(db, user.id)
    return {
        "study_streak": stats["study_streak"],
        "total_xp": stats["total_xp"],
        "total_sessions": stats["total_sessions"],
        "accuracy": stats["accuracy"],
    }

@router.get("/insights")
async def get_ai_insights(
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """AI-personalized learning tip based on user's stats."""
    stats = analytics_service.get_user_stats(db, user.id)

    if stats["total_sessions"] == 0:
        return {"insight": "Start your first practice session to get AI-powered insights!"}

    if stats["study_streak"] >= 7:
        return {"insight": f"🔥 Amazing! You're on a {stats['study_streak']}-day streak. You're building a powerful learning habit!"}

    if stats["accuracy"] < 50:
        return {"insight": "Focus on the basics. Your accuracy is below 50%. Try 'Easy' difficulty topics first."}
    elif stats["accuracy"] > 85:
        return {"insight": f"Excellent work! You've earned {stats['total_xp']} XP. Try challenging yourself with 'Hard' difficulty to keep growing."}
    else:
        return {"insight": f"Consistent progress — {stats['study_streak']} day streak and {stats['total_xp']} XP earned. Keep practicing daily to reach the 90% accuracy milestone!"}
