<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                REGISTRASI PENGURUS
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">AUTH-USER-NEW</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Tambah Pengurus Baru</h1>
        <p class="page-subtitle">Pemberian akses akun pengelola sistem keuangan angkatan Navasena 27</p>
    </div>
    <a href="<?= View::url('/users') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Pengguna</a>
</div>

<div style="max-width: 640px; margin: 0 auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <form method="POST" action="<?= View::url('/users/create') ?>" onsubmit="return confirm('Apakah Anda yakin ingin membuat akun pengguna pengurus baru ini?');">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username Pengguna <span style="color:var(--void-fg)">*</span></label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Contoh: bendahara2" autofocus style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid);">
                <div class="form-text">Gunakan huruf kecil atau angka tanpa spasi (wajib unik).</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap Pengurus <span style="color:var(--void-fg)">*</span></label>
                <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Nama lengkap pengurus angkatan">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Peran <span style="color:var(--void-fg)">*</span></label>
                    <select name="role" id="role" class="form-control" required style="font-weight: 600;">
                        <option value="bendahara" selected>Bendahara Angkatan</option>
                        <option value="ketua">Ketua Angkatan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="pengurus@navasena.internal">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi Awal <span style="color:var(--void-fg)">*</span></label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter" style="font-family: var(--font-mono);">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <a href="<?= View::url('/users') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Akun Pengurus
                </button>
            </div>
        </form>
    </div>
</div>
