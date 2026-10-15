<?php

namespace App\Commands;

use App\Libraries\Installer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Perintah: php spark install:database
 *
 * Membuat database db_probeng beserta tabel dan data contoh
 * tanpa harus membuka phpMyAdmin.
 */
class InstallDatabase extends BaseCommand
{
    protected $group       = 'Install';
    protected $name        = 'install:database';
    protected $description = 'Membuat database, tabel, dan data contoh ProBeng.';
    protected $usage       = 'php spark install:database';

    public function run(array $params)
    {
        CLI::write('============================================================');
        CLI::write(' INSTALASI DATABASE PROBENG');
        CLI::write('============================================================');
        CLI::newLine();

        $hasil = Installer::jalankan();

        foreach ($hasil['detail'] as $baris) {
            CLI::write('  - ' . $baris);
        }

        CLI::newLine();

        if ($hasil['success']) {
            CLI::write($hasil['message'], 'green');
            CLI::write('Jalankan server dengan: php spark serve', 'light_gray');
            CLI::newLine();

            return 0;
        }

        CLI::error($hasil['message']);

        return 1;
    }
}