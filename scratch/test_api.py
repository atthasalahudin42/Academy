import requests

url = "http://127.0.0.1:8000/practice/generate"
payload = {
    "topic": "Physics",
    "question_type": "mcq",
    "difficulty": "easy",
    "num_questions": 3
}

try:
    response = requests.post(url, json=payload)
    print(f"Status Code: {response.status_code}")
    print(f"Response: {response.text}")
except Exception as e:
    print(f"Error: {str(e)}")
