# Diagram Perancangan ProBeng

Folder ini berisi model perancangan (hasil terjemahan ke kode) beserta file sumbernya.

## Isi Folder

| File | Format | Isi |
|---|---|---|
| `01 Use Case Diagram - ProBeng.vsdx` | Visio | Use case, aktor, relasi `<<include>>` |
| `01 Use Case Diagram - ProBeng.png` | Gambar | Versi siap tempel ke PowerPoint |
| `02 Class Diagram - ProBeng.vsdx` | Visio | Class Pelanggan, Sparepart, Servis, DetailServis |
| `02 Class Diagram - ProBeng.png` | Gambar | Versi siap tempel ke PowerPoint |
| `03 ERD - ProBeng.vsdx` | Visio | 4 tabel beserta PK, FK, dan relasi 1 : N |
| `03 ERD - ProBeng.png` | Gambar | Versi siap tempel ke PowerPoint |
| `generator-diagram.vbs` | VBScript | Skrip yang menghasilkan ketiga diagram di atas |

## Pemetaan Diagram ke Implementasi

| Diagram | Terjemahan ke | File Kode |
|---|---|---|
| Use Case Diagram | Controller + Routing | `app/Controllers/`, `app/Config/Routes.php` |
| Class Diagram | Model (Query Builder) | `app/Models/PelangganModel.php`, `SparepartModel.php`, `ServisModel.php` |
| ERD | Struktur tabel & foreign key | `database/probeng.sql` |

## Cara Memakai

- **PowerPoint** → gunakan file `.png`, sisipkan ke slide.
- **Visio** → buka file `.vsdx`, seluruh objek bisa diedit (warna, teks, posisi).
- **Ingin menyusun ulang** → jalankan `cscript generator-diagram.vbs` (memerlukan Microsoft Visio terpasang).

## Daftar Entitas ERD

```
pelanggan (1) ────< (N) servis (1) ────< (N) detail_servis (N) >──── (1) sparepart
```

| Tabel | Primary Key | Foreign Key |
|---|---|---|
| `pelanggan` | `id_pelanggan` | — |
| `sparepart` | `id_sparepart` | — |
| `servis` | `id_servis` | `id_pelanggan` → `pelanggan` |
| `detail_servis` | `id_detail` | `id_servis` → `servis`, `id_sparepart` → `sparepart` |