import requests

api_key = "AIzaSyBhgMjM-SzEoGImcwE9-q68IajtL7M5rbY"
url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={api_key}"

payload = {
    "contents": [{"parts": [{"text": "Say hello"}]}]
}

print(f"Testing new key with gemini-2.0-flash: {api_key}...")
try:
    response = requests.post(url, json=payload, timeout=10)
    print(f"Status Code: {response.status_code}")
    print(f"Response Body: {response.text}")
except Exception as e:
    print(f"Failed! Error: {str(e)}")
