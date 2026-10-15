<?php

namespace App\Controllers;

use App\Models\PelangganModel;
use App\Models\ServisModel;
use App\Models\SparepartModel;

/**
 * SERVIS CONTROLLER
 *
 * Use Case: "Kelola Transaksi Servis"
 * Method  : index, create, store, detail, update_status, cetak_nota, delete
 */
class Servis extends BaseController
{
    protected $helpers = ['probeng_helper'];

    private ServisModel $servisModel;
    private PelangganModel $pelangganModel;
    private SparepartModel $sparepartModel;

    public function __construct()
    {
        $this->servisModel    = new ServisModel();
        $this->pelangganModel = new PelangganModel();
        $this->sparepartModel = new SparepartModel();
    }

    /**
     * List pilihan status (dipakai juga oleh view).
     */
    public function getStatusList(): array
    {
        return ['Antre', 'Dikerjakan', 'Selesai', 'Diambil'];
    }

    // -----------------------------------------------------------------
    // READ - daftar transaksi servis
    // -----------------------------------------------------------------
    public function index()
    {
        $status  = $this->request->getGet('status') ?? '';
        $keyword = $this->request->getGet('q') ?? '';

        $data = [
            'title'    => 'Data Servis',
            'menu'     => 'servis',
            'servis'   => $this->servisModel->getAll($status !== '' ? $status : null, $keyword),
            'status'   => $status,
            'keyword'  => $keyword,
            'statusList' => $this->getStatusList(),
        ];

        return view('servis/index', $data);
    }

    // -----------------------------------------------------------------
    // CREATE - form transaksi servis baru
    // -----------------------------------------------------------------
    public function create()
    {
        $data = [
            'title'      => 'Tambah Transaksi Servis',
            'menu'       => 'servis',
            'pelanggan'  => $this->pelangganModel->getAll(),
            'sparepart'  => $this->sparepartModel->getAll(),
            'statusList' => $this->getStatusList(),
        ];

        return view('servis/create', $data);
    }

    // -----------------------------------------------------------------
    // CREATE - menyimpan transaksi servis + rincian barang
    // -----------------------------------------------------------------
    public function store()
    {
        $rules = [
            'id_pelanggan'  => 'required|numeric',
            'keluhan'       => 'required|min_length[5]',
            'biaya_jasa'    => 'required|numeric',
            'status_servis' => 'required|in_list[Antre,Dikerjakan,Selesai,Diambil]',
            'tanggal_masuk' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(', ', $this->validator->getError()));
        }

        if ((int) $this->request->getPost('biaya_jasa') < 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Biaya jasa tidak boleh bernilai negatif.');
        }

        // Susun rincian barang dari input baris dinamis
        $barangDipakai = $this->request->getPost('barang') ?? [];
        $detail = [];

        foreach ((array) $barangDipakai as $baris) {
            $idSparepart = (int) ($baris['id_sparepart'] ?? 0);
            $jumlah      = (int) ($baris['jumlah'] ?? 0);

            if ($idSparepart > 0 && $jumlah > 0) {
                $detail[] = ['id_sparepart' => $idSparepart, 'jumlah' => $jumlah];
            }
        }

        $header = [
            'id_pelanggan'  => (int) $this->request->getPost('id_pelanggan'),
            'keluhan'       => trim($this->request->getPost('keluhan')),
            'biaya_jasa'    => (int) $this->request->getPost('biaya_jasa'),
            'status_servis' => $this->request->getPost('status_servis'),
            'tanggal_masuk' => date('Y-m-d H:i:s', strtotime($this->request->getPost('tanggal_masuk'))),
        ];

        try {
            $idServis = $this->servisModel->simpanTransaksi($header, $detail);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect()->to('servis/detail/' . $idServis)
            ->with('success', 'Transaksi servis #' . $idServis . ' berhasil disimpan.');
    }

    // -----------------------------------------------------------------
    // READ - detail transaksi + rincian barang
    // -----------------------------------------------------------------
    public function detail($id = null)
    {
        $id    = (int) $id;
        $servis = $this->servisModel->getById($id);

        if ($servis === null) {
            return redirect()->to('servis')->with('error', 'Data servis tidak ditemukan.');
        }

        $detail = $this->servisModel->getDetailServis($id);
        $totalBarang = array_sum(array_column($detail, 'subtotal'));

        $data = [
            'title'        => 'Detail Servis',
            'menu'         => 'servis',
            'servis'       => $servis,
            'detail'       => $detail,
            'totalBarang'  => $totalBarang,
            'totalBayar'   => $totalBarang + (int) $servis['biaya_jasa'],
            'statusList'   => $this->getStatusList(),
        ];

        return view('servis/detail', $data);
    }

    // -----------------------------------------------------------------
    // UPDATE - mengubah status servicing
    // -----------------------------------------------------------------
    public function update_status($id = null)
    {
        $id     = (int) $id;
        $status = $this->request->getPost('status_servis');

        if ($this->servisModel->getById($id) === null) {
            return redirect()->to('servis')->with('error', 'Data servis tidak ditemukan.');
        }

        if (! in_array($status, $this->getStatusList(), true)) {
            return redirect()->back()->with('error', 'Status servis tidak valid.');
        }

        $this->servisModel->updateStatus($id, $status);

        return redirect()->to('servis/detail/' . $id)
            ->with('success', 'Status servis diubah menjadi "' . $status . '".');
    }

    // -----------------------------------------------------------------
    // LAPORAN - cetak nota / resi pembayaran
    // -----------------------------------------------------------------
    public function cetak_nota($id = null)
    {
        $id    = (int) $id;
        $servis = $this->servisModel->getById($id);

        if ($servis === null) {
            return redirect()->to('servis')->with('error', 'Data servis tidak ditemukan.');
        }

        $detail = $this->servisModel->getDetailServis($id);
        $totalBarang = array_sum(array_column($detail, 'subtotal'));

        return view('servis/nota', [
            'title'       => 'Nota Servis',
            'servis'      => $servis,
            'detail'      => $detail,
            'totalBarang' => $totalBarang,
            'totalBayar'  => $totalBarang + (int) $servis['biaya_jasa'],
        ]);
    }

    // -----------------------------------------------------------------
    // DELETE - membatalkan transaksi (stok barang dikembalikan)
    // -----------------------------------------------------------------
    public function delete($id = null)
    {
        $id  = (int) $id;
        $row = $this->servisModel->getById($id);

        if ($row === null) {
            return redirect()->to('servis')->with('error', 'Data servis tidak ditemukan.');
        }

        $this->servisModel->deleteServis($id);

        return redirect()->to('servis')->with('success', 'Transaksi servis #' . $id . ' berhasil dihapus dan stok dikembalikan.');
    }
}