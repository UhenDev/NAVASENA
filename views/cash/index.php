<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Buku Kas Angkatan</h1>
        <p class="page-subtitle">Pencatatan arus kas masuk, pengeluaran kegiatan/administrasi, dan saldo kas riil</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= View::url('/cash/create') ?>" class="btn btn-primary">+ Catat Transaksi Kas</a>
        <a href="<?= View::url('/cash/export') ?>" class="btn btn-secondary">Ekspor Buku Kas (CSV)</a>
    </div>
</div>

<!-- Ringkasan Saldo Kas -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Kas Masuk (Sah)</div>
        <div class="metric-value success"><?= View::formatRupiah($totalIn) ?></div>
        <div class="metric-sub">Penerimaan iuran terverifikasi &amp; kas masuk lain</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Total Kas Keluar (Pengeluaran)</div>
        <div class="metric-value" style="color: var(--danger-text);"><?= View::formatRupiah($totalOut) ?></div>
        <div class="metric-sub">Biaya album, graduation, dan operasional angkatan</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Saldo Kas Aktual</div>
        <div class="metric-value highlight" style="color: <?= $balance >= 0 ? 'var(--primary)' : 'var(--danger-text)' ?>;">
            <?= View::formatRupiah($balance) ?>
        </div>
        <div class="metric-sub">Kas masuk dikurangi kas keluar</div>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/cash') ?>" class="filter-bar">
    <div style="flex: 1; min-width: 140px;">
        <select name="type" class="form-control">
            <option value="all">Semua Jenis Kas</option>
            <option value="masuk" <?= $type === 'masuk' ? 'selected' : '' ?>>Kas Masuk</option>
            <option value="keluar" <?= $type === 'keluar' ? 'selected' : '' ?>>Kas Keluar</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="category" class="form-control">
            <option value="all">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= View::e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= View::e($cat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 1; min-width: 130px;">
        <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control" title="Dari Tanggal">
    </div>

    <div style="flex: 1; min-width: 130px;">
        <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control" title="Sampai Tanggal">
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="<?= View::url('/cash') ?>" class="btn btn-secondary">Reset</a>
</form>

<!-- Tabel Buku Kas -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Keterangan Transaksi</th>
                    <th class="text-right">Masuk (Rp)</th>
                    <th class="text-right">Keluar (Rp)</th>
                    <th>Pencatat</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                            Belum ada catatan transaksi buku kas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $c): ?>
                        <tr style="<?= (int)$c['is_cancelled'] === 1 ? 'opacity: 0.6; background: #FFF5F5;' : '' ?>">
                            <td style="white-space:nowrap;"><?= View::formatDate($c['transaction_date']) ?></td>
                            <td>
                                <?php if ($c['transaction_type'] === 'masuk'): ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span>Masuk</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span>Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color:var(--text-secondary);"><?= View::e($c['category']) ?></strong></td>
                            <td>
                                <div><?= View::e($c['description']) ?></div>
                                <?php if (!empty($c['receipt_number'])): ?>
                                    <div style="font-size: 0.725rem; color: var(--brand);">
                                        No. Kuitansi: <?= View::e($c['receipt_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" style="font-weight: 600; color: var(--success-text);">
                                <?= $c['transaction_type'] === 'masuk' ? View::formatRupiah($c['amount']) : '-' ?>
                            </td>
                            <td class="text-right" style="font-weight: 600; color: var(--danger-text);">
                                <?= $c['transaction_type'] === 'keluar' ? View::formatRupiah($c['amount']) : '-' ?>
                            </td>
                            <td style="font-size:0.8rem;"><?= View::e($c['recorder_name']) ?></td>
                            <td class="text-center">
                                <?php if ((int)$c['is_cancelled'] === 1): ?>
                                    <span class="badge badge-danger" title="<?= View::e($c['cancellation_note']) ?>">Dibatalkan</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Sah</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <?php if ((int)$c['is_cancelled'] === 0 && $c['payment_id'] === null): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" style="color:var(--danger-text);" onclick="openModal('cancelTransactionModal', {formAction: '<?= View::url('/cash/' . $c['id'] . '/cancel') ?>', targetName: 'Transaksi Kas #<?= $c['id'] ?> (<?= View::formatRupiah($c['amount']) ?>)'})">
                                        Batal
                                    </button>
                                <?php elseif ($c['payment_id'] !== null): ?>
                                    <a href="<?= View::url('/payments/' . $c['payment_id']) ?>" class="btn btn-secondary btn-sm" title="Lihat di Menu Pembayaran">Kuitansi</a>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
