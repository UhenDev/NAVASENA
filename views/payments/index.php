<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                MODUL BENDAHARA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">MOD-PAY-27</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Pengelolaan Pembayaran Iuran</h1>
        <p class="page-subtitle">Pencatatan kas masuk, nomor kuitansi otomatis KW-YYYYMMDD-XXXX, dan verifikasi transaksi</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            + Catat Pembayaran Baru
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= View::url('/payments') ?>" class="filter-bar" style="margin: 0; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 220px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Pencarian</label>
            <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari No. Kuitansi, NIS, atau Nama Siswa..." class="form-control">
        </div>

        <div style="flex: 1.2; min-width: 180px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Status Transaksi</label>
            <select name="status" class="form-control">
                <option value="all">Semua Status (All)</option>
                <option value="menunggu_verifikasi" <?= $status === 'menunggu_verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi (<?= $pendingCount ?>)</option>
                <option value="terverifikasi" <?= $status === 'terverifikasi' ? 'selected' : '' ?>>Terverifikasi (Sah)</option>
                <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                <option value="dibatalkan" <?= $status === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;">Metode Bayar</label>
            <select name="method" class="form-control">
                <option value="all">Semua Metode</option>
                <option value="tunai" <?= $method === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                <option value="transfer" <?= $method === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
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
            <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.25rem;">Terapkan</button>
            <a href="<?= View::url('/payments') ?>" class="btn btn-secondary" style="padding: 0.65rem 1rem;">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Data Pembayaran -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.4);">
        <div style="font-weight: 600; font-size: 0.95rem; color: var(--gold-text); display: flex; align-items: center; gap: 0.5rem;">
            <span>Daftar Transaksi Pembayaran Siswa</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total Ditemukan: <?= count($payments) ?> Rekaman
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">NO. KUITANSI</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">TANGGAL</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">SISWA</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">NOMINAL (RP)</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">METODE</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">CATATAN REF</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">STATUS</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">PENCATAT</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">VERIFIKASI</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            <div style="font-size: 1.1rem; margin-bottom: 0.25rem;">Tidak ada transaksi pembayaran yang ditemukan.</div>
                            <div style="font-size: 0.825rem;">Silakan periksa filter pencarian Anda atau tambahkan transaksi baru.</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td>
                                <a href="<?= View::url('/payments/' . $p['id']) ?>" style="font-family: var(--font-mono); font-weight: 700; color: var(--gold-solid); text-decoration: none; letter-spacing: 0.02em;">
                                    <?= View::e($p['receipt_number']) ?>
                                </a>
                            </td>
                            <td style="white-space: nowrap; font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary);">
                                <?= View::formatDate($p['payment_date']) ?>
                            </td>
                            <td>
                                <a href="<?= View::url('/students/' . $p['student_id']) ?>" style="font-weight: 600; color: #FFFFFF; text-decoration: none; display: block;">
                                    <?= View::e($p['student_name']) ?>
                                </a>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono); margin-top: 2px;">
                                    <?= View::e($p['class_name']) ?> &bull; NIS: <?= View::e($p['student_number']) ?>
                                </div>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; font-size: 0.95rem; color: <?= $p['status'] === 'terverifikasi' ? 'var(--verified-fg)' : ($p['status'] === 'dibatalkan' || $p['status'] === 'ditolak' ? 'var(--void-fg)' : 'var(--pending-fg)') ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td>
                                <?= View::methodBadge($p['payment_method']) ?>
                            </td>
                            <td style="max-width: 180px; font-size: 0.8rem; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= View::e($p['reference_note'] ?: '-') ?>
                            </td>
                            <td class="text-center">
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--void-fg); margin-top: 3px;">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php elseif (!empty($p['cancellation_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--text-muted); margin-top: 3px;">Batal: <?= View::e($p['cancellation_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                <?= View::e($p['recorder_name']) ?>
                            </td>
                            <td style="font-size: 0.8rem;">
                                <?php if (!empty($p['verifier_name'])): ?>
                                    <span style="color: var(--verified-fg); font-weight: 600;"><?= View::e($p['verifier_name']) ?></span>
                                    <?php if (!empty($p['verified_at'])): ?>
                                        <div style="font-size: 0.7rem; color: var(--text-muted); font-family: var(--font-mono);"><?= View::formatDate($p['verified_at'], true) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-style: italic;">Belum</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                    <a href="<?= View::url('/payments/' . $p['id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">
                                        Rincian
                                    </a>
                                    
                                    <?php if ($p['status'] === 'menunggu_verifikasi' && $canVerify): ?>
                                        <form method="POST" action="<?= View::url('/payments/' . $p['id'] . '/verify') ?>" style="display:inline;" onsubmit="return confirm('Verifikasi dan sahkan pembayaran <?= View::e($p['receipt_number']) ?>? Transaksi ini akan otomatis masuk ke Buku Kas.');">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-success btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4);">
                                                Setujui Sah
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($p['status'] === 'terverifikasi'): ?>
                                        <a href="<?= View::url('/payments/' . $p['id'] . '/receipt') ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: var(--glass-border-gold); color: var(--gold-solid);" title="Cetak Kuitansi Resmi">
                                            Kuitansi
                                        </a>
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
