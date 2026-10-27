<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan Sistem &amp; Jadwal Cicilan</h1>
        <p class="page-subtitle">Konfigurasi jadwal jatuh tempo cicilan, kewenangan verifikasi, dan pengumuman resmi</p>
    </div>
</div>

<div style="max-width: 860px; margin: 0 auto;">
    <form method="POST" action="<?= View::url('/settings') ?>">
        <?= CSRF::field() ?>

        <!-- Jadwal dan Nominal Tahap Cicilan -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Jadwal &amp; Nominal Tahap Cicilan (Total Rp250.000 / Siswa)</h2>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                        Tanggal jatuh tempo tahap kedua dan ketiga dapat diatur sesuai kepastian tanggal resmi dari sekolah.
                    </p>
                </div>
            </div>

            <?php foreach ($stages as $stg): ?>
                <div style="background: var(--bg-subtle); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-weight: 700; color: var(--primary); margin-bottom: 0.5rem; font-size: 0.95rem;">
                        Tahap <?= (int)$stg['stage_number'] ?> &mdash; <?= View::e($stg['stage_name']) ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label class="form-label" style="font-size: 0.8rem;">Nama Tahap</label>
                            <input type="text" name="stages[<?= $stg['id'] ?>][stage_name]" class="form-control" value="<?= View::e($stg['stage_name']) ?>" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label class="form-label" style="font-size: 0.8rem;">Target Nominal per Siswa (Rp)</label>
                            <input type="text" name="stages[<?= $stg['id'] ?>][target_amount]" class="form-control input-rupiah" value="<?= number_format((float)$stg['target_amount'], 0, ',', '.') ?>" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label class="form-label" style="font-size: 0.8rem;">Tanggal Jatuh Tempo Resmi</label>
                            <input type="date" name="stages[<?= $stg['id'] ?>][due_date]" class="form-control" value="<?= View::e($stg['due_date']) ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0; margin-top: 0.5rem;">
                        <label class="form-label" style="font-size: 0.8rem;">Keterangan / Instruksi Tahap</label>
                        <input type="text" name="stages[<?= $stg['id'] ?>][description]" class="form-control" value="<?= View::e($stg['description']) ?>" placeholder="Deskripsi tahapan">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Kewenangan & Kebijakan Keuangan -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Kewenangan &amp; Kebijakan Sistem</h2>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="treasurer_can_verify" value="1" <?= ($settings['treasurer_can_verify'] ?? '1') === '1' ? 'checked' : '' ?> style="margin-top: 0.2rem;">
                    <div>
                        <strong>Izinkan Bendahara Melakukan Verifikasi Pembayaran</strong>
                        <div class="form-text">Jika dinonaktifkan, Bendahara hanya berhak mencatat transaksi, sedangkan persetujuan/verifikasi sah menjadi wewenang eksklusif Ketua Angkatan.</div>
                    </div>
                </label>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="allow_overpayment" value="1" <?= ($settings['allow_overpayment'] ?? '0') === '1' ? 'checked' : '' ?> style="margin-top: 0.2rem;">
                    <div>
                        <strong>Izinkan Pembayaran Melebihi Sisa Tagihan (Overpayment)</strong>
                        <div class="form-text">Jika dinonaktifkan (direkomendasikan), sistem akan menolak pencatatan jika nominal pembayaran melebihi sisa tagihan siswa.</div>
                    </div>
                </label>
            </div>

            <div class="form-group" style="margin-top: 1.25rem;">
                <label class="form-label" for="announcement">Teks Pengumuman Resmi untuk Portal Siswa</label>
                <textarea name="announcement" id="announcement" rows="3" class="form-control" placeholder="Pengumuman ini akan langsung tampil di halaman depan portal seluruh siswa"><?= View::e($settings['announcement'] ?? '') ?></textarea>
                <div class="form-text">Gunakan untuk menyampaikan nomor rekening transfer resmi, batas waktu pembayaran, atau instruksi pengambilan album.</div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 2rem;">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Simpan perubahan pengaturan aplikasi?')">
                Simpan Seluruh Pengaturan
            </button>
        </div>
    </form>
</div>
