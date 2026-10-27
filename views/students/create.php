<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Siswa Baru</h1>
        <p class="page-subtitle">Pencatatan data siswa aktif ke dalam daftar angkatan</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Siswa</a>
</div>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/students/create') ?>">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="student_number">Nomor Siswa (NIS) <span style="color:red">*</span></label>
                    <input type="text" name="student_number" id="student_number" class="form-control" required placeholder="Contoh: 20261001" autofocus>
                    <div class="form-text">Nomor siswa harus unik dan akan digunakan sebagai username login portal.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="class_name">Kelas / Rombel <span style="color:red">*</span></label>
                    <input type="text" name="class_name" id="class_name" class="form-control" required placeholder="Contoh: XII RPL 1">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap Siswa <span style="color:red">*</span></label>
                <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Masukkan nama lengkap siswa">
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
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxxxx">
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

            <div class="alert alert-info">
                <div>
                    <strong>Informasi Kewajiban Iuran Otomatis:</strong><br>
                    Siswa yang ditambahkan otomatis memiliki kewajiban total <strong>Rp250.000</strong> (Album Fisik Rp200.000 + Graduation Rp50.000) dan 3 tahap jadwal cicilan resmi. Akun portal siswa juga akan dibuatkan secara otomatis.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="<?= View::url('/students') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
            </div>
        </form>
    </div>
</div>
