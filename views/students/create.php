<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                REGISTRASI DATA SISWA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">FORM-STUDENT-NEW</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Tambah Siswa Baru</h1>
        <p class="page-subtitle">Pencatatan data siswa aktif ke dalam daftar angkatan Guardian of Navasena 27</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Siswa</a>
</div>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <form method="POST" action="<?= View::url('/students/create') ?>" onsubmit="return confirm('Apakah Anda yakin data siswa baru yang dimasukkan sudah benar?');">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="student_number">Nomor Induk Siswa (NIS) <span style="color:var(--void-fg)">*</span></label>
                    <input type="text" name="student_number" id="student_number" class="form-control" required placeholder="Contoh: 20261001" autofocus style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid);">
                    <div class="form-text">Nomor siswa harus unik dan otomatis menjadi akun login portal siswa.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="class_name">Kelas / Rombel <span style="color:var(--void-fg)">*</span></label>
                    <input type="text" name="class_name" id="class_name" class="form-control" required placeholder="Contoh: XII RPL 1">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap Siswa <span style="color:var(--void-fg)">*</span></label>
                <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Masukkan nama lengkap siswa sesuai data sekolah">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="gender">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">No. WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxxxx" style="font-family: var(--font-mono);">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Siswa (Opsional)</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="siswa@sekolah.sch.id">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Keterangan khusus jika ada"></textarea>
            </div>

            <div style="background: rgba(223, 178, 86, 0.08); border: 1px solid var(--glass-border-gold); border-radius: var(--radius-sm); padding: 1rem; margin-top: 1.25rem;">
                <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                    <div style="color: var(--gold-solid); font-size: 1.25rem; line-height: 1;">&#9432;</div>
                    <div style="font-size: 0.85rem; color: var(--gold-text); line-height: 1.5;">
                        <strong>Pemberitahuan Otomatisasi Sistem:</strong><br>
                        Siswa otomatis memiliki kewajiban total <strong>Rp250.000</strong> (Album Fisik Rp200.000 + Acara Graduation Rp50.000), 3 tahap jadwal cicilan, dan akun portal mandiri aktif.
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <a href="<?= View::url('/students') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>
