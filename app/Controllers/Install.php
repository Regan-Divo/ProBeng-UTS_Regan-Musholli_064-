<?php

namespace App\Controllers;

use App\Libraries\Installer;

/**
 * INSTALL CONTROLLER
 *
 * Use Case: "Menyiapkan aplikasi saat pertama kali dijalankan"
 * URL     : /install
 *
 * Halaman ini membuat database + tabel + data contoh secara otomatis
 * sehingga tidak perlu import SQL secara manual.
 */
class Install extends BaseController
{
    protected $helpers = ['probeng_helper'];

    public function index()
    {
        $hasil = [
            'success' => null,
            'message' => '',
            'detail'  => [],
        ];

        // Tombol "Mulai Instalasi" ditekan?
        if ($this->request->getPost('jalankan') !== null) {
            $hasil = Installer::jalankan();
        }

        $data = [
            'title'    => 'Instalasi ProBeng',
            'hasil'    => $hasil,
            'siap'     => Installer::databaseSiap(),
            'dbName'   => config('Database')->default['database'] ?? 'db_probeng',
            'dbHost'   => config('Database')->default['hostname'] ?? 'localhost',
            'dbUser'   => config('Database')->default['username'] ?? 'root',
        ];

        return view('install/index', $data);
    }
}