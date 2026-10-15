<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Data Suku Cadang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Ringkasan inventaris -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kartu p-3">
            <small class="text-secondary">Jenis Barang</small>
            <h4 class="mb-0 text-primary"><?= $ringkasan['jumlah_barang'] ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kartu p-3">
            <small class="text-secondary">Total Unit Stok</small>
            <h4 class="mb-0 text-success"><?= $ringkasan['total_unit'] ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kartu p-3">
            <small class="text-secondary">Nilai Pembelian</small>
            <h4 class="mb-0 text-warning"><?= rupiah($ringkasan['nilai_beli']) ?></h4>
        </div>
    </div>
</div>

<div class="kartu p-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Daftar Suku Cadang</h5>
        <a href="<?= site_url('sparepart/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Suku Cadang
        </a>
    </div>

    <form method="get" action="<?= site_url('sparepart') ?>" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Cari nama barang..."
                   value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i> Cari</button>
        </div>
        <div class="col-auto">
            <a href="<?= site_url('sparepart') ?>" class="btn btn-outline-danger">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:50px">#</th>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th style="width:300px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sparepart)): ?>
                    <tr><td colspan="7" class="text-center text-secondary py-4">Data suku cadang tidak ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($sparepart as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $b['id_sparepart'] ?></td>
                            <td class="fw-semibold"><?= esc($b['nama_barang']) ?></td>
                            <td class="text-end"><?= rupiah($b['harga_beli']) ?></td>
                            <td class="text-end"><?= rupiah($b['harga_jual']) ?></td>
                            <td class="text-center"><?= notif_stok($b['stok']) ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('sparepart/edit/' . $b['id_sparepart']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <!-- Form tambah stok -->
                                <form action="<?= site_url('sparepart/stok/' . $b['id_sparepart']) ?>" method="post"
                                      class="d-inline-flex gap-1 align-items-center ms-1">
                                    <?= csrf_field() ?>
                                    <input type="number" name="jumlah" class="form-control form-control-sm"
                                           style="width:70px" min="1" value="1" title="Jumlah stok tambahan">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </button>
                                </form>

                                <form action="<?= site_url('sparepart/hapus/' . $b['id_sparepart']) ?>" method="post"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin menghapus barang ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
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
