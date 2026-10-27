<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                PROFIL KEUANGAN SISWA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">NIS: <?= View::e($student['student_number']) ?></span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;"><?= View::e($student['full_name']) ?></h1>
        <p class="page-subtitle">
            Kelas: <strong style="color: #FFFFFF;"><?= View::e($student['class_name']) ?></strong> &bull; 
            Status Akun: <?= View::statusBadge($student['status']) ?> &bull;
            Telepon: <span style="font-family: var(--font-mono); color: var(--text-secondary);"><?= View::e($student['phone'] ?: '-') ?></span>
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments/create?student_id=' . $student['id']) ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            + Catat Pembayaran Baru
        </a>
        <a href="<?= View::url('/students/' . $student['id'] . '/edit') ?>" class="btn btn-secondary">
            Edit Profil
        </a>
        
        <form method="POST" action="<?= View::url('/students/' . $student['id'] . '/toggle-status') ?>" onsubmit="return confirm('Ubah status keaktifan siswa ini? Transaksi pembayaran tidak akan terhapus.');" style="display:inline;">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-secondary" style="color: <?= $student['status'] === 'aktif' ? 'var(--void-fg)' : 'var(--verified-fg)' ?>;">
                <?= $student['status'] === 'aktif' ? 'Nonaktifkan Siswa' : 'Aktifkan Siswa' ?>
            </button>
        </form>
    </div>
</div>

<!-- Ringkasan Kewajiban Iuran -->
<div class="metrics-grid" style="margin-bottom: 2rem;">
    <div class="metric-card" style="border-left: 3px solid var(--text-muted);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Total Kewajiban Iuran</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: #FFFFFF; margin: 0.35rem 0;">
            <?= View::formatRupiah($student['total_obligation']) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Album: <?= View::formatRupiah($student['album_portion']) ?> | Grad: <?= View::formatRupiah($student['graduation_portion']) ?></div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--verified-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Pembayaran Sah (Terverifikasi)</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: var(--verified-fg); margin: 0.35rem 0;">
            <?= View::formatRupiah($student['total_paid']) ?>
        </div>
        <div class="metric-sub"><?= View::statusBadge($student['payment_status']) ?></div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--gold-solid); background: rgba(223, 178, 86, 0.05);">
        <div class="metric-label" style="color: var(--gold-text); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Sisa Tagihan Anda</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: <?= (float)$student['remaining_balance'] > 0 ? 'var(--gold-solid)' : 'var(--verified-fg)' ?>; margin: 0.35rem 0;">
            <?= View::formatRupiah($student['remaining_balance']) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">
            <?= (float)$student['remaining_balance'] <= 0 ? 'Kewajiban angkatan telah selesai penuh (Lunas)' : 'Menunggu pelunasan tahap cicilan resmi' ?>
        </div>
    </div>
</div>

<!-- Rincian Progres 3 Tahap Cicilan -->
<div class="card" style="margin-bottom: 2rem; border-color: var(--glass-border);">
    <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
        <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.15rem; color: var(--gold-text);">
            Status Progres per Tahap Pembayaran Resmi
        </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
        <?php foreach ($stageProgress as $sp): ?>
            <div style="background: rgba(11, 16, 29, 0.65); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 1.25rem; transition: transform 0.2s ease, border-color 0.2s ease;" onmouseover="this.style.borderColor='var(--glass-border-gold)'" onmouseout="this.style.borderColor='var(--glass-border)'">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <div>
                        <div style="font-weight: 700; color: #FFFFFF; font-size: 1rem; font-family: var(--font-display);"><?= View::e($sp['stage_name']) ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono); margin-top: 2px;">Jatuh Tempo: <?= View::formatDate($sp['due_date']) ?></div>
                    </div>
                    <div><?= View::statusBadge($sp['status']) ?></div>
                </div>

                <div class="progress-container" style="height: 8px; margin: 0.85rem 0; background: rgba(255, 255, 255, 0.08); border-radius: 99px; overflow: hidden;">
                    <div class="progress-bar" style="width: <?= min(100, $sp['percentage']) ?>%; height: 100%; background: <?= $sp['percentage'] >= 100 ? 'var(--verified-fg)' : 'var(--gold-gradient)' ?>; border-radius: 99px;"></div>
                </div>

                <div style="display:flex; justify-content:space-between; font-size: 0.8rem; margin-top: 0.5rem;">
                    <span style="color: var(--text-secondary);">Terbayar: <strong style="font-family: var(--font-mono); color: var(--verified-fg);"><?= View::formatRupiah($sp['paid_amount']) ?></strong></span>
                    <span style="color: var(--text-secondary);">Target: <strong style="font-family: var(--font-mono); color: #FFFFFF;"><?= View::formatRupiah($sp['target_amount']) ?></strong></span>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; padding-top: 0.35rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                    Sisa Tahap: <strong style="font-family: var(--font-mono); color: <?= (float)$sp['remaining_amount'] > 0 ? 'var(--gold-solid)' : 'var(--text-muted)' ?>;"><?= View::formatRupiah($sp['remaining_amount']) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Riwayat Transaksi Pembayaran Siswa -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); background: rgba(11, 16, 29, 0.4);">
        <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.15rem; color: var(--gold-text); margin: 0;">
            Riwayat Transaksi Pembayaran Siswa
        </h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NO. KUITANSI</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">TANGGAL</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">NOMINAL</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">METODE</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">STATUS</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">DICATAT OLEH</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">VERIFIKASI</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            Belum ada riwayat transaksi pembayaran untuk siswa ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td>
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid); text-decoration: none;">
                                    <?= View::e($p['receipt_number']) ?>
                                </a>
                            </td>
                            <td style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap;">
                                <?= View::formatDate($p['payment_date']) ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: <?= $p['status'] === 'terverifikasi' ? 'var(--verified-fg)' : ($p['status'] === 'dibatalkan' || $p['status'] === 'ditolak' ? 'var(--void-fg)' : 'var(--pending-fg)') ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td><?= View::methodBadge($p['payment_method']) ?></td>
                            <td class="text-center">
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--void-fg); margin-top: 2px;">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);"><?= View::e($p['recorder_name']) ?></td>
                            <td style="font-size: 0.8rem;">
                                <?php if (!empty($p['verifier_name'])): ?>
                                    <span style="color: var(--verified-fg);"><?= View::e($p['verifier_name']) ?></span>
                                    <div style="font-size: 0.7rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::formatDate($p['verified_at'], true) ?></div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-style: italic;">Belum</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <a href="<?= View::url('/payments/' . $p['id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">Detail</a>
                                    <?php if ($p['status'] === 'terverifikasi'): ?>
                                        <a href="<?= View::url('/payments/' . $p['id'] . '/receipt') ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; border-color: var(--glass-border-gold); color: var(--gold-solid);" title="Cetak Kuitansi Resmi">Kuitansi</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
