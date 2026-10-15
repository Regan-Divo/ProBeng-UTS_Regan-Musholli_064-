<?php

namespace App\Controllers;

use App\Models\PelangganModel;

/**
 * PELANGGAN CONTROLLER
 *
 * Use Case: "Kelola Data Pelanggan" (Tambah, Ubah, Hapus, Lihat)
 * Method  : index, create, store, edit, update, delete
 */
class Pelanggan extends BaseController
{
    protected $helpers = ['probeng_helper'];

    private PelangganModel $model;

    public function __construct()
    {
        $this->model = new PelangganModel();
    }

    // -----------------------------------------------------------------
    // READ - Menampilkan seluruh data pelanggan
    // -----------------------------------------------------------------
    public function index()
    {
        $keyword = $this->request->getGet('q') ?? '';

        $data = [
            'title'     => 'Data Pelanggan',
            'menu'      => 'pelanggan',
            'pelanggan' => $this->model->getAll($keyword),
            'keyword'   => $keyword,
        ];

        return view('pelanggan/index', $data);
    }

    // -----------------------------------------------------------------
    // CREATE - Form tambah pelanggan
    // -----------------------------------------------------------------
    public function create()
    {
        return view('pelanggan/create', [
            'title' => 'Tambah Pelanggan',
            'menu'  => 'pelanggan',
        ]);
    }

    // -----------------------------------------------------------------
    // CREATE - Menyimpan pelanggan baru
    // -----------------------------------------------------------------
    public function store()
    {
        $rules = [
            'nama_pelanggan'  => 'required|min_length[3]',
            'no_telepon'      => 'required|min_length[9]|max_length[20]',
            'plat_nomor'      => 'required|max_length[20]',
            'jenis_kendaraan' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(', ', $this->validator->getError()));
        }

        $data = [
            'nama_pelanggan'  => trim($this->request->getPost('nama_pelanggan')),
            'no_telepon'      => trim($this->request->getPost('no_telepon')),
            'plat_nomor'      => strtoupper(trim($this->request->getPost('plat_nomor'))),
            'jenis_kendaraan' => trim($this->request->getPost('jenis_kendaraan')),
        ];

        if (! $this->model->cekPlatNomor($data['plat_nomor'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Plat nomor sudah terdaftar pada pelanggan lain.');
        }

        $this->model->savePelanggan($data);

        return redirect()->to('pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    // -----------------------------------------------------------------
    // UPDATE - Form edit pelanggan
    // -----------------------------------------------------------------
    public function edit($id = null)
    {
        $id = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        return view('pelanggan/edit', [
            'title'     => 'Edit Pelanggan',
            'menu'      => 'pelanggan',
            'pelanggan' => $row,
        ]);
    }

    // -----------------------------------------------------------------
    // UPDATE - Menyimpan perubahan pelanggan
    // -----------------------------------------------------------------
    public function update($id = null)
    {
        $id  = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $rules = [
            'nama_pelanggan'  => 'required|min_length[3]',
            'no_telepon'      => 'required|min_length[9]|max_length[20]',
            'plat_nomor'      => 'required|max_length[20]',
            'jenis_kendaraan' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(', ', $this->validator->getError()));
        }

        $data = [
            'nama_pelanggan'  => trim($this->request->getPost('nama_pelanggan')),
            'no_telepon'      => trim($this->request->getPost('no_telepon')),
            'plat_nomor'      => strtoupper(trim($this->request->getPost('plat_nomor'))),
            'jenis_kendaraan' => trim($this->request->getPost('jenis_kendaraan')),
        ];

        if (! $this->model->cekPlatNomor($data['plat_nomor'], $id)) {
            return redirect()->back()->withInput()
                ->with('error', 'Plat nomor sudah digunakan pelanggan lain.');
        }

        $this->model->updatePelanggan($id, $data);

        return redirect()->to('pelanggan')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    // -----------------------------------------------------------------
    // DELETE - Menghapus pelanggan
    // -----------------------------------------------------------------
    public function delete($id = null)
    {
        $id  = (int) $id;
        $row = $this->model->getById($id);

        if ($row === null) {
            return redirect()->to('pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $this->model->deletePelanggan($id);

        return redirect()->to('pelanggan')->with('success', 'Data pelanggan "' . $row['nama_pelanggan'] . '" berhasil dihapus.');
    }
}