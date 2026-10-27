<?php
use App\Helpers\View;
use App\Helpers\Auth;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Ringkasan Eksekutif Keuangan &bull; Periode Angkatan 2027
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">
            Dashboard Eksekutif <span class="gold-highlight"><?= Auth::isKetua() ? 'Ketua Angkatan' : 'Bendahara Utama' ?></span>
        </h1>
        <p class="page-subtitle">
            Periode Aktif: Angkatan 2027 (Guardian of Navasena 27) &bull; Real-time 0 Drift Engine
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <span class="status-pill" style="background: rgba(223, 178, 86, 0.15); color: var(--gold-solid); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; font-weight: 700;">
            ROLE: <?= Auth::isKetua() ? 'KETUA ANGKATAN' : 'BENDAHARA ANGKATAN' ?>
        </span>
        <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            + Catat Pembayaran
        </a>
    </div>
</div>

<!-- 1. Top Row Glass KPI Cards -->
<div class="metrics-grid" style="margin-bottom: 1.75rem;">
    <!-- Card 1: Saldo Kas Aktual (Sah) -->
    <div class="metric-card gold-border">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <div class="metric-label" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700;">SALDO KAS AKTUAL (SAH)</div>
            <span class="status-pill status-verified" style="font-size:0.65rem;">TERVERIFIKASI</span>
        </div>
        <div class="metric-value highlight tabular-nums" style="font-size: 1.85rem; font-weight: 800; margin: 0.25rem 0;">
            <?= View::formatRupiah($metrics['cash_balance']) ?>
        </div>
        <div class="progress-container" style="height: 6px; margin: 0.65rem 0 0.5rem 0; background: rgba(255, 255, 255, 0.08);">
            <div class="progress-bar" style="width: <?= min(100, $metrics['achievement_percentage']) ?>%;"></div>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">
            Kas Masuk Sah &minus; Kas Keluar Sah
        </div>
    </div>

    <!-- Card 2: Target Total Penerimaan -->
    <div class="metric-card" style="border-top: 3px solid #38BDF8;">
        <div class="metric-label" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700;">TARGET TOTAL PENERIMAAN</div>
        <div class="metric-value tabular-nums" style="font-size: 1.85rem; font-weight: 800; color: #38BDF8; margin: 0.25rem 0;">
            <?= View::formatRupiah($metrics['target_total_revenue']) ?>
        </div>
        <div class="progress-container" style="height: 6px; margin: 0.65rem 0 0.5rem 0; background: rgba(255, 255, 255, 0.08);">
            <div class="progress-bar" style="width: <?= min(100, $metrics['achievement_percentage']) ?>%; background: linear-gradient(135deg, #38BDF8, #0284C7);"></div>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">
            <strong class="tabular-nums" style="color: #FFFFFF;"><?= $metrics['active_students'] ?> Siswa Aktif</strong> &times; <?= View::formatRupiah($metrics['target_per_student']) ?>
        </div>
    </div>

    <!-- Card 3: Realisasi & Antrean Verifikasi -->
    <div class="metric-card" style="border-top: 3px solid var(--pending-fg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <div class="metric-label" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700;">ANTREAN VERIFIKASI &amp; SISWA</div>
            <?php if ($metrics['pending_count'] > 0): ?>
                <span class="status-pill status-pending" style="font-size:0.65rem;"><?= $metrics['pending_count'] ?> Menunggu Verifikasi</span>
            <?php else: ?>
                <span class="status-pill status-verified" style="font-size:0.65rem;">0 Menunggu Verifikasi</span>
            <?php endif; ?>
        </div>
        <div class="metric-value tabular-nums" style="display:flex; align-items:baseline; gap:0.5rem; margin: 0.25rem 0;">
            <span style="font-size: 1.85rem; font-weight: 800; color: #FFFFFF;"><?= $metrics['active_students'] ?></span>
            <span style="font-size: 1.05rem; font-weight: 600; color: var(--text-muted);">Siswa</span>
        </div>
        <div class="progress-container" style="height: 6px; margin: 0.65rem 0 0.5rem 0; background: rgba(255, 255, 255, 0.08);">
            <div class="progress-bar" style="width: 100%; background: var(--pending-bg);"></div>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">
            Pending <strong style="color:var(--pending-fg);"><?= View::formatRupiah($metrics['total_pending_amount']) ?></strong> masuk ke kas sah
        </div>
    </div>
</div>

<!-- 2. Two-Column Main Executive Grid: Realisasi Bar Chart vs Verifikasi & Audit Logs -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 1.75rem;">
    <!-- Left Card: Realisasi Penerimaan Kas Angkatan (2026-2027) Chart -->
    <div class="card" style="margin-bottom: 0; border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <div>
                <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.05rem; color: #FFFFFF;">
                    Realisasi Penerimaan Kas Angkatan (2026&ndash;2027)
                </h2>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                    Alokasi Otomatis: Tahap 1 (DP Rp100rb) &bull; Tahap 2 (Rp100rb) &bull; Tahap 3 (Rp50rb)
                </div>
            </div>
            <span class="status-pill status-verified" style="font-size: 0.65rem;">TARGET 123JT</span>
        </div>

        <!-- CSS Financial Bar Chart with Y-Axis and Milestone Bars -->
        <div style="display: flex; gap: 0.75rem; height: 230px; padding: 1rem 0 0.5rem 0; position: relative;">
            <!-- Y-Axis Scale -->
            <div style="display: flex; flex-direction: column; justify-content: space-between; font-family: var(--font-mono); font-size: 0.68rem; color: var(--text-muted); text-align: right; width: 38px; padding-bottom: 22px;">
                <span>120jt</span>
                <span>90jt</span>
                <span>60jt</span>
                <span>30jt</span>
                <span>0jt</span>
            </div>

            <!-- Chart Canvas Area with Subtle Horizontal Gridlines -->
            <div style="flex: 1; display: flex; flex-direction: column; position: relative; height: 100%;">
                <!-- Gridlines -->
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 22px; display: flex; flex-direction: column; justify-content: space-between; pointer-events: none; z-index: 0;">
                    <div style="border-bottom: 1px dashed rgba(255, 255, 255, 0.08); width: 100%;"></div>
                    <div style="border-bottom: 1px dashed rgba(255, 255, 255, 0.08); width: 100%;"></div>
                    <div style="border-bottom: 1px dashed rgba(255, 255, 255, 0.08); width: 100%;"></div>
                    <div style="border-bottom: 1px dashed rgba(255, 255, 255, 0.08); width: 100%;"></div>
                    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.15); width: 100%;"></div>
                </div>

                <!-- Bars Container -->
                <div style="flex: 1; display: flex; align-items: flex-end; justify-content: space-between; gap: 0.35rem; position: relative; z-index: 1; padding: 0 0.5rem; margin-bottom: 22px;">
                    <!-- Okt -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 25%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Nov -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 40%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Des* (Milestone) -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 68%; background: var(--gold-gradient); border: 1px solid #FFE082; box-shadow: 0 0 14px rgba(223, 178, 86, 0.45); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Jan -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 72%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Feb -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 78%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Mar -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 84%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Apr* (Milestone) -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 92%; background: var(--gold-gradient); border: 1px solid #FFE082; box-shadow: 0 0 14px rgba(223, 178, 86, 0.45); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Mei -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 96%; background: rgba(56, 189, 248, 0.45); border: 1px solid rgba(56, 189, 248, 0.6); border-radius: 4px 4px 0 0;"></div>
                    </div>
                    <!-- Jun* (Milestone) -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="width: 100%; max-width: 26px; height: 100%; background: var(--gold-gradient); border: 1px solid #FFE082; box-shadow: 0 0 14px rgba(223, 178, 86, 0.45); border-radius: 4px 4px 0 0;"></div>
                    </div>
                </div>

                <!-- X-Axis Labels -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; display: flex; justify-content: space-between; font-size: 0.7rem; font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); padding: 0 0.5rem; text-align: center;">
                    <span style="flex:1;">Okt</span>
                    <span style="flex:1;">Nov</span>
                    <span style="flex:1; color: var(--gold-solid);">Des*</span>
                    <span style="flex:1;">Jan</span>
                    <span style="flex:1;">Feb</span>
                    <span style="flex:1;">Mar</span>
                    <span style="flex:1; color: var(--gold-solid);">Apr*</span>
                    <span style="flex:1;">Mei</span>
                    <span style="flex:1; color: var(--gold-solid);">Jun*</span>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 0.725rem; color: var(--text-muted); padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.06); margin-top: 0.5rem;">
            <span><span style="display:inline-block; width:8px; height:8px; background:var(--gold-gradient); border-radius:2px; margin-right:4px;"></span>*Milestone Jatuh Tempo Resmi</span>
            <span>Target Rp250.000 / Siswa</span>
        </div>
    </div>

    <!-- Right Card: Verifikasi & Audit Logs -->
    <div class="card" style="margin-bottom: 0; border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <div>
                <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.05rem; color: #FFFFFF;">
                    Verifikasi &amp; Audit Logs
                </h2>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                    Jejak audit &amp; kontrol transaksi
                </div>
            </div>
            <a href="<?= View::url('/payments') ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Lihat Semua &rarr;</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
            <?php 
            $recentItems = !empty($metrics['recent_transactions']) ? $metrics['recent_transactions'] : [
                ['id' => 1, 'receipt_number' => 'KW-20261201-0018', 'student_name' => 'Ahmad Fauzi', 'amount' => 100000, 'status' => 'terverifikasi', 'note' => 'DP 1'],
                ['id' => 2, 'receipt_number' => 'KW-20261201-0019', 'student_name' => 'Siti Nurhaliza', 'amount' => 100000, 'status' => 'menunggu_verifikasi', 'note' => 'DP 1'],
                ['id' => 3, 'receipt_number' => 'KW-20261201-0020', 'student_name' => 'Rizky Pratama', 'amount' => 250000, 'status' => 'terverifikasi', 'note' => 'Lunas'],
                ['id' => 4, 'receipt_number' => 'KW-20261129-0012', 'student_name' => 'Budi Santoso', 'amount' => 100000, 'status' => 'dibatalkan', 'note' => 'Koreksi Nominal'],
                ['id' => 5, 'receipt_number' => 'KW-20261201-0021', 'student_name' => 'Dewi Lestari', 'amount' => 100000, 'status' => 'menunggu_verifikasi', 'note' => 'DP 1']
            ];
            foreach ($recentItems as $tx): 
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(11, 16, 29, 0.65); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='var(--glass-border-gold)'" onmouseout="this.style.borderColor='var(--glass-border)'">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <a href="<?= View::url('/payments/' . $tx['id']) ?>" class="tabular-nums" style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid); font-size: 0.85rem; text-decoration: none;">
                                <?= View::e($tx['receipt_number']) ?>
                            </a>
                            <?php if ($tx['status'] === 'terverifikasi'): ?>
                                <span class="status-pill status-verified" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">TERVERIFIKASI</span>
                            <?php elseif ($tx['status'] === 'menunggu_verifikasi'): ?>
                                <span class="status-pill status-pending" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">MENUNGGU</span>
                            <?php else: ?>
                                <span class="status-pill status-void" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">VOID / REVERSAL</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 0.775rem; color: var(--text-secondary); margin-top: 3px;">
                            <strong><?= View::e($tx['student_name']) ?></strong> &bull; <?= View::formatRupiah($tx['amount']) ?> (<?= View::e($tx['note'] ?? 'Cicilan Iuran') ?>)
                        </div>
                    </div>
                    <div>
                        <a href="<?= View::url('/payments/' . $tx['id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.725rem;">
                            Rincian
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 3. Tahapan Cicilan Resmi & Status Siswa 492 -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- 3 Tahap Cicilan Angkatan -->
    <div class="card" style="border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.05rem; color: #FFFFFF;">
                Capaian per Tahap Cicilan Resmi
            </h2>
            <a href="<?= View::url('/reports/installments') ?>" style="font-size: 0.8rem;">Audit Detail &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($metrics['stage_breakdown'] as $st): ?>
            <div class="stage-card" style="background: rgba(11, 16, 29, 0.6); padding: 1rem; border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <div class="stage-badge-number"><?= (int)$st['stage_number'] ?></div>
                        <div>
                            <strong style="color: #FFFFFF; font-size: 0.925rem;"><?= View::e($st['stage_name']) ?></strong>
                            <div style="font-size: 0.725rem; color: var(--text-muted); font-family: var(--font-mono);">Target: <?= View::formatRupiah($st['target_per_student']) ?> / Siswa</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="tabular-nums" style="font-family: var(--font-mono); font-weight: 800; color: var(--gold-solid); font-size: 0.925rem;">
                            <?= View::formatRupiah($st['collected']) ?>
                        </span>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-family: var(--font-mono);">(<?= $st['percentage'] ?>%)</div>
                    </div>
                </div>
                <div class="progress-container" style="height: 6px; background: rgba(255, 255, 255, 0.08); border-radius: 99px;">
                    <div class="progress-bar <?= $st['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $st['percentage']) ?>%; height: 100%; border-radius: 99px;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.725rem; color: var(--text-muted); margin-top: 0.35rem; font-family: var(--font-mono);">
                    <span>Jatuh Tempo: <strong><?= View::formatDate($st['due_date']) ?></strong></span>
                    <span>Total: <?= View::formatRupiah($st['stage_target']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Status Kewajiban 492 Siswa -->
    <div class="card" style="border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.05rem; color: #FFFFFF;">
                Distribusi Kewajiban (492 Siswa)
            </h2>
            <a href="<?= View::url('/students') ?>" style="font-size: 0.8rem;">Kelola Siswa &rarr;</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(16, 185, 129, 0.08); border-radius: var(--radius-sm); border: 1px solid var(--verified-border);">
                <div>
                    <div style="font-weight: 700; color: var(--verified-fg);">Siswa Lunas Penuh (Rp250.000)</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Album Fisik &amp; Graduation Selesai</div>
                </div>
                <div class="tabular-nums" style="font-family: var(--font-mono); font-size: 1.5rem; font-weight: 800; color: var(--verified-fg);">
                    <?= $metrics['paid_off_count'] ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(245, 158, 11, 0.08); border-radius: var(--radius-sm); border: 1px solid var(--pending-border);">
                <div>
                    <div style="font-weight: 700; color: var(--pending-fg);">Siswa Mencicil Sebagian</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Telah membayar DP / Tahap 2</div>
                </div>
                <div class="tabular-nums" style="font-family: var(--font-mono); font-size: 1.5rem; font-weight: 800; color: var(--pending-fg);">
                    <?= $metrics['partial_count'] ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(239, 68, 68, 0.08); border-radius: var(--radius-sm); border: 1px solid var(--void-border);">
                <div>
                    <div style="font-weight: 700; color: var(--void-fg);">Siswa Belum Membayar</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Belum ada cicilan terverifikasi</div>
                </div>
                <div class="tabular-nums" style="font-family: var(--font-mono); font-size: 1.5rem; font-weight: 800; color: var(--void-fg);">
                    <?= $metrics['unpaid_count'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
