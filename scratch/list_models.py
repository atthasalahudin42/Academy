from google import genai

api_key = "AIzaSyBhgMjM-SzEoGImcwE9-q68IajtL7M5rbY"
client = genai.Client(api_key=api_key)

try:
    for model in client.models.list():
        print(model)
except Exception as e:
    print(f"Failed to list models: {str(e)}")
