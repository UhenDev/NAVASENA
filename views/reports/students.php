<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                REKAPITULASI SISWA
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">REP-STUDENT-LEDGER</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Rekapitulasi Pembayaran per Siswa</h1>
        <p class="page-subtitle">Posisi kewajiban iuran, realisasi pembayaran sah, dan sisa kewajiban seluruh siswa angkatan</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
        <a href="<?= View::url('/reports/export/students?status=' . urlencode($paymentStatus) . '&class=' . urlencode($className)) ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            Unduh CSV / Excel
        </a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/reports/students') ?>" class="filter-bar">
    <div style="flex: 1.5; min-width: 180px;">
        <label>Status Pembayaran</label>
        <select name="status" class="form-control">
            <option value="all">Semua Status Iuran</option>
            <option value="belum_bayar" <?= $paymentStatus === 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
            <option value="sebagian" <?= $paymentStatus === 'sebagian' ? 'selected' : '' ?>>Membayar Sebagian</option>
            <option value="lunas" <?= $paymentStatus === 'lunas' ? 'selected' : '' ?>>Lunas Penuh</option>
        </select>
    </div>

    <div style="flex: 1.5; min-width: 180px;">
        <label>Rombel / Kelas</label>
        <select name="class" class="form-control">
            <option value="all">Semua Kelas</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= View::e($c) ?>" <?= $className === $c ? 'selected' : '' ?>><?= View::e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
        <a href="<?= View::url('/reports/students') ?>" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- Tabel Siswa -->
<div class="card" style="padding: 0; overflow: hidden; border-color: var(--glass-border);">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.4);">
        <div style="font-weight: 600; font-size: 0.95rem; color: var(--gold-text);">
            Daftar Status Keuangan per Siswa
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: var(--font-mono);">
            Total: <?= count($students) ?> Siswa
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: var(--gold-text); font-family: var(--font-display); width: 45px;">NO.</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NIS</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">NAMA LENGKAP SISWA</th>
                    <th style="color: var(--gold-text); font-family: var(--font-display);">KELAS</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">KEWAJIBAN</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TERBAYAR SAH</th>
                    <th class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">SISA TAGIHAN</th>
                    <th class="text-center" style="color: var(--gold-text); font-family: var(--font-display);">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 3rem 1.5rem; color: var(--text-muted);">
                            Tidak ada data siswa yang sesuai dengan parameter filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($students as $s): ?>
                        <tr>
                            <td style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-muted);"><?= $no++ ?></td>
                            <td style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--gold-solid); font-weight: 600;">
                                <?= View::e($s['student_number']) ?>
                            </td>
                            <td>
                                <a href="<?= View::url('/students/' . $s['id']) ?>" style="font-weight: 600; color: #FFFFFF; text-decoration: none;">
                                    <?= View::e($s['full_name']) ?>
                                </a>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 0.85rem;"><?= View::e($s['class_name']) ?></td>
                            <td class="text-right" style="font-family: var(--font-mono); color: var(--text-secondary);">
                                <?= View::formatRupiah($s['total_obligation']) ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: var(--verified-fg);">
                                <?= View::formatRupiah($s['total_paid']) ?>
                            </td>
                            <td class="text-right" style="font-family: var(--font-mono); font-weight: 700; color: <?= (float)$s['remaining_balance'] > 0 ? 'var(--gold-solid)' : 'var(--text-muted)' ?>;">
                                <?= View::formatRupiah($s['remaining_balance']) ?>
                            </td>
                            <td class="text-center">
                                <?= View::statusBadge($s['payment_status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: rgba(11, 16, 29, 0.75); font-weight: 700; border-top: 1px solid var(--glass-border-gold);">
                    <td colspan="4" class="text-right" style="color: var(--gold-text); font-family: var(--font-display);">TOTAL AKUMULASI ANGKATAN:</td>
                    <td class="text-right" style="font-family: var(--font-mono);"><?= View::formatRupiah($sumObligation) ?></td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--verified-fg); font-size: 1rem;"><?= View::formatRupiah($sumPaid) ?></td>
                    <td class="text-right" style="font-family: var(--font-mono); color: var(--gold-solid); font-size: 1rem;"><?= View::formatRupiah($sumRemaining) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
