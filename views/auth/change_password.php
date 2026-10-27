<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                KEAMANAN AKUN
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">SEC-PWD-UPDATE</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Perbarui Kata Sandi</h1>
        <p class="page-subtitle">Pastikan akun Anda terlindungi dengan kombinasi kata sandi yang kuat</p>
    </div>
</div>

<div style="max-width: 520px; margin: 1rem auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <form method="POST" action="<?= View::url('/change-password') ?>">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="current_password">Kata Sandi Saat Ini <span style="color:var(--void-fg)">*</span></label>
                <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Masukkan kata sandi lama Anda" style="font-family: var(--font-mono);">
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru <span style="color:var(--void-fg)">*</span></label>
                <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required placeholder="Minimal 6 karakter" style="font-family: var(--font-mono);">
                <div class="form-text">Gunakan perpaduan huruf, angka, dan karakter simbol untuk keamanan optimal.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Konfirmasi Kata Sandi Baru <span style="color:var(--void-fg)">*</span></label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required placeholder="Ulangi kata sandi baru" style="font-family: var(--font-mono);">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Kata Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>
