from fastapi import FastAPI
from pydantic import BaseModel
import requests

app = FastAPI()


class AskRequest(BaseModel):
    question: str
    session_id: str = "default"

chat_memory = {}

import os
API_KEY = os.getenv("GEMINI_API_KEY")


@app.post("/ask")
def ask_ai(data: AskRequest):

    question = (data.question or "").strip()

    if not question:
        return {"success": False, "error": "Empty question"}

    session_id = data.session_id or "default"

    # init memory
    if session_id not in chat_memory:
        chat_memory[session_id] = []

    history = chat_memory[session_id]

    history.append({
        "role": "user",
        "parts": [{"text": question}]
    })

    contents = history[-10:]

    url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={API_KEY}"

    try:
        r = requests.post(url, json={"contents": contents}, timeout=20)
        res = r.json()
    except Exception as e:
        return {"success": False, "error": f"Request failed: {str(e)}"}

    if "error" in res:
        return {
            "success": False,
            "error": res["error"].get("message", "Gemini API error"),
            "debug": res
        }


    try:
        candidates = res.get("candidates", [])

        if not candidates:
            return {
                "success": False,
                "error": "No candidates returned",
                "debug": res
            }

        reply = candidates[0]["content"]["parts"][0]["text"]

    except Exception as e:
        return {
            "success": False,
            "error": f"Parsing error: {str(e)}",
            "debug": res
        }

    history.append({
        "role": "model",
        "parts": [{"text": reply}]
    })

    return {
        "success": True,
        "response": reply
    }