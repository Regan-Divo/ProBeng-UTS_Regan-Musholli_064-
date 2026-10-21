-- =====================================================================
--  PROBENG - Sistem Manajemen Servis & Inventaris Suku Cadang Bengkel
--  SKEMA DATABASE (hasil terjemahan ERD)
--  DBMS : MySQL / MariaDB
--  Cara pakai: mysql -u root < database/probeng.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `db_probeng`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `db_probeng`;

-- ---------------------------------------------------------------------
-- 1. Menghapus tabel (urutan dari child ke parent)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `detail_servis`;
DROP TABLE IF EXISTS `servis`;
DROP TABLE IF EXISTS `sparepart`;
DROP TABLE IF EXISTS `pelanggan`;

-- =====================================================================
-- TABEL 1: PELANGGAN  (entitas mandiri)
-- Relasi: 1 pelanggan  --(0..N)-- 1 servis
-- =====================================================================
CREATE TABLE `pelanggan` (
  `id_pelanggan`   INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `no_telepon`     VARCHAR(20)  NOT NULL,
  `plat_nomor`     VARCHAR(20)  NOT NULL,
  `jenis_kendaraan` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `uk_pelanggan_plat_nomor` (`plat_nomor`),
  KEY `idx_pelanggan_nama` (`nama_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- TABEL 2: SPAREPART / SUKU CADANG  (entitas mandiri)
-- Relasi: 1 sparepart  --(0..N)-- 1 detail_servis
-- =====================================================================
CREATE TABLE `sparepart` (
  `id_sparepart` INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_barang`  VARCHAR(100) NOT NULL,
  `harga_beli`   INT(11)      NOT NULL DEFAULT 0,
  `harga_jual`   INT(11)      NOT NULL DEFAULT 0,
  `stok`         INT(11)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_sparepart`),
  KEY `idx_sparepart_nama` (`nama_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- TABEL 3: SERVIS  (transaksi utama / parent)
-- Relasi: N:1 ke pelanggan
-- =====================================================================
CREATE TABLE `servis` (
  `id_servis`      INT(11)     NOT NULL AUTO_INCREMENT,
  `id_pelanggan`   INT(11)     NOT NULL,
  `keluhan`        TEXT        NOT NULL,
  `biaya_jasa`     INT(11)     NOT NULL DEFAULT 0,
  `status_servis`  ENUM('Antre','Dikerjakan','Selesai','Diambil') NOT NULL DEFAULT 'Antre',
  `tanggal_masuk`  DATETIME    NOT NULL,
  PRIMARY KEY (`id_servis`),
  KEY `fk_servis_pelanggan` (`id_pelanggan`),
  KEY `idx_servis_status` (`status_servis`),
  CONSTRAINT `fk_servis_pelanggan`
    FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- TABEL 4: DETAIL_SERVIS  (child / asosiasi)
-- Relasi: N:1 ke servis dan N:1 ke sparepart
-- =====================================================================
CREATE TABLE `detail_servis` (
  `id_detail`    INT(11) NOT NULL AUTO_INCREMENT,
  `id_servis`    INT(11) NOT NULL,
  `id_sparepart` INT(11) NOT NULL,
  `jumlah`       INT(11) NOT NULL DEFAULT 1,
  `subtotal`     INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_servis` (`id_servis`),
  KEY `fk_detail_sparepart` (`id_sparepart`),
  CONSTRAINT `fk_detail_servis`
    FOREIGN KEY (`id_servis`) REFERENCES `servis` (`id_servis`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_sparepart`
    FOREIGN KEY (`id_sparepart`) REFERENCES `sparepart` (`id_sparepart`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
--  DATA CONTOH (data awal / dummy)
-- =====================================================================
INSERT INTO `pelanggan` (`nama_pelanggan`, `no_telepon`, `plat_nomor`, `jenis_kendaraan`) VALUES
('Ahmad Fauzi',   '081234567890', 'B 1234 ABC', 'Honda Beat'),
('Budi Santoso',  '082345678901', 'B 5678 DEF', 'Yamaha Mio'),
('Citra Lestari', '083456789012', 'L 9012 GHI', 'Toyota Avanza'),
('Dewi Anggraini','084567890123', 'N 3456 JKL', 'Honda Brio');

INSERT INTO `sparepart` (`nama_barang`, `harga_beli`, `harga_jual`, `stok`) VALUES
('Oli Synthetic 10W-30 (1L)', 55000,  65000,  20),
('Suku Cadang Rem Depan',    45000,  60000,  12),
('Busi / Kampas Rem',          3000,   5000,  60),
('Filter Oli',                18000,  25000,  8),
('Spion Motor Kiri',          75000, 100000,  4),
('Ban luar 70/17-17',        185000, 215000,  6),
('Kabel Busi Set',            12000,  18000,  3),
('Aki 12V 7Ah',              520000, 585000,  2);

INSERT INTO `servis` (`id_pelanggan`, `keluhan`, `biaya_jasa`, `status_servis`, `tanggal_masuk`) VALUES
(1, 'Ganti oli rutin dan servis rem', 50000, 'Selesai', '2024-05-10 09:15:00'),
(2, 'Ganti kampas rem yang sudah tipis',35000, 'Diambil', '2024-05-11 10:30:00'),
(3, 'Bunyi tidak normal saat dikendarikan', 75000, 'Dikerjakan', '2024-05-12 13:45:00'),
(4, 'Servis berkala 10.000 km',       60000, 'Antre',    '2024-05-13 08:20:00');

INSERT INTO `detail_servis` (`id_servis`, `id_sparepart`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 65000),
(1, 3, 2, 10000),
(2, 2, 1, 60000),
(3, 4, 1, 25000),
(4, 6, 2, 430000);