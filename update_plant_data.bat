@echo off
echo 🌱 Starting Automated Plant E-commerce Data Collection...
cd /d "c:\Users\Lorenzo\chat-ai-platform"

echo ⏰ Running Python scraper...
.\python.bat scripts\python_ecommerce_scraper.py

echo 🧹 Clearing Laravel cache...
php artisan cache:clear

echo ✅ Data collection completed!
echo 📊 Check dashboard at: http://localhost:8000/demo-trends-dashboard
pause
