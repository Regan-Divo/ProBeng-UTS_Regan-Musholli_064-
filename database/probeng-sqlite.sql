-- =====================================================================
--  PROBENG - Versi SQLite untuk Node.js
--
--  Dipakai sebagai alternatif ringan bila MySQL/MariaDB tidak tersedia.
--  Tidak perlu install database server apa pun - cukup PHP.
--
--  Perbedaan dari versi MySQL (database/probeng.sql):
--    - INTEGER PRIMARY KEY AUTOINCREMENT  pengganti INT AUTO_INCREMENT
--    - TEXT + CHECK                     pengganti ENUM
--    - Tidak ada ENGINE=InnoDB / charset
--
--  Jalankan: php spark install:database  (otomatis memilih versi yang tepat)
-- =====================================================================

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan   INTEGER PRIMARY KEY AUTOINCREMENT,
    nama_pelanggan TEXT NOT NULL,
    no_telepon     TEXT NOT NULL,
    plat_nomor     TEXT NOT NULL UNIQUE,
    jenis_kendaraan TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS sparepart (
    id_sparepart INTEGER PRIMARY KEY AUTOINCREMENT,
    nama_barang  TEXT NOT NULL,
    harga_beli   INTEGER NOT NULL DEFAULT 0,
    harga_jual   INTEGER NOT NULL DEFAULT 0,
    stok         INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS servis (
    id_servis     INTEGER PRIMARY KEY AUTOINCREMENT,
    id_pelanggan  INTEGER NOT NULL,
    keluhan       TEXT NOT NULL,
    biaya_jasa    INTEGER NOT NULL DEFAULT 0,
    status_servis TEXT NOT NULL DEFAULT 'Antre'
                  CHECK (status_servis IN ('Antre','Dikerjakan','Selesai','Diambil')),
    tanggal_masuk TEXT NOT NULL,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS detail_servis (
    id_detail    INTEGER PRIMARY KEY AUTOINCREMENT,
    id_servis    INTEGER NOT NULL,
    id_sparepart INTEGER NOT NULL,
    jumlah       INTEGER NOT NULL DEFAULT 1,
    subtotal     INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (id_servis) REFERENCES servis(id_servis)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_sparepart) REFERENCES sparepart(id_sparepart)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ---------------------------------------------------------------------
--  Data contoh
-- ---------------------------------------------------------------------
DELETE FROM detail_servis;
DELETE FROM servis;
DELETE FROM sparepart;
DELETE FROM pelanggan;
DELETE FROM sqlite_sequence;

INSERT INTO pelanggan (nama_pelanggan, no_telepon, plat_nomor, jenis_kendaraan) VALUES
('Ahmad Fauzi',    '081234567890', 'B 1234 ABC', 'Honda Beat'),
('Budi Santoso',   '082345678901', 'B 5678 DEF', 'Yamaha Mio'),
('Citra Lestari',  '083456789012', 'L 9012 GHI', 'Toyota Avanza'),
('Dewi Anggraini', '084567890123', 'N 3456 JKL', 'Honda Brio');

INSERT INTO sparepart (nama_barang, harga_beli, harga_jual, stok) VALUES
('Oli Synthetic 10W-30 (1L)', 55000,  65000,  20),
('Suku Cadang Rem Depan',    45000,  60000,  12),
('Busi / Kampas Rem',          3000,   5000,  60),
('Filter Oli',                18000,  25000,   8),
('Spion Motor Kiri',          75000, 100000,   4),
('Ban luar 70/17-17',        185000, 215000,   6),
('Kabel Busi Set',            12000,  18000,   3),
('Aki 12V 7Ah',              520000, 585000,   2);

INSERT INTO servis (id_pelanggan, keluhan, biaya_jasa, status_servis, tanggal_masuk) VALUES
(1, 'Ganti oli rutin dan servis rem', 50000, 'Selesai',    '2024-05-10 09:15:00'),
(2, 'Ganti kampas rem yang sudah tipis', 35000, 'Diambil', '2024-05-11 10:30:00'),
(3, 'Bunyi tidak normal saat dikendarikan', 75000, 'Dikerjakan', '2024-05-12 13:45:00'),
(4, 'Servis berkala 10.000 km', 60000, 'Antre', '2024-05-13 08:20:00');

INSERT INTO detail_servis (id_servis, id_sparepart, jumlah, subtotal) VALUES
(1, 1, 1, 65000),
(1, 3, 2, 10000),
(2, 2, 1, 60000),
(3, 4, 1, 25000),
(4, 6, 2, 430000);