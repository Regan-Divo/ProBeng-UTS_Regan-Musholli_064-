<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PELANGGAN MODEL
 *
 * Terjemahan class diagram "Pelanggan":
 *   - Atribut : id_pelanggan, nama_pelanggan, no_telepon, plat_nomor, jenis_kendaraan
 *   - Metode  : getAll(), getById(), save(), delete(), cekPlatNomor()
 *   - Tabel   : pelanggan
 */
class PelangganModel extends Model
{
    protected $table      = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_pelanggan',
        'no_telepon',
        'plat_nomor',
        'jenis_kendaraan',
    ];

    // View: menampilkan seluruh data pelanggan (diurutkan nama)
    public function getAll(string $keyword = ''): array
    {
        $builder = $this->builder();

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('nama_pelanggan', $keyword)
                ->orLike('plat_nomor', $keyword)
                ->orLike('jenis_kendaraan', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('nama_pelanggan', 'ASC')->get()->getResultArray();
    }

    // View: mengambil satu baris data berdasarkan primary key
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    // Create: menambah data pelanggan baru
    public function savePelanggan(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    // Update: mengubah data pelanggan
    public function updatePelanggan(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    // Delete: menghapus data pelanggan
    public function deletePelanggan(int $id): bool
    {
        return $this->delete($id);
    }

    // Validasi: plat nomor harus unik (kecuali saat edit id yang sama)
    public function cekPlatNomor(string $plat, int $ignoreId = 0): bool
    {
        return $this->builder()
            ->where('plat_nomor', $plat)
            ->where('id_pelanggan !=', $ignoreId)
            ->countAllResults() === 0;
    }

    // Dashboard: total pelanggan
    public function countPelanggan(): int
    {
        return $this->countAllResults();
    }

    // Dashboard: pelanggan yang sedang memiliki transaksi servis
    public function getPelangganTeraktif(int $limit = 5): array
    {
        return $this->builder()
            ->select('pelanggan.*, COUNT(servis.id_servis) AS total_servis')
            ->join('servis', 'servis.id_pelanggan = pelanggan.id_pelanggan', 'left')
            ->groupBy('pelanggan.id_pelanggan')
            ->orderBy('total_servis', 'DESC')
            ->get($limit)
            ->getResultArray();
    }
}