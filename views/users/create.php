<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Pengurus Baru</h1>
        <p class="page-subtitle">Pemberian akses akun pengelola sistem keuangan angkatan</p>
    </div>
    <a href="<?= View::url('/users') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Pengguna</a>
</div>

<div style="max-width: 640px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/users/create') ?>">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username Pengguna <span style="color:red">*</span></label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Contoh: bendahara2" autofocus>
                <div class="form-text">Gunakan huruf kecil atau angka tanpa spasi.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Nama lengkap pengurus">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Role <span style="color:red">*</span></label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="bendahara" selected>Bendahara Angkatan</option>
                        <option value="ketua">Ketua Angkatan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Pengurus</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="pengurus@navasena.internal">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi Awal <span style="color:red">*</span></label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="<?= View::url('/users') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Akun Pengurus</button>
            </div>
        </form>
    </div>
</div>
