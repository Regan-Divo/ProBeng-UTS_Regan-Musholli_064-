<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Edit Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kartu p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-pencil-square"></i> Form Edit Pelanggan</h5>
            <p class="text-secondary small mb-4">ID Pelanggan: <?= $pelanggan['id_pelanggan'] ?></p>

            <form action="<?= site_url('pelanggan/edit/' . $pelanggan['id_pelanggan']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_pelanggan" class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                           value="<?= old('nama_pelanggan', $pelanggan['nama_pelanggan']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="no_telepon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                               value="<?= old('no_telepon', $pelanggan['no_telepon']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="plat_nomor" class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="plat_nomor" name="plat_nomor"
                               value="<?= old('plat_nomor', $pelanggan['plat_nomor']) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                        <?php
                        $kendaraan = ['Motor', 'Mobil', 'Truck', 'Bus', 'Lainnya'];
                        $sekarang = old('jenis_kendaraan', $pelanggan['jenis_kendaraan']);
                        foreach ($kendaraan as $k):
                        ?>
                            <option value="<?= $k ?>" <?= $sekarang === $k ? 'selected' : '' ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update</button>
                    <a href="<?= site_url('pelanggan') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>