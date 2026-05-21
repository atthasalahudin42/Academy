from fastapi import APIRouter, Depends, HTTPException, Request
from sqlalchemy.orm import Session
from backend.database.database import get_db
from backend.models.models import AIHistory, User
from backend.schemas.ai import AIRequest, AIResponse, AIHistoryItem
from typing import List
from backend.services.ai_service import ai_service
from backend.auth.dependencies import get_current_user

router = APIRouter(prefix="/ai", tags=["ai"])

@router.post("/ask", response_model=AIResponse)
async def ask_ai(
    data: AIRequest, 
    request: Request,
    db: Session = Depends(get_db)
):
    # Try to get user if token exists (Guest support)
    user_id = None
    try:
        user = get_current_user(request, db)
        user_id = user.id
    except:
        pass # Stay as guest

    # Get response from AI Service
    response_text = await ai_service.get_response(data.prompt, model=data.model)
    
    # Save to history if logged in
    if user_id:
        new_history = AIHistory(
            user_id=user_id,
            prompt=data.prompt,
            response=response_text,
            model_used=data.model
        )
        db.add(new_history)
        db.commit()

    return {
        "success": True,
        "response": response_text
    }

@router.get("/history", response_model=List[AIHistoryItem])
async def get_ai_history(
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    history = (
        db.query(AIHistory)
        .filter(AIHistory.user_id == user.id)
        .order_by(AIHistory.created_at.desc())
        .limit(20)
        .all()
    )
    return history

@router.delete("/history")
async def delete_all_chats(
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Delete all chat history for the current user."""
    deleted = (
        db.query(AIHistory)
        .filter(AIHistory.user_id == user.id)
        .delete()
    )
    db.commit()
    return {"success": True, "deleted": deleted}

@router.delete("/history/{history_id}")
async def delete_chat(
    history_id: int,
    user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Delete a single chat from history."""
    entry = (
        db.query(AIHistory)
        .filter(AIHistory.id == history_id, AIHistory.user_id == user.id)
        .first()
    )
    if not entry:
        raise HTTPException(status_code=404, detail="Chat not found")
    db.delete(entry)
    db.commit()
    return {"success": True, "message": "Chat deleted"}
