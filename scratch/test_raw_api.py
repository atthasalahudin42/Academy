import requests

api_key = "AIzaSyAFeDzbUgv07wtEjia9bh5RwuzLxVU9dHo"
url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={api_key}"

payload = {
    "contents": [{"parts": [{"text": "Say hello"}]}]
}

print("Starting raw request test with gemini-2.0-flash...")
try:
    response = requests.post(url, json=payload, timeout=10)
    print(f"Status Code: {response.status_code}")
    print(f"Response Body: {response.text}")
except Exception as e:
    print(f"Failed! Error: {str(e)}")
