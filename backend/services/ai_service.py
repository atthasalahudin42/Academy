import os
import logging
import httpx
from typing import List, Dict, Any, Optional
from backend.config import get_settings

settings = get_settings()
logger = logging.getLogger(__name__)


class AIService:
    def __init__(self):
        # OpenAI (optional fallback)
        self.openai_client = None
        if settings.OPENAI_API_KEY and settings.OPENAI_API_KEY not in ("your_openai_key_here", ""):
            try:
                from openai import OpenAI
                self.openai_client = OpenAI(api_key=settings.OPENAI_API_KEY)
                logger.info("OpenAI client configured successfully")
            except Exception as e:
                logger.error(f"Failed to initialize OpenAI client: {str(e)}")

        logger.info("Mashq AI service initialized as main engine")

    async def get_response(
        self,
        prompt: str,
        history: List[Dict[str, str]] = [],
        model: str = "gemini",
        image_base64: Optional[str] = None
    ) -> str:
        # If user explicitly requested gpt and we have OpenAI client, use it
        if model == "gpt" and self.openai_client:
            return await self._get_gpt_response(prompt, history)
            
        # Default all other requests to Mashq AI
        return await self._get_mashq_response(prompt)

    async def _get_mashq_response(self, prompt: str) -> str:
        """
        Sends request to Mashq AI Q&A generation API.
        """
        try:
            url = "https://mashq-ai.com/api/generate/qa"
            payload = {
                "topic": prompt,
                "count": 1,
                "language": "English"
            }
            
            headers = {
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
            
            async with httpx.AsyncClient() as client:
                response = await client.post(url, json=payload, headers=headers, timeout=30.0)
                
                if response.status_code == 429:
                    logger.warning("Mashq AI Q&A API rate limit hit.")
                    return "⚠️ Too many requests to Mashq AI. Please wait a moment and try again."
                
                if response.status_code != 200:
                    logger.error(f"Mashq AI API error. Code: {response.status_code}, Body: {response.text}")
                    return f"⚠️ Mashq AI Error: Received status code {response.status_code}."
                
                res_data = response.json()
                qa_pairs = res_data.get("qaPairs", [])
                
                if not qa_pairs:
                    return "⚠️ Mashq AI returned an empty response."
                
                # Get the answer from the first Q&A pair generated
                return qa_pairs[0].get("answer", "⚠️ No answer found in Mashq AI response.")

        except Exception as e:
            logger.error(f"Mashq AI general error: {str(e)}")
            return f"⚠️ Mashq AI Error: {str(e)}"

    async def _get_gpt_response(self, prompt: str, history: List[Dict[str, str]]) -> str:
        if not self.openai_client:
            return "⚠️ OpenAI API is not configured."

        try:
            messages = [{"role": "system", "content": "You are a helpful study assistant."}]
            for msg in history:
                messages.append({"role": msg.get("role", "user"), "content": msg.get("content", "")})
            messages.append({"role": "user", "content": prompt})

            response = self.openai_client.chat.completions.create(
                model="gpt-4o",
                messages=messages
            )
            return response.choices[0].message.content
        except Exception as e:
            logger.error(f"OpenAI API Error: {str(e)}")
            return f"⚠️ OpenAI Error: {str(e)}"


# Singleton instance
ai_service = AIService()
