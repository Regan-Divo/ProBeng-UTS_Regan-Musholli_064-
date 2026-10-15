<?= $this->extend('template') ?>

<?= $this->section('title') ?>
Tambah Transaksi Servis
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<form action="<?= site_url('servis/tambah') ?>" method="post" id="formServis">
    <?= csrf_field() ?>

    <div class="row g-3">

        <!-- ================= DATA SERVIS ================= -->
        <div class="col-lg-5">
            <div class="kartu p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-wrench-adjustable"></i> Data Servis</h5>

                <div class="mb-3">
                    <label for="id_pelanggan" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($pelanggan as $p): ?>
                            <option value="<?= $p['id_pelanggan'] ?>"
                                <?= old('id_pelanggan') == $p['id_pelanggan'] ? 'selected' : '' ?>>
                                <?= esc($p['nama_pelanggan']) ?> (<?= esc($p['plat_nomor']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($pelanggan)): ?>
                        <div class="form-text text-danger">
                            Belum ada pelanggan. <a href="<?= site_url('pelanggan/tambah') ?>">Tambah pelanggan</a> dulu.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="keluhan" class="form-label">Keluhan pelanggan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="keluhan" name="keluhan" rows="3"
                              placeholder="Contoh: Motor sering brebet dan suara rem keras" required><?= old('keluhan') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="biaya_jasa" class="form-label">Biaya Jasa (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="biaya_jasa" name="biaya_jasa" min="0"
                           value="<?= old('biaya_jasa', 50000) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_servis" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_servis" name="status_servis" required>
                            <?php foreach ($statusList as $s): ?>
                                <option value="<?= $s ?>" <?= old('status_servis', 'Antre') === $s ? 'selected' : '' ?>>
                                    <?= $s ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                               value="<?= old('tanggal_masuk', date('Y-m-d\TH:i')) ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= RINCIAN SUKU CADANG ================= -->
        <div class="col-lg-7">
            <div class="kartu p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Rincian Suku Cadang</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahBaris">
                        <i class="bi bi-plus-lg"></i> Tambah Baris
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="tabelBarang">
                        <thead class="table-light">
                            <tr>
                                <th>Suku Cadang</th>
                                <th style="width:110px">Harga Jual</th>
                                <th style="width:90px">Jumlah</th>
                                <th style="width:130px" class="text-end">Subtotal</th>
                                <th style="width:45px"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBarang">
                            <!-- Baris template -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Barang</th>
                                <th class="text-end" id="totalBarang">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-secondary small">
                    <i class="bi bi-info-circle"></i> Harga &amp; subtotal dihitung otomatis dari master suku cadang.
                    Stok barang berkurang otomatis saat transaksi disimpan.
                </p>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Biaya Jasa</span>
                    <strong><?= rupiah(old('biaya_jasa', 50000)) ?></strong>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Transaksi</button>
                    <a href="<?= site_url('servis') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template baris barang -->
<script type="text/template" id="templateBaris">
    <tr>
        <td>
            <select class="form-select selectBarang" name="barang[__i__][id_sparepart]">
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($sparepart as $b): ?>
                    <option value="<?= $b['id_sparepart'] ?>" data-harga="<?= $b['harga_jual'] ?>" data-stok="<?= $b['stok'] ?>">
                        <?= esc($b['nama_barang']) ?> (stok: <?= $b['stok'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="text-end hargaJual">Rp 0</td>
        <td><input type="number" class="form-control inputJumlah" name="barang[__i__][jumlah]" min="1" value="1"></td>
        <td class="text-end subtotal">Rp 0</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btnHapusBaris"><i class="bi bi-x-lg"></i></button>
        </td>
    </tr>
</script>

<script>
(function () {
    const tbody   = document.getElementById('tbodyBarang');
    const template= document.getElementById('templateBaris').innerHTML;
    const totalEl = document.getElementById('totalBarang');
    let index = 0;

    // Daftar harga & stok (diambil dari data master)
    const master = {};
    document.querySelectorAll('#templateBaris option[data-harga]').forEach(function (o) {
        master[o.value] = { harga: parseInt(o.dataset.harga), stok: parseInt(o.dataset.stok), nama: o.textContent.trim() };
    });

    function rupiah(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

    function tambahBaris() {
        const tr = document.createElement('tbody');
        tr.innerHTML = template.replace(/__i__/g, index);
        const row = tr.firstElementChild;
        tbody.appendChild(row);
        index++;
        hitungTotal();
        return row;
    }

    function hitungTotal() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            const select = row.querySelector('.selectBarang');
            const jumlah = parseInt(row.querySelector('.inputJumlah').value || 0);
            const data   = master[select.value] || { harga: 0 };
            const sub    = data.harga * jumlah;

            row.querySelector('.hargaJual').textContent = rupiah(data.harga);
            row.querySelector('.subtotal').textContent   = rupiah(sub);
            total += sub;
        });
        totalEl.textContent = rupiah(total);
    }

    document.getElementById('btnTambahBaris').addEventListener('click', function () {
        tambahBaris().querySelector('.selectBarang').focus();
    });

    // Delegasi event untuk baris yang ditambahkan dinamis
    tbody.addEventListener('change', hitungTotal);
    tbody.addEventListener('input', hitungTotal);
    tbody.addEventListener('click', function (e) {
        if (e.target.closest('.btnHapusBaris')) {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                tbody.innerHTML = '';
                tambahBaris();
            }
            hitungTotal();
        }
    });

    // Baris pertama langsung tersedia
    tambahBaris();
})();
</script>

<?= $this->endSection() ?>