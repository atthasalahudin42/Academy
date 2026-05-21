from fastapi import APIRouter, Depends, HTTPException, Request
from sqlalchemy.orm import Session
from backend.database.database import get_db
from backend.models.models import PracticeSession, PracticeQuestion, PracticeAnswer, User
from backend.schemas.practice import PracticeGenerateRequest, PracticeSessionSubmit, PracticeSessionResponse
from backend.services.practice_service import practice_service
from backend.auth.dependencies import get_current_user
from typing import List

router = APIRouter(prefix="/practice", tags=["practice"])

@router.post("/generate")
async def generate_practice(
    data: PracticeGenerateRequest,
    request: Request,
    db: Session = Depends(get_db)
):
    # Optional user
    user_id = None
    try:
        user = get_current_user(request, db)
        user_id = user.id
    except:
        pass

    # 1. Generate questions via AI
    ai_questions, error_msg = await practice_service.generate_questions(data)
    
    if not ai_questions:
        detail = error_msg if error_msg else "Failed to generate questions. Please try again."
        raise HTTPException(status_code=500, detail=detail)

    # 2. Create Session
    session = PracticeSession(
        user_id=user_id,
        topic=data.topic,
        question_type=data.question_type,
        difficulty=data.difficulty,
        num_questions=len(ai_questions),
        total_questions=len(ai_questions)
    )
    db.add(session)
    db.commit()
    db.refresh(session)

    # 3. Save Questions
    for q in ai_questions:
        new_q = PracticeQuestion(
            session_id=session.id,
            question_text=q.get("question_text"),
            options=q.get("options"),
            correct_answer=str(q.get("correct_answer")),
            explanation=q.get("explanation")
        )
        db.add(new_q)
    
    db.commit()

    # 4. Return session + questions (excluding correct answers for security)
    questions_to_return = []
    db_questions = db.query(PracticeQuestion).filter(PracticeQuestion.session_id == session.id).all()
    for db_q in db_questions:
        questions_to_return.append({
            "id": db_q.id,
            "question_text": db_q.question_text,
            "options": db_q.options
        })

    return {
        "session_id": session.id,
        "topic": session.topic,
        "questions": questions_to_return
    }

@router.post("/submit")
async def submit_practice(
    data: PracticeSessionSubmit,
    request: Request,
    db: Session = Depends(get_db)
):
    session = db.query(PracticeSession).filter(PracticeSession.id == data.session_id).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found")

    user_id = None
    try:
        user = get_current_user(request, db)
        user_id = user.id
    except:
        pass

    correct_count = 0
    results = []

    for ans in data.answers:
        question = db.query(PracticeQuestion).filter(PracticeQuestion.id == ans.question_id).first()
        if not question:
            continue
        
        is_correct = str(ans.selected_answer).strip().lower() == str(question.correct_answer).strip().lower()
        if is_correct:
            correct_count += 1
        
        # Save answer
        db_answer = PracticeAnswer(
            question_id=question.id,
            user_id=user_id,
            selected_answer=ans.selected_answer,
            is_correct=is_correct
        )
        db.add(db_answer)
        
        results.append({
            "question_id": question.id,
            "is_correct": is_correct,
            "correct_answer": question.correct_answer,
            "explanation": question.explanation
        })

    # Update session score
    session.score = (correct_count / session.total_questions) * 100 if session.total_questions > 0 else 0
    db.commit()

    if user_id:
        from backend.services.analytics_service import analytics_service
        analytics_service.get_user_stats(db, user_id)

    return {
        "score": session.score,
        "correct_count": correct_count,
        "total": session.total_questions,
        "results": results
    }

@router.get("/history", response_model=List[PracticeSessionResponse])
async def get_practice_history(
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    history = db.query(PracticeSession).filter(PracticeSession.user_id == user.id).order_by(PracticeSession.created_at.desc()).all()
    return history
