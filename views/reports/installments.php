<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Rekap Pembayaran per Tahap Cicilan</h1>
        <p class="page-subtitle">Rincian target, pencapaian nominal, dan persentase kelulusan tahap cicilan angkatan</p>
    </div>
    <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
</div>

<div class="metrics-grid">
    <?php foreach ($stageBreakdown as $sb): ?>
        <div class="metric-card">
            <div class="metric-label"><?= View::e($sb['stage_name']) ?></div>
            <div class="metric-value highlight"><?= View::formatRupiah($sb['collected']) ?></div>
            <div class="progress-container" style="margin: 0.5rem 0;">
                <div class="progress-bar <?= $sb['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $sb['percentage']) ?>%;"></div>
            </div>
            <div class="metric-sub" style="display:flex; justify-content:space-between;">
                <span>Target: <?= View::formatRupiah($sb['stage_target']) ?></span>
                <span style="font-weight:700; color:var(--brand);"><?= $sb['percentage'] ?>%</span>
            </div>
            <div style="font-size:0.725rem; color:var(--text-muted); margin-top:0.35rem;">
                Jatuh Tempo: <strong><?= View::formatDate($sb['due_date']) ?></strong>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Tabel Rincian Tahap Pembayaran Iuran</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tahap</th>
                    <th>Nama Tahap Cicilan</th>
                    <th class="text-right">Nominal / Siswa</th>
                    <th>Jatuh Tempo Resmi</th>
                    <th class="text-right">Target Total (<?= $activeCount ?> Siswa)</th>
                    <th class="text-right">Terkumpul Sah</th>
                    <th class="text-right">Kekurangan</th>
                    <th class="text-center">Pencapaian</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sumTarget = 0.0;
                $sumCollected = 0.0;
                foreach ($stageBreakdown as $sb): 
                    $sumTarget += (float)$sb['stage_target'];
                    $sumCollected += (float)$sb['collected'];
                    $diff = max(0.0, (float)$sb['stage_target'] - (float)$sb['collected']);
                ?>
                    <tr>
                        <td><strong>Tahap <?= $sb['stage_number'] ?></strong></td>
                        <td><?= View::e($sb['stage_name']) ?></td>
                        <td class="text-right"><?= View::formatRupiah($sb['target_per_student']) ?></td>
                        <td><?= View::formatDate($sb['due_date']) ?></td>
                        <td class="text-right"><?= View::formatRupiah($sb['stage_target']) ?></td>
                        <td class="text-right" style="font-weight:700; color:var(--success-text);">
                            <?= View::formatRupiah($sb['collected']) ?>
                        </td>
                        <td class="text-right" style="color:var(--danger-text); font-weight:600;">
                            <?= View::formatRupiah($diff) ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $sb['percentage'] >= 100 ? 'badge-success' : 'badge-warning' ?>">
                                <?= $sb['percentage'] ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--bg-subtle); font-weight: 700; border-top: 2px solid var(--border);">
                    <td colspan="4" class="text-right">Total Keseluruhan (Rp250.000/siswa):</td>
                    <td class="text-right"><?= View::formatRupiah($sumTarget) ?></td>
                    <td class="text-right" style="color:var(--success-text); font-size:1rem;"><?= View::formatRupiah($sumCollected) ?></td>
                    <td class="text-right" style="color:var(--danger-text);"><?= View::formatRupiah(max(0, $sumTarget - $sumCollected)) ?></td>
                    <td class="text-center">
                        <strong><?= $sumTarget > 0 ? round(($sumCollected / $sumTarget) * 100, 2) : 0 ?>%</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
