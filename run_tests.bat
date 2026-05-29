@echo off

echo Checking virtual environment...
if exist venv\Scripts\python.exe (
    set PYTHON_EXE=venv\Scripts\python.exe
) else if exist myenv\Scripts\python.exe (
    set PYTHON_EXE=myenv\Scripts\python.exe
) else (
    set PYTHON_EXE=python
)

echo Installing Python dependencies...
%PYTHON_EXE% -m pip install -r requirements.txt pytest

echo Installing PHP dependencies...
composer install

echo Running PHP unit tests...
vendor\bin\phpunit

echo Running Python unit tests...
%PYTHON_EXE% -m pytest tests/python -q

echo Running E2E tests...
%PYTHON_EXE% -m pytest tests/e2e -q

echo DONE
pause
