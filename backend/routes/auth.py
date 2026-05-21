from fastapi import APIRouter, Depends, HTTPException, status, Response
from pydantic import BaseModel
from sqlalchemy.orm import Session
from typing import Optional
from backend.database.database import get_db
from backend.models.models import User
from backend.schemas.auth import UserCreate, UserLogin, Token
from backend.auth.security import get_password_hash, verify_password
from backend.auth.jwt_handler import create_access_token

router = APIRouter(prefix="/auth", tags=["auth"])

@router.post("/signup", status_code=status.HTTP_201_CREATED)
def signup(user_data: UserCreate, db: Session = Depends(get_db)):
    # Check if user already exists
    db_user = db.query(User).filter(User.email == user_data.email).first()
    if db_user:
        raise HTTPException(status_code=400, detail="Email already registered")
    
    # Create new user
    hashed_password = get_password_hash(user_data.password)
    new_user = User(
        name=user_data.name,
        email=user_data.email,
        password=hashed_password,
        is_verified=True  # Auto-verify for now to simplify, can add email flow later
    )
    db.add(new_user)
    db.commit()
    db.refresh(new_user)
    return {"message": "User created successfully", "user_id": new_user.id}

@router.post("/login", response_model=Token)
def login(response: Response, user_data: UserLogin, db: Session = Depends(get_db)):
    user = db.query(User).filter(User.email == user_data.email).first()
    if not user or not verify_password(user_data.password, user.password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect email or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    access_token = create_access_token(data={"sub": user.email, "user_id": user.id})
    
    # Set cookie for PHP frontend compatibility
    response.set_cookie(
        key="access_token", 
        value=access_token, 
        httponly=True, 
        max_age=86400, 
        samesite="lax"
    )
    
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user_id": user.id,
        "user_name": user.name,
        "user_email": user.email,
        "profile_picture": user.profile_picture,
    }

@router.post("/logout")
def logout(response: Response):
    response.delete_cookie("access_token")
    return {"message": "Successfully logged out"}


# ─── Internal endpoint: Issue JWT for a PHP-verified session ──────────────────
class SessionLoginRequest(BaseModel):
    email: str
    name: Optional[str] = None

@router.post("/login-via-session", response_model=Token)
def login_via_session(data: SessionLoginRequest, db: Session = Depends(get_db)):
    """
    Called by verify_login.php after PHP session is established.
    Issues a JWT for use by FastAPI-powered features (Ask AI, Practice, Performance).
    Auto-creates the user in the Python DB if they exist in PHP but not yet synced.
    """
    user = db.query(User).filter(User.email == data.email).first()
    if not user:
        display_name = (data.name or data.email.split("@")[0]).strip() or "Learner"
        new_user = User(
            name=display_name,
            email=data.email,
            password=get_password_hash("php-magic-link-sync"),
            is_verified=True,
        )
        db.add(new_user)
        db.commit()
        db.refresh(new_user)
        user = new_user
    
    access_token = create_access_token(data={"sub": user.email, "user_id": user.id})
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user_id": user.id,
        "user_name": user.name,
        "user_email": user.email,
        "profile_picture": user.profile_picture,
    }
