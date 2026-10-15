<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Edit Suku Cadang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kartu p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-pencil-square"></i> Form Edit Suku Cadang</h5>
            <p class="text-secondary small mb-4">ID Suku Cadang: <?= $sparepart['id_sparepart'] ?></p>

            <form action="<?= site_url('sparepart/edit/' . $sparepart['id_sparepart']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                           value="<?= old('nama_barang', $sparepart['nama_barang']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0"
                               value="<?= old('harga_beli', $sparepart['harga_beli']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" min="0"
                               value="<?= old('harga_jual', $sparepart['harga_jual']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0"
                               value="<?= old('stok', $sparepart['stok']) ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update</button>
                    <a href="<?= site_url('sparepart') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>