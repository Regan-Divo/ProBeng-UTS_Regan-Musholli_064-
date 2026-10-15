<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Data Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="kartu p-4">

    <!-- Header + tombol tambah -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-people"></i> Daftar Pelanggan</h5>
        <a href="<?= site_url('pelanggan/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </a>
    </div>

    <!-- Pencarian -->
    <form method="get" action="<?= site_url('pelanggan') ?>" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Cari nama / plat nomor / kendaraan..."
                   value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i> Cari</button>
        </div>
        <div class="col-auto">
            <a href="<?= site_url('pelanggan') ?>" class="btn btn-outline-danger">Reset</a>
        </div>
    </form>

    <!-- Tabel data -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:50px">#</th>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Plat Nomor</th>
                    <th>Jenis Kendaraan</th>
                    <th style="width:190px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pelanggan)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            Data pelanggan tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pelanggan as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $p['id_pelanggan'] ?></td>
                            <td class="fw-semibold"><?= esc($p['nama_pelanggan']) ?></td>
                            <td><?= esc($p['no_telepon']) ?></td>
                            <td><span class="badge bg-dark"><?= esc($p['plat_nomor']) ?></span></td>
                            <td><?= esc($p['jenis_kendaraan']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('pelanggan/edit/' . $p['id_pelanggan']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <!-- Form hapus tetap berfungsi tanpa JavaScript -->
                                <form action="<?= site_url('pelanggan/hapus/' . $p['id_pelanggan']) ?>" method="post"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin menghapus pelanggan ini beserta transaksi servisnya?');">
                                      <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
