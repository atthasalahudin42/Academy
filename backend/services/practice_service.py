import logging
import httpx
from typing import List, Dict, Any, Optional
from backend.schemas.practice import PracticeGenerateRequest

logger = logging.getLogger(__name__)


class PracticeService:
    async def generate_questions(self, data: PracticeGenerateRequest) -> tuple[List[Dict[str, Any]], Optional[str]]:
        """
        Generate structured practice questions using Mashq AI.
        """
        try:
            # Map difficulty to Mashq AI expectations:
            # easy -> Easy, medium -> Intermediate, hard -> Advanced
            diff_map = {
                "easy": "Easy",
                "medium": "Intermediate",
                "hard": "Advanced"
            }
            difficulty = diff_map.get(str(data.difficulty).lower(), "Intermediate")
            
            topic = data.topic
            if str(data.question_type).lower() == "true_false":
                topic = f"{topic} (Format questions as True/False questions)"
                
            payload = {
                "topic": topic,
                "count": data.num_questions if data.num_questions else 5,
                "difficulty": difficulty,
                "language": "English"
            }
            
            headers = {
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
            
            async with httpx.AsyncClient() as client:
                response = await client.post(
                    "https://mashq-ai.com/api/generate/mcqs",
                    json=payload,
                    headers=headers,
                    timeout=45.0
                )
                
                if response.status_code == 429:
                    logger.warning("Mashq AI MCQ API rate limit hit.")
                    return [], "⚠️ Too many requests to Mashq AI. Please wait a moment and try again."
                
                if response.status_code != 200:
                    logger.error(f"Mashq AI MCQ API error. Code: {response.status_code}, Body: {response.text}")
                    return [], f"⚠️ Mashq AI Error: Received status code {response.status_code}."
                
                res_data = response.json()
                mcqs = res_data.get("mcqs", [])
                
                if not mcqs:
                    return [], "⚠️ Mashq AI returned no practice questions."
                
                # Transform to the structure expected by the backend
                formatted_questions = []
                for item in mcqs:
                    question_text = item.get("question")
                    options = item.get("options", [])
                    correct_idx = item.get("correctAnswer", 0)
                    explanation = item.get("explanation", "")
                    
                    # Convert 0-based index to string option value
                    correct_answer = ""
                    try:
                        correct_idx_int = int(correct_idx)
                        if options and 0 <= correct_idx_int < len(options):
                            correct_answer = options[correct_idx_int]
                        else:
                            # Fallback if index out of bounds
                            correct_answer = options[0] if options else ""
                    except Exception:
                        correct_answer = str(correct_idx)
                        
                    formatted_questions.append({
                        "question_text": question_text,
                        "options": options,
                        "correct_answer": correct_answer,
                        "explanation": explanation
                    })
                    
                logger.info(f"Successfully generated {len(formatted_questions)} practice questions via Mashq AI")
                return formatted_questions, None
                
        except Exception as e:
            logger.error(f"Failed to generate practice questions: {str(e)}")
            return [], str(e)


practice_service = PracticeService()
