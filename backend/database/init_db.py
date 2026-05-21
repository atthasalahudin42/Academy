from backend.database.database import engine, Base
from backend.models.models import User, PracticeSession, PracticeQuestion, PracticeAnswer, PerformanceMetric, AIHistory

def init_db():
    print("Initializing database...")
    Base.metadata.create_all(bind=engine)
    print("Database initialized successfully!")

if __name__ == "__main__":
    init_db()
