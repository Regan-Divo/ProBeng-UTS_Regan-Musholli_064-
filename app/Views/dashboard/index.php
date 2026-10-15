<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ============ KARTU STATISTIK ============ -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kartu p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-secondary mb-1">Total Pendapatan</p>
                    <h4 class="mb-0 text-success"><?= rupiah($totalPendapatan) ?></h4>
                    <small class="text-secondary">Status Selesai &amp; Diambil</small>
                </div>
                <i class="bi bi-cash-stack fs-1 text-success opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kartu p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-secondary mb-1">Servis Aktif</p>
                    <h4 class="mb-0 text-warning"><?= $servisAktif ?></h4>
                    <small class="text-secondary">Antre &amp; Dikerjakan</small>
                </div>
                <i class="bi bi-tools fs-1 text-warning opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kartu p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-secondary mb-1">Stok Menipis</p>
                    <h4 class="mb-0 text-danger"><?= count($stokMenipis) ?></h4>
                    <small class="text-secondary">Sisa &le; 5 unit</small>
                </div>
                <i class="bi bi-box-seam fs-1 text-danger opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kartu p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-secondary mb-1">Jumlah Pelanggan</p>
                    <h4 class="mb-0 text-primary"><?= $totalPelanggan ?></h4>
                    <small class="text-secondary"><?= $totalServis ?> transaksi servis</small>
                </div>
                <i class="bi bi-people fs-1 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============ GRAFIK + STOK MENIPIS ============ -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="kartu p-4 h-100">
            <h6 class="fw-bold mb-3">Status Transaksi Servis</h6>
            <?php
            $maxStatus = max($rekapStatus ?: [1]);
            foreach ($rekapStatus as $status => $jumlah):
                $persen = $maxStatus > 0 ? round($jumlah / $maxStatus * 100) : 0;
                $warna = [
                    'Antre' => 'bg-secondary',
                    'Dikerjakan' => 'bg-warning',
                    'Selesai' => 'bg-info',
                    'Diambil' => 'bg-success',
                ][$status];
            ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= badge_status($status) ?></span>
                        <span class="fw-semibold"><?= $jumlah ?> transaksi</span>
                    </div>
                    <div class="progress" style="height:10px">
                        <div class="progress-bar <?= $warna ?>" style="width: <?= $persen ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <hr>
            <p class="text-secondary small mb-0">
                <i class="bi bi-box-seam"></i> Inventaris: <strong><?= $inventaris['jumlah_barang'] ?></strong> jenis barang,
                <strong><?= $inventaris['total_unit'] ?></strong> unit
                (nilai pembelian <?= rupiah($inventaris['nilai_beli']) ?>).
            </p>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="kartu p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Stok Perlu Restok</h6>
                <a href="<?= site_url('sparepart') ?>" class="btn btn-sm btn-outline-primary">Lihat semua</a>
            </div>

            <?php if (empty($stokMenipis)): ?>
                <p class="text-secondary mb-0"><i class="bi bi-check-circle"></i> Semua stok aman.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($stokMenipis as $barang): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-nut me-1"></i><?= esc($barang['nama_barang']) ?></span>
                            <?= notif_stok($barang['stok']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============ TABEL TRANSAKSI TERBARU ============ -->
<div class="kartu p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Transaksi Terbaru</h6>
        <a href="<?= site_url('servis') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Transaksi Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Servis</th>
                    <th>Pelanggan</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    <th>Tanggal Masuk</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksiTerbaru)): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada transaksi.</td></tr>
                <?php else: ?>
                    <?php foreach ($transaksiTerbaru as $s): ?>
                        <tr>
                            <td class="fw-semibold">#<?= $s['id_servis'] ?></td>
                            <td>
                                <?= esc($s['nama_pelanggan']) ?><br>
                                <small class="text-secondary"><?= esc($s['plat_nomor']) ?></small>
                            </td>
                            <td><?= esc($s['keluhan']) ?></td>
                            <td><?= badge_status($s['status_servis']) ?></td>
                            <td class="small"><?= tanggal_indonesia($s['tanggal_masuk'], true) ?></td>
                            <td class="text-end">
                                <a href="<?= site_url('servis/detail/' . $s['id_servis']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
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