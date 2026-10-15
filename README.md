# ProBeng
### Sistem Manajemen Servis & Inventaris Suku Cadang Bengkel
Tugas Rekayasa Perangkat Lunak — Penerjemahan Model Perancangan (Use Case, Class Diagram, ERD, Mockup) ke Framework **CodeIgniter 4** dengan pola **MVC**.

| Model Perancangan | Hasil Terjemahan |
|---|---|
| Use Case Diagram | Controller + Routing (`app/Controllers`, `app/Config/Routes.php`) |
| Class Diagram | Model / Query Builder (`app/Models`) |
| ERD | Struktur Database relasional (`database/probeng.sql`) |
| Mockup UI | View Bootstrap 5 (`app/Views`) |

Rincian pemetaannya ada di **[DOKUMENTASI.md](DOKUMENTASI.md)**.

---

## CARA MENJALANKAN

### ⭐ Cara Paling Mudah (Windows)

Ekstrak folder ini, lalu **klik dua kali `JALANKAN.bat`**.

Selesai. Otomatis:
1. Mencari PHP (Laragon / XAMPP / PHP di PATH)
2. Memastikan MySQL berjalan (menyalakannya bila perlu)
3. Membuat database + tabel + data contoh
4. Membuka browser ke **http://localhost:8080**

> Tidak perlu PHP放到 PATH. Tidak perlu Composer. Tidak perlu import SQL manual.
> Tutup jendela hitam (atau tekan `Ctrl+C`) untuk menghentikan server.

### Cara Manual

**Langkah 1 — Nyalakan MySQL**
- **Laragon**: jalankan `laragon.exe`, lalu klik tombol **Start All**
- **XAMPP**: buka XAMPP Control Panel, klik **Start** pada baris **MySQL**

**Langkah 2 — Jalankan aplikasi**
```bash
cd probeng
JALANKAN.bat
```

atau bila PHP sudah ada di PATH:
```bash
php spark install:database
php spark serve
```

**Langkah 3 — Buka di browser**
Kunjungi **http://localhost:8080**

Saat pertama dibuka, aplikasi mengarahkan ke halaman **Instalasi** (`/install`).
Klik **"Mulai Instalasi"** — database `db_probeng`, 4 tabel, dan data contoh akan
dibuat otomatis. Setelah itu klik **"Masuk ke Aplikasi"**.

### Untuk Linux / macOS
```bash
cd probeng
chmod +x JALANKAN.sh
./JALANKAN.sh
```
atau `powershell -ExecutionPolicy Bypass -File .\JALANKAN.ps1` bila PowerShell tersedia.

### MENARUH PROYEK INI KE GITHUB

### Cara tercepat (Windows)

Klik dua kali **`UNGGAH-KE-GITHUB.bat`**

Skrip akan meminta Anda masuk ke GitHub (browser terbuka sekali saja), membuat
repository, lalu mengunggah seluruh kode secara otomatis. Alamat repository Anda
akan ditampilkan di layar — buka alamat itu di browser untuk melihat kodenya.

### Manual

```bash
# 1. Login (sekali saja)
gh auth login --hostname github.com --git-protocol https --web

# 2. Buat repository dan unggah
gh repo create probeng --public --source=. --remote=origin --push

# 3. Perbarui bila ada perubahan
git add .
git commit -m "Deskripsi perubahan"
git push
```

### Menyalakan proyek yang diunduh dari GitHub

```bash
git clone https://github.com/akun-anda/probeng.git
cd probeng
composer install        # memasang library, folder vendor tidak ikut di-commit
copy .env.example .env  # Windows   |   cp .env.example .env untuk Linux/macOS
JALANKAN.bat
```

`JALANKAN.bat` otomatis menjalankan `composer install` bila folder `vendor`
belum ada, sehingga cukup klik dua kali setelah menyalin `.env`.

### Apa yang tidak ikut di-commit

| Item | Alasan |
|---|---|
| `.env` | Konfigurasi lokal, hanya `.env.example` yang di-commit |
| `vendor/` | Library hasil `composer install` (3.056 file) |
| `writable/logs`, `writable/cache`, `writable/session` | Berkas sementara saat aplikasi berjalan |

---

## Pemecahan Masalah

| Gejala | Penyebab & Solusi |
|---|---|
| `[ERROR] PHP tidak ditemukan` | Install Laragon atau XAMPP, lalu jalankan ulang `JALANKAN.bat` |
| `[PERINGATAN] MySQL belum merespons` | Nyalakan MySQL di Laragon/XAMPP, lalu tekan Enter |
| Halaman kosong / putih | Jalankan ulang `JALANKAN.bat` dari dalam folder `probeng` |
| Error 500 saat membuka aplikasi | Pastikan MySQL aktif, lalu buka `http://localhost:8080/install` lalu klik "Mulai Instalasi" |
| Port 8080 sudah dipakai | Jalankan `php spark serve --port 8081`, lalu buka `http://localhost:8081` |

---

## Kebutuhan Perangkat Lunak
- **PHP 8.1+** beserta ekstensi `mysqli` — sudah dimiliki **Laragon** dan **XAMPP**
- **MySQL 8 / MariaDB 10.4+**
- ~~Composer~~ → **tidak perlu lagi**, seluruh dependensi sudah ikut di folder `vendor/`

## Isi Paket
```
probeng/
├── app/
│   ├── Commands/InstallDatabase.php     perintah php spark install:database
│   ├── Controllers/                     Dashboard, Pelanggan, Sparepart, Servis, Install
│   ├── Filters/DatabaseReadyFilter.php  mengarahkan ke /install bila DB belum ada
│   ├── Helpers/probeng_helper.php       rupiah(), tanggal_indonesia(), badge_status()
│   ├── Libraries/Installer.php          membaca & menjalankan database/probeng.sql
│   ├── Models/                          PelangganModel, SparepartModel, ServisModel
│   └── Views/                           template + dashboard, pelanggan, sparepart, servis
├── database/probeng.sql                 DDL + data contoh
├── public/                              document root
├── vendor/                              library CodeIgniter 4
├── writable/                            cache, log, session
├── .env                                 konfigurasi koneksi database
├── DOKUMENTASI.md                       tabel pemetaan model perancangan
├── JALANKAN.bat                         shortcut menjalankan aplikasi
└── spark                                perintah CLI CodeIgniter
```

## Konfigurasi `.env`
```ini
database.default.hostname = localhost
database.default.database = db_probeng
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```
Ubah `username` / `password` bila MySQL Anda memakai kredensial lain.

## Daftar URL (Routing)

| URL | Controller::Method | Keterangan |
|---|---|---|
| `/install` | `Install::index` | Instalasi database otomatis |
| `/` , `/dashboard` | `Dashboard::index` | Ringkasan pendapatan, servis aktif, stok menipis |
| `/pelanggan` | `Pelanggan::index` | Daftar pelanggan + pencarian |
| `/pelanggan/tambah` | `Pelanggan::create` `::store` | Form & simpan pelanggan |
| `/pelanggan/edit/{id}` | `Pelanggan::edit` `::update` | Form & ubah pelanggan |
| `/pelanggan/hapus/{id}` | `Pelanggan::delete` | Hapus pelanggan (+ transaksi) |
| `/sparepart` | `Sparepart::index` | Daftar & inventaris suku cadang |
| `/sparepart/tambah` | `Sparepart::create` `::store` | Form & simpan barang |
| `/sparepart/edit/{id}` | `Sparepart::edit` `::update` | Form & ubah barang |
| `/sparepart/stok/{id}` | `Sparepart::tambah_stok` | Tambah stok (barang masuk) |
| `/sparepart/hapus/{id}` | `Sparepart::delete` | Hapus barang |
| `/servis` | `Servis::index` | Daftar transaksi + filter status |
| `/servis/tambah` | `Servis::create` `::store` | Transaksi servis + rincian barang |
| `/servis/detail/{id}` | `Servis::detail` | Detail transaksi & total biaya |
| `/servis/status/{id}` | `Servis::update_status` | Ubah status servis |
| `/servis/nota/{id}` | `Servis::cetak_nota` | Cetak nota/resi pembayaran |
| `/servis/hapus/{id}` | `Servis::delete` | Batalkan transaksi, stok dikembalikan |

## Struktur Basis Data

```
pelanggan (1) ──────< (N) servis (1) ──────< (N) detail_servis (N) >──── (1) sparepart
```

- `pelanggan.plat_nomor` UNIQUE
- `servis.status_servis` ENUM('Antre','Dikerjakan','Selesai','Diambil')
- Hapus pelanggan → transaksi servis & detail terhapus otomatis (ON DELETE CASCADE)
- Barang yang sudah dipakai transaksi tidak dapat dihapus

## Catatan Teknis
- Harga jual & subtotal dihitung **di server** dari master `sparepart` (input pengguna tidak dipercaya).
- Stok berkurang saat transaksi disimpan dan kembali saat transaksi dihapus, dibungkus *database transaction*.
- `Total Bayar = Biaya Jasa + Σ (Harga Jual × Jumlah)`.
- `Total Pendapatan` di dashboard hanya menghitung status **Selesai** dan **Diambil**.
- Filter CSRF aktif secara default (`app/Config/Filters.php`), seluruh form menyertakan `csrf_field()`.
- Memperbarui dependensi (opsional): `composer update` (butuh koneksi internet).