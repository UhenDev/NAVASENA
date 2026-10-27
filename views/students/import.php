<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Impor Data Siswa (Excel / CSV)</h1>
        <p class="page-subtitle">Unggah data siswa angkatan melalui file CSV dengan format standar UTF-8</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary">&larr; Kembali ke Data Siswa</a>
</div>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/students/import') ?>" enctype="multipart/form-data">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="csv_file">Pilih File CSV Siswa <span style="color:red">*</span></label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv, text/csv, text/plain" required style="padding: 0.8rem;">
                <div class="form-text">File wajib berupa format CSV (Comma Separated Values) dengan encoding UTF-8. Ukuran maksimal 10MB.</div>
            </div>

            <div style="background: var(--bg-subtle); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem;">
                <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--text-primary);">Opsi Pengolahan Data:</div>
                
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.875rem; cursor: pointer; margin-bottom: 0.5rem;">
                    <input type="checkbox" name="create_accounts" value="1" checked style="margin-top: 0.2rem;">
                    <span><strong>Buat Akun Portal Siswa Otomatis</strong> — Siswa dapat langsung login dengan NIS sebagai username dan kata sandi default <code>password</code>.</span>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="update_existing" value="1" style="margin-top: 0.2rem;">
                    <span><strong>Perbarui Data Siswa Jika NIS Sudah Ada</strong> — Jika tidak dicentang, siswa dengan NIS yang sudah ada di database akan dilewati secara otomatis untuk mencegah duplikasi.</span>
                </label>
            </div>

            <div class="card" style="border: 1px dashed var(--border-strong); background: #FAFBFD; margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">Panduan Kolom Header CSV:</h3>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Sistem secara fleksibel mengenali variasi nama kolom berikut:
                </p>
                <div class="table-responsive">
                    <table class="data-table" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>Informasi</th>
                                <th>Header yang Dikenali</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Nomor Siswa</strong></td>
                                <td><code>nis</code>, <code>no_siswa</code>, <code>nomor_siswa</code>, <code>nisn</code></td>
                                <td><span style="color:red; font-weight:600;">Wajib</span> (Harus unik)</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td><code>nama</code>, <code>nama_lengkap</code>, <code>nama_siswa</code>, <code>full_name</code></td>
                                <td><span style="color:red; font-weight:600;">Wajib</span></td>
                            </tr>
                            <tr>
                                <td><strong>Kelas</strong></td>
                                <td><code>kelas</code>, <code>rombel</code>, <code>class</code>, <code>class_name</code></td>
                                <td><span style="color:red; font-weight:600;">Wajib</span></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td><code>jk</code>, <code>jenis_kelamin</code>, <code>gender</code></td>
                                <td>Opsional (L / P)</td>
                            </tr>
                            <tr>
                                <td><strong>No. HP / WhatsApp</strong></td>
                                <td><code>telepon</code>, <code>no_hp</code>, <code>phone</code>, <code>whatsapp</code></td>
                                <td>Opsional</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td><code>email</code>, <code>surel</code></td>
                                <td>Opsional</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= View::url('/students') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Mulai proses impor data siswa?')">Mulai Proses Impor</button>
            </div>
        </form>
    </div>
</div>
