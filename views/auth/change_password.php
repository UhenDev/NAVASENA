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

        <form method="POST" action="<?= View::url('/change-password') ?>" onsubmit="return confirm('Apakah Anda yakin ingin memperbarui kata sandi akun Anda?');">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="current_password">Kata Sandi Saat Ini <span style="color:var(--void-fg)">*</span></label>
                <div class="password-field-container">
                    <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Masukkan kata sandi lama Anda" style="font-family: var(--font-mono);">
                    <button type="button" class="password-toggle-btn" aria-label="Lihat kata sandi" title="Lihat kata sandi">
                        <svg class="eye-icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru <span style="color:var(--void-fg)">*</span></label>
                <div class="password-field-container">
                    <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required placeholder="Minimal 6 karakter" style="font-family: var(--font-mono);">
                    <button type="button" class="password-toggle-btn" aria-label="Lihat kata sandi" title="Lihat kata sandi">
                        <svg class="eye-icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
                <div class="form-text">Gunakan perpaduan huruf, angka, dan karakter simbol untuk keamanan optimal.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Konfirmasi Kata Sandi Baru <span style="color:var(--void-fg)">*</span></label>
                <div class="password-field-container">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required placeholder="Ulangi kata sandi baru" style="font-family: var(--font-mono);">
                    <button type="button" class="password-toggle-btn" aria-label="Lihat kata sandi" title="Lihat kata sandi">
                        <svg class="eye-icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Kata Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>
