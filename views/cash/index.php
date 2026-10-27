<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                BUKU KAS &amp; ARUS KAS
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">CASHBOOK-REALTIME</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Buku Kas Angkatan</h1>
        <p class="page-subtitle">Pencatatan arus kas masuk, pengeluaran album/graduation/administrasi, dan saldo riil</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/cash/create') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            + Catat Transaksi Kas
        </a>
        <a href="<?= View::url('/cash/export') ?>" class="btn btn-secondary">
            Ekspor Buku Kas (CSV)
        </a>
    </div>
</div>

<!-- Ringkasan Saldo Kas -->
<div class="metrics-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card" style="border-left: 3px solid var(--verified-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Kas Masuk (Sah)</div>
        <div class="metric-value" style="font-family: var(--font-mono); color: var(--verified-fg); font-size: 1.6rem; font-weight: 800; margin: 0.35rem 0;">
            <?= View::formatRupiah($totalIn) ?>
        </div>
        <div class="metric-sub" style="color: var(--text-secondary); font-size: 0.75rem;">Penerimaan iuran terverifikasi &amp; kas masuk lain</div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--void-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Kas Keluar (Beban)</div>
        <div class="metric-value" style="font-family: var(--font-mono); color: var(--void-fg); font-size: 1.6rem; font-weight: 800; margin: 0.35rem 0;">
            <?= View::formatRupiah($totalOut) ?>
        </div>
        <div class="metric-sub" style="color: var(--text-secondary); font-size: 0.75rem;">Biaya album fisik, graduation, operasional</div>
    </div>

    <div class="metric-card" style="border-left: 3px solid var(--gold-solid); background: rgba(223, 178, 86, 0.04);">
        <div class="metric-label" style="color: var(--gold-text); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem; font-weight: 700;">Saldo Kas Aktual Tersedia</div>
        <div class="metric-value" style="font-family: var(--font-mono); color: <?= $balance >= 0 ? 'var(--gold-solid)' : 'var(--void-fg)' ?>; font-size: 1.6rem; font-weight: 800; margin: 0.35rem 0;">
            <?= View::formatRupiah($balance) ?>
        </div>
        <div class="metric-sub" style="color: var(--text-secondary); font-size: 0.75rem;">Total kas masuk sah dikurangi beban keluar</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= View::url('/cash') ?>" class="filter-bar" style="margin: 0; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Jenis Kas</label>
            <select name="type" class="form-control">
                <option value="all">Semua Jenis Kas</option>
                <option value="masuk" <?= $type === 'masuk' ? 'selected' : '' ?>>Kas Masuk (+)</option>
                <option value="keluar" <?= $type === 'keluar' ? 'selected' : '' ?>>Kas Keluar (-)</option>
            </select>
        </div>

        <div style="flex: 1.5; min-width: 180px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Kategori Beban / Kas</label>
            <select name="category" class="form-control">
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= View::e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= View::e($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Dari Tanggal</label>
            <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control">
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control">
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.25rem;">Filter</button>
            <a href="<?= View::url('/cash') ?>" class="btn btn-secondary" style="padding: 0.65rem 1rem;">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Buku Kas -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.4);">
        <div style="font-weight: 600; font-size: 0.95rem; color: var(--gold-text);">
            Mutasi Buku Kas Angkatan
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total Data: <?= count($transactions) ?> Baris
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
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">MASUK (RP)</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">KELUAR (RP)</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">PENCATAT</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">STATUS</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            Belum ada catatan transaksi buku kas yang sesuai.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $c): ?>
                        <tr style="<?= (int)$c['is_cancelled'] === 1 ? 'opacity: 0.45; text-decoration: line-through;' : '' ?>">
                            <td style="white-space: nowrap; font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary);">
                                <?= View::formatDate($c['transaction_date']) ?>
                            </td>
                            <td>
                                <?php if ($c['transaction_type'] === 'masuk'): ?>
                                    <span class="badge" style="background: var(--verified-bg); color: var(--verified-fg); border: 1px solid rgba(16, 185, 129, 0.3);">
                                        <span class="badge-dot" style="background: var(--verified-fg);"></span>Masuk
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--void-bg); color: var(--void-fg); border: 1px solid rgba(239, 68, 68, 0.3);">
                                        <span class="badge-dot" style="background: var(--void-fg);"></span>Keluar
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 0.85rem;"><?= View::e($c['category']) ?></strong>
                            </td>
                            <td>
                                <div style="color: #FFFFFF; font-size: 0.9rem;"><?= View::e($c['description']) ?></div>
                                <?php if (!empty($c['receipt_number'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--gold-solid); font-family: var(--font-mono); margin-top: 2px;">
                                        Ref Kuitansi: <?= View::e($c['receipt_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--verified-fg);">
                                <?= $c['transaction_type'] === 'masuk' ? View::formatRupiah($c['amount']) : '-' ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--void-fg);">
                                <?= $c['transaction_type'] === 'keluar' ? View::formatRupiah($c['amount']) : '-' ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);"><?= View::e($c['recorder_name']) ?></td>
                            <td class="text-center">
                                <?php if ((int)$c['is_cancelled'] === 1): ?>
                                    <span class="badge" style="background: var(--void-bg); color: var(--void-fg); border: 1px solid rgba(239, 68, 68, 0.3);" title="<?= View::e($c['cancellation_note']) ?>">Dibatalkan</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--verified-bg); color: var(--verified-fg); border: 1px solid rgba(16, 185, 129, 0.3);">Sah</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <?php if ((int)$c['is_cancelled'] === 0 && $c['payment_id'] === null): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" style="color: var(--void-fg); border-color: rgba(239, 68, 68, 0.3); padding: 0.25rem 0.6rem; font-size: 0.75rem;" onclick="openModal('cancelTransactionModal', {formAction: '<?= View::url('/cash/' . $c['id'] . '/cancel') ?>', targetName: 'Transaksi Kas #<?= $c['id'] ?> (<?= View::formatRupiah($c['amount']) ?>)'})">
                                        Batal
                                    </button>
                                <?php elseif ($c['payment_id'] !== null): ?>
                                    <a href="<?= View::url('/payments/' . $c['payment_id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-color: var(--glass-border-gold); color: var(--gold-solid);" title="Lihat di Menu Pembayaran">
                                        Kuitansi
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
