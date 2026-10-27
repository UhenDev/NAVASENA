<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Data Siswa</h1>
        <p class="page-subtitle">Perbarui data profil siswa: <?= View::e($student['full_name']) ?></p>
    </div>
    <a href="<?= View::url('/students/' . $student['id']) ?>" class="btn btn-secondary">&larr; Kembali ke Detail Siswa</a>
</div>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/students/' . $student['id'] . '/edit') ?>">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="student_number">Nomor Siswa (NIS) <span style="color:red">*</span></label>
                    <input type="text" name="student_number" id="student_number" class="form-control" required value="<?= View::e($student['student_number']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="class_name">Kelas / Rombel <span style="color:red">*</span></label>
                    <input type="text" name="class_name" id="class_name" class="form-control" required value="<?= View::e($student['class_name']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap Siswa <span style="color:red">*</span></label>
                <input type="text" name="full_name" id="full_name" class="form-control" required value="<?= View::e($student['full_name']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="gender">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= $student['gender'] === 'L' ? 'selected' : '' ?>>Laki-laki (L)</option>
                        <option value="P" <?= $student['gender'] === 'P' ? 'selected' : '' ?>>Perempuan (P)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">No. WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= View::e($student['phone']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status Siswa</label>
                    <select name="status" id="status" class="form-control">
                        <option value="aktif" <?= $student['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $student['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Catatan</label>
                <textarea name="notes" id="notes" rows="2" class="form-control"><?= View::e($student['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="<?= View::url('/students/' . $student['id']) ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
