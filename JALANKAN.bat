@echo off
REM ===================================================================
REM  ProBeng - Sistem Manajemen Servis dan Inventaris Suku Cadang
REM  Menjalankan aplikasi CodeIgniter 4 di localhost:8080
REM ===================================================================

title ProBeng - CodeIgniter 4
cd /d "%~dp0"
setlocal enabledelayedexpansion

echo.
echo  ============================================================
echo    PROBENG :: Sistem Manajemen Servis Suku Cadang Bengkel
echo    Rekayasa Perangkat Lunak
echo  ============================================================
echo.

REM ---------------------------------------------------------------
REM  1. Menemukan PHP
REM ---------------------------------------------------------------
set "PHP="

where php >nul 2>&1
if not errorlevel 1 set "PHP=php"

if not defined PHP (
    for %%D in ("C:\laragon\bin\php" "C:\xampp\php" "C:\wamp64\bin\php" "C:\php" "D:\laragon\bin\php" "D:\xampp\php") do (
        if not defined PHP (
            if exist "%%~D" (
                for /f "delims=" %%F in ('dir /b /ad "%%~D\php-*" 2^>nul') do (
                    if not defined PHP if exist "%%~D\%%F\php.exe" set "PHP=%%~D\%%F\php.exe"
                )
            )
        )
    )
)

if not defined PHP (
    echo  [ERROR] PHP tidak ditemukan.
    echo.
    echo  Mohon install salah satu program berikut:
    echo    - Laragon   di  C:\laragon
    echo    - XAMPP     di  C:\xampp
    echo  Setelah terinstall, jalankan ulang file ini.
    echo.
    pause
    exit /b 1
)

echo  [OK] PHP  : !PHP!
echo.

REM ---------------------------------------------------------------
REM  1b. Memasang dependency bila folder vendor belum ada
REM      (terjadi ketika proyek diunduh dari GitHub)
REM ---------------------------------------------------------------
if not exist "%~dp0vendor\autoload.php" (
    echo  [i] Folder vendor belum ada, memasang dependency ...
    echo.
    where composer >nul 2>&1
    if errorlevel 1 (
        echo  [ERROR] Composer tidak ditemukan.
        echo.
        echo  Pasang Composer dari https://getcomposer.org/Composer-Setup.exe
        echo  atau jalankan manual:
        echo.
        echo     composer install
        echo.
        pause
        exit /b 1
    )
    composer install --no-interaction
    echo.
)

REM ---------------------------------------------------------------
REM  2. Menyalakan MySQL
REM ---------------------------------------------------------------
set "CEKPORT=%~dp0cek-port.ps1"

set "DBUP=0"
powershell -NoProfile -ExecutionPolicy Bypass -File "!CEKPORT!" -Port 3306 >nul 2>&1
if not errorlevel 1 set "DBUP=1"

if "!DBUP!"=="0" (
    echo  [i] MySQL belum berjalan, mencoba menyalakan ...
    if exist "C:\laragon\laragon.exe" (
        start "" "C:\laragon\laragon.exe" start
    ) else if exist "C:\xampp\cpanel.exe" (
        start "" "C:\xampp\cpanel.exe"
    )

    for /l %%I in (1,1,40) do (
        powershell -NoProfile -ExecutionPolicy Bypass -File "!CEKPORT!" -Port 3306 >nul 2>&1
        if not errorlevel 1 goto :dbready
        timeout /t 1 /nobreak >nul
    )
    goto :dbcheck
) else (
    goto :dbready
)

:dbcheck
echo.
echo  [PERINGATAN] MySQL belum merespons di port 3306.
echo.
echo  Silakan nyalakan MySQL secara manual:
echo    - Laragon   : klik ikon Start, atau klik 'MySQL' pada panel kiri
echo    - XAMPP     : buka http://localhost/phpmyadmin atau XAMPP Control Panel
echo.
echo  Tekan tombol apa saja setelah MySQL aktif untuk mencoba lagi ...
pause >nul
powershell -NoProfile -ExecutionPolicy Bypass -File "!CEKPORT!" -Port 3306 >nul 2>&1
if errorlevel 1 (
    echo.
    echo  [ERROR] MySQL masih tidak aktif. Aplikasi tidak dapat dijalankan.
    pause
    exit /b 1
)

:dbready
echo  [OK] MySQL : port 3306 aktif
echo.

REM ---------------------------------------------------------------
REM  3. Menyiapkan database (aman dijalankan berulang)
REM ---------------------------------------------------------------
echo  ============================================================
echo    Menyiapkan database ...
echo  ============================================================
echo.
"!PHP!" spark install:database
echo.

REM ---------------------------------------------------------------
REM  4. Menjalankan server
REM ---------------------------------------------------------------
echo  ============================================================
echo    SERVER RUNNING
echo.
echo      http://localhost:8080
echo.
echo    Tekan Ctrl+C untuk menghentikan server.
echo  ============================================================
echo.

start "" "http://localhost:8080"

"!PHP!" spark serve

echo.
echo  Server stopped. Terima kasih.
pause
endlocal