from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class AIRequest(BaseModel):
    prompt: str
    model: Optional[str] = "gemini" # gemini or gpt
    session_id: Optional[str] = "default"

class AIResponse(BaseModel):
    success: bool
    response: str
    error: Optional[str] = None

class AIHistoryItem(BaseModel):
    id: int
    prompt: str
    response: str
    model_used: Optional[str] = None
    created_at: datetime

    class Config:
        from_attributes = True
