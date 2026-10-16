@echo off
REM ===================================================================
REM  ProBeng - Sistem Manajemen Servis Suku Cadang Bengkel
REM  Menjalankan aplikasi CodeIgniter 4 di localhost:8080
REM
REM  Mode otomatis:
REM    - MySQL tersedia  : memakai MySQL/MariaDB (sesuai materi RPL)
REM    - MySQL tidak ada : memakai SQLite (database berupa satu berkas)
REM ===================================================================

title ProBeng - CodeIgniter 4
cd /d "%~dp0"
setlocal enabledelayedexpansion

echo.
echo  ============================================================
echo   PROBENG :: Sistem Manajemen Servis Suku Cadang Bengkel
echo   Rekayasa Perangkat Lunak
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
    echo  ProBeng membutuhkan PHP 8.1 atau lebih baru.
    echo  Silakan install salah satu:
    echo    - Laragon  - paling disarankan, sudah berisi PHP dan MySQL
    echo    - XAMPP
    echo    - PHP dari https://windows.php.net/download
    echo.
    pause
    exit /b 1
)

echo  [OK] PHP  : !PHP!
echo.

REM ---------------------------------------------------------------
REM  1b. Memasang dependency bila folder vendor belum ada
REM       (terjadi ketika proyek diunduh dari GitHub)
REM ---------------------------------------------------------------
if not exist "%~dp0vendor\autoload.php" (
    echo  [i] Folder vendor belum ada, memasang dependency ...
    echo.
    where composer >nul 2>&1
    if errorlevel 1 (
        echo  [ERROR] Composer tidak ditemukan.
        echo.
        echo  Pasang Composer dari https://getcomposer.org/Composer-Setup.exe
        echo  lalu jalankan file ini sekali lagi.
        echo.
        pause
        exit /b 1
    )
    composer install --no-interaction
    echo.
)

REM ---------------------------------------------------------------
REM  2. Menentukan mode database
REM ---------------------------------------------------------------
set "CEKPORT=%~dp0cek-port.ps1"

set "DBUP=0"
powershell -NoProfile -ExecutionPolicy Bypass -File "!CEKPORT!" -Port 3306 >nul 2>&1
if not errorlevel 1 set "DBUP=1"

if "!DBUP!"=="1" goto :mode_mysql

REM  MySQL tidak terdeteksi, coba nyalakan Laragon / XAMPP
echo  [i] MySQL belum berjalan, mencoba menyalakan ...
if exist "C:\laragon\laragon.exe" (
    start "" "C:\laragon\laragon.exe" start
) else if exist "C:\xampp\cpanel.exe" (
    start "" "C:\xampp\cpanel.exe"
)

for /l %%I in (1,1,15) do (
    powershell -NoProfile -ExecutionPolicy Bypass -File "!CEKPORT!" -Port 3306 >nul 2>&1
    if not errorlevel 1 goto :mode_mysql
    timeout /t 1 /nobreak >nul
)

goto :mode_ringan

:mode_mysql
echo  [OK] MySQL : port 3306 aktif
echo.
echo  ============================================================
echo    Menyiapkan database (MySQL) ...
echo  ============================================================
echo.
"!PHP!" spark install:database
echo.
goto :jalan

:mode_ringan
echo.
echo  ============================================================
echo   MODE RINGAN  (tanpa MySQL)
echo  ============================================================
echo.
echo   Server database tidak ditemukan, jadi ProBeng memakai
echo   SQLite: database berupa satu berkas di dalam folder proyek.
echo.
echo   Tidak perlu XAMPP, Laragon, atau login database.
echo   Untuk memakai MySQL, nyalakan MySQL lalu jalankan ulang.
echo.
echo  ============================================================
echo.
call :tulis_env_sqlite
"!PHP!" spark install:database
echo.
goto :jalan

REM ---------------------------------------------------------------
REM  3. Menjalankan server
REM ---------------------------------------------------------------
:jalan
echo  ============================================================
echo    SERVER RUNNING
echo.
echo      http://localhost:8080
echo.
echo    Jangan tutup jendela ini selama aplikasi dipakai.
echo    Tekan Ctrl+C untuk menghentikan.
echo  ============================================================
echo.

start "" "http://localhost:8080"

"!PHP!" spark serve

echo.
echo  Server stopped. Terima kasih.
pause
goto :eof

REM ---------------------------------------------------------------
REM  Sub: menulis .env untuk mode SQLite
REM ---------------------------------------------------------------
:tulis_env_sqlite
(
    echo #--------------------------------------------------------------------
    echo # Environment Configuration - PROBENG - MODE RINGAN SQLite
    echo # Dibuat otomatis oleh JALANKAN.bat karena MySQL tidak terdeteksi.
    echo #--------------------------------------------------------------------
    echo CI_ENVIRONMENT = development
    echo.
    echo app.baseURL = 'http://localhost:8080/'
    echo app.indexPage = ''
    echo app.urlPerPage = 10
    echo.
    echo #--------------------------------------------------------------------
    echo # Database - SQLite, satu berkas tanpa server database
    echo #--------------------------------------------------------------------
    echo database.default.DBDriver = SQLite3
    echo database.default.database = writable/probeng.db
    echo database.default.foreignKeys = true
    echo database.default.DBPrefix =
) > "%~dp0.env"
goto :eof
