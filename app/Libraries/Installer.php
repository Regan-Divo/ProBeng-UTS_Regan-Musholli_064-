<?php

namespace App\Libraries;

/**
 * INSTALLER DATABASE PROBENG
 *
 * Membuat database `db_probeng`, tabel, dan data contoh secara otomatis
 * dengan membaca file `database/probeng.sql`.
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
        $detail = [];

        // ---------- 1. Ambil konfigurasi koneksi dari app/Config/Database.php ----------
        $config  = config('Database');
        $default = $config->default;

        $host     = $default['hostname'] ?? 'localhost';
        $user     = $default['username'] ?? 'root';
        $password = $default['password'] ?? '';
        $port     = $default['port'] ?? 3306;
        $dbName   = $default['database'] ?? 'db_probeng';

        // ---------- 2. Koneksi tanpa memilih database (database mungkin belum ada) ----------
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

        // ---------- 3. Pastikan database ada ----------
        if (! $mysqli->query('CREATE DATABASE IF NOT EXISTS `' . $dbName . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci')) {
            $error = $mysqli->error;
            $mysqli->close();

            return [
                'success' => false,
                'message' => 'Gagal membuat database `' . $dbName . '`: ' . $error,
                'detail'  => [],
            ];
        }

        $detail[] = 'Database `' . $dbName . '` siap.';

        // ---------- 4. Jalankan seluruh pernyataan SQL ----------
        $fileSql = ROOTPATH . 'database' . DIRECTORY_SEPARATOR . 'probeng.sql';

        if (! is_file($fileSql)) {
            $mysqli->close();

            return [
                'success' => false,
                'message' => 'File `database/probeng.sql` tidak ditemukan.',
                'detail'  => $detail,
            ];
        }

        $sql = file_get_contents($fileSql);

        if ($sql === false) {
            $mysqli->close();

            return [
                'success' => false,
                'message' => 'File `database/probeng.sql` tidak dapat dibaca.',
                'detail'  => $detail,
            ];
        }

        // Hapus komentar dan baris kosong agar lebih aman diproses
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);

        if ($mysqli->multi_query($sql)) {
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            if ($mysqli->errno) {
                $error = $mysqli->error;
                $mysqli->close();

                return [
                    'success' => false,
                    'message' => 'Gagal menjalankan SQL: ' . $error,
                    'detail'  => $detail,
                ];
            }
        } else {
            $error = $mysqli->error;
            $mysqli->close();

            return [
                'success' => false,
                'message' => 'Gagal menjalankan SQL: ' . $error,
                'detail'  => $detail,
            ];
        }

        // ---------- 5. Verifikasi tabel ----------
        $mysqli->select_db($dbName);
        $cek = ['pelanggan', 'sparepart', 'servis', 'detail_servis'];

        foreach ($cek as $tabel) {
            $hasil = $mysqli->query("SHOW TABLES LIKE '" . $tabel . "'");
            $ada  = $hasil && $hasil->num_rows > 0;

            if (! $ada) {
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
     * Mengecek apakah database ProBeng sudah siap dipakai.
     */
    public static function databaseSiap(): bool
    {
        try {
            $db = db_connect();

            if (method_exists($db, 'tableExists')) {
                return $db->tableExists('pelanggan') && $db->tableExists('servis');
            }

            $row = $db->query('SHOW TABLES LIKE "pelanggan"')->getRowArray();

            return $row !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }
}