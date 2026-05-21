from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import os
from dotenv import load_dotenv
from backend.routes import auth, ai, practice, performance
from backend.database.database import engine, Base

load_dotenv()

app = FastAPI(title="AI Study Helper API")

@app.on_event("startup")
def on_startup():
    """Ensure all analytics & chat tables exist on server start."""
    Base.metadata.create_all(bind=engine)

# Configure CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, specify your domain
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(auth.router)
app.include_router(ai.router)
app.include_router(practice.router)
app.include_router(performance.router)

@app.get("/")
async def root():
    return {"message": "Welcome to AI Study Helper API"}

if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8001))
    uvicorn.run("backend.main:app", host="0.0.0.0", port=port, reload=True)
