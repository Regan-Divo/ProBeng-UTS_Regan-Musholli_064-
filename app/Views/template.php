<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title', 'ProBeng') ?> &mdash; ProBeng</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background-color: #f4f6f9; }
        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1f2d3d;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
        }
        .sidebar a.nav-link {
            color: #cfd8e3;
            border-radius: .5rem;
            margin: 0 .75rem;
        }
        .sidebar a.nav-link:hover { background: #2c3e50; color: #fff; }
        .sidebar a.nav-link.active { background: #0d6efd; color: #fff; }
        /* Konten */
        .main-content { margin-left: 250px; padding: 1.5rem; }
        /* Navigasi alternatif untuk layar kecil (sidebar disembunyikan) */
        .nav-mobile { display: none; gap: .5rem; overflow-x: auto; padding-bottom: .25rem; }
        .nav-mobile a {
            white-space: nowrap;
            font-size: .9rem;
            text-decoration: none;
            border-radius: .5rem;
        }
        @media (max-width: 991.98px) {
            /* !important diperlukan karena kelas .d-flex memakai !important */
            .sidebar { display: none !important; }
            .main-content { margin-left: 0; padding: 1rem; }
            .nav-mobile { display: flex !important; }
        }
        .kartu {
            background: #fff;
            border-radius: .75rem;
            border: 0;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.06);
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar d-flex flex-column">
        <div class="p-3 border-bottom border-secondary">
            <h5 class="text-white mb-0">
                <i class="bi bi-wrench-adjustable-circle text-info"></i> ProBeng
            </h5>
            <small class="text-secondary">Sistem Servis &amp; Suku Cadang</small>
        </div>

        <nav class="nav flex-column mt-3">
            <?php
            // $menu berisi nama menu aktif yang dikirim controller
            $menu = $menu ?? 'dashboard';
            ?>
            <a class="nav-link <?= $menu === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link <?= $menu === 'pelanggan' ? 'active' : '' ?>" href="<?= site_url('pelanggan') ?>">
                <i class="bi bi-people"></i> Data Pelanggan
            </a>
            <a class="nav-link <?= $menu === 'sparepart' ? 'active' : '' ?>" href="<?= site_url('sparepart') ?>">
                <i class="bi bi-box-seam"></i> Suku Cadang
            </a>
            <a class="nav-link <?= $menu === 'servis' ? 'active' : '' ?>" href="<?= site_url('servis') ?>">
                <i class="bi bi-tools"></i> Transaksi Servis
            </a>
        </nav>

        <div class="mt-auto p-3 border-top border-secondary">
            <small class="text-secondary">
                ProBeng v1.0<br>
                Rekayasa Perangkat Lunak
            </small>
        </div>
    </aside>

    <!-- ================= KONTEN ================= -->
    <main class="main-content">

        <!-- Navbar atas -->
        <nav class="navbar bg-white rounded-3 px-3 mb-3" style="box-shadow:0 .125rem .25rem rgba(0,0,0,.06)">
            <span class="navbar-brand fw-semibold"><?= $this->renderSection('title', 'Dashboard') ?></span>
            <span class="text-secondary small d-none d-sm-inline"><?= date('l, d F Y') ?></span>
        </nav>

        <!-- Menu navigasi untuk layar kecil (hanya tampil < 992px) -->
        <nav class="nav-mobile mb-3">
            <a class="btn btn-sm <?= ($menu ?? '') === 'dashboard' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="<?= site_url('dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="btn btn-sm <?= ($menu ?? '') === 'pelanggan' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="<?= site_url('pelanggan') ?>">
                <i class="bi bi-people"></i> Pelanggan
            </a>
            <a class="btn btn-sm <?= ($menu ?? '') === 'sparepart' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="<?= site_url('sparepart') ?>">
                <i class="bi bi-box-seam"></i> Suku Cadang
            </a>
            <a class="btn btn-sm <?= ($menu ?? '') === 'servis' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="<?= site_url('servis') ?>">
                <i class="bi bi-tools"></i> Servis
            </a>
        </nav>

        <!-- Pesan notifikasi -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>

        <footer class="text-center text-secondary small mt-4">
            &copy; <?= date('Y') ?> ProBeng &mdash; Tugas Rekayasa Perangkat Lunak
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>