<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                TRANSAKSI KAS
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">FORM-CASH-INOUT</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Catat Transaksi Kas</h1>
        <p class="page-subtitle">Pencatatan pengeluaran buku kas (album/graduation/operasional) atau kas masuk non-iuran</p>
    </div>
    <a href="<?= View::url('/cash') ?>" class="btn btn-secondary">&larr; Kembali ke Buku Kas</a>
</div>

<div style="max-width: 680px; margin: 0 auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>
        
        <form method="POST" action="<?= View::url('/cash/create') ?>">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="transaction_type">Jenis Transaksi Kas <span style="color:var(--void-fg)">*</span></label>
                    <select name="transaction_type" id="transaction_type" class="form-control" required style="font-weight: 600;">
                        <option value="keluar" selected>Kas Keluar (Pengeluaran Angkatan)</option>
                        <option value="masuk">Kas Masuk (Pemasukan Non-Iuran Siswa)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategori Kas <span style="color:var(--void-fg)">*</span></label>
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
                    <label class="form-label" for="amount">Nominal Uang (Rp) <span style="color:var(--void-fg)">*</span></label>
                    <input type="text" name="amount" id="amount" class="form-control input-rupiah" required placeholder="Contoh: 500.000" style="font-family: var(--font-mono); font-size: 1.25rem; font-weight: 700; color: var(--gold-solid);">
                </div>

                <div class="form-group">
                    <label class="form-label" for="transaction_date">Tanggal Transaksi <span style="color:var(--void-fg)">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required style="font-family: var(--font-mono);">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Keterangan Lengkap &amp; Peruntukan <span style="color:var(--void-fg)">*</span></label>
                <textarea name="description" id="description" rows="3" class="form-control" required placeholder="Contoh: Pembayaran DP Vendor Percetakan Album Kenangan Fisik Angkatan"></textarea>
                <div class="form-text">Pastikan mencantumkan detail peruntukan, rekanan vendor, atau nomor nota fisik untuk audit 0-drift.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <a href="<?= View::url('/cash') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Transaksi Kas
                </button>
            </div>
        </form>
    </div>
</div>
