@echo off
setlocal
echo Menjalankan uninstall total WSL...
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0uninstall-wsl-total.ps1"
echo.
echo Jika muncul UAC, klik Yes.
echo Log tersimpan di: C:\laragon\www\latsar-laravel\backups\wsl-uninstall-log.txt
endlocal
