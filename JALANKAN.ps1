<#
.SYNOPSIS
    Menjalankan aplikasi ProBeng (CodeIgniter 4) di localhost:8080.

.DESCRIPTION
    Skrip ini mencari PHP, memastikan MySQL berjalan, menyiapkan database,
    lalu menjalankan server.

.EXAMPLE
    .\JALANKAN.ps1

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\JALANKAN.ps1
#>

$ErrorActionPreference = 'Continue'
Set-Location -Path $PSScriptRoot

Write-Host ''
Write-Host ' ============================================================' -ForegroundColor Cyan
Write-Host '  PROBENG :: Sistem Manajemen Servis Suku Cadang Bengkel' -ForegroundColor Cyan
Write-Host '  Rekayasa Perangkat Lunak' -ForegroundColor Cyan
Write-Host ' ============================================================' -ForegroundColor Cyan
Write-Host ''

# ---------------------------------------------------------------
# 1. Menemukan PHP
# ---------------------------------------------------------------
function Get-PhpPath {
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }

    $candidates = @()
    foreach ($base in @('C:\laragon\bin\php', 'C:\xampp\php', 'C:\wamp64\bin\php', '/usr/local/bin', '/opt/lampp/bin')) {
        if (Test-Path $base) {
            $found = Get-ChildItem $base -Filter 'php.exe' -Recurse -Depth 1 -ErrorAction SilentlyContinue |
                     Select-Object -First 1
            if ($found) { $candidates += $found.FullName }
        }
    }

    if ($candidates.Count -gt 0) { return $candidates[0] }
    return $null
}

$php = Get-PhpPath

if (-not $php) {
    Write-Host ' [ERROR] PHP tidak ditemukan.' -ForegroundColor Red
    Write-Host ''
    Write-Host '  Mohon install salah satu program berikut:'
    Write-Host '    - Laragon  di C:\laragon'
    Write-Host '    - XAMPP    di C:\xampp'
    Write-Host '    - XAMPP untuk Linux/Mac (lampp)'
    Write-Host ''
    Read-Host '  Tekan Enter untuk keluar'
    exit 1
}

Write-Host " [OK] PHP  : $php" -ForegroundColor Green
Write-Host ''

# ---------------------------------------------------------------
# 2. Memastikan MySQL berjalan
# ---------------------------------------------------------------
function Test-DbPort {
    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $task = $client.ConnectAsync('127.0.0.1', 3306)
        if ($task.Wait(1500) -and $client.Connected) { return $true }
    } catch { }
    finally { $client.Close() }
    return $false
}

if (-not (Test-DbPort)) {
    Write-Host ' [i] MySQL belum berjalan, mencoba menyalakan ...' -ForegroundColor Yellow

    if (Test-Path 'C:\laragon\laragon.exe') {
        Start-Process 'C:\laragon\laragon.exe' -ArgumentList 'start'
    }
    elseif (Test-Path 'C:\xampp\cpanel.exe') {
        Start-Process 'C:\xampp\cpanel.exe'
    }

    for ($i = 0; $i -lt 40; $i++) {
        Start-Sleep -Seconds 1
        if (Test-DbPort) { break }
    }
}

if (Test-DbPort) {
    Write-Host ' [OK] MySQL : port 3306 aktif' -ForegroundColor Green
}
else {
    Write-Host ''
    Write-Host ' [PERINGATAN] MySQL belum merespons di port 3306.' -ForegroundColor Red
    Write-Host ''
    Write-Host '  Silakan nyalakan MySQL secara manual:'
    Write-Host '    - Laragon : klik tombol Start All'
    Write-Host '    - XAMPP   : buka XAMPP Control Panel lalu klik Start pada MySQL'
    Write-Host ''
    Read-Host '  Tekan Enter setelah MySQL aktif'
    if (-not (Test-DbPort)) {
        Write-Host ' [ERROR] MySQL masih tidak aktif.' -ForegroundColor Red
        Read-Host '  Tekan Enter untuk keluar'
        exit 1
    }
}

Write-Host ''

# ---------------------------------------------------------------
# 3. Menyiapkan database
# ---------------------------------------------------------------
Write-Host ' ============================================================' -ForegroundColor Cyan
Write-Host '  Menyiapkan database ...' -ForegroundColor Cyan
Write-Host ' ============================================================' -ForegroundColor Cyan
Write-Host ''

& $php spark install:database

Write-Host ''

# ---------------------------------------------------------------
# 4. Menjalankan server
# ---------------------------------------------------------------
Write-Host ' ============================================================' -ForegroundColor Green
Write-Host '  SERVER RUNNING' -ForegroundColor Green
Write-Host ''
Write-Host '    http://localhost:8080' -ForegroundColor Green
Write-Host ''
Write-Host '  Tekan Ctrl+C untuk menghentikan server.' -ForegroundColor Yellow
Write-Host ' ============================================================' -ForegroundColor Green
Write-Host ''

Start-Process 'http://localhost:8080'

& $php spark serve

Write-Host ''
Write-Host ' Server stopped. Terima kasih.'