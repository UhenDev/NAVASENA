<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Catat Transaksi Kas</h1>
        <p class="page-subtitle">Pencatatan pengeluaran operasional/kegiatan atau kas masuk selain iuran siswa</p>
    </div>
    <a href="<?= View::url('/cash') ?>" class="btn btn-secondary">&larr; Kembali ke Buku Kas</a>
</div>

<div style="max-width: 680px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/cash/create') ?>">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="transaction_type">Jenis Transaksi <span style="color:red">*</span></label>
                    <select name="transaction_type" id="transaction_type" class="form-control" required>
                        <option value="keluar" selected>Kas Keluar (Pengeluaran)</option>
                        <option value="masuk">Kas Masuk (Pemasukan Lain)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategori Kas <span style="color:red">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= View::e($cat) ?>"><?= View::e($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="amount">Nominal Uang (Rp) <span style="color:red">*</span></label>
                    <input type="text" name="amount" id="amount" class="form-control input-rupiah" required placeholder="Contoh: 500.000" style="font-size:1.15rem; font-weight:700;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="transaction_date">Tanggal Transaksi <span style="color:red">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Keterangan Lengkap &amp; Peruntukan <span style="color:red">*</span></label>
                <textarea name="description" id="description" rows="3" class="form-control" required placeholder="Contoh: Pembayaran DP Vendor Percetakan Album Kenangan Fisik Angkatan"></textarea>
                <div class="form-text">Pastikan mencantumkan detail peruntukan atau nomor nota/bukti fisik untuk kemudahan audit.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="<?= View::url('/cash') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Transaksi Kas</button>
            </div>
        </form>
    </div>
</div>
