from pydantic_settings import BaseSettings
from functools import lru_cache
import os

class Settings(BaseSettings):
    # Server
    PORT: int = 8000
    HOST: str = "0.0.0.0"
    
    # Database
    DATABASE_URL: str = "sqlite:///./study_helper.db"
    
    # JWT
    SECRET_KEY: str = "yoursupersecretkeyhere"
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 1440
    
    # AI
    GEMINI_API_KEY: str = ""
    OPENAI_API_KEY: str = ""
    
    # Mail
    SMTP_HOST: str = "smtp.gmail.com"
    SMTP_PORT: int = 587
    SMTP_USER: str = ""
    SMTP_PASSWORD: str = ""
    MAIL_FROM: str = ""

    class Config:
        env_file = "backend/.env"

@lru_cache()
def get_settings():
    return Settings()
