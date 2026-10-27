<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Rincian Kuitansi <?= View::e($payment['receipt_number']) ?></h1>
        <p class="page-subtitle">Detail transaksi pembayaran iuran siswa dan alokasi cicilan</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">&larr; Kembali ke Pembayaran</a>
        
        <?php if ($payment['status'] === 'terverifikasi'): ?>
            <a href="<?= View::url('/payments/' . $payment['id'] . '/receipt') ?>" target="_blank" class="btn btn-primary">Cetak Kuitansi Resmi</a>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Informasi Pembayaran -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h2 class="card-title">Informasi Pembayaran</h2>
            <div><?= View::statusBadge($payment['status']) ?></div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted); width: 140px;">No. Kuitansi</td>
                <td style="padding: 0.6rem 0; font-weight: 700; color: var(--brand);"><?= View::e($payment['receipt_number']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Siswa</td>
                <td style="padding: 0.6rem 0; font-weight: 600;">
                    <a href="<?= View::url('/students/' . $payment['student_id']) ?>">
                        <?= View::e($payment['student_name']) ?> (<?= View::e($payment['student_number']) ?>)
                    </a>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Kelas</td>
                <td style="padding: 0.6rem 0; font-weight: 500;"><?= View::e($payment['class_name']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Nominal Dibayar</td>
                <td style="padding: 0.6rem 0; font-size: 1.25rem; font-weight: 800; color: <?= $payment['status'] === 'terverifikasi' ? 'var(--success-text)' : 'var(--text-primary)' ?>;">
                    <?= View::formatRupiah($payment['amount']) ?>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Metode</td>
                <td style="padding: 0.6rem 0;"><?= View::methodBadge($payment['payment_method']) ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Tanggal Bayar</td>
                <td style="padding: 0.6rem 0; font-weight: 500;"><?= View::formatDate($payment['payment_date']) ?></td>
            </tr>
            <tr>
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Referensi / Ket.</td>
                <td style="padding: 0.6rem 0; color: var(--text-secondary);"><?= View::e($payment['reference_note'] ?: '-') ?></td>
            </tr>
        </table>
    </div>

    <!-- Informasi Jejak Rekam & Audit -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h2 class="card-title">Jejak Rekam Transaksi</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted); width: 140px;">Dicatat Oleh</td>
                <td style="padding: 0.6rem 0;">
                    <strong><?= View::e($payment['recorder_name']) ?></strong> (<?= ucfirst($payment['recorder_role']) ?>)
                    <div style="font-size:0.75rem; color:var(--text-muted);"><?= View::formatDate($payment['created_at'], true) ?></div>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 0.6rem 0; color: var(--text-muted);">Diverifikasi Oleh</td>
                <td style="padding: 0.6rem 0;">
                    <?php if (!empty($payment['verifier_name'])): ?>
                        <strong><?= View::e($payment['verifier_name']) ?></strong> (<?= ucfirst($payment['verifier_role'] ?? '') ?>)
                        <div style="font-size:0.75rem; color:var(--text-muted);"><?= View::formatDate($payment['verified_at'], true) ?></div>
                    <?php else: ?>
                        <span style="color: var(--text-muted);">&mdash; Menunggu Pemeriksaan &mdash;</span>
                    <?php endif; ?>
                </td>
            </tr>

            <?php if (!empty($payment['rejection_reason'])): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.6rem 0; color: var(--danger-text); font-weight: 600;">Alasan Ditolak</td>
                    <td style="padding: 0.6rem 0; color: var(--danger-text);"><?= View::e($payment['rejection_reason']) ?></td>
                </tr>
            <?php endif; ?>

            <?php if (!empty($payment['cancellation_reason'])): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.6rem 0; color: var(--text-muted); font-weight: 600;">Dibatalkan Oleh</td>
                    <td style="padding: 0.6rem 0;">
                        <strong><?= View::e($payment['canceller_name'] ?? 'Pengurus') ?></strong>
                        <div style="font-size:0.75rem; color:var(--text-muted);"><?= View::formatDate($payment['cancelled_at'], true) ?></div>
                        <div style="font-size:0.8rem; color:var(--danger-text); margin-top:2px;">Alasan: <?= View::e($payment['cancellation_reason']) ?></div>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Aksi Pengurus -->
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 0.5rem;">
            <?php if ($payment['status'] === 'menunggu_verifikasi'): ?>
                <?php if ($canVerify): ?>
                    <form method="POST" action="<?= View::url('/payments/' . $payment['id'] . '/verify') ?>" onsubmit="return confirm('Apakah Anda yakin menyetujui transaksi ini?');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            &check; Setujui &amp; Sahkan Pembayaran
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger" style="width: 100%;" onclick="openModal('rejectPaymentModal', {formAction: '<?= View::url('/payments/' . $payment['id'] . '/reject') ?>', targetName: '<?= View::e($payment['receipt_number']) ?>'})">
                        &times; Tolak Transaksi Pembayaran
                    </button>
                <?php else: ?>
                    <div class="alert alert-info" style="margin-bottom:0;">
                        Pembayaran ini sedang menunggu verifikasi dari Ketua Angkatan.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($payment['status'] === 'terverifikasi'): ?>
                <button type="button" class="btn btn-secondary" style="color: var(--danger-text); border-color: var(--danger-border);" onclick="openModal('cancelTransactionModal', {formAction: '<?= View::url('/payments/' . $payment['id'] . '/cancel') ?>', targetName: '<?= View::e($payment['receipt_number']) ?>'})">
                    Batalkan Transaksi (Audit Reversal)
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alokasi ke Tahapan Cicilan -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Alokasi Pembayaran ke Tahap Cicilan</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tahap</th>
                    <th>Nama Tahap Cicilan</th>
                    <th class="text-right">Target Tahap</th>
                    <th class="text-right">Nominal Dialokasikan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allocations)): ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 1.5rem; color: var(--text-muted);">
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
                            <td><strong>Tahap <?= (int)$al['stage_number'] ?></strong></td>
                            <td><?= View::e($al['stage_name']) ?></td>
                            <td class="text-right"><?= View::formatRupiah($al['target_amount']) ?></td>
                            <td class="text-right" style="font-weight: 700; color: var(--brand);">
                                <?= View::formatRupiah($al['allocated_amount']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: var(--bg-subtle); font-weight: 700;">
                        <td colspan="3" class="text-right">Total Alokasi:</td>
                        <td class="text-right" style="color: var(--success-text); font-size: 1rem;">
                            <?= View::formatRupiah($totalAlloc) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
