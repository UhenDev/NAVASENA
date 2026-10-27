<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                LAPORAN KAS PERIODIK
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">REP-CASH-FLOW</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Laporan Arus Kas Masuk &amp; Keluar</h1>
        <p class="page-subtitle">Pernyataan mutasi kas angkatan dan rekonsiliasi saldo kas periode terpilih</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
        <a href="<?= View::url('/cash/export') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            Unduh Buku Kas (CSV)
        </a>
    </div>
</div>

<!-- Filter Periode -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= View::url('/reports/cash') ?>" class="filter-bar" style="margin: 0; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 200px;">
            <label style="font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Dari:</label>
            <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control">
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 200px;">
            <label style="font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Sampai:</label>
            <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control">
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="<?= View::url('/reports/cash') ?>" class="btn btn-secondary">Bulan Ini</a>
        </div>
    </form>
</div>

<!-- Ringkasan Periode & Kumulatif -->
<div class="metrics-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card" style="border-left: 3px solid var(--verified-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Kas Masuk Periode Ini</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: var(--verified-fg); margin: 0.35rem 0;">
            <?= View::formatRupiah($periodIn) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);"><?= View::formatDate($startDate) ?> s.d. <?= View::formatDate($endDate) ?></div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--void-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Kas Keluar Periode Ini</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: var(--void-fg); margin: 0.35rem 0;">
            <?= View::formatRupiah($periodOut) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Total beban operasional pada periode</div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--gold-solid);">
        <div class="metric-label" style="color: var(--gold-text); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Net Surplus / Defisit</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: <?= $periodNet >= 0 ? 'var(--gold-solid)' : 'var(--void-fg)' ?>; margin: 0.35rem 0;">
            <?= View::formatRupiah($periodNet) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Net mutasi arus kas pada periode</div>
    </div>

    <div class="metric-card" style="border-left: 3px solid #3B82F6; background: rgba(59, 130, 246, 0.05);">
        <div class="metric-label" style="color: #93C5FD; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Saldo Kas Riil Keseluruhan</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.6rem; font-weight: 800; color: #60A5FA; margin: 0.35rem 0;">
            <?= View::formatRupiah($allBalance) ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Total saldo kas fisik &amp; bank saat ini</div>
    </div>
</div>

<!-- Tabel Mutasi Kas -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.4);">
        <div style="font-weight: 600; font-size: 0.95rem; color: var(--gold-text);">
            Rincian Mutasi Kas Periode
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total: <?= count($transactions) ?> Baris Transaksi
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">TANGGAL</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">JENIS</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">KATEGORI</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">KETERANGAN TRANSAKSI</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">KAS MASUK (RP)</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">KAS KELUAR (RP)</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">PENCATAT</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            Tidak ada mutasi kas tercatat pada rentang tanggal ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td style="white-space: nowrap; font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary);">
                                <?= View::formatDate($tx['transaction_date']) ?>
                            </td>
                            <td>
                                <?php if ($tx['transaction_type'] === 'masuk'): ?>
                                    <span class="badge" style="background: var(--verified-bg); color: var(--verified-fg); border: 1px solid rgba(16, 185, 129, 0.3);">
                                        <span class="badge-dot" style="background: var(--verified-fg);"></span>Masuk
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--void-bg); color: var(--void-fg); border: 1px solid rgba(239, 68, 68, 0.3);">
                                        <span class="badge-dot" style="background: var(--void-fg);"></span>Keluar
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: var(--text-primary);"><?= View::e($tx['category']) ?></strong></td>
                            <td>
                                <div style="color: #FFFFFF; font-size: 0.9rem;"><?= View::e($tx['description']) ?></div>
                                <?php if (!empty($tx['receipt_number'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--gold-solid); font-family: var(--font-mono); margin-top: 2px;">
                                        Ref Kuitansi: <?= View::e($tx['receipt_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--verified-fg);">
                                <?= $tx['transaction_type'] === 'masuk' ? View::formatRupiah($tx['amount']) : '-' ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--void-fg);">
                                <?= $tx['transaction_type'] === 'keluar' ? View::formatRupiah($tx['amount']) : '-' ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);"><?= View::e($tx['recorder_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: rgba(11, 16, 29, 0.75); font-weight: 700; border-top: 1px solid var(--glass-border-gold);">
                    <td colspan="4" class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TOTAL MUTASI PERIODE:</td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--verified-fg); font-size: 1rem;"><?= View::formatRupiah($periodIn) ?></td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--void-fg); font-size: 1rem;"><?= View::formatRupiah($periodOut) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
