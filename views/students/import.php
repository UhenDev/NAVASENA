<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                BATCH IMPORT ENGINE
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">CSV-UTF8-AUTO</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Impor Data Siswa (Excel / CSV)</h1>
        <p class="page-subtitle">Unggah data angkatan via CSV UTF-8 dengan deteksi anti-duplikasi dan pembuatan akun portal otomatis</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary">&larr; Kembali ke Data Siswa</a>
</div>

<div style="max-width: 820px; margin: 0 auto;">
    <div class="card" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <form method="POST" action="<?= View::url('/students/import') ?>" enctype="multipart/form-data">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="csv_file">Pilih Berkas CSV Siswa <span style="color:var(--void-fg)">*</span></label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv, text/csv, text/plain" required style="padding: 1rem; border-style: dashed; border-color: var(--glass-border-gold); background: rgba(11, 16, 29, 0.6);">
                <div class="form-text">Berkas wajib berekstensi .csv dengan encoding UTF-8 (maksimal 10MB). Pemisah tanda koma (,) atau titik-koma (;).</div>
            </div>

            <div style="background: rgba(11, 16, 29, 0.6); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.75rem; color: var(--gold-text); text-transform: uppercase; letter-spacing: 0.05em;">Opsi Pengolahan Data Otomatis:</div>
                
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.875rem; cursor: pointer; margin-bottom: 0.75rem; color: #FFFFFF;">
                    <input type="checkbox" name="create_accounts" value="1" checked style="margin-top: 0.2rem; accent-color: var(--gold-solid);">
                    <span><strong>Buat Akun Portal Siswa Otomatis</strong> &mdash; Siswa dapat langsung login menggunakan NIS sebagai username dan kata sandi default <code>password</code>.</span>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.875rem; cursor: pointer; color: #FFFFFF;">
                    <input type="checkbox" name="update_existing" value="1" style="margin-top: 0.2rem; accent-color: var(--gold-solid);">
                    <span><strong>Perbarui Data Siswa Jika NIS Sudah Ada</strong> &mdash; Jika tidak dicentang, nomor NIS yang sudah ada akan dilewati guna mencegah duplikasi.</span>
                </label>
            </div>

            <!-- Panduan Kolom CSV -->
            <div class="card" style="border: 1px solid var(--glass-border); background: rgba(7, 10, 18, 0.6); margin-bottom: 1.75rem; padding: 1.25rem;">
                <h3 style="font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gold-text);">
                    Panduan Nama Kolom Header CSV
                </h3>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    Parser cerdas mengenali otomatis variasi penamaan kolom berikut:
                </p>
                <div class="table-responsive">
                    <table class="data-table" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th style="color: var(--gold-text);">INFORMASI</th>
                                <th style="color: var(--gold-text);">HEADER YANG DIKENALI</th>
                                <th style="color: var(--gold-text);">SIFAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Nomor Induk Siswa</strong></td>
                                <td><code style="color: var(--gold-solid);">nis</code>, <code>no_siswa</code>, <code>nomor_siswa</code>, <code>nisn</code></td>
                                <td><span style="color: var(--void-fg); font-weight: 600;">Wajib Unik</span></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td><code style="color: var(--gold-solid);">nama</code>, <code>nama_lengkap</code>, <code>nama_siswa</code>, <code>full_name</code></td>
                                <td><span style="color: var(--void-fg); font-weight: 600;">Wajib</span></td>
                            </tr>
                            <tr>
                                <td><strong>Kelas / Rombel</strong></td>
                                <td><code style="color: var(--gold-solid);">kelas</code>, <code>rombel</code>, <code>class</code>, <code>class_name</code></td>
                                <td><span style="color: var(--void-fg); font-weight: 600;">Wajib</span></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td><code style="color: var(--gold-solid);">jk</code>, <code>jenis_kelamin</code>, <code>gender</code></td>
                                <td>Opsional (L / P)</td>
                            </tr>
                            <tr>
                                <td><strong>No. HP / WhatsApp</strong></td>
                                <td><code style="color: var(--gold-solid);">telepon</code>, <code>no_hp</code>, <code>phone</code>, <code>whatsapp</code></td>
                                <td>Opsional</td>
                            </tr>
                            <tr>
                                <td><strong>Email Siswa</strong></td>
                                <td><code style="color: var(--gold-solid);">email</code>, <code>surel</code></td>
                                <td>Opsional</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border);">
                <a href="<?= View::url('/students') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Mulai proses impor data siswa ke sistem?')" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                    Mulai Proses Impor
                </button>
            </div>
        </form>
    </div>
</div>
