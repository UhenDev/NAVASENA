<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                KAS MASUK &amp; KUITANSI
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">NAV-PAYMENTS-27</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Kuitansi &amp; Pembayaran Iuran</h1>
        <p class="page-subtitle">Daftar transaksi kas masuk, pencetakan kuitansi resmi, dan riwayat mutasi iuran siswa angkatan</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="<?= View::url('/payments/create') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28); font-weight: 700;">
            + Catat Pembayaran Baru
        </a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/payments') ?>" class="filter-bar">
    <div style="flex: 2; min-width: 220px;">
        <label>Pencarian Kuitansi / Siswa</label>
        <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari No. Kuitansi, NIS, atau Nama Siswa..." class="form-control">
    </div>

    <div style="flex: 1.2; min-width: 170px;">
        <label>Status Pembayaran</label>
        <select name="status" class="form-control">
            <option value="all">Semua Status</option>
            <option value="terverifikasi" <?= $status === 'terverifikasi' ? 'selected' : '' ?>>Terverifikasi (Sah)</option>
            <?php if ($pendingCount > 0): ?>
                <option value="menunggu_verifikasi" <?= $status === 'menunggu_verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi (<?= $pendingCount ?>)</option>
            <?php endif; ?>
            <option value="dibatalkan" <?= $status === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan (Void)</option>
            <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
        <label>Metode Pembayaran</label>
        <select name="method" class="form-control">
            <option value="all">Semua Metode</option>
            <option value="tunai" <?= $method === 'tunai' ? 'selected' : '' ?>>Tunai (Cash)</option>
            <option value="transfer" <?= $method === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 130px;">
        <label>Dari Tanggal</label>
        <input type="date" name="start_date" value="<?= View::e($startDate) ?>" class="form-control tabular-nums">
    </div>

    <div style="flex: 1; min-width: 130px;">
        <label>Sampai Tanggal</label>
        <input type="date" name="end_date" value="<?= View::e($endDate) ?>" class="form-control tabular-nums">
    </div>

    <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
        <button type="submit" class="btn btn-primary" style="height: 42px;">Terapkan</button>
        <a href="<?= View::url('/payments') ?>" class="btn btn-secondary" style="height: 42px;">Reset</a>
    </div>
</form>

<!-- Tabel Data Pembayaran -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.35rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.5);">
        <div style="font-weight: 700; font-size: 0.95rem; color: var(--gold-text); display: flex; align-items: center; gap: 0.5rem;">
            <span>Daftar Transaksi Kuitansi Sah</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total: <?= count($payments) ?> Data
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">NO. KUITANSI</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">TANGGAL</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">SISWA &amp; KELAS</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">NOMINAL (RP)</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">METODE</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em;">STATUS</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display); letter-spacing: 0.05em; min-width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
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
                                <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 2px;">
                                    Oleh: <?= View::e($p['recorder_name']) ?>
                                </div>
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
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 800; font-size: 1rem; color: <?= $p['status'] === 'terverifikasi' ? 'var(--verified-fg)' : ($p['status'] === 'dibatalkan' || $p['status'] === 'ditolak' ? 'var(--void-fg)' : 'var(--pending-fg)') ?>;">
                                <?= View::formatRupiah($p['amount']) ?>
                            </td>
                            <td>
                                <?= View::methodBadge($p['payment_method']) ?>
                                <?php if (!empty($p['reference_note'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--text-muted); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 2px;" title="<?= View::e($p['reference_note']) ?>">
                                        <?= View::e($p['reference_note']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= View::statusBadge($p['status']) ?>
                                <?php if (!empty($p['rejection_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--void-fg); margin-top: 3px;">Alasan: <?= View::e($p['rejection_reason']) ?></div>
                                <?php elseif (!empty($p['cancellation_reason'])): ?>
                                    <div style="font-size:0.7rem; color:var(--text-muted); margin-top: 3px;">Batal: <?= View::e($p['cancellation_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                    <?php if ($p['status'] === 'terverifikasi'): ?>
                                        <a href="<?= View::url('/payments/' . $p['id'] . '/receipt') ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; border-color: var(--glass-border-gold); color: var(--gold-solid);" title="Cetak Kuitansi Resmi">
                                            📄 Kuitansi
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= View::url('/payments/' . $p['id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                                        Rincian
                                    </a>
                                    
                                    <?php if ($p['status'] === 'menunggu_verifikasi' && $canVerify): ?>
                                        <form method="POST" action="<?= View::url('/payments/' . $p['id'] . '/verify') ?>" style="display:inline;" onsubmit="return confirm('Sahkan pembayaran <?= View::e($p['receipt_number']) ?>?');">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-success btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4);">
                                                Setujui Sah
                                            </button>
                                        </form>
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
