<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa Angkatan</h1>
        <p class="page-subtitle">Kelola data siswa, kewajiban iuran Rp250.000, dan status pembayaran</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= View::url('/students/create') ?>" class="btn btn-primary">+ Tambah Siswa</a>
        <a href="<?= View::url('/students/import') ?>" class="btn btn-secondary">Impor CSV / Excel</a>
        <a href="<?= View::url('/students/export') ?>" class="btn btn-secondary">Ekspor Data</a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/students') ?>" class="filter-bar">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Cari NIS, Nama Siswa, atau Kelas..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 140px;">
        <select name="class" class="form-control">
            <option value="all">Semua Kelas</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= View::e($c) ?>" <?= $className === $c ? 'selected' : '' ?>><?= View::e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="payment_status" class="form-control">
            <option value="all">Semua Status Iuran</option>
            <option value="belum_bayar" <?= $paymentStatus === 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
            <option value="sebagian" <?= $paymentStatus === 'sebagian' ? 'selected' : '' ?>>Membayar Sebagian</option>
            <option value="lunas" <?= $paymentStatus === 'lunas' ? 'selected' : '' ?>>Lunas Penuh</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 130px;">
        <select name="status" class="form-control">
            <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Status Aktif</option>
            <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Status Nonaktif</option>
            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Semua Siswa</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary">Reset</a>
</form>

<!-- Tabel Data Siswa -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Siswa (NIS)</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th class="text-right">Kewajiban</th>
                    <th class="text-right">Terbayar Sah</th>
                    <th class="text-right">Sisa Tagihan</th>
                    <th class="text-center">Status Iuran</th>
                    <th class="text-center">Status Siswa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                            Tidak ada data siswa yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td>
                                <a href="<?= View::url('/students/' . $s['id']) ?>" style="font-weight: 600;">
                                    <?= View::e($s['student_number']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= View::url('/students/' . $s['id']) ?>" style="font-weight: 600; color:var(--text-primary);">
                                    <?= View::e($s['full_name']) ?>
                                </a>
                                <?php if (!empty($s['phone'])): ?>
                                    <div style="font-size:0.75rem; color:var(--text-muted);"><?= View::e($s['phone']) ?></div>
                                <?php endif; ?>
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
                            <td class="text-center"><?= View::statusBadge($s['status']) ?></td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="<?= View::url('/students/' . $s['id']) ?>" class="btn btn-secondary btn-sm" title="Lihat Profil">Detail</a>
                                <a href="<?= View::url('/payments/create?student_id=' . $s['id']) ?>" class="btn btn-primary btn-sm" title="Bayar">+ Bayar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
