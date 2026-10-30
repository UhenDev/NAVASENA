<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                AUDIT RINCIAN TRANSAKSI
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::e($payment['receipt_number']) ?></span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Kuitansi <?= View::e($payment['receipt_number']) ?></h1>
        <p class="page-subtitle">Rincian parameter pembayaran iuran angkatan, jejak audit, dan pembagian alokasi cicilan</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">&larr; Kembali ke Pembayaran</a>
        
        <?php if ($payment['status'] === 'terverifikasi'): ?>
            <a href="<?= View::url('/payments/' . $payment['id'] . '/receipt') ?>" target="_blank" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
                Cetak Kuitansi Resmi (PDF)
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="responsive-grid-2" style="margin-bottom: 1.5rem;">
    <!-- Informasi Pembayaran -->
    <div class="card" style="margin-bottom: 0; border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--gold-text);">Informasi Pokok Pembayaran</h2>
            <div><?= View::statusBadge($payment['status']) ?></div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted); width: 140px;">No. Kuitansi</td>
                <td style="padding: 0.75rem 0; font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid);"><?= View::e($payment['receipt_number']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Siswa</td>
                <td style="padding: 0.75rem 0; font-weight: 600;">
                    <a href="<?= View::url('/students/' . $payment['student_id']) ?>" style="color: #FFFFFF; text-decoration: none;">
                        <?= View::e($payment['student_name']) ?>
                    </a>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">NIS: <?= View::e($payment['student_number']) ?></div>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Kelas</td>
                <td style="padding: 0.75rem 0; font-weight: 500; color: var(--text-primary);"><?= View::e($payment['class_name']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Nominal Dibayar</td>
                <td style="padding: 0.75rem 0; font-family: var(--font-mono); font-size: 1.35rem; font-weight: 800; color: <?= $payment['status'] === 'terverifikasi' ? 'var(--verified-fg)' : ($payment['status'] === 'dibatalkan' || $payment['status'] === 'ditolak' ? 'var(--void-fg)' : 'var(--pending-fg)') ?>;">
                    <?= View::formatRupiah($payment['amount']) ?>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Metode</td>
                <td style="padding: 0.75rem 0;"><?= View::methodBadge($payment['payment_method']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Tanggal Bayar</td>
                <td style="padding: 0.75rem 0; font-family: var(--font-mono); font-weight: 500; color: var(--text-primary);"><?= View::formatDate($payment['payment_date']) ?></td>
            </tr>
            <tr>
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Referensi / Ket.</td>
                <td style="padding: 0.75rem 0; color: var(--text-secondary);"><?= View::e($payment['reference_note'] ?: '-') ?></td>
            </tr>
        </table>
    </div>

    <!-- Informasi Jejak Rekam & Audit -->
    <div class="card" style="margin-bottom: 0; border-color: var(--glass-border);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--gold-text);">Jejak Audit &amp; Kontrol Otoritas</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted); width: 140px;">Dicatat Oleh</td>
                <td style="padding: 0.75rem 0;">
                    <strong style="color: #FFFFFF;"><?= View::e($payment['recorder_name']) ?></strong> 
                    <span style="font-size: 0.75rem; color: var(--gold-text);">(<?= ucfirst($payment['recorder_role']) ?>)</span>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::formatDate($payment['created_at'], true) ?></div>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <td style="padding: 0.75rem 0; color: var(--text-muted);">Diverifikasi Oleh</td>
                <td style="padding: 0.75rem 0;">
                    <?php if (!empty($payment['verifier_name'])): ?>
                        <strong style="color: var(--verified-fg);"><?= View::e($payment['verifier_name']) ?></strong> 
                        <span style="font-size: 0.75rem; color: var(--gold-text);">(<?= ucfirst($payment['verifier_role'] ?? '') ?>)</span>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::formatDate($payment['verified_at'], true) ?></div>
                    <?php else: ?>
                        <span style="color: var(--pending-fg); font-style: italic;">&mdash; Menunggu Pemeriksaan Ketua &mdash;</span>
                    <?php endif; ?>
                </td>
            </tr>

            <?php if (!empty($payment['rejection_reason'])): ?>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 0.75rem 0; color: var(--void-fg); font-weight: 600;">Alasan Ditolak</td>
                    <td style="padding: 0.75rem 0; color: var(--void-fg);"><?= View::e($payment['rejection_reason']) ?></td>
                </tr>
            <?php endif; ?>

            <?php if (!empty($payment['cancellation_reason'])): ?>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 0.75rem 0; color: var(--void-fg); font-weight: 600;">Dibatalkan Oleh</td>
                    <td style="padding: 0.75rem 0;">
                        <strong style="color: #FFFFFF;"><?= View::e($payment['canceller_name'] ?? 'Pengurus') ?></strong>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::formatDate($payment['cancelled_at'], true) ?></div>
                        <div style="font-size: 0.8rem; color: var(--void-fg); margin-top: 4px; padding: 0.35rem 0.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 4px;">Alasan: <?= View::e($payment['cancellation_reason']) ?></div>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Aksi Pengurus -->
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--glass-border); display: flex; flex-direction: column; gap: 0.65rem;">
            <?php if ($payment['status'] === 'menunggu_verifikasi'): ?>
                <?php if ($canVerify): ?>
                    <form method="POST" action="<?= View::url('/payments/' . $payment['id'] . '/verify') ?>" onsubmit="return confirm('Apakah Anda yakin menyetujui transaksi ini?');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center; background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.5); color: #34D399;">
                            &check; Setujui &amp; Sahkan Pembayaran Kas
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger" style="width: 100%; justify-content: center; background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #F87171;" onclick="openModal('rejectPaymentModal', {formAction: '<?= View::url('/payments/' . $payment['id'] . '/reject') ?>', targetName: '<?= View::e($payment['receipt_number']) ?>'})">
                        &times; Tolak Transaksi Pembayaran
                    </button>
                <?php else: ?>
                    <div style="padding: 0.85rem; border-radius: var(--radius-sm); background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); color: var(--gold-text); font-size: 0.85rem;">
                        Pembayaran ini berstatus <strong>Menunggu Verifikasi</strong> dari Ketua Angkatan sebelum dapat masuk ke kas sah.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($payment['status'] === 'terverifikasi'): ?>
                <button type="button" class="btn btn-secondary" style="width: 100%; justify-content: center; color: var(--void-fg); border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.08);" onclick="openModal('cancelTransactionModal', {formAction: '<?= View::url('/payments/' . $payment['id'] . '/cancel') ?>', targetName: '<?= View::e($payment['receipt_number']) ?>'})">
                    Batalkan Transaksi (Audit Reversal)
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alokasi ke Tahapan Cicilan -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); background: rgba(11, 16, 29, 0.4);">
        <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--gold-text); margin: 0;">
            Alokasi Pembayaran ke Tahap Cicilan Angkatan
        </h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">TAHAP</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NAMA TAHAP CICILAN</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TARGET TAHAP</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">NOMINAL DIALOKASIKAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allocations)): ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                            Belum ada alokasi tahap khusus tercatat.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $totalAlloc = 0.0;
                    foreach ($allocations as $al): 
                        $totalAlloc += (float)$al['allocated_amount'];
                    ?>
                        <tr>
                            <td><strong style="color: #FFFFFF;">Tahap <?= (int)$al['stage_number'] ?></strong></td>
                            <td style="color: var(--text-primary);"><?= View::e($al['stage_name']) ?></td>
                            <td class="text-right" style="font-family: var(--font-mono); color: var(--text-secondary);"><?= View::formatRupiah($al['target_amount']) ?></td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid);">
                                <?= View::formatRupiah($al['allocated_amount']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: rgba(11, 16, 29, 0.7); font-weight: 700; border-top: 1px solid var(--glass-border-gold);">
                        <td colspan="3" class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TOTAL ALOKASI SAH:</td>
                        <td class="text-right" style="font-family: var(--font-mono); color: var(--verified-fg); font-size: 1.05rem;">
                            <?= View::formatRupiah($totalAlloc) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
