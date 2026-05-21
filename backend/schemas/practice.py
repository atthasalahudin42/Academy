from pydantic import BaseModel
from typing import Optional, List, Any

class PracticeGenerateRequest(BaseModel):
    topic: str
    question_type: str = "MCQ" # MCQ, True/False, Short, Long
    difficulty: str = "Medium"
    num_questions: int = 5
    image_base64: Optional[str] = None

class AnswerSubmission(BaseModel):
    question_id: int
    selected_answer: str

class PracticeSessionSubmit(BaseModel):
    session_id: int
    answers: List[AnswerSubmission]

class PracticeSessionResponse(BaseModel):
    id: int
    topic: str
    score: float
    total_questions: int
    created_at: Any
    
    class Config:
        from_attributes = True
