<?php
/**
 * NOTA / RESI PEMBAYARAN
 * Layout khusus cetak: tanpa sidebar & navbar, dioptimalkan untuk printer A4.
 * Tidak memakai template utama (template.php).
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Servis #<?= $servis['id_servis'] ?> - ProBeng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #eceff3; }
        .nota { width: 210mm; min-height: 297mm; margin: 20px auto; background: #fff; padding: 15mm; }
        .garis-pisah { border-top: 2px dashed #333; }

        /* Aturan khusus saat dicetak */
        @media print {
            body { background: #fff; }
            .nota { width: auto; margin: 0; box-shadow: none; }
            .tombol { display: none; }           /* sembunyikan tombol cetak */
            @page { size: A4; margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="nota">

    <!-- ============ KOP NOTA ============ -->
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable-circle"></i> ProBeng</h3>
            <small class="text-secondary">
                Bengkel Servis &amp; Suku Cadang<br>
                Jl. Raya Otomotif No. 123, Bandung<br>
                Telp: (022) 876-5432 &middot; Email: admin@probeng.test
            </small>
        </div>
        <div class="text-end">
            <h5 class="mb-1">NOTA SERVIS</h5>
            <div class="fs-5 fw-bold">#<?= $servis['id_servis'] ?></div>
            <small class="text-secondary"><?= tanggal_indonesia($servis['tanggal_masuk']) ?></small>
        </div>
    </div>

    <hr class="garis-pisah my-3">

    <!-- ============ DATA PELANGGAN & KENDARAAN ============ -->
    <div class="row mb-3">
        <div class="col-7">
            <table class="table table-sm table-borderless mb-0 small">
                <tr><td class="text-secondary" style="width:110px">Pelanggan</td><td><strong><?= esc($servis['nama_pelanggan']) ?></strong></td></tr>
                <tr><td class="text-secondary">No. Telepon</td><td><?= esc($servis['no_telepon']) ?></td></tr>
                <tr><td class="text-secondary">Alamat</td><td>-</td></tr>
            </table>
        </div>
        <div class="col-5">
            <table class="table table-sm table-borderless mb-0 small">
                <tr><td class="text-secondary" style="width:90px">Plat</td><td><strong><?= esc($servis['plat_nomor']) ?></strong></td></tr>
                <tr><td class="text-secondary">Kendaraan</td><td><?= esc($servis['jenis_kendaraan']) ?></td></tr>
                <tr><td class="text-secondary">Status</td><td><?= badge_status($servis['status_servis']) ?></td></tr>
            </table>
        </div>
    </div>

    <p class="small mb-2"><strong>Keluhan pelanggan:</strong> <?= esc($servis['keluhan']) ?></p>

    <!-- ============ RINCIAN ============ -->
    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th style="width:40px">No.</th>
                <th>Item / Suku Cadang</th>
                <th style="width:110px" class="text-end">Harga</th>
                <th style="width:60px" class="text-center">Qty</th>
                <th style="width:130px" class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detail)): ?>
                <tr><td colspan="5" class="text-center text-secondary">- Biaya jasa saja -</td></tr>
            <?php else: ?>
                <?php foreach ($detail as $i => $d): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= esc($d['nama_barang']) ?></td>
                        <td class="text-end"><?= rupiah($d['harga_jual']) ?></td>
                        <td class="text-center"><?= $d['jumlah'] ?></td>
                        <td class="text-end"><?= rupiah($d['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr>
                <td colspan="4" class="text-end"><strong>Biaya Jasa</strong></td>
                <td class="text-end"><?= rupiah($servis['biaya_jasa']) ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Total Barang</strong></td>
                <td class="text-end"><?= rupiah($totalBarang) ?></td>
            </tr>
            <tr class="table-light">
                <td colspan="4" class="text-end fs-5"><strong>TOTAL BAYAR</strong></td>
                <td class="text-end fs-5 fw-bold"><?= rupiah($totalBayar) ?></td>
            </tr>
        </tbody>
    </table>

    <p class="small text-secondary">
        <strong>Catatan:</strong> Barang yang telah dibeli tidak dapat ditukar. Mohon simpan nota ini sebagai bukti servicing kendaraan.
    </p>

    <hr class="garis-pisah my-4">

    <!-- ============ TANDA TANGAN ============ -->
    <div class="row text-center small">
        <div class="col-4 offset-4">
            <p class="mb-5">Hormat saya,</p>
            <p class="fw-bold mb-0">_____________________</p>
            <p class="mb-0">Pelanggan</p>
        </div>
        <div class="col-4">
            <p class="mb-5">Hormat saya,</p>
            <p class="fw-bold mb-0">_____________________</p>
            <p class="mb-0">Mekanik</p>
        </div>
    </div>

    <hr class="garis-pisah mt-4 mb-2">

    <!-- ============ TOMBOL (tidak ikut tercetak) ============ -->
    <div class="tombol d-flex justify-content-between align-items-center">
        <a href="<?= site_url('servis/detail/' . $servis['id_servis']) ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer"></i> Cetak Nota
        </button>
    </div>
</div>

<script>
    // Nota langsung terbuka dialog cetak (opsional)
    window.addEventListener('load', function () {
        // window.print();   // aktifkan bila ingin otomatis mencetak
    });
</script>
</body>
</html>