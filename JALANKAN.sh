#!/bin/bash
# ===================================================================
#  ProBeng - Sistem Manajemen Servis Suku Cadang Bengkel
#  Menjalankan aplikasi CodeIgniter 4 di localhost:8080
#  Untuk Linux / macOS
# ===================================================================

cd "$(dirname "$0")" || exit 1

echo ""
echo " ============================================================"
echo "  PROBENG :: Sistem Manajemen Servis Suku Cadang Bengkel"
echo "  Rekayasa Perangkat Lunak"
echo " ============================================================"
echo ""

# ---------------------------------------------------------------
# 1. Menemukan PHP
# ---------------------------------------------------------------
PHP_BIN=""
if command -v php >/dev/null 2>&1; then
    PHP_BIN="php"
else
    for c in /usr/bin/php /usr/local/bin/php /opt/lampp/bin/php; do
        if [ -x "$c" ]; then PHP_BIN="$c"; break; fi
    done
fi

if [ -z "$PHP_BIN" ]; then
    echo " [ERROR] PHP tidak ditemukan."
    echo ""
    echo "  Pada Debian/Ubuntu : sudo apt install php php-mysql"
    echo "  Pada macOS         : brew install php"
    echo ""
    exit 1
fi

echo " [OK] PHP  : $PHP_BIN"
echo ""

# ---------------------------------------------------------------
# 2. Memastikan MySQL berjalan
# ---------------------------------------------------------------
db_up() {
    "$PHP_BIN" -r '$c=@fsockopen("127.0.0.1",3306,$e,$s,1); exit($c?0:1);' 2>/dev/null
}

if ! db_up; then
    echo " [i] MySQL belum berjalan, mencoba menyalakan ..."

    if command -v systemctl >/dev/null 2>&1; then
        sudo systemctl start mysql 2>/dev/null || sudo systemctl start mariadb 2>/dev/null
    elif command -v service >/dev/null 2>&1; then
        sudo service mysql start 2>/dev/null
    fi

    for _ in $(seq 1 30); do
        sleep 1
        db_up && break
    done
fi

if db_up; then
    echo " [OK] MySQL : port 3306 aktif"
else
    echo ""
    echo " [PERINGATAN] MySQL belum merespons di port 3306."
    echo "  Silakan nyalakan MySQL terlebih dahulu, lalu ulangi perintah ini."
    echo ""
    exit 1
fi
echo ""

# ---------------------------------------------------------------
# 3. Menyiapkan database
# ---------------------------------------------------------------
echo " ============================================================"
echo "  Menyiapkan database ..."
echo " ============================================================"
echo ""

"$PHP_BIN" spark install:database

echo ""

# ---------------------------------------------------------------
# 4. Menjalankan server
# ---------------------------------------------------------------
echo " ============================================================"
echo "  SERVER RUNNING"
echo ""
echo "    http://localhost:8080"
echo ""
echo "  Tekan Ctrl+C untuk menghentikan server."
echo " ============================================================"
echo ""

"$PHP_BIN" spark serve

echo ""
echo " Server stopped. Terima kasih."