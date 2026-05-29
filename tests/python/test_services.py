import pytest

# Example service logic test (adjust to your real service if exists)
def hash_password(password: str):
    return "hashed_" + password

def test_password_hashing():
    result = hash_password("123456")

    assert result.startswith("hashed_")
    assert "123456" in result