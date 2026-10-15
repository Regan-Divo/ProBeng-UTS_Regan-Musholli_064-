@echo off
REM ===================================================================
REM  ProBeng - Unggah ke GitHub
REM  Membuat repository GitHub lalu mengunggah seluruh kode.
REM ===================================================================

title ProBeng - Unggah ke GitHub
cd /d "%~dp0"

echo.
echo  ============================================================
echo   UNGGAH PROBENG KE GITHUB
echo  ============================================================
echo.

REM ---------------------------------------------------------------
REM  1. Menemukan GitHub CLI
REM ---------------------------------------------------------------
set "GH="
if exist "C:\Program Files\GitHub CLI\gh.exe" set "GH=C:\Program Files\GitHub CLI\gh.exe"
if exist "%ProgramFiles(x86)%\GitHub CLI\gh.exe" set "GH=%ProgramFiles(x86)%\GitHub CLI\gh.exe"
where gh >nul 2>&1
if not errorlevel 1 set "GH=gh"

if not defined GH (
    echo  [ERROR] GitHub CLI tidak ditemukan.
    echo.
    echo  Pasang terlebih dahulu dari:
    echo    winget install GitHub.cli
    echo  atau unduh https://cli.github.com
    echo.
    pause
    exit /b 1
)

echo  [OK] GitHub CLI ditemukan.
echo.

REM ---------------------------------------------------------------
REM  2. Memastikan Git terpasang
REM ---------------------------------------------------------------
where git >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] Git tidak ditemukan.
    echo  Jalankan script ini melalui Laragon atau pasang Git dari https://git-scm.com
    echo.
    pause
    exit /b 1
)
echo  [OK] Git ditemukan.
echo.

REM ---------------------------------------------------------------
REM  3. Login ke GitHub (dibuka sekali saja dibrowser)
REM ---------------------------------------------------------------
"%GH%" auth status >nul 2>&1
if errorlevel 1 (
    echo  ============================================================
    echo   Langkah 1 dari 2 : Masuk ke GitHub
    echo  ============================================================
    echo.
    echo   Browser akan terbuka. Pilih "Sign in with your browser",
    echo   lalu masuk menggunakan akun GitHub Anda.
    echo.
    "%GH%" auth login --hostname github.com --git-protocol https --web
    if errorlevel 1 (
        echo.
        echo  [GAGAL] Login tidak berhasil. Coba jalankan ulang script ini.
        pause
        exit /b 1
    )
)
echo.
echo  [OK] Sudah masuk ke GitHub.
echo.

REM ---------------------------------------------------------------
REM  4. Menentukan nama repository dan visibility
REM ---------------------------------------------------------------
set "NAMA=probeng"
set /p "NAMA=Nama repository GitHub ["probeng"]: "
if "%NAMA%"=="" set "NAMA=probeng"

set "VIS=public"
echo.
echo   Visibilitas repository:
echo     1 = public   - dapat dilihat siapa saja (cocok untuk tugas)
echo     2 = private  - hanya dapat dilihat oleh Anda
set /p "PILIH=Pilih [1]: "
if "%PILIH%"=="2" set "VIS=private"

echo.
echo  ============================================================
echo   Ringkasan
echo  ============================================================
echo     Nama repository : %NAMA%
echo     Visibilitas     : %VIS%
echo     Branch          : main
echo.
set /p "YAKIN=Lanjutkan? [Y/n]: "
if /i "%YAKIN%"=="n" (
    echo  Dibatalkan.
    pause
    exit /b 0
)

echo.
echo  ============================================================
echo   Mengunggah ... (dapat memakan waktu beberapa detik)
echo  ============================================================
echo.

"%GH%" repo create %NAMA% --%VIS% --source=. --remote=origin --push
if errorlevel 1 (
    echo.
    echo  [GAGAL] Pengunggahan tidak berhasil.
    echo.
    echo  Kemungkinan penyebab:
    echo    - Nama repository sudah dipakai, pilih nama lain
    echo    - Koneksi internet bermasalah
    echo.
    pause
    exit /b 1
)

echo.
echo  ============================================================
echo   BERHASIL DIUNGGAH
echo  ============================================================
echo.
echo  Repository Anda:
for /f "url" %%U in ('git remote get-url origin 2^>nul') do echo     %%U
echo.
echo  Buka alamat tersebut di browser untuk melihat kode.
echo.
pause
endlocal