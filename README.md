# 🚀 Academy Project

This is a full-stack web application built using **PHP (frontend/backend)** and optional **Python FastAPI (AI services)**.

---

## 📌 Features
- User authentication system
- Dynamic PHP-based frontend
- Backend API integration
- AI/ML service support (FastAPI module)
- Database integration
- Responsive UI

---

## 🛠️ Tech Stack
- PHP (Core backend)
- HTML, CSS, JavaScript
- MySQL (Database)
- Python FastAPI (AI module - optional)
- Vercel / InfinityFree (Deployment)

---

## ⚙️ Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/your-username/academy.git
cd academy
```

### 2. Frontend Setup (PHP)
The frontend and core PHP backend scripts are contained in the `frontend/` directory.
```bash
cd frontend
# Start the local PHP development server
php -S localhost:8000
```
*Note: Make sure your local MySQL server is running and the database is configured in `frontend/src/Database.php`.*

### 3. Backend Setup (Python FastAPI - Optional)
The AI capabilities and advanced APIs are handled by the Python FastAPI service in the `backend/` directory.
```bash
cd backend

# Create and activate a virtual environment
python -m venv venv
venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Run the FastAPI development server
uvicorn main:app --host 127.0.0.1 --port 8001 --reload
```

## 🚀 Deployment

- **Frontend:** Deploy the `frontend/` folder to Vercel (Ensure the Root Directory is set to `frontend/`).
- **Backend:** Deploy the `backend/` folder to Railway (Ensure the Root Directory is set to `backend/`).