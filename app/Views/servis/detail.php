<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Detail Servis
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row g-3">

    <!-- ========== KARTU INFORMASI SERVIS ========== -->
    <div class="col-lg-5">
        <div class="kartu p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Servis #<?= $servis['id_servis'] ?></h5>
                <?= badge_status($servis['status_servis']) ?>
            </div>

            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-secondary" style="width:120px">Pelanggan</td>
                    <td class="fw-semibold"><?= esc($servis['nama_pelanggan']) ?></td>
                </tr>
                <tr>
                    <td class="text-secondary">No. Telepon</td>
                    <td><?= esc($servis['no_telepon']) ?></td>
                </tr>
                <tr>
                    <td class="text-secondary">Plat Nomor</td>
                    <td><span class="badge bg-dark"><?= esc($servis['plat_nomor']) ?></span></td>
                </tr>
                <tr>
                    <td class="text-secondary">Kendaraan</td>
                    <td><?= esc($servis['jenis_kendaraan']) ?></td>
                </tr>
                <tr>
                    <td class="text-secondary">Tanggal Masuk</td>
                    <td><?= tanggal_indonesia($servis['tanggal_masuk'], true) ?></td>
                </tr>
                <tr>
                    <td class="text-secondary">Keluhan</td>
                    <td><?= esc($servis['keluhan']) ?></td>
                </tr>
            </table>

            <hr>

            <form action="<?= site_url('servis/status/' . $servis['id_servis']) ?>" method="post"
                  class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-7">
                    <label class="form-label small mb-1">Ubah Status Servis</label>
                    <select name="status_servis" class="form-select form-select-sm">
                        <?php foreach ($statusList as $s): ?>
                            <option value="<?= $s ?>" <?= $servis['status_servis'] === $s ? 'selected' : '' ?>>
                                <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-5">
                    <button class="btn btn-sm btn-primary w-100"><i class="bi bi-check2"></i> Update</button>
                </div>
            </form>

            <hr>

            <div class="d-grid gap-2">
                <a href="<?= site_url('servis/nota/' . $servis['id_servis']) ?>" class="btn btn-dark">
                    <i class="bi bi-receipt"></i> Lihat / Cetak Nota
                </a>
                <a href="<?= site_url('servis') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- ========== RINCIAN BARANG & BIAYA ========== -->
    <div class="col-lg-7">
        <div class="kartu p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-box-seam"></i> Rincian Suku Cadang</h6>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    Tidak ada suku cadang yang digunakan (servis tanpa barang).
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($detail as $i => $d): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= esc($d['nama_barang']) ?></td>
                                    <td class="text-end"><?= rupiah($d['harga_jual']) ?></td>
                                    <td class="text-center"><?= $d['jumlah'] ?></td>
                                    <td class="text-end"><?= rupiah($d['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-7">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-secondary">Total Biaya Barang</td>
                            <td class="text-end"><?= rupiah($totalBarang) ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Biaya Jasa</td>
                            <td class="text-end"><?= rupiah($servis['biaya_jasa']) ?></td>
                        </tr>
                        <tr class="table-dark">
                            <th>Total Bayar</th>
                            <th class="text-end"><?= rupiah($totalBayar) ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>