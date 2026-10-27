<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Pengelolaan Pembayaran Iuran</h1>
        <p class="page-subtitle">Pencatatan pembayaran tunai/transfer, nomor kuitansi, dan verifikasi transaksi</p>
    </div>
    <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary">+ Catat Pembayaran Baru</a>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/payments') ?>" class="filter-bar">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari No. Kuitansi, NIS, atau Nama Siswa..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="status" class="form-control">
            <option value="all">Semua Status Transaksi</option>
            <option value="menunggu_verifikasi" <?= $status === 'menunggu_verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi (<?= $pendingCount ?>)</option>
            <option value="terverifikasi" <?= $status === 'terverifikasi' ? 'selected' : '' ?>>Terverifikasi (Sah)</option>
            <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            <option value="dibatalkan" <?= $status === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
        <select name="method" class="form-control">
            <option value="all">Semua Metode</option>
            <option value="tunai" <?= $method === 'tunai' ? 'selected' : '' ?>>Tunai</option>
            <option value="transfer" <?= $method === 'transfer' ? 'selected' : '' ?>>Transfer</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 130px;">
        <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control" title="Dari Tanggal">
    </div>

    <div style="flex: 1; min-width: 130px;">
        <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control" title="Sampai Tanggal">
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">Reset</a>
</form>

<!-- Tabel Data Pembayaran -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Kuitansi</th>
                    <th>Tanggal</th>
                    <th>Siswa</th>
                    <th class="text-right">Nominal</th>
                    <th>Metode</th>
                    <th>Keterangan / Ref</th>
                    <th class="text-center">Status</th>
                    <th>Dicatat Oleh</th>
                    <th>Verifikasi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                            Tidak ada transaksi pembayaran yang ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td>
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" style="font-weight: 700;">
                                    <?= View::e($p['receipt_number']) ?>
                                </a>
                            </td>
                            <td style="white-space: nowrap;"><?= View::formatDate($p['payment_date']) ?></td>
                            <td>
                                <a href="<?= View::url('/students/' . $p['student_id']) ?>" style="font-weight: 600; color: var(--text-primary);">
                                    <?= View::e($p['student_name']) ?>
                                </a>
                                <div style="font-size: 0.725rem; color: var(--text-muted);"><?= View::e($p['class_name']) ?> (<?= View::e($p['student_number']) ?>)</div>
                            </td>
                            <td class="text-right" style="font-weight: 700; color: <?= $p['status'] === 'terverifikasi' ? 'var(--success-text)' : 'var(--text-primary)' ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td><?= View::methodBadge($p['payment_method']) ?></td>
                            <td style="max-width: 180px; font-size: 0.8rem; color: var(--text-secondary);">
                                <?= View::e($p['reference_note'] ?: '-') ?>
                            </td>
                            <td class="text-center">
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--danger-text);">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php elseif (!empty($p['cancellation_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--text-muted);">Batal: <?= View::e($p['cancellation_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8rem;"><?= View::e($p['recorder_name']) ?></td>
                            <td style="font-size: 0.8rem;">
                                <?= View::e($p['verifier_name'] ?? '-') ?>
                                <?php if (!empty($p['verified_at'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);"><?= View::formatDate($p['verified_at'], true) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" class="btn btn-secondary btn-sm">Rincian</a>
                                
                                <?php if ($p['status'] === 'menunggu_verifikasi' && $canVerify): ?>
                                    <form method="POST" action="<?= View::url('/payments/' . $p['id'] . '/verify') ?>" style="display:inline;" onsubmit="return confirm('Verifikasi dan sahkan pembayaran <?= View::e($p['receipt_number']) ?>? Transaksi ini akan otomatis masuk ke Buku Kas.');">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                    </form>
                                <?php endif; ?>

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
