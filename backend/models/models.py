from sqlalchemy import Column, Integer, String, Boolean, DateTime, ForeignKey, Text, JSON, Float, Table
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from backend.database.database import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(100), nullable=False)
    email = Column(String(150), unique=True, index=True, nullable=False)
    password = Column(String(255), nullable=False)
    is_verified = Column(Boolean, default=False)
    verification_token = Column(String(64), nullable=True)
    token_expiry = Column(DateTime, nullable=True)
    login_token = Column(String(64), nullable=True)
    login_token_expiry = Column(DateTime, nullable=True)
    profile_picture = Column(String(255), default="avatar.png")
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    sessions = relationship("PracticeSession", back_populates="user")
    ai_history = relationship("AIHistory", back_populates="user")
    performance = relationship("PerformanceMetric", back_populates="user", uselist=False)

class PracticeSession(Base):
    __tablename__ = "practice_sessions"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=True)  # Nullable for guests
    topic = Column(String(255), nullable=False)
    question_type = Column(String(50))  # MCQ, TF, Short, Long
    difficulty = Column(String(20))
    num_questions = Column(Integer)
    score = Column(Float, default=0.0)
    total_questions = Column(Integer)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    user = relationship("User", back_populates="sessions")
    questions = relationship("PracticeQuestion", back_populates="session")

class PracticeQuestion(Base):
    __tablename__ = "practice_questions"

    id = Column(Integer, primary_key=True, index=True)
    session_id = Column(Integer, ForeignKey("practice_sessions.id"), nullable=False)
    question_text = Column(Text, nullable=False)
    options = Column(JSON, nullable=True)  # Store options as JSON array
    correct_answer = Column(Text, nullable=False)
    explanation = Column(Text, nullable=True)

    # Relationships
    session = relationship("PracticeSession", back_populates="questions")
    answers = relationship("PracticeAnswer", back_populates="question")

class PracticeAnswer(Base):
    __tablename__ = "practice_answers"

    id = Column(Integer, primary_key=True, index=True)
    question_id = Column(Integer, ForeignKey("practice_questions.id"), nullable=False)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    selected_answer = Column(Text, nullable=False)
    is_correct = Column(Boolean)
    ai_feedback = Column(Text, nullable=True)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    question = relationship("PracticeQuestion", back_populates="answers")

class PerformanceMetric(Base):
    __tablename__ = "performance_metrics"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    total_sessions = Column(Integer, default=0)
    total_questions = Column(Integer, default=0)
    correct_answers = Column(Integer, default=0)
    average_accuracy = Column(Float, default=0.0)
    study_time_minutes = Column(Integer, default=0)
    study_streak = Column(Integer, default=0)   # consecutive days with practice
    total_xp = Column(Integer, default=0)        # gamification XP points
    last_updated = Column(DateTime(timezone=True), onupdate=func.now())

    # Relationships
    user = relationship("User", back_populates="performance")

class AIHistory(Base):
    __tablename__ = "ai_history"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    prompt = Column(Text, nullable=False)
    response = Column(Text, nullable=False)
    model_used = Column(String(50))
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    user = relationship("User", back_populates="ai_history")
