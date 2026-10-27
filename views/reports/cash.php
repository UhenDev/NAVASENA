<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Laporan Arus Kas Masuk &amp; Keluar</h1>
        <p class="page-subtitle">Pernyataan mutasi kas angkatan dan rekonsiliasi saldo kas periode</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
        <a href="<?= View::url('/cash/export') ?>" class="btn btn-primary">Unduh Buku Kas (CSV)</a>
    </div>
</div>

<!-- Filter Periode -->
<form method="GET" action="<?= View::url('/reports/cash') ?>" class="filter-bar">
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <label style="font-weight:600; font-size:0.85rem;">Periode Dari:</label>
        <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control">
    </div>

    <div style="display:flex; align-items:center; gap:0.5rem;">
        <label style="font-weight:600; font-size:0.85rem;">Sampai:</label>
        <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Tampilkan</button>
    <a href="<?= View::url('/reports/cash') ?>" class="btn btn-secondary">Bulan Ini</a>
</form>

<!-- Ringkasan Periode & Kumulatif -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Kas Masuk Periode Ini</div>
        <div class="metric-value success"><?= View::formatRupiah($periodIn) ?></div>
        <div class="metric-sub"><?= View::formatDate($startDate) ?> s.d. <?= View::formatDate($endDate) ?></div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Kas Keluar Periode Ini</div>
        <div class="metric-value" style="color: var(--danger-text);"><?= View::formatRupiah($periodOut) ?></div>
        <div class="metric-sub">Total beban pengeluaran pada periode</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Surplus / (Defisit) Periode</div>
        <div class="metric-value" style="color: <?= $periodNet >= 0 ? 'var(--brand)' : 'var(--danger-text)' ?>;">
            <?= View::formatRupiah($periodNet) ?>
        </div>
        <div class="metric-sub">Net arus kas pada periode terpilih</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Saldo Kas Riil Keseluruhan</div>
        <div class="metric-value highlight"><?= View::formatRupiah($allBalance) ?></div>
        <div class="metric-sub">Total saldo kas fisik / bank saat ini</div>
    </div>
</div>

<!-- Tabel Mutasi Kas -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Keterangan Transaksi</th>
                    <th class="text-right">Kas Masuk (Rp)</th>
                    <th class="text-right">Kas Keluar (Rp)</th>
                    <th>Pencatat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                            Tidak ada mutasi kas tercatat pada rentang tanggal ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td style="white-space:nowrap;"><?= View::formatDate($tx['transaction_date']) ?></td>
                            <td>
                                <?php if ($tx['transaction_type'] === 'masuk'): ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span>Masuk</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span>Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= View::e($tx['category']) ?></strong></td>
                            <td>
                                <div><?= View::e($tx['description']) ?></div>
                                <?php if (!empty($tx['receipt_number'])): ?>
                                    <div style="font-size:0.75rem; color:var(--brand);">
                                        Ref Kuitansi: <?= View::e($tx['receipt_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" style="font-weight:600; color:var(--success-text);">
                                <?= $tx['transaction_type'] === 'masuk' ? View::formatRupiah($tx['amount']) : '-' ?>
                            </td>
                            <td class="text-right" style="font-weight:600; color:var(--danger-text);">
                                <?= $tx['transaction_type'] === 'keluar' ? View::formatRupiah($tx['amount']) : '-' ?>
                            </td>
                            <td style="font-size:0.8rem;"><?= View::e($tx['recorder_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--bg-subtle); font-weight: 700; border-top: 2px solid var(--border);">
                    <td colspan="4" class="text-right">Total Mutasi Periode:</td>
                    <td class="text-right" style="color:var(--success-text);"><?= View::formatRupiah($periodIn) ?></td>
                    <td class="text-right" style="color:var(--danger-text);"><?= View::formatRupiah($periodOut) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
