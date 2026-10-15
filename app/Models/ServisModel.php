<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SERVIS MODEL
 *
 * Terjemahan class diagram "Servis":
 *   - Atribut : id_servis, id_pelanggan, keluhan, biaya_jasa, status_servis, tanggal_masuk
 *   - Metode  : getAll(), getById(), getWithPelanggan(), saveServis(),
 *               updateStatus(), deleteServis(), getTotalPendapatan()
 *   - Tabel   : servis  (dijoint dengan pelanggan & detail_servis)
 *
 * Relasi:
 *   - N:1  servis  -> pelanggan
 *   - 1:N servis  -> detail_servis -> sparepart
 */
class ServisModel extends Model
{
    protected $table      = 'servis';
    protected $primaryKey = 'id_servis';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_pelanggan',
        'keluhan',
        'biaya_jasa',
        'status_servis',
        'tanggal_masuk',
    ];

    /** Status yang dihitung sebagai transaksi selesai (pendapatan) */
    public const STATUS_PENDAPATAN = ['Selesai', 'Diambil'];

    /** Status yang masih berjalan di bengkel */
    public const STATUS_AKTIF = ['Antre', 'Dikerjakan'];

    // -----------------------------------------------------------------
    // QUERY DASHBOARD
    // -----------------------------------------------------------------

    /**
     * List transaksi servis dengan JOIN ke tabel pelanggan
     * sehingga data pelanggan ikut terbawa (nama, plat, kendaraan).
     */
    public function getAll(?string $status = null, string $keyword = ''): array
    {
        $builder = $this->builder()
            ->select('servis.*, pelanggan.nama_pelanggan, pelanggan.plat_nomor, pelanggan.jenis_kendaraan, pelanggan.no_telepon')
            ->join('pelanggan', 'pelanggan.id_pelanggan = servis.id_pelanggan', 'inner');

        if ($status !== null && $status !== '') {
            $builder->where('servis.status_servis', $status);
        }

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('pelanggan.nama_pelanggan', $keyword)
                ->orLike('pelanggan.plat_nomor', $keyword)
                ->orLike('servis.keluhan', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('servis.tanggal_masuk', 'DESC')->get()->getResultArray();
    }

    /**
     * Detail satu transaksi servis (join pelanggan).
     */
    public function getById(int $id): ?array
    {
        return $this->builder()
            ->select('servis.*, pelanggan.nama_pelanggan, pelanggan.no_telepon, pelanggan.plat_nomor, pelanggan.jenis_kendaraan')
            ->join('pelanggan', 'pelanggan.id_pelanggan = servis.id_pelanggan', 'inner')
            ->where('servis.id_servis', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Rincian barang yang digunakan (join detail_servis + sparepart).
     */
    public function getDetailServis(int $idServis): array
    {
        return $this->db->table('detail_servis')
            ->select('detail_servis.*, sparepart.nama_barang, sparepart.harga_jual, sparepart.harga_beli')
            ->join('sparepart', 'sparepart.id_sparepart = detail_servis.id_sparepart', 'inner')
            ->where('detail_servis.id_servis', $idServis)
            ->orderBy('detail_servis.id_detail', 'ASC')
            ->get()
            ->getResultArray();
    }

    // -----------------------------------------------------------------
    // TRANSAKSI DATA
    // -----------------------------------------------------------------

    /**
     * Simpan transaksi servis beserta rincian barang.
     * Stok barang otomatis berkurang. Dipakai di dalam DB transaction.
     *
     * @param array $dataHeader  ['id_pelanggan','keluhan','biaya_jasa','status_servis','tanggal_masuk']
     * @param array $dataDetail  [['id_sparepart'=>1,'jumlah'=>2], ...]
     */
    public function simpanTransaksi(array $dataHeader, array $dataDetail = []): int
    {
        $this->db->transStart();

        $this->insert($dataHeader);
        $idServis = (int) $this->getInsertID();

        foreach ($dataDetail as $baris) {
            $idSparepart = (int) $baris['id_sparepart'];
            $jumlah      = (int) $baris['jumlah'];

            if ($idSparepart <= 0 || $jumlah <= 0) {
                continue;
            }

            // Harga diambil dari master barang (bukan dari input user)
            $barang = $this->db->table('sparepart')->where('id_sparepart', $idSparepart)->get()->getRowArray();

            if ($barang === null || $barang['stok'] < $jumlah) {
                $this->db->transRollback();
                throw new \RuntimeException('Stok tidak cukup untuk: ' . ($barang['nama_barang'] ?? 'barang #' . $idSparepart));
            }

            $this->db->table('detail_servis')->insert([
                'id_sparepart' => $idSparepart,
                'id_servis'    => $idServis,
                'jumlah'       => $jumlah,
                'subtotal'     => (int) $barang['harga_jual'] * $jumlah,
            ]);

            // Management stok: berkurang
            $this->db->table('sparepart')
                ->where('id_sparepart', $idSparepart)
                ->set('stok', 'stok - ' . $jumlah, false)
                ->update();
        }

        $this->db->transCommit();

        return $idServis;
    }

    // Update: ubah data header servis
    public function updateServis(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    // Update: ubah status (Antre -> Dikerjakan -> Selesai -> Diambil)
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status_servis' => $status]);
    }

    /**
     * Hapus transaksi servis; stok barang yang sudah terpakai dikembalikan.
     */
    public function deleteServis(int $id): bool
    {
        $this->db->transStart();

        // Kembalikan stok
        foreach ($this->getDetailServis($id) as $d) {
            $this->db->table('sparepart')
                ->where('id_sparepart', $d['id_sparepart'])
                ->set('stok', 'stok + ' . (int) $d['jumlah'], false)
                ->update();
        }

        // Detail dihapus eksplisit (ON DELETE CASCADE tetap menjadi jaring pengaman)
        $this->db->table('detail_servis')->where('id_servis', $id)->delete();

        $this->delete($id);

        $this->db->transCommit();

        return true;
    }

    // -----------------------------------------------------------------
    // LAPORAN / STATISTIK
    // -----------------------------------------------------------------

    /**
     * Total pendapatan = biaya jasa + subtotal barang,
     * hanya untuk transaksi berstatus Selesai / Diambil.
     */
    public function getTotalPendapatan(): int
    {
        $jasa = (int) $this->builder()
            ->selectSum('biaya_jasa', 'total')
            ->whereIn('status_servis', self::STATUS_PENDAPATAN)
            ->get()
            ->getRowArray()['total'];

        $barang = (int) $this->db->table('detail_servis')
            ->selectSum('detail_servis.subtotal', 'total')
            ->join('servis', 'servis.id_servis = detail_servis.id_servis')
            ->whereIn('servis.status_servis', self::STATUS_PENDAPATAN)
            ->get()
            ->getRowArray()['total'];

        return $jasa + $barang;
    }

    // Jumlah servis yang masih berjalan (Antre / Dikerjakan)
    public function countServisAktif(): int
    {
        return $this->builder()
            ->whereIn('status_servis', self::STATUS_AKTIF)
            ->countAllResults();
    }

    // Jumlah seluruh transaksi
    public function countServis(): int
    {
        return $this->countAllResults();
    }

    /**
     * Rekap jumlah transaksi per status (untuk grafik dashboard).
     */
    public function getRekapStatus(): array
    {
        $rows = $this->builder()
            ->select('status_servis, COUNT(*) AS jumlah')
            ->groupBy('status_servis')
            ->get()
            ->getResultArray();

        $rekap = [
            'Antre'      => 0,
            'Dikerjakan' => 0,
            'Selesai'    => 0,
            'Diambil'    => 0,
        ];

        foreach ($rows as $r) {
            $rekap[$r['status_servis']] = (int) $r['jumlah'];
        }

        return $rekap;
    }

    /**
     * 5 transaksi terakhir untuk dashboard.
     */
    public function getTransaksiTerbaru(int $limit = 5): array
    {
        return $this->builder()
            ->select('servis.*, pelanggan.nama_pelanggan, pelanggan.plat_nomor')
            ->join('pelanggan', 'pelanggan.id_pelanggan = servis.id_pelanggan', 'inner')
            ->orderBy('servis.tanggal_masuk', 'DESC')
            ->get($limit)
            ->getResultArray();
    }
}