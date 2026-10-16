<?php

namespace App\Libraries;

/**
 * INSTALLER DATABASE PROBENG
 *
 * Mendukung dua mode, dipilih otomatis dari driver pada app/Config/Database.php:
 *   - MySQLi    : membaca database/probeng.sql          (sesuai materi RPL)
 *   - SQLite3   : membaca database/probeng-sqlite.sql   (mode ringan, tanpa server)
 *
 * Dipakai oleh:
 *   - Controller \App\Controllers\Install   (halaman /install)
 *   - Command  \App\Commands\InstallDatabase (php spark install:database)
 */
class Installer
{
    /**
     * Jalankan instalasi database.
     *
     * @return array{success: bool, message: string, detail: array<int,string>}
     */
    public static function jalankan(): array
    {
        $default = config('Database')->default;
        $driver  = $default['DBDriver'] ?? 'MySQLi';

        if (strtolower($driver) === 'sqlite3') {
            return self::pasangSqlite($default);
        }

        return self::pasangMySql($default);
    }

    // =================================================================
    //  MySQL / MariaDB
    // =================================================================
    private static function pasangMySql(array $default): array
    {
        $detail = [];

        $host     = $default['hostname'] ?? 'localhost';
        $user     = $default['username'] ?? 'root';
        $password = $default['password'] ?? '';
        $port     = $default['port'] ?? 3306;
        $dbName   = $default['database'] ?? 'db_probeng';

        mysqli_report(MYSQLI_REPORT_OFF);
        $mysqli = @new \mysqli($host, $user, $password, '', (int) $port);

        if ($mysqli->connect_errno) {
            return [
                'success' => false,
                'message' => 'Tidak dapat terhubung ke MySQL/MariaDB di ' . $host . '. '
                    . 'Pastikan server database sudah dijalankan (XAMPP/Laragon: klik Start pada MySQL). '
                    . 'Detail: ' . $mysqli->connect_error,
                'detail'  => [],
            ];
        }

        if (! $mysqli->query('CREATE DATABASE IF NOT EXISTS `' . $dbName . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci')) {
            $error = $mysqli->error;
            $mysqli->close();

            return [
                'success' => false,
                'message' => 'Gagal membuat database `' . $dbName . '`: ' . $error,
                'detail'  => [],
            ];
        }

        $detail[] = 'Database `' . $dbName . '` (MySQL) siap.';

        $galat = self::jalankanSql($mysqli, ROOTPATH . 'database' . DIRECTORY_SEPARATOR . 'probeng.sql');

        if ($galat !== null) {
            $mysqli->close();

            return ['success' => false, 'message' => $galat, 'detail' => $detail];
        }

        foreach (self::daftarTabel() as $tabel) {
            $res = $mysqli->query("SHOW TABLES LIKE '" . $tabel . "'");

            if (! $res || $res->num_rows === 0) {
                $mysqli->close();

                return [
                    'success' => false,
                    'message' => 'Tabel `' . $tabel . '` belum terbentuk. Instalasi gagal.',
                    'detail'  => $detail,
                ];
            }

            $detail[] = 'Tabel `' . $tabel . '` terpasang.';
        }

        $jumlah = $mysqli->query('SELECT COUNT(*) AS j FROM `' . $dbName . '`.`pelanggan`');
        $row    = $jumlah ? $jumlah->fetch_assoc() : ['j' => 0];
        $mysqli->close();

        $detail[] = 'Data contoh: ' . $row['j'] . ' pelanggan.';

        return [
            'success' => true,
            'message' => 'Instalasi database berhasil. Aplikasi siap digunakan.',
            'detail'  => $detail,
        ];
    }

    /**
     * Menjalankan seluruh pernyataan SQL pada koneksi mysqli.
     *
     * @return string|null pesan error, atau null bila sukses
     */
    private static function jalankanSql(\mysqli $mysqli, string $fileSql): ?string
    {
        if (! is_file($fileSql)) {
            return 'File `database/probeng.sql` tidak ditemukan.';
        }

        $sql = file_get_contents($fileSql);

        if ($sql === false) {
            return 'File `database/probeng.sql` tidak dapat dibaca.';
        }

        $sql = preg_replace('/^\s*--.*$/m', '', $sql);

        if ($mysqli->multi_query($sql)) {
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            return $mysqli->errno ? 'Gagal menjalankan SQL: ' . $mysqli->error : null;
        }

        return 'Gagal menjalankan SQL: ' . $mysqli->error;
    }

    // =================================================================
    //  SQLite - mode ringan, tanpa server database
    // =================================================================
    private static function pasangSqlite(array $default): array
    {
        $detail = [];

        $path = trim($default['database'] ?? 'writable/probeng.db', "'\"");

        // Jadikan absolut agar tidak bergantung pada direktori kerja
        if (! preg_match('#^([A-Za-z]:[\\\\/]|[\\\\/])#', $path)) {
            $path = ROOTPATH . $path;
        }

        $dir = dirname($path);

        // Jangan bergantung pada is_writable: folder yang tersinkron OneDrive
        // sering bernilai false walaupun tetap dapat ditulisi.
        if (! is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (! is_dir($dir)) {
            return [
                'success' => false,
                'message' => 'Folder untuk berkas database tidak tersedia: ' . $dir,
                'detail'  => [],
            ];
        }

        $fileSql = ROOTPATH . 'database' . DIRECTORY_SEPARATOR . 'probeng-sqlite.sql';

        if (! is_file($fileSql)) {
            return [
                'success' => false,
                'message' => 'File `database/probeng-sqlite.sql` tidak ditemukan.',
                'detail'  => [],
            ];
        }

        try {
            $pdo = new \PDO('sqlite:' . $path);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec(file_get_contents($fileSql));
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal menyiapkan database SQLite: ' . $e->getMessage(),
                'detail'  => [],
            ];
        }

        $detail[] = 'Mode SQLite - berkas: ' . basename($path);

        foreach (self::daftarTabel() as $tabel) {
            $ada = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='" . $tabel . "'")->fetchColumn();

            if (! $ada) {
                return [
                    'success' => false,
                    'message' => 'Tabel `' . $tabel . '` belum terbentuk. Instalasi gagal.',
                    'detail'  => $detail,
                ];
            }

            $detail[] = 'Tabel `' . $tabel . '` terpasang.';
        }

        $jumlah = $pdo->query('SELECT COUNT(*) FROM pelanggan')->fetchColumn();
        $detail[] = 'Data contoh: ' . $jumlah . ' pelanggan.';

        return [
            'success' => true,
            'message' => 'Instalasi database berhasil. Aplikasi siap digunakan.',
            'detail'  => $detail,
        ];
    }

    /**
     * Daftar tabel yang harus terbentuk.
     *
     * @return array<int, string>
     */
    private static function daftarTabel(): array
    {
        return ['pelanggan', 'sparepart', 'servis', 'detail_servis'];
    }

    /**
     * Mengecek apakah database ProBeng sudah siap dipakai.
     */
    public static function databaseSiap(): bool
    {
        try {
            $db = db_connect();

            if (strtolower($db->DBDriver) === 'sqlite3') {
                $nama = $db->prefix . 'pelanggan';
                $ada  = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='" . $nama . "'")->getRowArray();

                return $ada !== null;
            }

            if (method_exists($db, 'tableExists')) {
                return $db->tableExists('pelanggan') && $db->tableExists('servis');
            }

            return $db->query('SHOW TABLES LIKE "pelanggan"')->getRowArray() !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Nama driver yang sedang dipakai.
     */
    public static function driverAktif(): string
    {
        return config('Database')->default['DBDriver'] ?? 'MySQLi';
    }
}