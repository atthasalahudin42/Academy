from pydantic import BaseModel
import pytest

# Adjust if you already have a User model in your project
class User(BaseModel):
    name: str
    email: str

def test_user_model_validation():
    user = User(name="Ali", email="ali@example.com")

    assert user.name == "Ali"
    assert user.email == "ali@example.com"