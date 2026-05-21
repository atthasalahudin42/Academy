from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
import requests
import os

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

import os
API_KEY = os.getenv("GEMINI_API_KEY", "AIzaSyBhgMjM-SzEoGImcwE9-q68IajtL7M5rbY")


chat_memory = {}

@app.get("/")
def home():
    return {
        "success": True,
        "message": "Python AI Server Running"
    }

@app.post("/ask")
async def ask_ai(request: Request):

    try:
        body = await request.json()

        question = body.get("question", "").strip()
        session_id = body.get("session_id", "default")

        if not question:
            return {
                "success": False,
                "error": "Question cannot be empty"
            }

        if session_id not in chat_memory:
            chat_memory[session_id] = []

        history = chat_memory[session_id]

        # Save user question
        history.append({
            "role": "user",
            "parts": [{"text": question}]
        })

        # Keep last 10 messages only
        history = history[-10:]
        chat_memory[session_id] = history

        url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={API_KEY}"

        payload = {
            "contents": history,
            "generationConfig": {
                "temperature": 0.7,
                "maxOutputTokens": 2048
            }
        }
        response = requests.post(
            url,
            json=payload,
            timeout=60
        )

        data = response.json()
        
        reply = (
            data.get("candidates", [{}])[0]
            .get("content", {})
            .get("parts", [{}])[0]
            .get("text", "No response from AI.")
        )

        # Save AI reply
        chat_memory[session_id].append({
            "role": "model",
            "parts": [{"text": reply}]
        })

        return {
            "success": True,
            "response": reply
        }

    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }

@app.post("/clear-chat")
async def clear_chat(request: Request):

    try:
        body = await request.json()
        session_id = body.get("session_id", "default")

        chat_memory[session_id] = []

        return {
            "success": True,
            "message": "Chat cleared successfully"
        }

    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }