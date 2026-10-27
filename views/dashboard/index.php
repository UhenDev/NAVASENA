<?php
use App\Helpers\View;
use App\Helpers\Auth;
?>
<div class="page-header">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Ringkasan Eksekutif Keuangan &bull; Periode Angkatan 2027
        </div>
        <h1 class="page-title">
            Dashboard Eksekutif <span class="gold-highlight"><?= Auth::isKetua() ? 'Ketua Angkatan' : 'Bendahara Utama' ?></span>
        </h1>
        <p class="page-subtitle">
            Guardian of Navasena 27 &bull; SMKN 1 Panyingkiran &bull; Real-time 0 Drift Engine
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary">+ Catat Pembayaran</a>
        <a href="<?= View::url('/cash/create') ?>" class="btn btn-secondary">+ Catat Arus Kas</a>
        <a href="<?= View::url('/reports/print') ?>" target="_blank" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Rekap LPJ
        </a>
    </div>
</div>

<!-- 1. Top Row Glass KPI Cards -->
<div class="metrics-grid">
    <!-- Card 1: Saldo Kas Aktual (Sah) -->
    <div class="metric-card gold-border">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <div class="metric-label" style="margin-bottom:0;">Saldo Kas Aktual (Sah)</div>
            <span class="status-pill status-verified" style="font-size:0.65rem;">TERVERIFIKASI</span>
        </div>
        <div class="metric-value highlight tabular-nums">
            <?= View::formatRupiah($metrics['cash_balance']) ?>
        </div>
        <div class="progress-container" style="height: 6px; margin: 0.65rem 0 0.5rem 0;">
            <div class="progress-bar" style="width: <?= min(100, $metrics['achievement_percentage']) ?>%;"></div>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem;">
            Kas Masuk Sah: <strong><?= View::formatRupiah($metrics['total_cash_in']) ?></strong> &bull; Keluar: <strong><?= View::formatRupiah($metrics['total_cash_out']) ?></strong>
        </div>
    </div>

    <!-- Card 2: Target Total Penerimaan -->
    <div class="metric-card">
        <div class="metric-label">Target Total Penerimaan</div>
        <div class="metric-value tabular-nums">
            <?= View::formatRupiah($metrics['target_total_revenue']) ?>
        </div>
        <div class="metric-sub">
            <strong class="tabular-nums" style="color:var(--gold-text);"><?= $metrics['active_students'] ?> Siswa Aktif</strong> &times; <?= View::formatRupiah($metrics['target_per_student']) ?> / Siswa
        </div>
    </div>

    <!-- Card 3: Realisasi & Antrean Verifikasi -->
    <div class="metric-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <div class="metric-label" style="margin-bottom:0;">Antrean Verifikasi &amp; Siswa</div>
            <?php if ($metrics['pending_count'] > 0): ?>
                <span class="status-pill status-pending" style="font-size:0.65rem;"><?= $metrics['pending_count'] ?> Menunggu</span>
            <?php else: ?>
                <span class="status-pill status-verified" style="font-size:0.65rem;">Semua Bersih</span>
            <?php endif; ?>
        </div>
        <div class="metric-value tabular-nums" style="display:flex; align-items:baseline; gap:0.5rem;">
            <span><?= $metrics['active_students'] ?></span>
            <span style="font-size:1rem; font-weight:600; color:var(--text-muted);">Siswa Aktif</span>
        </div>
        <div class="metric-sub">
            Pending: <strong style="color:var(--pending-fg);"><?= View::formatRupiah($metrics['total_pending_amount']) ?></strong> (0 Masuk ke kas sah)
        </div>
    </div>
</div>

<!-- 2. Progres Pencapaian Angkatan -->
<div class="card" style="margin-bottom: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
        <div>
            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">
                Progres Akumulasi Penerimaan Kas Angkatan
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                Telah Terverifikasi Sah Masuk Buku Kas Angkatan
            </div>
        </div>
        <div style="text-align: right;">
            <span class="tabular-nums" style="font-size: 1.35rem; font-weight: 800; color: var(--gold-solid);">
                <?= $metrics['achievement_percentage'] ?>%
            </span>
        </div>
    </div>
    <div class="progress-container" style="height: 10px;">
        <div class="progress-bar <?= $metrics['achievement_percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $metrics['achievement_percentage']) ?>%;"></div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted);" class="tabular-nums">
        <span>Terkumpul Sah: <strong style="color:var(--verified-fg);"><?= View::formatRupiah($metrics['total_verified_revenue']) ?></strong></span>
        <span>Target Akhir: <strong><?= View::formatRupiah($metrics['target_total_revenue']) ?></strong></span>
    </div>
</div>

<!-- 3. Two-Column Executive Grid: Realisasi Chart vs Verifikasi & Audit Logs -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 1.75rem;">
    <!-- Left Card: Realisasi Penerimaan Kas Angkatan (2026-2027) Chart -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Realisasi Penerimaan Kas Angkatan</h2>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                    Alokasi Otomatis: Tahap 1 (DP Rp100rb) &bull; Tahap 2 (Rp100rb) &bull; Tahap 3 (Rp50rb)
                </div>
            </div>
            <span class="status-pill status-verified" style="font-size:0.65rem;">TARGET 123JT</span>
        </div>

        <!-- CSS Bar Chart with Milestone Bars -->
        <div class="chart-container">
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 25%;"></div>
                <div class="chart-label">Okt</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 40%;"></div>
                <div class="chart-label">Nov</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar milestone" style="height: 68%;"></div>
                <div class="chart-label" style="color:var(--gold-solid);">Des*</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 72%;"></div>
                <div class="chart-label">Jan</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 78%;"></div>
                <div class="chart-label">Feb</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 84%;"></div>
                <div class="chart-label">Mar</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar milestone" style="height: 92%;"></div>
                <div class="chart-label" style="color:var(--gold-solid);">Apr*</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar" style="height: 96%;"></div>
                <div class="chart-label">Mei</div>
            </div>
            <div class="chart-bar-col">
                <div class="chart-bar milestone" style="height: 100%;"></div>
                <div class="chart-label" style="color:var(--gold-solid);">Jun*</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 0.725rem; color: var(--text-muted); padding-top: 0.5rem;">
            <span><span style="display:inline-block; width:8px; height:8px; background:var(--gold-gradient); border-radius:2px; margin-right:4px;"></span>*Milestone Jatuh Tempo Resmi</span>
            <span>Target Rp250.000 / Siswa</span>
        </div>
    </div>

    <!-- Right Card: Verifikasi & Audit Reversal Logs -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Verifikasi &amp; Audit Logs</h2>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                    Jejak audit, approval kas &amp; kontrol transaksi
                </div>
            </div>
            <a href="<?= View::url('/payments') ?>" class="btn btn-secondary btn-sm">Lihat Semua &rarr;</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
            <?php if (empty($metrics['recent_transactions'])): ?>
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                    Belum ada transaksi pembayaran dalam antrean.
                </div>
            <?php else: ?>
                <?php foreach ($metrics['recent_transactions'] as $tx): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--glass-inner); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); transition: all 0.2s ease;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <a href="<?= View::url('/payments/' . $tx['id']) ?>" class="tabular-nums" style="font-weight: 700; color: var(--gold-solid); font-size: 0.85rem;">
                                    <?= View::e($tx['receipt_number']) ?>
                                </a>
                                <?php if ($tx['status'] === 'terverifikasi'): ?>
                                    <span class="status-pill status-verified" style="font-size: 0.65rem;">TERVERIFIKASI</span>
                                <?php elseif ($tx['status'] === 'menunggu_verifikasi'): ?>
                                    <span class="status-pill status-pending" style="font-size: 0.65rem;">MENUNGGU</span>
                                <?php else: ?>
                                    <span class="status-pill status-void" style="font-size: 0.65rem;">VOID / REVERSAL</span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.775rem; color: var(--text-secondary); margin-top: 2px;">
                                <strong><?= View::e($tx['student_name']) ?></strong> &bull; <?= View::e($tx['class_name']) ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="tabular-nums" style="font-weight: 800; font-size: 0.95rem; color: <?= $tx['status'] === 'terverifikasi' ? 'var(--verified-fg)' : 'var(--text-primary)' ?>;">
                                <?= View::formatRupiah($tx['amount']) ?>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">
                                <?= View::formatDate($tx['payment_date']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 4. Tahapan Cicilan Resmi & Status Siswa 492 -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- 3 Tahap Cicilan Angkatan -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Capaian per Tahap Cicilan</h2>
            <a href="<?= View::url('/reports/installments') ?>" style="font-size: 0.8rem;">Audit Detail &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1.15rem;">
            <?php foreach ($metrics['stage_breakdown'] as $idx => $st): ?>
            <div class="stage-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <div class="stage-badge-number"><?= $idx ?></div>
                        <div>
                            <strong style="color: var(--text-primary); font-size: 0.925rem;"><?= View::e($st['stage_name']) ?></strong>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">Target: <?= View::formatRupiah($st['target_per_student']) ?> / Siswa</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="tabular-nums" style="font-weight: 800; color: var(--gold-solid); font-size: 0.9rem;">
                            <?= View::formatRupiah($st['collected']) ?>
                        </span>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">(<?= $st['percentage'] ?>%)</div>
                    </div>
                </div>
                <div class="progress-container" style="height: 6px;">
                    <div class="progress-bar <?= $st['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $st['percentage']) ?>%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.725rem; color: var(--text-muted); margin-top: 0.35rem;">
                    <span>Jatuh Tempo: <strong><?= View::formatDate($st['due_date']) ?></strong></span>
                    <span>Total Target: <?= View::formatRupiah($st['stage_target']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Status Kewajiban 492 Siswa -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Distribusi Kewajiban (492 Siswa)</h2>
            <a href="<?= View::url('/students') ?>" style="font-size: 0.8rem;">Kelola Siswa &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--glass-inner); border-radius: var(--radius-sm); border: 1px solid var(--verified-border);">
                <div>
                    <div style="font-weight: 700; color: var(--verified-fg);">Siswa Lunas Penuh (Rp250.000)</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Album Fisik &amp; Graduation Selesai</div>
                </div>
                <div class="tabular-nums" style="font-size: 1.45rem; font-weight: 800; color: var(--verified-fg);">
                    <?= $metrics['paid_off_count'] ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--glass-inner); border-radius: var(--radius-sm); border: 1px solid var(--pending-border);">
                <div>
                    <div style="font-weight: 700; color: var(--pending-fg);">Siswa Mencicil Sebagian</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Telah membayar DP / Tahap 2</div>
                </div>
                <div class="tabular-nums" style="font-size: 1.45rem; font-weight: 800; color: var(--pending-fg);">
                    <?= $metrics['partial_count'] ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--glass-inner); border-radius: var(--radius-sm); border: 1px solid var(--void-border);">
                <div>
                    <div style="font-weight: 700; color: var(--void-fg);">Siswa Belum Membayar</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Belum ada cicilan terverifikasi</div>
                </div>
                <div class="tabular-nums" style="font-size: 1.45rem; font-weight: 800; color: var(--void-fg);">
                    <?= $metrics['unpaid_count'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
