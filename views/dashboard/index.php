<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Ringkasan Eksekutif Keuangan</h1>
        <p class="page-subtitle">Pantauan operasional penerimaan iuran, arus kas, dan status kewajiban angkatan</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary">+ Catat Pembayaran</a>
        <a href="<?= View::url('/cash/create') ?>" class="btn btn-secondary">+ Catat Pengeluaran Kas</a>
        <a href="<?= View::url('/reports/print') ?>" target="_blank" class="btn btn-secondary">Cetak Rekap</a>
    </div>
</div>

<!-- Grid Metrik Utama -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Target Total Iuran</div>
        <div class="metric-value highlight"><?= View::formatRupiah($metrics['target_total_revenue']) ?></div>
        <div class="metric-sub"><?= $metrics['active_students'] ?> Siswa Aktif &times; <?= View::formatRupiah($metrics['target_per_student']) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Penerimaan Sah (Terverifikasi)</div>
        <div class="metric-value success"><?= View::formatRupiah($metrics['total_verified_revenue']) ?></div>
        <div class="metric-sub">
            <span style="font-weight:600; color:var(--success-text);"><?= $metrics['achievement_percentage'] ?>%</span> dari target keseluruhan
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Saldo Kas Aktual</div>
        <div class="metric-value" style="color: <?= $metrics['cash_balance'] >= 0 ? 'var(--text-primary)' : 'var(--danger-text)' ?>;">
            <?= View::formatRupiah($metrics['cash_balance']) ?>
        </div>
        <div class="metric-sub">Masuk: <?= View::formatRupiah($metrics['total_cash_in']) ?> | Keluar: <?= View::formatRupiah($metrics['total_cash_out']) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Menunggu Verifikasi</div>
        <div class="metric-value" style="color: <?= $metrics['pending_count'] > 0 ? '#D97706' : 'var(--text-muted)' ?>;">
            <?= $metrics['pending_count'] ?> Transaksi
        </div>
        <div class="metric-sub">Nominal: <?= View::formatRupiah($metrics['total_pending_amount']) ?> (Belum sah)</div>
    </div>
</div>

<!-- Progress Bar Pencapaian Target Keseluruhan -->
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
        <span style="font-size:0.9rem; font-weight:600;">Progres Penerimaan Keseluruhan</span>
        <span style="font-size:0.9rem; font-weight:700; color:var(--brand);"><?= $metrics['achievement_percentage'] ?>%</span>
    </div>
    <div class="progress-container" style="height: 12px;">
        <div class="progress-bar <?= $metrics['achievement_percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $metrics['achievement_percentage']) ?>%;"></div>
    </div>
    <div style="display:flex; justify-content:space-between; margin-top:0.4rem; font-size:0.8rem; color:var(--text-muted);">
        <span>Terkumpul: <strong><?= View::formatRupiah($metrics['total_verified_revenue']) ?></strong></span>
        <span>Target: <strong><?= View::formatRupiah($metrics['target_total_revenue']) ?></strong></span>
    </div>
</div>

<!-- Status Siswa & Progres 3 Tahap Cicilan -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Card Status Siswa -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h2 class="card-title">Status Kewajiban Siswa (<?= $metrics['active_students'] ?> Siswa)</h2>
            <a href="<?= View::url('/students') ?>" style="font-size: 0.8rem;">Lihat Semua &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--success-bg); border-radius: var(--radius-sm); border: 1px solid var(--success-border);">
                <div>
                    <div style="font-weight: 700; color: var(--success-text);">Siswa Lunas (Rp250.000)</div>
                    <div style="font-size: 0.75rem; color: var(--success-text);">Kewajiban selesai penuh</div>
                </div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--success-text);"><?= $metrics['paid_off_count'] ?></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--warning-bg); border-radius: var(--radius-sm); border: 1px solid var(--warning-border);">
                <div>
                    <div style="font-weight: 700; color: var(--warning-text);">Siswa Membayar Sebagian</div>
                    <div style="font-size: 0.75rem; color: var(--warning-text);">Telah mencicil, belum lunas</div>
                </div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--warning-text);"><?= $metrics['partial_count'] ?></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--danger-bg); border-radius: var(--radius-sm); border: 1px solid var(--danger-border);">
                <div>
                    <div style="font-weight: 700; color: var(--danger-text);">Siswa Belum Membayar</div>
                    <div style="font-size: 0.75rem; color: var(--danger-text);">Belum ada pembayaran sah</div>
                </div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--danger-text);"><?= $metrics['unpaid_count'] ?></div>
            </div>
        </div>
    </div>

    <!-- Card Progres Tahap Cicilan -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h2 class="card-title">Capaian per Tahap Pembayaran</h2>
            <a href="<?= View::url('/reports/installments') ?>" style="font-size: 0.8rem;">Detail &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($metrics['stage_breakdown'] as $st): ?>
            <div>
                <div style="display:flex; justify-content:space-between; font-size:0.875rem; margin-bottom:0.25rem;">
                    <div>
                        <strong><?= View::e($st['stage_name']) ?></strong> 
                        <span style="font-size:0.75rem; color:var(--text-muted);">(<?= View::formatRupiah($st['target_per_student']) ?>/siswa)</span>
                    </div>
                    <div>
                        <strong><?= View::formatRupiah($st['collected']) ?></strong>
                        <span style="color:var(--brand); font-weight:600;">(<?= $st['percentage'] ?>%)</span>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar <?= $st['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $st['percentage']) ?>%;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.725rem; color:var(--text-muted); margin-top:0.2rem;">
                    <span>Jatuh Tempo: <?= View::formatDate($st['due_date']) ?></span>
                    <span>Target: <?= View::formatRupiah($st['stage_target']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Transaksi Terbaru & Aktivitas Sistem -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- Transaksi Pembayaran Terbaru -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Transaksi Pembayaran Terbaru</h2>
            <a href="<?= View::url('/payments') ?>" style="font-size: 0.8rem;">Semua Transaksi &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Kuitansi</th>
                        <th>Siswa</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($metrics['recent_transactions'])): ?>
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                                Belum ada transaksi pembayaran tercatat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($metrics['recent_transactions'] as $tx): ?>
                            <tr>
                                <td>
                                    <a href="<?= View::url('/payments/' . $tx['id']) ?>" style="font-weight:600;">
                                        <?= View::e($tx['receipt_number']) ?>
                                    </a>
                                    <div style="font-size:0.725rem; color:var(--text-muted);"><?= View::formatDate($tx['payment_date']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight:500;"><?= View::e($tx['student_name']) ?></div>
                                    <div style="font-size:0.725rem; color:var(--text-muted);"><?= View::e($tx['class_name']) ?> (<?= View::e($tx['student_number']) ?>)</div>
                                </td>
                                <td style="font-weight:600;"><?= View::formatRupiah($tx['amount']) ?></td>
                                <td><?= View::methodBadge($tx['payment_method']) ?></td>
                                <td><?= View::statusBadge($tx['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Aktivitas & Audit Log -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Jejak Audit Aktivitas Terakhir</h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <?php if (empty($recentLogs)): ?>
                <div style="text-align:center; padding: 2rem; color: var(--text-muted); font-size:0.875rem;">
                    Belum ada riwayat aktivitas.
                </div>
            <?php else: ?>
                <?php foreach ($recentLogs as $log): ?>
                    <div style="padding: 0.65rem 0.85rem; background: var(--bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 0.825rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                            <span style="font-weight: 700; color: var(--primary);"><?= View::e($log['action']) ?></span>
                            <span style="color: var(--text-muted); font-size: 0.725rem;"><?= View::formatDate($log['created_at'], true) ?></span>
                        </div>
                        <div style="color: var(--text-secondary);"><?= View::e($log['details']) ?></div>
                        <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.25rem;">
                            Oleh: <strong><?= View::e($log['user_full_name'] ?? 'Sistem') ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
