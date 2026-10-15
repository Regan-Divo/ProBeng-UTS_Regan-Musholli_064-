# DOKUMENTASI PENERJEMAHAN MODEL PERANCANGAN → IMPLEMENTASI
## ProBeng — Sistem Manajemen Servis & Inventaris Suku Cadang Bengkel

Bab ini menjelaskan bagaimana setiap model perancangan (Use Case, Class Diagram,
ERD, Mockup UI) diterjemahkan menjadi kode pada framework CodeIgniter 4.

---

## 1. Use Case Diagram → Controller & Routing

| No | Use Case (Aktor = Admin/Mekanik) | Controller::Method | URL |
|----|----------------------------------|--------------------|-----|
| 1 | Melihat ringkasan aktivitas bengkel | `Dashboard::index` | `/`, `/dashboard` |
| 2 | Melihat daftar pelanggan | `Pelanggan::index` | `/pelanggan` |
| 3 | Menambah pelanggan | `Pelanggan::create`, `::store` | `/pelanggan/tambah` |
| 4 | Mengubah pelanggan | `Pelanggan::edit`, `::update` | `/pelanggan/edit/{id}` |
| 5 | Menghapus pelanggan | `Pelanggan::delete` | `/pelanggan/hapus/{id}` |
| 6 | Melihat daftar suku cadang | `Sparepart::index` | `/sparepart` |
| 7 | Menambah suku cadang | `Sparepart::create`, `::store` | `/sparepart/tambah` |
| 8 | Mengubah suku cadang | `Sparepart::edit`, `::update` | `/sparepart/edit/{id}` |
| 9 | Menambah stok barang | `Sparepart::tambah_stok` | `/sparepart/stok/{id}` |
| 10 | Menghapus suku cadang | `Sparepart::delete` | `/sparepart/hapus/{id}` |
| 11 | Melihat daftar transaksi servis | `Servis::index` | `/servis` |
| 12 | Membuat transaksi servis | `Servis::create`, `::store` | `/servis/tambah` |
| 13 | Melihat detail transaksi | `Servis::detail` | `/servis/detail/{id}` |
| 14 | Mengubah status servis | `Servis::update_status` | `/servis/status/{id}` |
| 15 | Mencetak nota pembayaran | `Servis::cetak_nota` | `/servis/nota/{id}` |
| 16 | Membatalkan transaksi | `Servis::delete` | `/servis/hapus/{id}` |
| 17 | Menyiapkan instalasi aplikasi | `Install::index` | `/install` |

Setiap use case "aksi tunggal" dipecah menjadi dua method: method *request*
(create/store, edit/update) untuk menangani kiriman formulir, dan method
*tampilan* (index, detail, cetak_nota) untuk menampilkan data.

---

## 2. Class Diagram → Model

### 2.1 Entitas Pelanggan
```
+-------------------+
|    Pelanggan      |
+-------------------+
| - id_pelanggan    | PK
| - nama_pelanggan  |
| - no_telepon      |
| - plat_nomor      | (unique)
| - jenis_kendaraan |
+-------------------+
| + getAll()        |
| + getById()       |
| + save()          |
| + update()        |
| + delete()        |
| + cekPlatNomor()  |
+-------------------+
```
→ `app/Models/PelangganModel.php` (tabel `pelanggan`)

### 2.2 Entitas Sparepart
```
+-------------------+
|     Sparepart     |
+-------------------+
| - id_sparepart    | PK
| - nama_barang     |
| - harga_beli      |
| - harga_jual      |
| - stok            |
+-------------------+
| + getAll()        |
| + getById()       |
| + save()          |
| + update()        |
| + delete()        |
| + stokBertambah() |
| + stokBerkurang() |
+-------------------+
```
→ `app/Models/SparepartModel.php` (tabel `sparepart`)

### 2.3 Entitas Servis
```
+-------------------+
|       Servis      |
+-------------------+
| - id_servis       | PK
| - id_pelanggan    | FK
| - keluhan         |
| - biaya_jasa      |
| - status_servis   | ENUM
| - tanggal_masuk   |
+-------------------+
| + getAll()        |
| + getById()       |
| + simpanTransaksi() |
| + updateStatus()  |
| + deleteServis()  |
| + getTotalPendapatan() |
+-------------------+
```
→ `app/Models/ServisModel.php` (tabel `servis`)

### 2.4 Entitas Asosiasi DetailServis
```
+-------------------+
|   DetailServis    |
+-------------------+
| - id_detail       | PK
| - id_servis       | FK
| - id_sparepart    | FK
| - jumlah          |
| - subtotal        |
+-------------------+
```
→ tabel `detail_servis`, diakses melalui `ServisModel::getDetailServis()`

Semua model mewarisi `CodeIgniter\Model` dan menggunakan **Query Builder**
(`$this->builder()`, `->select()`, `->join()`, `->where()`), sehingga query
aman dari parameterized query secara bawaan.

---

## 3. ERD → Struktur Database Relasional

```
┌────────────────┐          ┌──────────────────┐          ┌──────────────────┐
│   pelanggan    │ 1     N │      servis      │ 1     N │  detail_servis   │
├────────────────┤─────────>├──────────────────┤─────────>├──────────────────┤
│ PK id_pelanggan│          │ PK id_servis     │          │ PK id_detail     │
│    nama_pelanggan│        │ FK id_pelanggan  │          │ FK id_servis     │
│    no_telepon  │          │    keluhan       │          │ FK id_sparepart  │
│ UQ plat_nomor  │          │    biaya_jasa    │          │     jumlah       │
│    jenis_kendaraan│        │ ENUM status_servis│         │     subtotal     │
└────────────────┘          │    tanggal_masuk │          └────────┬─────────┘
                            └──────────────────┘                   │ N
                                                                   │
                                                        ┌──────────▼───────┐
                                                        │    sparepart     │
                                                        ├──────────────────┤
                                                        │ PK id_sparepart  │
                                                        │    nama_barang   │
                                                        │    harga_beli    │
                                                        │    harga_jual    │
                                                        │    stok          │
                                                        └──────────────────┘
```

**Relasi:**
- `pelanggan` 1 — N `servis` (satu pelanggan dapat memiliki banyak transaksi)
- `servis` 1 — N `detail_servis` (satu transaksi dapat memakai banyak barang)
- `sparepart` 1 — N `detail_servis` (satu barang dapat dipakai banyak transaksi)
- `detail_servis` merupakan **entitas asosiasi** yang menyelesaikan relasi many-to-many
  antara `servis` dan `sparepart`

**Aturan referential integrity:**
- Hapus pelanggan → seluruh transaksi & detail terpapus otomatis (`ON DELETE CASCADE`)
- Barang yang sudah dipakai transaksi → tidak dapat dihapus (`ON DELETE RESTRICT`)

---

## 4. Mockup UI → View

| Komponen pada Mockup | File View |("// Technology: PHP CodeIgniter 4") |
|---|---|---|
| Kerangka: navbar + sidebar | `app/Views/template.php` | Layout utama seluruh halaman |
| Kartu statistik & tabel | `app/Views/dashboard/index.php` | Ringkasan sistem |
| Tabel data pelanggan | `app/Views/pelanggan/index.php` | Daftar + pencarian |
| Formulir pelanggan | `app/Views/pelanggan/create.php`, `edit.php` | Tambah / ubah |
| Tabel inventaris | `app/Views/sparepart/index.php` | Daftar + stok |
| Formulir barang | `app/Views/sparepart/create.php`, `edit.php` | Tambah / ubah |
| Tabel transaksi | `app/Views/servis/index.php` | Daftar + filter status |
| Formulir transaksi | `app/Views/servis/create.php` | Baris barang dinamis |
| Detail & ubah status | `app/Views/servis/detail.php` | Rincian + total |
| Resi pembayaran | `app/Views/servis/nota.php` | Layout khusus cetak A4 |
| Halaman instalasi | `app/Views/install/index.php` | Setup database |

Pola MVC yang dipakai pada setiap halaman:

```
Model    -> mengambil data dari database
Controller -> memvalidasi, memproses, lalu mengirim $data ke View
View     -> menampilkan HTML (Bootstrap 5) berdasarkan $data
```

---

## 5. Pengujian (Black Box)

| No | Skenario | Hasil Diharapkan | Status |
|----|----------|-------------------|--------|
| 1 | Buka `/` tanpa database terpasang | Redirect ke `/install` | ✅ |
| 2 | Jalankan instalasi | Database, 4 tabel, dan data contoh terbentuk | ✅ |
| 3 | Tambah pelanggan | Data tersimpan & tampil di tabel | ✅ |
| 4 | Tambah pelanggan dengan plat nomor sama | Ditolak, pesan "Plat nomor sudah terdaftar" | ✅ |
| 5 | Tambah suku cadang | Data tersimpan, ringkasan inventaris ikut berubah | ✅ |
| 6 | Harga jual lebih kecil dari harga beli | Ditolak | ✅ |
| 7 | Buat transaksi servis 2 jenis barang | Stok berkurang, subtotal & total bayar terhitung | ✅ |
| 8 | Pesan barang melebihi stok | Ditolak, pesan "Stok tidak cukup untuk ..." | ✅ |
| 9 | Ubah status Antre → Selesai | Badge & dropdown ikut berubah | ✅ |
| 10 | Hapus transaksi | Stok kembali seperti semula, detail terhapus | ✅ |
| 11 | Hapus barang yang sudah dipakai transaksi | Ditolak | ✅ |
| 12 | Cetak nota | Halaman A4 tanpa sidebar, memuat TOTAL BAYAR | ✅ |
| 13 | Buka URL tidak dikenal | 404 | ✅ |

Dokumentasi visual (hasil tangkapan layar aplikasi) tersimpan pada folder
[`docs/screenshots/`](docs/screenshots):
`01-dashboard`, `02-data-pelanggan`, `03-suku-cadang`, `04-transaksi-servis`,
`05-form-transaksi-baru`, `06-detail-servis`, `07-nota-pembayaran`.