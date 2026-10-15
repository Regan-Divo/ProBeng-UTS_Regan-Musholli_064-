<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Data Servis
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="kartu p-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-tools"></i> Daftar Transaksi Servis</h5>
        <a href="<?= site_url('servis/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Transaksi Baru
        </a>
    </div>

    <!-- Filter status + pencarian -->
    <form method="get" action="<?= site_url('servis') ?>" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <?php foreach ($statusList as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Cari pelanggan / plat / keluhan..."
                   value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i> Cari</button>
        </div>
        <div class="col-auto">
            <a href="<?= site_url('servis') ?>" class="btn btn-outline-danger">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No. Servis</th>
                    <th>Pelanggan</th>
                    <th>Keluhan</th>
                    <th class="text-end">Biaya Jasa</th>
                    <th class="text-center">Status</th>
                    <th>Tanggal Masuk</th>
                    <th style="width:230px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($servis)): ?>
                    <tr><td colspan="7" class="text-center text-secondary py-4">Tidak ada transaksi servis.</td></tr>
                <?php else: ?>
                    <?php foreach ($servis as $s): ?>
                        <tr>
                            <td class="fw-semibold">#<?= $s['id_servis'] ?></td>
                            <td>
                                <?= esc($s['nama_pelanggan']) ?><br>
                                <small class="text-secondary"><?= esc($s['plat_nomor']) ?> &middot; <?= esc($s['jenis_kendaraan']) ?></small>
                            </td>
                            <td><small><?= esc($s['keluhan']) ?></small></td>
                            <td class="text-end"><?= rupiah($s['biaya_jasa']) ?></td>
                            <td class="text-center"><?= badge_status($s['status_servis']) ?></td>
                            <td class="small"><?= tanggal_indonesia($s['tanggal_masuk']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('servis/detail/' . $s['id_servis']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="<?= site_url('servis/nota/' . $s['id_servis']) ?>" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-receipt"></i> Nota
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>