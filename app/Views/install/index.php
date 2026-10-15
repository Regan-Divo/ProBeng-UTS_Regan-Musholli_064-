<?php
// Layout mandiri untuk halaman instalasi (belum memakai template utama)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi ProBeng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 760px">

    <div class="text-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-wrench-adjustable-circle text-primary"></i> ProBeng</h1>
        <p class="text-secondary">Sistem Manajemen Servis &amp; Inventaris Suku Cadang Bengkel</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-3"><i class="bi bi-database-check"></i> Instalasi Database</h5>

            <!-- Konfigurasi koneksi -->
            <table class="table table-sm small mb-4">
                <tr>
                    <td class="text-secondary" style="width:110px">Server</td>
                    <td><?= esc($dbHost) ?></td>
                </tr>
                <tr>
                    <td class="text-secondary">Database</td>
                    <td><code><?= esc($dbName) ?></code></td>
                </tr>
                <tr>
                    <td class="text-secondary">User</td>
                    <td><?= esc($dbUser) ?></td>
                </tr>
            </table>

            <!-- Hasil instalasi -->
            <?php if ($hasil['success'] === true): ?>
                <div class="alert alert-success">
                    <h6 class="fw-bold"><i class="bi bi-check-circle"></i> <?= esc($hasil['message']) ?></h6>
                    <ul class="mb-0 small">
                        <?php foreach ($hasil['detail'] as $baris): ?>
                            <li><?= esc($baris) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="<?= site_url('dashboard') ?>" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-speedometer2"></i> Masuk ke Aplikasi
                </a>

            <?php elseif ($hasil['success'] === false): ?>
                <div class="alert alert-danger">
                    <h6 class="fw-bold"><i class="bi bi-exclamation-triangle"></i> Instalasi Gagal</h6>
                    <p class="mb-1"><?= esc($hasil['message']) ?></p>
                    <?php if (! empty($hasil['detail'])): ?>
                        <ul class="small mb-0">
                            <?php foreach ($hasil['detail'] as $baris): ?>
                                <li><?= esc($baris) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <form method="post" action="<?= site_url('install') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" name="jalankan" value="1" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                    </button>
                </form>

                <div class="alert alert-secondary small mt-3 mb-0">
                    <strong>Tidak bisa konek ke database?</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pastikan MySQL/MariaDB sudah berjalan (XAMPP/Laragon → klik <em>Start</em> pada MySQL).</li>
                        <li>Periksa username &amp; password pada file <code>.env</code>.</li>
                        <li>Atau import manual file <code>database/probeng.sql</code> melalui phpMyAdmin.</li>
                    </ul>
                </div>

            <?php else: ?>
                <?php if ($siap): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Database <strong>sudah siap</strong>.
                        Anda dapat langsung masuk ke aplikasi.
                    </div>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn-primary w-100">
                        <i class="bi bi-speedometer2"></i> Masuk ke Aplikasi
                    </a>
                <?php else: ?>
                    <p class="text-secondary">
                        Database <code><?= esc($dbName) ?></code> belum tersedia. Tekan tombol di bawah untuk
                        membuat database, tabel, dan data contoh secara otomatis.
                    </p>
                    <form method="post" action="<?= site_url('install') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" name="jalankan" value="1" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-download"></i> Mulai Instalasi
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>

    <p class="text-center text-secondary small mt-4">
        &copy; <?= date('Y') ?> ProBeng &mdash; Tugas Rekayasa Perangkat Lunak
    </p>
</div>

</body>
</html>