<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Tambah Suku Cadang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kartu p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-box-seam"></i> Form Tambah Suku Cadang</h5>

            <form action="<?= site_url('sparepart/tambah') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                           value="<?= old('nama_barang') ?>" placeholder="Contoh: Oli Synthetic 10W-30 (1L)" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0"
                               value="<?= old('harga_beli', 0) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" min="0"
                               value="<?= old('harga_jual', 0) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0"
                               value="<?= old('stok', 0) ?>" required>
                    </div>
                </div>

                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle"></i> Harga jual dihitung dari harga beli.本格olan sekitar 10&ndash;20%.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                    <a href="<?= site_url('sparepart') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>