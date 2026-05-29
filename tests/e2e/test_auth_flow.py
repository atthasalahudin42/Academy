from fastapi.testclient import TestClient
from backend.main import app
import random

client = TestClient(app)

def test_full_auth_flow():
    # Use random email to avoid duplicate key errors in database
    email = f"test_{random.randint(100000, 999999)}@example.com"

    # 1. Register user
    register_response = client.post("/auth/signup", json={
        "name": "Test User",
        "email": email,
        "password": "123456"
    })

    assert register_response.status_code in [200, 201]

    # 2. Login user
    login_response = client.post("/auth/login", json={
        "email": email,
        "password": "123456"
    })

    assert login_response.status_code == 200

    token = login_response.json().get("access_token")
    assert token is not None

    # 3. Access protected route
    profile_response = client.get(
        "/performance/dashboard-summary",
        headers={"Authorization": f"Bearer {token}"}
    )

    assert profile_response.status_code == 200
    assert "total_xp" in profile_response.json()