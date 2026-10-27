<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Akun Pengguna &amp; Hak Akses</h1>
        <p class="page-subtitle">Kelola akun Ketua Angkatan, Bendahara, dan akun akses siswa</p>
    </div>
    <a href="<?= View::url('/users/create') ?>" class="btn btn-primary">+ Tambah Pengurus Baru</a>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/users') ?>" class="filter-bar">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari Username atau Nama Lengkap..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="role" class="form-control">
            <option value="all">Semua Role Pengguna</option>
            <option value="ketua" <?= $role === 'ketua' ? 'selected' : '' ?>>Ketua Angkatan</option>
            <option value="bendahara" <?= $role === 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
            <option value="siswa" <?= $role === 'siswa' ? 'selected' : '' ?>>Siswa</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="<?= View::url('/users') ?>" class="btn btn-secondary">Reset</a>
</form>

<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Email / Kontak</th>
                    <th class="text-center">Status</th>
                    <th>Login Terakhir</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                            Tidak ada akun pengguna yang ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <strong><?= View::e($u['username']) ?></strong>
                                <?php if (!empty($u['student_number'])): ?>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">NIS: <?= View::e($u['student_number']) ?> &bull; <?= View::e($u['class_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= View::e($u['full_name']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'ketua'): ?>
                                    <span class="badge" style="background:#DBEAFE; color:#1E40AF; border:1px solid #93C5FD; font-weight:700;">KETUA</span>
                                <?php elseif ($u['role'] === 'bendahara'): ?>
                                    <span class="badge" style="background:#EDE9FE; color:#5B21B6; border:1px solid #C4B5FD; font-weight:700;">BENDAHARA</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">SISWA</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);"><?= View::e($u['email'] ?: '-') ?></td>
                            <td class="text-center">
                                <?= (int)$u['is_active'] === 1 ? View::statusBadge('aktif') : View::statusBadge('nonaktif') ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-muted);"><?= View::formatDate($u['last_login'], true) ?></td>
                            <td class="text-center" style="white-space: nowrap;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openModal('resetPasswordModal', {formAction: '<?= View::url('/users/' . $u['id'] . '/reset-password') ?>', targetName: '<?= View::e($u['full_name']) ?> (<?= View::e($u['username']) ?>)'})">
                                    Reset Password
                                </button>

                                <form method="POST" action="<?= View::url('/users/' . $u['id'] . '/toggle-status') ?>" style="display:inline;" onsubmit="return confirm('Ubah status keaktifan akun pengguna ini?');">
                                    <?= CSRF::field() ?>
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: <?= (int)$u['is_active'] === 1 ? 'var(--danger-text)' : 'var(--success-text)' ?>;">
                                        <?= (int)$u['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
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
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Reset Kata Sandi Pengguna</h3>
            <button type="button" class="modal-close" onclick="closeModal('resetPasswordModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <?= CSRF::field() ?>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                Setel ulang kata sandi untuk <strong class="modal-target-name"></strong>. Pengguna akan diwajibkan mengganti kata sandi ini saat login berikutnya.
            </p>
            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru Sementara <span style="color:red">*</span></label>
                <input type="text" name="new_password" id="new_password" class="form-control" required minlength="6" placeholder="Masukkan password baru">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>
