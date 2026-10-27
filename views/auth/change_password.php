<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div style="max-width: 520px; margin: 1rem auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Perbarui Kata Sandi Akun</h2>
        </div>

        <form method="POST" action="<?= View::url('/change-password') ?>">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="current_password">Kata Sandi Saat Ini <span style="color:red">*</span></label>
                <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Masukkan kata sandi lama">
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru <span style="color:red">*</span></label>
                <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required placeholder="Minimal 6 karakter">
                <div class="form-text">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Konfirmasi Kata Sandi Baru <span style="color:red">*</span></label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required placeholder="Ulangi kata sandi baru">
            </div>

            <div style="display:flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Simpan Kata Sandi Baru</button>
            </div>
        </form>
    </div>
</div>
