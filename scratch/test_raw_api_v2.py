import requests

api_key = "AIzaSyDJWCYZpaI5XeaRwb_ZfXew5UKgvIYrIQo"
url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={api_key}"

payload = {
    "contents": [{"parts": [{"text": "Say hello"}]}]
}

print(f"Testing key: {api_key}...")
try:
    response = requests.post(url, json=payload, timeout=10)
    print(f"Status Code: {response.status_code}")
    print(f"Response Body: {response.text}")
except Exception as e:
    print(f"Failed! Error: {str(e)}")
