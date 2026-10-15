<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SPAREPART MODEL
 *
 * Terjemahan class diagram "Sparepart":
 *   - Atribut : id_sparepart, nama_barang, harga_beli, harga_jual, stok
 *   - Metode  : getAll(), getById(), save(), delete(), stokBertambah(),
 *               stokBerkurang(), getStokMenipis()
 *   - Tabel   : sparepart
 */
class SparepartModel extends Model
{
    protected $table      = 'sparepart';
    protected $primaryKey = 'id_sparepart';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'stok',
    ];

    // View: seluruh data suku cadang
    public function getAll(string $keyword = ''): array
    {
        $builder = $this->builder();

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('nama_barang', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('nama_barang', 'ASC')->get()->getResultArray();
    }

    // View: satu baris data
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    // Create: menambah suku cadang baru
    public function saveSparepart(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    // Update: mengubah data suku cadang
    public function updateSparepart(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    // Delete: menghapus suku cadang
    public function deleteSparepart(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Manajemen stok: menambah stok (mis. barang masuk / pembatalan).
     */
    public function stokBertambah(int $id, int $jumlah): bool
    {
        $barang = $this->find($id);
        if ($barang === null) {
            return false;
        }

        return $this->update($id, ['stok' => (int) $barang['stok'] + $jumlah]);
    }

    /**
     * Manajemen stok: mengurangi stok ketika barang dipakai pada servis.
     */
    public function stokBerkurang(int $id, int $jumlah): bool
    {
        $barang = $this->find($id);
        if ($barang === null || $barang['stok'] < $jumlah) {
            return false;
        }

        return $this->update($id, ['stok' => (int) $barang['stok'] - $jumlah]);
    }

    // Dashboard: daftar barang yang stoknya <= 5
    public function getStokMenipis(int $batas = 5): array
    {
        return $this->builder()
            ->where('stok <=', $batas)
            ->orderBy('stok', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Dashboard: ringkasan jumlah & nilai inventaris
    public function getRingkasanInventaris(): array
    {
        $row = $this->builder()
            ->select('COUNT(*) AS jumlah_barang, COALESCE(SUM(stok), 0) AS total_unit, COALESCE(SUM(stok * harga_beli), 0) AS nilai_beli')
            ->get()
            ->getRowArray();

        return [
            'jumlah_barang' => (int) ($row['jumlah_barang'] ?? 0),
            'total_unit'    => (int) ($row['total_unit'] ?? 0),
            'nilai_beli'    => (int) ($row['nilai_beli'] ?? 0),
        ];
    }
}