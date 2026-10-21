# ProBeng

Sistem Manajemen Servis & Suku Cadang Bengkel.

Dibangun dengan framework CodeIgniter 4 menggunakan pola MVC untuk tugas
Rekayasa Perangkat Lunak, penerjemahan model perancangan ke implementasi.

## Tools

- CodeIgniter 4
- PHP 8.1+
- MySQL / MariaDB
- Bootstrap 5

## Cara Install

1. Clone atau download repository ini
2. Jalankan `composer install` untuk memasang library
3. Salin `env` menjadi `.env`, lalu sesuaikan bagian database:

```ini
database.default.hostname = localhost
database.default.database = db_probeng
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

4. Buat database `db_probeng`. Dua cara:

   **Cara cepat** — import file `probeng.sql` lewat phpMyAdmin:
   ```
   http://localhost/phpmyadmin  ->  Import  ->  pilih probeng.sql  ->  Go
   ```

   **Cara migration** — buat database kosong `db_probeng` di phpMyAdmin, lalu:
   ```
   php spark migrate
   php spark db:seed ProbengSeeder
   ```

5. Jalankan server:
   ```
   php spark serve
   ```

6. Buka `http://localhost:8080`

## Modul

| Modul | Keterangan |
|---|---|
| Dashboard | Ringkasan pendapatan, servis berjalan, dan barang stok menipis |
| Pelanggan | Tambah, ubah, hapus, dan cari data pelanggan |
| Suku Cadang | Data barang, harga beli/jual, dan management stok |
| Transaksi Servis | Penerimaan servis, rincian barang, dan perubahan status |
| Nota | Cetak resi pembayaran |

## Struktur

```
app/
├── Config/          konfigurasi dan routing
├── Controllers/     Dashboard, Pelanggan, Sparepart, Servis
├── Database/
│   ├── Migrations/  pembuatan tabel
│   └── Seeds/       data contoh
├── Helpers/         fungsi bantu tampilan
├── Models/          PelangganModel, SparepartModel, ServisModel
└── Views/           tampilan halaman
```

## Catatan

- Harga jual dan subtotal dihitung di server dari master barang, bukan dari input pengguna.
- Stok barang berkurang saat transaksi disimpan dan dikembalikan saat transaksi dihapus.
- `probeng.sql` berisi DDL dan data contoh untuk empat tabel beserta foreign key-nya.
