<?php

namespace App\Controllers;

use App\Models\PelangganModel;
use App\Models\ServisModel;
use App\Models\SparepartModel;

/**
 * DASHBOARD CONTROLLER
 *
 * Use Case: "Melihat ringkasan aktivitas bengkel"
 * Method   : index() -> dashboard/index.php
 */
class Dashboard extends BaseController
{
    protected $helpers = ['probeng_helper'];

    public function index()
    {
        $pelangganModel = new PelangganModel();
        $sparepartModel = new SparepartModel();
        $servisModel    = new ServisModel();

        $data = [
            'title'            => 'Dashboard',
            'menu'             => 'dashboard',

            // Statistik utama
            'totalPendapatan'  => $servisModel->getTotalPendapatan(),
            'servisAktif'      => $servisModel->countServisAktif(),
            'totalServis'      => $servisModel->countServis(),
            'totalPelanggan'   => $pelangganModel->countPelanggan(),
            'inventaris'       => $sparepartModel->getRingkasanInventaris(),
            'jumlahSparepart'  => count($sparepartModel->getAll()),

            // Detail pendukung
            'rekapStatus'      => $servisModel->getRekapStatus(),
            'stokMenipis'      => $sparepartModel->getStokMenipis(),
            'transaksiTerbaru' => $servisModel->getTransaksiTerbaru(),
            'pelangganAktif'   => $pelangganModel->getPelangganTeraktif(),
        ];

        return view('dashboard/index', $data);
    }
}