import asyncio
import logging
from backend.services.ai_service import ai_service
from backend.services.practice_service import practice_service
from backend.schemas.practice import PracticeGenerateRequest

# Setup logging
logging.basicConfig(level=logging.INFO)

async def test_integration():
    print("--------------------------------------------------")
    print("🧪 Starting Mashq AI Integration Verification Test")
    print("--------------------------------------------------\n")

    # 1. Test Q&A chat endpoint
    print("🤖 Testing Ask AI (Q&A Generator) service...")
    chat_prompt = "What is the capital of France and what is it famous for?"
    response = await ai_service.get_response(chat_prompt)
    print(f"Prompt: {chat_prompt}")
    print(f"Response:\n{response}\n")
    
    if "⚠️" in response:
        print("❌ Q&A Test FAILED!")
    else:
        print("✅ Q&A Test PASSED!")
        
    print("\n--------------------------------------------------\n")

    # 2. Test MCQ practice quiz endpoint
    print("🎯 Testing Practice Arena (MCQ Generator) service...")
    request_data = PracticeGenerateRequest(
        topic="Basic Algebra",
        question_type="MCQ",
        difficulty="medium",
        num_questions=3
    )
    
    questions, error = await practice_service.generate_questions(request_data)
    
    if error:
        print(f"❌ MCQ Test FAILED! Error: {error}")
    elif not questions:
        print("❌ MCQ Test FAILED! No questions returned.")
    else:
        print(f"✅ MCQ Test PASSED! Successfully generated {len(questions)} questions:")
        for i, q in enumerate(questions, 1):
            print(f"\nQuestion {i}: {q['question_text']}")
            print(f"Options: {q['options']}")
            print(f"Correct Answer: {q['correct_answer']}")
            print(f"Explanation: {q['explanation']}")

    print("\n--------------------------------------------------")
    print("🧪 Test suite finished execution.")
    print("--------------------------------------------------")

if __name__ == "__main__":
    asyncio.run(test_integration())
