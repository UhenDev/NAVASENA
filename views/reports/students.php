<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Rekapitulasi Pembayaran per Siswa</h1>
        <p class="page-subtitle">Laporan posisi kewajiban, realisasi pembayaran, dan sisa tagihan siswa angkatan</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?= View::url('/reports') ?>" class="btn btn-secondary">&larr; Pusat Laporan</a>
        <a href="<?= View::url('/reports/export/students?status=' . urlencode($paymentStatus) . '&class=' . urlencode($className)) ?>" class="btn btn-primary">Unduh CSV / Excel</a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/reports/students') ?>" class="filter-bar">
    <div style="flex: 1; min-width: 160px;">
        <select name="status" class="form-control">
            <option value="all">Semua Status Iuran</option>
            <option value="belum_bayar" <?= $paymentStatus === 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
            <option value="sebagian" <?= $paymentStatus === 'sebagian' ? 'selected' : '' ?>>Membayar Sebagian</option>
            <option value="lunas" <?= $paymentStatus === 'lunas' ? 'selected' : '' ?>>Lunas Penuh</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="class" class="form-control">
            <option value="all">Semua Kelas</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= View::e($c) ?>" <?= $className === $c ? 'selected' : '' ?>><?= View::e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
    <a href="<?= View::url('/reports/students') ?>" class="btn btn-secondary">Reset</a>
</form>

<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Siswa (NIS)</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th class="text-right">Kewajiban (Rp)</th>
                    <th class="text-right">Terbayar Sah (Rp)</th>
                    <th class="text-right">Sisa Tagihan (Rp)</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                            Tidak ada data siswa yang sesuai filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($students as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= View::e($s['student_number']) ?></td>
                            <td>
                                <a href="<?= View::url('/students/' . $s['id']) ?>" style="font-weight: 600;">
                                    <?= View::e($s['full_name']) ?>
                                </a>
                            </td>
                            <td><?= View::e($s['class_name']) ?></td>
                            <td class="text-right"><?= View::formatRupiah($s['total_obligation']) ?></td>
                            <td class="text-right" style="font-weight: 600; color: var(--success-text);">
                                <?= View::formatRupiah($s['total_paid']) ?>
                            </td>
                            <td class="text-right" style="font-weight: 600; color: <?= (float)$s['remaining_balance'] > 0 ? 'var(--danger-text)' : 'var(--text-muted)' ?>;">
                                <?= View::formatRupiah($s['remaining_balance']) ?>
                            </td>
                            <td class="text-center"><?= View::statusBadge($s['payment_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--bg-subtle); font-weight: 700; border-top: 2px solid var(--border);">
                    <td colspan="4" class="text-right">Total Akumulasi:</td>
                    <td class="text-right"><?= View::formatRupiah($sumObligation) ?></td>
                    <td class="text-right" style="color: var(--success-text);"><?= View::formatRupiah($sumPaid) ?></td>
                    <td class="text-right" style="color: var(--danger-text);"><?= View::formatRupiah($sumRemaining) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
