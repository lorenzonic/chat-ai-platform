@echo off
REM Script batch per testare l'aggiornamento Google Trends

echo ================================
echo Test aggiornamento Google Trends
echo ================================
echo.

echo [1/3] Verifica connessione database...
cd /d "C:\Users\Lorenzo\chat-ai-platform"
php artisan migrate:status
if %errorlevel% neq 0 (
    echo ERRORE: Problema con la connessione al database
    pause
    exit /b 1
)

echo.
echo [2/3] Test script Python...
"C:\Users\Lorenzo\AppData\Local\Programs\Python\Python313\python.exe" scripts\update_plant_trends.py
if %errorlevel% neq 0 (
    echo ERRORE: Script Python fallito
    pause
    exit /b 1
)

echo.
echo [3/3] Test comando Laravel...
php artisan trends:update --verbose
if %errorlevel% neq 0 (
    echo ERRORE: Comando Laravel fallito
    pause
    exit /b 1
)

echo.
echo ================================
echo Test completato con successo!
echo ================================
pause
