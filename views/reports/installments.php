<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                CAPAIAN PER TAHAP
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">REP-STAGES-PROGRESS</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Rekap Pembayaran per Tahap Cicilan</h1>
        <p class="page-subtitle">Rincian target angkatan, pencapaian nominal kas sah, dan rasio pemenuhan cicilan</p>
    </div>
    <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
</div>

<!-- 3 Stage Metrics Grid -->
<div class="metrics-grid" style="margin-bottom: 2rem;">
    <?php foreach ($stageBreakdown as $sb): ?>
        <div class="metric-card" style="border-top: 3px solid var(--gold-solid);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <div class="metric-label" style="color: var(--gold-text); text-transform: uppercase; font-size: 0.8rem; font-weight: 700;">
                    <?= View::e($sb['stage_name']) ?>
                </div>
                <span class="badge" style="background: rgba(223, 178, 86, 0.15); color: var(--gold-solid); border: 1px solid var(--glass-border-gold); font-family: var(--font-mono); font-size: 0.75rem;">
                    Tahap <?= (int)$sb['stage_number'] ?>
                </span>
            </div>

            <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: #FFFFFF; margin: 0.25rem 0;">
                <?= View::formatRupiah($sb['collected']) ?>
            </div>

            <div class="progress-container" style="margin: 0.75rem 0; height: 8px; background: rgba(255, 255, 255, 0.08); border-radius: 99px; overflow: hidden;">
                <div class="progress-bar" style="width: <?= min(100, $sb['percentage']) ?>%; height: 100%; background: <?= $sb['percentage'] >= 100 ? 'var(--verified-fg)' : 'var(--gold-gradient)' ?>; border-radius: 99px;"></div>
            </div>

            <div class="metric-sub" style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary);">
                <span>Target: <strong style="font-family: var(--font-mono); color: var(--text-primary);"><?= View::formatRupiah($sb['stage_target']) ?></strong></span>
                <span style="font-family: var(--font-mono); font-weight: 700; color: <?= $sb['percentage'] >= 100 ? 'var(--verified-fg)' : 'var(--gold-text)' ?>;"><?= $sb['percentage'] ?>%</span>
            </div>

            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.6rem; padding-top: 0.5rem; border-top: 1px solid var(--glass-border);">
                Jatuh Tempo: <strong style="font-family: var(--font-mono); color: var(--text-secondary);"><?= View::formatDate($sb['due_date']) ?></strong>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); background: rgba(11, 16, 29, 0.4);">
        <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--gold-text); margin: 0;">
            Tabel Rincian Capaian Tahap Pembayaran Iuran
        </h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">TAHAP</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NAMA TAHAP CICILAN</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">NOMINAL / SISWA</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">JATUH TEMPO RESMI</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TARGET TOTAL (<?= $activeCount ?> SISWA)</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TERKUMPUL SAH</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">KEKURANGAN</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">PENCAPAIAN</th>
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
                        <td><strong style="color: #FFFFFF; font-family: var(--font-mono);">Tahap <?= $sb['stage_number'] ?></strong></td>
                        <td style="color: var(--text-primary); font-weight: 500;"><?= View::e($sb['stage_name']) ?></td>
                        <td class="text-right" style="font-family: var(--font-mono); color: var(--text-secondary);"><?= View::formatRupiah($sb['target_per_student']) ?></td>
                        <td style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary);"><?= View::formatDate($sb['due_date']) ?></td>
                        <td class="text-right" style="font-family: var(--font-mono); color: var(--text-primary);"><?= View::formatRupiah($sb['stage_target']) ?></td>
                        <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--verified-fg);">
                            <?= View::formatRupiah($sb['collected']) ?>
                        </td>
                        <td class="text-right" style="font-family: var(--font-mono); font-weight: 600; color: <?= $diff > 0 ? 'var(--void-fg)' : 'var(--text-muted)' ?>;">
                            <?= View::formatRupiah($diff) ?>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background: <?= $sb['percentage'] >= 100 ? 'var(--verified-bg)' : 'rgba(223, 178, 86, 0.15)' ?>; color: <?= $sb['percentage'] >= 100 ? 'var(--verified-fg)' : 'var(--gold-solid)' ?>; border: 1px solid <?= $sb['percentage'] >= 100 ? 'rgba(16, 185, 129, 0.3)' : 'var(--glass-border-gold)' ?>; font-family: var(--font-mono); font-weight: 700;">
                                <?= $sb['percentage'] ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: rgba(11, 16, 29, 0.75); font-weight: 700; border-top: 1px solid var(--glass-border-gold);">
                    <td colspan="4" class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TOTAL KESELURUHAN (RP250.000 / SISWA):</td>
                    <td class="text-right" style="font-family: var(--font-mono);"><?= View::formatRupiah($sumTarget) ?></td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--verified-fg); font-size: 1.05rem;"><?= View::formatRupiah($sumCollected) ?></td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--void-fg); font-size: 1.05rem;"><?= View::formatRupiah(max(0, $sumTarget - $sumCollected)) ?></td>
                    <td class="text-center" style="font-family: var(--font-mono); color: var(--gold-solid); font-size: 1.05rem;">
                        <strong><?= $sumTarget > 0 ? round(($sumCollected / $sumTarget) * 100, 2) : 0 ?>%</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
