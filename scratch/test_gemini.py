from google import genai

api_key = "AIzaSyAFeDzbUgv07wtEjia9bh5RwuzLxVU9dHo"
client = genai.Client(api_key=api_key)

print("Starting Gemini test...")
try:
    response = client.models.generate_content(
        model="gemini-2.0-flash",
        contents="Say hello"
    )
    print("API call finished.")
    print(f"Response text length: {len(response.text)}")
    print("Response text repr:")
    print(repr(response.text))
except Exception as e:
    print(f"Failed! Error type: {type(e).__name__}")
    print(f"Error message: {str(e)}")
