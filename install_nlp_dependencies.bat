@echo off
echo Installing Python NLP dependencies for ChatAI Plants...
echo.

REM Check if Python is installed
python --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo ERROR: Python is not installed or not in PATH
    echo Please install Python 3.8+ from https://www.python.org/downloads/
    pause
    exit /b 1
)

echo Python version:
python --version
echo.

echo Installing required packages...
pip install -r requirements.txt

echo.
echo Installing Italian language model for spaCy...
python -m spacy download it_core_news_sm

echo.
echo Testing spaCy installation...
python -c "import spacy; nlp = spacy.load('it_core_news_sm'); print('✅ spaCy con modello italiano installato correttamente!')"

echo.
echo Testing NLP script...
python scripts/spacy_nlp.py "Come si cura il ficus?"

echo.
echo ✅ Installation completed!
echo You can now use the advanced NLP features in your chatbot.
pause
