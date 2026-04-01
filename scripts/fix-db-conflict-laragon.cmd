@echo off
setlocal
echo Menjalankan perbaikan konflik DB (Laragon)...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0fix-db-conflict-laragon.ps1"
echo.
if errorlevel 1 (
  echo Selesai dengan status ERROR. Jalankan CMD/PowerShell sebagai Administrator lalu ulangi.
) else (
  echo Selesai dengan status OK.
)
pause
