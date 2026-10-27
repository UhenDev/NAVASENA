<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?= View::e($student['full_name']) ?></h1>
        <p class="page-subtitle">NIS: <strong><?= View::e($student['student_number']) ?></strong> &bull; Kelas: <strong><?= View::e($student['class_name']) ?></strong> &bull; Status: <?= View::statusBadge($student['status']) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments/create?student_id=' . $student['id']) ?>" class="btn btn-primary">+ Catat Pembayaran Baru</a>
        <a href="<?= View::url('/students/' . $student['id'] . '/edit') ?>" class="btn btn-secondary">Edit Profil</a>
        
        <form method="POST" action="<?= View::url('/students/' . $student['id'] . '/toggle-status') ?>" onsubmit="return confirm('Ubah status keaktifan siswa ini? Transaksi pembayaran tidak akan terhapus.');" style="display:inline;">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-secondary">
                <?= $student['status'] === 'aktif' ? 'Nonaktifkan Siswa' : 'Aktifkan Siswa' ?>
            </button>
        </form>
    </div>
</div>

<!-- Ringkasan Kewajiban Iuran -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Kewajiban</div>
        <div class="metric-value highlight"><?= View::formatRupiah($student['total_obligation']) ?></div>
        <div class="metric-sub">Album: <?= View::formatRupiah($student['album_portion']) ?> | Grad: <?= View::formatRupiah($student['graduation_portion']) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Pembayaran Sah (Terverifikasi)</div>
        <div class="metric-value success"><?= View::formatRupiah($student['total_paid']) ?></div>
        <div class="metric-sub">Status: <?= View::statusBadge($student['payment_status']) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Sisa Tagihan</div>
        <div class="metric-value" style="color: <?= (float)$student['remaining_balance'] > 0 ? 'var(--danger-text)' : 'var(--success-text)' ?>;">
            <?= View::formatRupiah($student['remaining_balance']) ?>
        </div>
        <div class="metric-sub"><?= (float)$student['remaining_balance'] <= 0 ? 'Kewajiban telah selesai penuh' : 'Menunggu penyelesaian cicilan' ?></div>
    </div>
</div>

<!-- Rincian Progres 3 Tahap Cicilan -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Status Progres per Tahap Pembayaran</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
        <?php foreach ($stageProgress as $sp): ?>
            <div style="background: var(--bg-subtle); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <div>
                        <div style="font-weight: 700; color: var(--primary); font-size: 1rem;"><?= View::e($sp['stage_name']) ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Jatuh Tempo: <?= View::formatDate($sp['due_date']) ?></div>
                    </div>
                    <div><?= View::statusBadge($sp['status']) ?></div>
                </div>

                <div class="progress-container" style="height: 10px; margin: 0.75rem 0;">
                    <div class="progress-bar <?= $sp['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $sp['percentage']) ?>%;"></div>
                </div>

                <div style="display:flex; justify-content:space-between; font-size: 0.8rem; margin-top: 0.4rem;">
                    <span>Terbayar: <strong><?= View::formatRupiah($sp['paid_amount']) ?></strong></span>
                    <span>Target: <strong><?= View::formatRupiah($sp['target_amount']) ?></strong></span>
                </div>
                <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Sisa Tahap: <strong><?= View::formatRupiah($sp['remaining_amount']) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Riwayat Transaksi Pembayaran Siswa -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Riwayat Transaksi Pembayaran Siswa</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Kuitansi</th>
                    <th>Tanggal</th>
                    <th class="text-right">Nominal</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Dicatat Oleh</th>
                    <th>Diverifikasi Oleh</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                            Belum ada transaksi pembayaran untuk siswa ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td>
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" style="font-weight: 600;">
                                    <?= View::e($p['receipt_number']) ?>
                                </a>
                            </td>
                            <td><?= View::formatDate($p['payment_date']) ?></td>
                            <td class="text-right" style="font-weight: 600;"><?= View::formatRupiah($p['amount']) ?></td>
                            <td><?= View::methodBadge($p['payment_method']) ?></td>
                            <td>
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size: 0.725rem; color: var(--danger-text); margin-top: 2px;">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= View::e($p['recorder_name']) ?></td>
                            <td>
                                <?= View::e($p['verifier_name'] ?? '-') ?>
                                <?php if (!empty($p['verified_at'])): ?>
                                    <div style="font-size: 0.725rem; color: var(--text-muted);"><?= View::formatDate($p['verified_at'], true) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" class="btn btn-secondary btn-sm">Detail</a>
                                <?php if ($p['status'] === 'terverifikasi'): ?>
                                    <a href="<?= View::url('/payments/' . $p['id'] . '/receipt') ?>" target="_blank" class="btn btn-secondary btn-sm" title="Cetak Kuitansi Resmi">Kuitansi</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
