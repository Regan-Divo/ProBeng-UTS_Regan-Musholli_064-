<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh untuk kebutuhan demo.
 *
 * Jalankan : php spark db:seed ProbengSeeder
 */
class ProbengSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('pelanggan')->insertBatch([
            ['nama_pelanggan' => 'Ahmad Fauzi', 'no_telepon' => '081234567890', 'plat_nomor' => 'B 1234 ABC', 'jenis_kendaraan' => 'Honda Beat'],
            ['nama_pelanggan' => 'Budi Santoso', 'no_telepon' => '082345678901', 'plat_nomor' => 'B 5678 DEF', 'jenis_kendaraan' => 'Yamaha Mio'],
            ['nama_pelanggan' => 'Citra Lestari', 'no_telepon' => '083456789012', 'plat_nomor' => 'L 9012 GHI', 'jenis_kendaraan' => 'Toyota Avanza'],
            ['nama_pelanggan' => 'Dewi Anggraini', 'no_telepon' => '084567890123', 'plat_nomor' => 'N 3456 JKL', 'jenis_kendaraan' => 'Honda Brio'],
        ]);

        $this->db->table('sparepart')->insertBatch([
            ['nama_barang' => 'Oli Synthetic 10W-30 (1L)', 'harga_beli' => 55000, 'harga_jual' => 65000, 'stok' => 20],
            ['nama_barang' => 'Suku Cadang Rem Depan', 'harga_beli' => 45000, 'harga_jual' => 60000, 'stok' => 12],
            ['nama_barang' => 'Busi / Kampas Rem', 'harga_beli' => 3000, 'harga_jual' => 5000, 'stok' => 60],
            ['nama_barang' => 'Filter Oli', 'harga_beli' => 18000, 'harga_jual' => 25000, 'stok' => 8],
            ['nama_barang' => 'Spion Motor Kiri', 'harga_beli' => 75000, 'harga_jual' => 100000, 'stok' => 4],
            ['nama_barang' => 'Ban luar 70/17-17', 'harga_beli' => 185000, 'harga_jual' => 215000, 'stok' => 6],
            ['nama_barang' => 'Kabel Busi Set', 'harga_beli' => 12000, 'harga_jual' => 18000, 'stok' => 3],
            ['nama_barang' => 'Aki 12V 7Ah', 'harga_beli' => 520000, 'harga_jual' => 585000, 'stok' => 2],
        ]);

        $this->db->table('servis')->insertBatch([
            ['id_pelanggan' => 1, 'keluhan' => 'Ganti oli rutin dan servis rem', 'biaya_jasa' => 50000, 'status_servis' => 'Selesai', 'tanggal_masuk' => '2024-05-10 09:15:00'],
            ['id_pelanggan' => 2, 'keluhan' => 'Ganti kampas rem yang sudah tipis', 'biaya_jasa' => 35000, 'status_servis' => 'Diambil', 'tanggal_masuk' => '2024-05-11 10:30:00'],
            ['id_pelanggan' => 3, 'keluhan' => 'Bunyi tidak normal saat dikendarikan', 'biaya_jasa' => 75000, 'status_servis' => 'Dikerjakan', 'tanggal_masuk' => '2024-05-12 13:45:00'],
            ['id_pelanggan' => 4, 'keluhan' => 'Servis berkala 10.000 km', 'biaya_jasa' => 60000, 'status_servis' => 'Antre', 'tanggal_masuk' => '2024-05-13 08:20:00'],
        ]);

        $this->db->table('detail_servis')->insertBatch([
            ['id_servis' => 1, 'id_sparepart' => 1, 'jumlah' => 1, 'subtotal' => 65000],
            ['id_servis' => 1, 'id_sparepart' => 3, 'jumlah' => 2, 'subtotal' => 10000],
            ['id_servis' => 2, 'id_sparepart' => 2, 'jumlah' => 1, 'subtotal' => 60000],
            ['id_servis' => 3, 'id_sparepart' => 4, 'jumlah' => 1, 'subtotal' => 25000],
            ['id_servis' => 4, 'id_sparepart' => 6, 'jumlah' => 2, 'subtotal' => 430000],
        ]);
    }
}