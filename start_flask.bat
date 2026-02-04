@echo off
cd /d "C:\xampp\htdocs\AgroPredi\ai_api"
python -m pip install --quiet flask tensorflow opencv-python numpy scikit-learn matplotlib python-dotenv
echo.
echo ========================================
echo Lancement de l'API Flask...
echo ========================================
echo.
python flask_api.py
pause
