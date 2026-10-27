<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                KONTROL AKSES &amp; PENGGUNA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">AUTH-USER-MGMT</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Manajemen Akun Pengguna &amp; Hak Akses</h1>
        <p class="page-subtitle">Kelola akun Ketua Angkatan, Bendahara Keuangan, dan kredensial portal mandiri siswa</p>
    </div>
    <a href="<?= View::url('/users/create') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
        + Tambah Pengurus Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= View::url('/users') ?>" class="filter-bar" style="margin: 0; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Cari Pengguna</label>
            <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari Username atau Nama Lengkap..." class="form-control">
        </div>

        <div style="flex: 1.2; min-width: 180px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Role Hak Akses</label>
            <select name="role" class="form-control">
                <option value="all">Semua Role Pengguna</option>
                <option value="ketua" <?= $role === 'ketua' ? 'selected' : '' ?>>Ketua Angkatan</option>
                <option value="bendahara" <?= $role === 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
                <option value="siswa" <?= $role === 'siswa' ? 'selected' : '' ?>>Siswa (Portal Mandiri)</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= View::url('/users') ?>" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Pengguna -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.4);">
        <div style="font-weight: 600; font-size: 0.95rem; color: var(--gold-text);">
            Daftar Akun Terdaftar
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total: <?= count($users) ?> Akun
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">USERNAME</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NAMA LENGKAP</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">ROLE AKSES</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">EMAIL / KONTAK</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">STATUS</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">LOGIN TERAKHIR</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            Tidak ada akun pengguna yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--gold-solid); font-family: var(--font-mono); font-size: 0.9rem;"><?= View::e($u['username']) ?></strong>
                                <?php if (!empty($u['student_number'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono); margin-top: 2px;">
                                        NIS: <?= View::e($u['student_number']) ?> &bull; <?= View::e($u['class_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="color: #FFFFFF; font-weight: 500;"><?= View::e($u['full_name']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'ketua'): ?>
                                    <span class="badge" style="background: rgba(223, 178, 86, 0.2); color: var(--gold-solid); border: 1px solid var(--glass-border-gold); font-weight: 700; letter-spacing: 0.05em;">
                                        KETUA ANGKATAN
                                    </span>
                                <?php elseif ($u['role'] === 'bendahara'): ?>
                                    <span class="badge" style="background: rgba(147, 51, 234, 0.2); color: #C084FC; border: 1px solid rgba(192, 132, 252, 0.4); font-weight: 700; letter-spacing: 0.05em;">
                                        BENDAHARA
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-neutral" style="font-family: var(--font-mono); font-size: 0.75rem;">SISWA</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);"><?= View::e($u['email'] ?: '-') ?></td>
                            <td class="text-center">
                                <?= (int)$u['is_active'] === 1 ? View::statusBadge('aktif') : View::statusBadge('nonaktif') ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono); white-space: nowrap;">
                                <?= View::formatDate($u['last_login'], true) ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;" onclick="openModal('resetPasswordModal', {formAction: '<?= View::url('/users/' . $u['id'] . '/reset-password') ?>', targetName: '<?= View::e($u['full_name']) ?> (<?= View::e($u['username']) ?>)'})">
                                        Reset Sandi
                                    </button>

                                    <form method="POST" action="<?= View::url('/users/' . $u['id'] . '/toggle-status') ?>" style="display:inline;" onsubmit="return confirm('Ubah status keaktifan akun pengguna ini?');">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; color: <?= (int)$u['is_active'] === 1 ? 'var(--void-fg)' : 'var(--verified-fg)' ?>;">
                                            <?= (int)$u['is_active'] === 1 ? 'Nonaktif' : 'Aktif' ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal-overlay" id="resetPasswordModal">
    <div class="modal-box" style="border: 1px solid var(--glass-border-gold); background: var(--bg-navy);">
        <div class="modal-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 class="modal-title" style="font-family: var(--font-display); color: var(--gold-text); font-size: 1.1rem;">Reset Kata Sandi Pengguna</h3>
            <button type="button" class="modal-close" onclick="closeModal('resetPasswordModal')" style="color: var(--text-muted);">&times;</button>
        </div>
        <form method="POST" action="">
            <?= CSRF::field() ?>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                Setel ulang kata sandi baru untuk akun <strong class="modal-target-name" style="color: var(--gold-solid);"></strong>.
            </p>
            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru Sementara <span style="color:var(--void-fg)">*</span></label>
                <input type="text" name="new_password" id="new_password" class="form-control" required minlength="6" placeholder="Masukkan password baru" style="font-family: var(--font-mono);">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--glass-border);">
                <button type="button" class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>
