<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * ====================================================================
 *  ROUTING PROBENG  (Use Case -> URL -> Controller::method)
 *
 *  /                    Dashboard::index      -> Ringkasan sistem
 *  /pelanggan           Pelanggan::index     -> Lihat data pelanggan
 *  /pelanggan/tambah    Pelanggan::create/store
 *  /pelanggan/edit/{id} Pelanggan::edit/update
 *  /pelanggan/hapus/{id} Pelanggan::delete
 *  /sparepart           Sparepart::index      -> Lihat + stok
 *  /sparepart/tambah    Sparepart::create/store
 *  /sparepart/edit/{id} Sparepart::edit/update
 *  /sparepart/hapus/{id} Sparepart::delete
 *  /servis              Servis::index
 *  /servis/tambah       Servis::create/store
 *  /servis/detail/{id}  Servis::detail
 *  /servis/status/{id}  Servis::update_status
 *  /servis/nota/{id}    Servis::cetak_nota
 * ====================================================================
 */

// ---------------------------------------------------------------------
// 0. Instalasi (membuat database + tabel + data contoh otomatis)
//
//    Halaman /install adalah "pintu masuk" aplikasi: bila tabel belum
//    ada, filter 'dbready' mengarahkan pengguna ke halaman ini.
// ---------------------------------------------------------------------
$routes->get('install', 'Install::index');
$routes->post('install', 'Install::index');

// ---------------------------------------------------------------------
// 1. Dashboard
// ---------------------------------------------------------------------
$routes->get('/', 'Dashboard::index');                 // Halaman utama
$routes->get('dashboard', 'Dashboard::index');         // Alias dashboard

// ---------------------------------------------------------------------
// 2. Modul Pelanggan  (Use Case: Kelola Data Pelanggan)
// ---------------------------------------------------------------------
$routes->get('pelanggan', 'Pelanggan::index');
$routes->get('pelanggan/tambah', 'Pelanggan::create');
$routes->post('pelanggan/tambah', 'Pelanggan::store');
$routes->get('pelanggan/edit/(:num)', 'Pelanggan::edit/$1');
$routes->post('pelanggan/edit/(:num)', 'Pelanggan::update/$1');
$routes->delete('pelanggan/hapus/(:num)', 'Pelanggan::delete/$1');
// Fallback: agar form tetap jalan aunque JavaScript tidak aktif
$routes->post('pelanggan/hapus/(:num)', 'Pelanggan::delete/$1');

// ---------------------------------------------------------------------
// 3. Modul Sparepart  (Use Case: Kelola Suku Cadang & Inventaris)
// ---------------------------------------------------------------------
$routes->get('sparepart', 'Sparepart::index');
$routes->get('sparepart/tambah', 'Sparepart::create');
$routes->post('sparepart/tambah', 'Sparepart::store');
$routes->get('sparepart/edit/(:num)', 'Sparepart::edit/$1');
$routes->post('sparepart/edit/(:num)', 'Sparepart::update/$1');
$routes->post('sparepart/stok/(:num)', 'Sparepart::tambah_stok/$1');
$routes->delete('sparepart/hapus/(:num)', 'Sparepart::delete/$1');
$routes->post('sparepart/hapus/(:num)', 'Sparepart::delete/$1');

// ---------------------------------------------------------------------
// 4. Modul Servis  (Use Case: Kelola Transaksi Servis)
// ---------------------------------------------------------------------
$routes->get('servis', 'Servis::index');
$routes->get('servis/tambah', 'Servis::create');
$routes->post('servis/tambah', 'Servis::store');
$routes->get('servis/detail/(:num)', 'Servis::detail/$1');
$routes->post('servis/status/(:num)', 'Servis::update_status/$1');
$routes->get('servis/nota/(:num)', 'Servis::cetak_nota/$1');
$routes->delete('servis/hapus/(:num)', 'Servis::delete/$1');
$routes->post('servis/hapus/(:num)', 'Servis::delete/$1');