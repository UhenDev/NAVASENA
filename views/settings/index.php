<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Manajemen Konfigurasi Sistem &bull; Ketua Angkatan
        </div>
        <h1 class="page-title">
            Pengaturan Jadwal <span class="gold-highlight">Cicilan &amp; Kebijakan</span>
        </h1>
        <p class="page-subtitle">Konfigurasi jatuh tempo 3 tahap, wewenang verifikasi bendahara, dan pengumuman resmi</p>
    </div>
</div>

<div style="max-width: 880px; margin: 0 auto;">
    <form method="POST" action="<?= View::url('/settings') ?>">
        <?= CSRF::field() ?>

        <!-- 3 Stacked Glass Configuration Cards -->
        <div class="card card-gold">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; background: rgba(223, 178, 86, 0.12); border: 1px solid var(--glass-border-gold);">
                        <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Logo" style="width: 20px; height: 20px; object-fit: contain;">
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0; font-family: var(--font-display); font-size: 1.1rem; color: #FFFFFF;">
                            Pengaturan Iuran &amp; Jadwal 3 Tahap
                        </h2>
                        <p style="font-size: 0.775rem; color: var(--text-muted); margin-top: 2px;">
                            Kewajiban Rp250.000 (Album Rp200rb + Graduation Rp50rb)
                        </p>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <?php foreach ($stages as $stg): ?>
                    <?php 
                        $isTahap1 = ((int)$stg['stage_number'] === 1);
                    ?>
                    <div class="stage-card" style="border-color: var(--glass-border-gold); background: var(--glass-inner);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.65rem;">
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="stage-badge-number"><?= (int)$stg['stage_number'] ?></div>
                                <div>
                                    <div style="font-weight: 800; color: #FFF; font-size: 1.05rem;">
                                        Tahap <?= (int)$stg['stage_number'] ?> &mdash; <?= View::e($stg['stage_name']) ?>
                                    </div>
                                    <div style="font-size: 0.725rem; color: var(--text-muted); font-family: var(--font-mono);">
                                        Jatuh Tempo: <?= View::formatDate($stg['due_date']) ?>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="font-family: var(--font-mono); font-size: 1.15rem; font-weight: 800; color: var(--gold-solid);">
                                    <?= View::formatRupiah($stg['target_amount']) ?>
                                </div>
                                <?php if ($isTahap1): ?>
                                    <span class="status-pill status-verified" style="font-size:0.7rem;">AKTIF / TETAP</span>
                                <?php else: ?>
                                    <span class="status-pill status-pending" style="font-size:0.7rem;">FLEKSIBEL</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <label class="form-label">Nama Tahap Cicilan</label>
                                <input type="text" name="stages[<?= $stg['id'] ?>][stage_name]" class="form-control" value="<?= View::e($stg['stage_name']) ?>" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <label class="form-label">Target Nominal per Siswa (Rp)</label>
                                <input type="text" name="stages[<?= $stg['id'] ?>][target_amount]" class="form-control input-rupiah tabular-nums" value="<?= number_format((float)$stg['target_amount'], 0, ',', '.') ?>" required style="font-weight:800; color:var(--gold-solid);">
                            </div>

                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <label class="form-label">Tanggal Jatuh Tempo Resmi</label>
                                <input type="date" name="stages[<?= $stg['id'] ?>][due_date]" class="form-control tabular-nums" value="<?= View::e($stg['due_date']) ?>">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0; margin-top: 0.65rem;">
                            <label class="form-label" style="font-size: 0.775rem;">Keterangan &amp; Instruksi Tahapan</label>
                            <input type="text" name="stages[<?= $stg['id'] ?>][description]" class="form-control" value="<?= View::e($stg['description']) ?>" placeholder="Deskripsi tahapan untuk portal siswa">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kewenangan & Kebijakan Sistem -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Kewenangan &amp; Kebijakan Audit Angkatan</h2>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="treasurer_can_verify" value="1" <?= ($settings['treasurer_can_verify'] ?? '1') === '1' ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                    <div>
                        <strong style="color:#FFF;">Izinkan Bendahara Melakukan Verifikasi Pembayaran Langsung</strong>
                        <div class="form-text">Jika dinonaktifkan, Bendahara hanya berhak mencatat draf transaksi, sedangkan hak verifikasi sah menjadi wewenang eksklusif Ketua Angkatan.</div>
                    </div>
                </label>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="allow_overpayment" value="1" <?= ($settings['allow_overpayment'] ?? '0') === '1' ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                    <div>
                        <strong style="color:#FFF;">Izinkan Pembayaran Melebihi Sisa Tagihan (Overpayment)</strong>
                        <div class="form-text">Jika dinonaktifkan (standar keamanan keuangan), sistem secara ketat memblokir nominal yang melebihi sisa tagihan siswa.</div>
                    </div>
                </label>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label" for="announcement">Teks Pengumuman Resmi untuk Portal Siswa</label>
                <textarea name="announcement" id="announcement" rows="3" class="form-control" placeholder="Pengumuman resmi ini akan langsung tampil di halaman depan portal seluruh siswa"><?= View::e($settings['announcement'] ?? '') ?></textarea>
                <div class="form-text">Informasi rekening bank resmi, jadwal pengambilan album fisik, atau petunjuk pembayaran.</div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Simpan konfigurasi pengaturan cicilan angkatan?')">
                SIMPAN KONFIGURASI ANGKATAN
            </button>
        </div>
    </form>
</div>
