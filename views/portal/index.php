<?php
use App\Helpers\View;
?>

<!-- Pengumuman Resmi Pengurus Angkatan -->
<?php if (!empty($announcement)): ?>
    <div class="alert alert-info" style="margin-bottom: 1.5rem; background:#EFF6FF; border-color:#BFDBFE;">
        <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor" style="color:#2563EB;">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <div style="font-weight: 700; color: #1E40AF; margin-bottom: 0.15rem;">Pengumuman Resmi Pengurus:</div>
            <div style="color: #1E3A8A;"><?= nl2br(View::e($announcement)) ?></div>
        </div>
    </div>
<?php endif; ?>

<!-- Identitas Siswa & Ringkasan Tagihan -->
<div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--brand);">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem;">
        <div>
            <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">Identitas Siswa</span>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--primary); margin: 0.15rem 0;"><?= View::e($student['full_name']) ?></h1>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                NIS: <strong><?= View::e($student['student_number']) ?></strong> &bull; Kelas: <strong><?= View::e($student['class_name']) ?></strong> &bull; Status: <?= View::statusBadge($student['status']) ?>
            </div>
        </div>
        <div>
            <?= View::statusBadge($student['payment_status']) ?>
        </div>
    </div>
</div>

<!-- 3 Kartu Metrik Tagihan Pribadi -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Kewajiban Anda</div>
        <div class="metric-value highlight"><?= View::formatRupiah($student['total_obligation']) ?></div>
        <div class="metric-sub">Album Fisik: <?= View::formatRupiah($student['album_portion']) ?> | Graduation: <?= View::formatRupiah($student['graduation_portion']) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Pembayaran Terverifikasi (Sah)</div>
        <div class="metric-value success"><?= View::formatRupiah($student['total_paid']) ?></div>
        <div class="metric-sub">Telah diverifikasi dan disahkan oleh Bendahara/Ketua</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Sisa Tagihan Anda</div>
        <div class="metric-value" style="color: <?= (float)$student['remaining_balance'] > 0 ? 'var(--danger-text)' : 'var(--success-text)' ?>;">
            <?= View::formatRupiah($student['remaining_balance']) ?>
        </div>
        <div class="metric-sub">
            <?= (float)$student['remaining_balance'] <= 0 ? 'Kewajiban Anda telah Lunas Penuh' : 'Segera lakukan pelunasan sesuai jadwal' ?>
        </div>
    </div>
</div>

<!-- Status Progres 3 Tahap Cicilan -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Status Tahapan Cicilan Resmi</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <?php foreach ($stages as $stg): ?>
            <div style="background: var(--bg-subtle); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-weight: 700; color: var(--primary); font-size: 1.05rem;"><?= View::e($stg['stage_name']) ?></div>
                        <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.1rem;">
                            Jatuh Tempo: <strong><?= View::formatDate($stg['due_date']) ?></strong>
                        </div>
                    </div>
                    <div><?= View::statusBadge($stg['status']) ?></div>
                </div>

                <div class="progress-container" style="height: 10px; margin: 0.85rem 0;">
                    <div class="progress-bar <?= $stg['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $stg['percentage']) ?>%;"></div>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 0.825rem;">
                    <span>Terbayar: <strong><?= View::formatRupiah($stg['paid_amount']) ?></strong></span>
                    <span>Target: <strong><?= View::formatRupiah($stg['target_amount']) ?></strong></span>
                </div>
                <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Sisa Tahap Ini: <strong><?= View::formatRupiah($stg['remaining_amount']) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Riwayat Pembayaran Siswa -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Riwayat Transaksi &amp; Bukti Kuitansi Anda</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Kuitansi</th>
                    <th>Tanggal Bayar</th>
                    <th class="text-right">Nominal</th>
                    <th>Metode</th>
                    <th>Keterangan / Ref</th>
                    <th class="text-center">Status Verifikasi</th>
                    <th class="text-center">Dokumen Kuitansi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                            Belum ada riwayat transaksi pembayaran tercatat.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--brand);">
                                <?= View::e($p['receipt_number']) ?>
                            </td>
                            <td><?= View::formatDate($p['payment_date']) ?></td>
                            <td class="text-right" style="font-weight: 700; color: <?= $p['status'] === 'terverifikasi' ? 'var(--success-text)' : 'var(--text-primary)' ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td><?= View::methodBadge($p['payment_method']) ?></td>
                            <td style="font-size: 0.825rem; color: var(--text-secondary);"><?= View::e($p['reference_note'] ?: '-') ?></td>
                            <td class="text-center">
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--danger-text); margin-top: 2px;">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['status'] === 'terverifikasi'): ?>
                                    <a href="<?= View::url('/portal/receipt/' . $p['id']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        Cetak Kuitansi Resmi
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">&mdash; Menunggu Sah &mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
