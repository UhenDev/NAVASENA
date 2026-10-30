<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                EDIT PROFIL SISWA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">NIS: <?= View::e($student['student_number']) ?></span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Edit Data Siswa</h1>
        <p class="page-subtitle">Perbarui data profil siswa: <strong style="color: #FFFFFF;"><?= View::e($student['full_name']) ?></strong></p>
    </div>
    <a href="<?= View::url('/students/' . $student['id']) ?>" class="btn btn-secondary">&larr; Kembali ke Detail Siswa</a>
</div>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <form method="POST" action="<?= View::url('/students/' . $student['id'] . '/edit') ?>" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan data siswa ini?');">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="student_number">Nomor Induk Siswa (NIS) <span style="color:var(--void-fg)">*</span></label>
                    <input type="text" name="student_number" id="student_number" class="form-control" required value="<?= View::e($student['student_number']) ?>" style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid);">
                </div>

                <div class="form-group">
                    <label class="form-label" for="class_name">Kelas / Rombel <span style="color:var(--void-fg)">*</span></label>
                    <input type="text" name="class_name" id="class_name" class="form-control" required value="<?= View::e($student['class_name']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap Siswa <span style="color:var(--void-fg)">*</span></label>
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
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= View::e($student['phone']) ?>" style="font-family: var(--font-mono);">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status Keaktifan</label>
                    <select name="status" id="status" class="form-control">
                        <option value="aktif" <?= $student['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $student['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Catatan Siswa</label>
                <textarea name="notes" id="notes" rows="2" class="form-control"><?= View::e($student['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <a href="<?= View::url('/students/' . $student['id']) ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
