<?php
use App\Helpers\View;
?>

<!-- Pengumuman Resmi Pengurus Angkatan -->
<?php if (!empty($announcement)): ?>
    <div class="alert alert-info" style="margin-bottom: 1.5rem;">
        <div class="alert-icon-wrapper">
            <svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div class="alert-content">
            <div class="alert-header">
                <span class="alert-badge" style="background: rgba(223, 178, 86, 0.2); color: var(--gold-solid); border: 1px solid var(--glass-border-gold);">PENGUMUMAN RESMI</span>
                <strong class="alert-title">Informasi Pengurus Angkatan</strong>
            </div>
            <div class="alert-message" style="color: #E2E8F0;"><?= nl2br(View::e($announcement)) ?></div>
        </div>
    </div>
<?php endif; ?>

<!-- 1. Hero Card: Identitas Siswa & Sisa Tagihan dengan Owl Watermark -->
<div class="portal-hero-card">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem; position:relative; z-index:2;">
        <div>
            <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; text-transform: uppercase; color: var(--gold-text); letter-spacing: 0.12em;">
                Identitas Siswa &bull; SMKN 1 Panyingkiran
            </div>
            <h1 style="font-family: var(--font-display); font-size: 1.65rem; font-weight: 800; color: #FFF; margin: 0.25rem 0;">
                <?= View::e($student['full_name']) ?>
            </h1>
            <div class="tabular-nums" style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.2rem;">
                NIS: <strong style="color:var(--gold-solid);"><?= View::e($student['student_number']) ?></strong> &bull; Kelas: <strong><?= View::e($student['class_name']) ?></strong> &bull; Status: <?= View::statusBadge($student['status']) ?>
            </div>
        </div>
        <div>
            <?php if ($student['payment_status'] === 'lunas'): ?>
                <span class="status-pill status-verified" style="font-size:0.85rem; padding:0.4rem 1rem;">LUNAS SAH</span>
            <?php elseif ($student['payment_status'] === 'sebagian'): ?>
                <span class="status-pill status-pending" style="font-size:0.85rem; padding:0.4rem 1rem;">MENCICIL SEBAGIAN</span>
            <?php else: ?>
                <span class="status-pill status-void" style="font-size:0.85rem; padding:0.4rem 1rem;">BELUM BAYAR</span>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; position:relative; z-index:2;">
        <div class="portal-remaining-title">Sisa Tagihan Iuran Anda</div>
        <div class="portal-remaining-amount tabular-nums">
            <?= View::formatRupiah($student['remaining_balance']) ?>
        </div>
        <div class="progress-container" style="height: 10px; max-width: 580px; margin-bottom: 0.65rem;">
            <?php 
                $pct = ($student['total_obligation'] > 0) ? ($student['total_paid'] / $student['total_obligation']) * 100 : 0;
            ?>
            <div class="progress-bar <?= $pct >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $pct) ?>%;"></div>
        </div>
        <div class="tabular-nums" style="font-size: 0.8rem; color: var(--text-muted); display:flex; flex-wrap:wrap; gap:1.25rem;">
            <span>Kewajiban: <strong style="color:#FFF;"><?= View::formatRupiah($student['total_obligation']) ?></strong></span>
            <span>Terbayar Sah: <strong style="color:var(--verified-fg);"><?= View::formatRupiah($student['total_paid']) ?></strong></span>
            <span>Rincian: Album Kenangan Rp200rb + Graduation Rp50rb</span>
        </div>
    </div>
</div>

<!-- 2. Jadwal & Status 3 Tahap Cicilan -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">Jadwal &amp; Status 3 Tahap Cicilan</h2>
        <span class="status-pill status-verified" style="font-size:0.7rem;">ALOKASI OTOMATIS</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
        <?php foreach ($stages as $idx => $stg): ?>
            <?php 
                $isLunas = ((float)$stg['remaining_amount'] <= 0 && (float)$stg['paid_amount'] >= (float)$stg['target_amount']);
                $isSebagian = ((float)$stg['paid_amount'] > 0 && !$isLunas);
            ?>
            <div class="stage-card" style="border-color: <?= $isLunas ? 'var(--verified-border)' : ($isSebagian ? 'var(--pending-border)' : 'var(--glass-border)') ?>;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="stage-badge-number"><?= (int)$stg['stage_number'] ?></div>
                        <div>
                            <div style="font-weight: 700; color: #FFF; font-size: 1rem;"><?= View::e($stg['stage_name']) ?></div>
                            <div class="tabular-nums" style="font-weight: 800; font-size: 1.15rem; color: var(--gold-solid); margin-top: 2px;">
                                <?= View::formatRupiah($stg['target_amount']) ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php if ($isLunas): ?>
                            <span class="status-pill status-verified">LUNAS SAH</span>
                        <?php elseif ($isSebagian): ?>
                            <span class="status-pill status-pending">BELUM LUNAS</span>
                        <?php else: ?>
                            <span class="status-pill status-void">BELUM BAYAR</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="progress-container" style="height: 8px; margin: 0.85rem 0;">
                    <div class="progress-bar <?= $stg['percentage'] >= 100 ? 'success' : '' ?>" style="width: <?= min(100, $stg['percentage']) ?>%;"></div>
                </div>

                <div class="tabular-nums" style="display: flex; justify-content: space-between; font-size: 0.775rem; color: var(--text-secondary);">
                    <span>Terbayar: <strong style="color:var(--verified-fg);"><?= View::formatRupiah($stg['paid_amount']) ?></strong></span>
                    <span>Sisa: <strong style="color:var(--void-fg);"><?= View::formatRupiah($stg['remaining_amount']) ?></strong></span>
                </div>
                <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.35rem;">
                    Jatuh Tempo: <strong><?= View::formatDate($stg['due_date']) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 3. Kuitansi Resmi Sah Anda -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Kuitansi Resmi Sah Anda</h2>
        <span style="font-size: 0.775rem; color: var(--text-muted);">Dilengkapi Watermark Resmi Guardian of Navasena 27</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Kuitansi</th>
                    <th>Tanggal Bayar</th>
                    <th class="text-right">Nominal Sah</th>
                    <th>Metode</th>
                    <th>Referensi</th>
                    <th class="text-center">Status</th>
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
                            <td class="tabular-nums" style="font-weight: 700; color: var(--gold-solid);">
                                <?= View::e($p['receipt_number']) ?>
                            </td>
                            <td class="tabular-nums"><?= View::formatDate($p['payment_date']) ?></td>
                            <td class="text-right tabular-nums" style="font-weight: 800; font-size: 0.95rem; color: <?= $p['status'] === 'terverifikasi' ? 'var(--verified-fg)' : 'var(--text-primary)' ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td>
                                <span class="badge badge-neutral" style="font-size:0.7rem; text-transform:capitalize;">
                                    <?= View::e($p['payment_method']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);"><?= View::e($p['reference_note'] ?: '-') ?></td>
                            <td class="text-center">
                                <?php if ($p['status'] === 'terverifikasi'): ?>
                                    <span class="status-pill status-verified">TERVERIFIKASI</span>
                                <?php elseif ($p['status'] === 'menunggu_verifikasi'): ?>
                                    <span class="status-pill status-pending">MENUNGGU</span>
                                <?php else: ?>
                                    <span class="status-pill status-void">DITOLAK / VOID</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['status'] === 'terverifikasi'): ?>
                                    <a href="<?= View::url('/portal/receipt/' . $p['id']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        UNDUH / CETAK KUITANSI RESMI (PDF)
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.775rem; color: var(--text-muted);">&mdash; Menunggu Sah &mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
