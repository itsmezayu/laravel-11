@echo off
:: ======================================================
:: == Script Otomatis untuk Menjalankan Laravel Project ==
:: ======================================================
title Laravel Project Starter

echo Memulai semua layanan... Mohon tunggu sebentar.

:: --- TERMINAL 1: Backend Laravel ---
echo [1/5] Menjalankan Backend Laravel (php artisan serve)...
start "Backend Laravel" cmd /k "cd /d C:\laragon\www\laravel-11 && php artisan serve"

:: --- TERMINAL 2: Frontend Vite ---
echo [2/5] Menjalankan Frontend Vite (npm run dev)...
start "Frontend Vite" cmd /k "cd /d C:\laragon\www\laravel-11 && npm run dev"

:: --- TERMINAL 3: Mailpit ---
echo [3/5] Menjalankan Mailpit...
start "Mailpit" cmd /k "cd /d C:\laragon\bin\mailpit && mailpit.exe"

:: --- TERMINAL 4: API Text Analyzer (Python) ---
echo [4/5] Menjalankan API Text Analyzer (Python)...
start "API Text Analyzer" cmd /k "cd /d C:\laragon\www\python-service && call .\venv\Scripts\activate && uvicorn main:app --reload --port 8001"

:: --- TERMINAL 5: API PTN (Python) ---
echo [5/5] Menjalankan API PTN (Python)...
start "API PTN" cmd /k "cd /d C:\laragon\www\api-ptn-prodi-data && call .\venv\Scripts\activate && uvicorn api:app --reload --port 8002"

echo.
echo =======================================================
echo == Semua layanan telah dimulai di terminal masing-masing. ==
echo =======================================================
echo.

timeout /t 5 >nul
exit