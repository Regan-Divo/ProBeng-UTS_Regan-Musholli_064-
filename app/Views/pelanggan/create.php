<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Tambah Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kartu p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-person-plus"></i> Form Tambah Pelanggan</h5>

            <form action="<?= site_url('pelanggan/tambah') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_pelanggan" class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                           value="<?= old('nama_pelanggan') ?>" placeholder="Contoh: Ahmad Fauzi" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="no_telepon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                               value="<?= old('no_telepon') ?>" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="plat_nomor" class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="plat_nomor" name="plat_nomor"
                               value="<?= old('plat_nomor') ?>" placeholder="B 1234 ABC" required>
                        <div class="form-text">Plat nomor harus unik.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                        <option value="">-- Pilih Jenis Kendaraan --</option>
                        <?php
                        $kendaraan = ['Motor', 'Mobil', 'Truck', 'Bus', 'Lainnya'];
                        foreach ($kendaraan as $k):
                        ?>
                            <option value="<?= $k ?>" <?= old('jenis_kendaraan') === $k ? 'selected' : '' ?>>
                                <?= $k ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                    <a href="<?= site_url('pelanggan') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>