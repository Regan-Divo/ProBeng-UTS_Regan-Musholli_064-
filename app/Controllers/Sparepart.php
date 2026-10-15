<?php

namespace App\Controllers;

use App\Models\ServisModel;
use App\Models\SparepartModel;

/**
 * SPAREPART CONTROLLER
 *
 * Use Case: "Kelola Suku Cadang & Inventaris"
 * Method  : index, create, store, edit, update, delete
 */
class Sparepart extends BaseController
{
    protected $helpers = ['probeng_helper'];

    private SparepartModel $model;
    private $db;

    public function __construct()
    {
        $this->model = new SparepartModel();
        $this->db    = db_connect();
    }

    // -----------------------------------------------------------------
    // READ - daftar suku cadang
    // -----------------------------------------------------------------
    public function index()
    {
        $keyword = $this->request->getGet('q') ?? '';

        $data = [
            'title'       => 'Data Suku Cadang',
            'menu'        => 'sparepart',
            'sparepart'   => $this->model->getAll($keyword),
            'keyword'     => $keyword,
            'ringkasan'   => $this->model->getRingkasanInventaris(),
            'stokMenipis' => $this->model->getStokMenipis(),
        ];

        return view('sparepart/index', $data);
    }

    // -----------------------------------------------------------------
    // CREATE - form tambah
    // -----------------------------------------------------------------
    public function create()
    {
        return view('sparepart/create', [
            'title' => 'Tambah Suku Cadang',
            'menu'  => 'sparepart',
        ]);
    }

    // -----------------------------------------------------------------
    // CREATE - simpan barang baru
    // -----------------------------------------------------------------
    public function store()
    {
        $rules = [
            'nama_barang' => 'required|min_length[3]',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'stok'        => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(', ', $this->validator->getError()));
        }

        $data = [
            'nama_barang' => trim($this->request->getPost('nama_barang')),
            'harga_beli'  => (int) $this->request->getPost('harga_beli'),
            'harga_jual'  => (int) $this->request->getPost('harga_jual'),
            'stok'        => (int) $this->request->getPost('stok'),
        ];

        if ($data['harga_beli'] < 0 || $data['harga_jual'] < 0 || $data['stok'] < 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Harga dan stok tidak boleh bernilai negatif.');
        }

        if ($data['harga_jual'] < $data['harga_beli']) {
            return redirect()->back()->withInput()
                ->with('error', 'Harga jual tidak boleh lebih kecil dari harga beli.');
        }

        $this->model->saveSparepart($data);

        return redirect()->to('sparepart')->with('success', 'Suku cadang berhasil ditambahkan.');
    }

    // -----------------------------------------------------------------
    // UPDATE - form edit
    // -----------------------------------------------------------------
    public function edit($id = null)
    {
        $id  = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('sparepart')->with('error', 'Data suku cadang tidak ditemukan.');
        }

        return view('sparepart/edit', [
            'title'     => 'Edit Suku Cadang',
            'menu'      => 'sparepart',
            'sparepart' => $row,
        ]);
    }

    // -----------------------------------------------------------------
    // UPDATE - simpan perubahan
    // -----------------------------------------------------------------
    public function update($id = null)
    {
        $id  = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('sparepart')->with('error', 'Data suku cadang tidak ditemukan.');
        }

        $rules = [
            'nama_barang' => 'required|min_length[3]',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'stok'        => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(', ', $this->validator->getError()));
        }

        $data = [
            'nama_barang' => trim($this->request->getPost('nama_barang')),
            'harga_beli'  => (int) $this->request->getPost('harga_beli'),
            'harga_jual'  => (int) $this->request->getPost('harga_jual'),
            'stok'        => (int) $this->request->getPost('stok'),
        ];

        if ($data['harga_beli'] < 0 || $data['harga_jual'] < 0 || $data['stok'] < 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Harga dan stok tidak boleh bernilai negatif.');
        }

        if ($data['harga_jual'] < $data['harga_beli']) {
            return redirect()->back()->withInput()
                ->with('error', 'Harga jual tidak boleh lebih kecil dari harga beli.');
        }

        $this->model->updateSparepart($id, $data);

        return redirect()->to('sparepart')->with('success', 'Data suku cadang berhasil diperbarui.');
    }

    // -----------------------------------------------------------------
    // UPDATE STOK - menambah stok barang masuk
    // -----------------------------------------------------------------
    public function tambah_stok($id = null)
    {
        $id  = (int) $id;
        $qty = (int) $this->request->getPost('jumlah');

        if ($this->model->getById($id) === null) {
            return redirect()->to('sparepart')->with('error', 'Data suku cadang tidak ditemukan.');
        }

        if ($qty <= 0) {
            return redirect()->to('sparepart')->with('error', 'Jumlah stok tambahan minimal 1.');
        }

        $this->model->stokBertambah($id, $qty);

        return redirect()->to('sparepart')->with('success', 'Stok berhasil bertambah ' . $qty . ' unit.');
    }

    // -----------------------------------------------------------------
    // DELETE - hapus suku cadang
    // -----------------------------------------------------------------
    public function delete($id = null)
    {
        $id  = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('sparepart')->with('error', 'Data suku cadang tidak ditemukan.');
        }

        // Barang yang sudah pernah dipakai pada transaksi tidak boleh dihapus
        $dipakai = $this->db->table('detail_servis')
            ->where('id_sparepart', $id)
            ->countAllResults();

        if ($dipakai > 0) {
            return redirect()->to('sparepart')
                ->with('error', 'Barang sudah digunakan pada ' . $dipakai . ' transaksi servis, tidak dapat dihapus.');
        }

        $this->model->deleteSparepart($id);

        return redirect()->to('sparepart')->with('success', 'Suku cadang "' . $row['nama_barang'] . '" berhasil dihapus.');
    }
}