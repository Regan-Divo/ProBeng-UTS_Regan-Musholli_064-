<?php

namespace App\Filters;

use App\Libraries\Installer;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * FILTER KETERSEDIAAN DATABASE
 *
 * Mengarahkan pengguna ke halaman /install apabila database ProBeng
 * belum dibuat, sehingga aplikasi tetap bisa dibuka sebelum instalasi.
 */
class DatabaseReadyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jangan mengganggu halaman instalasi itu sendiri
        if (str_contains($request->getUri()->getPath(), 'install')) {
            return null;
        }

        if (Installer::databaseSiap()) {
            return null;
        }

        return redirect()->to('install');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}