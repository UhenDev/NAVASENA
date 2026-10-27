<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Data 492 Siswa &bull; SMKN 1 Panyingkiran
        </div>
        <h1 class="page-title">
            Data Siswa <span class="gold-highlight">Angkatan 27</span>
        </h1>
        <p class="page-subtitle">Daftar anggota angkatan 16 kelas, kewajiban iuran Rp250.000, dan status realisasi cicilan</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= View::url('/students/create') ?>" class="btn btn-secondary">+ Tambah Siswa</a>
        <a href="<?= View::url('/students/import') ?>" class="btn btn-primary">+ IMPOR CSV/EXCEL</a>
        <a href="<?= View::url('/students/export') ?>" class="btn btn-secondary">Ekspor Data</a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= View::url('/students') ?>" class="filter-bar">
    <div style="flex: 2; min-width: 220px;">
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
            <option value="sebagian" <?= $paymentStatus === 'sebagian' ? 'selected' : '' ?>>Mencicil Sebagian</option>
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

    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
    <a href="<?= View::url('/students') ?>" class="btn btn-secondary btn-sm">Reset</a>
</form>

<!-- Glass Table Data Siswa -->
<div class="card card-gold" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem;">NIS</th>
                    <th>NAMA LENGKAP SISWA</th>
                    <th>KELAS</th>
                    <th class="text-right">TERBAYAR</th>
                    <th class="text-right">SISA</th>
                    <th class="text-center">STATUS IURAN</th>
                    <th class="text-center" style="padding-right: 1.5rem;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem 1rem; color: var(--text-muted);">
                            Tidak ada data siswa yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td class="tabular-nums" style="padding-left: 1.5rem; font-weight: 700; color: var(--gold-solid);">
                                <a href="<?= View::url('/students/' . $s['id']) ?>">
                                    <?= View::e($s['student_number']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= View::url('/students/' . $s['id']) ?>" style="font-weight: 700; color: #FFF;">
                                    <?= View::e($s['full_name']) ?>
                                </a>
                                <?php if (!empty($s['phone'])): ?>
                                    <div class="tabular-nums" style="font-size:0.75rem; color:var(--text-muted);"><?= View::e($s['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-family: var(--font-mono); font-weight: 600; color: #38BDF8; font-size: 0.85rem;">
                                <?= View::e($s['class_name']) ?>
                            </td>
                            <td class="text-right tabular-nums" style="font-family: var(--font-mono); font-weight: 700; color: var(--verified-fg);">
                                <?= View::formatRupiah($s['total_paid']) ?>
                            </td>
                            <td class="text-right tabular-nums" style="font-family: var(--font-mono); font-weight: 700; color: <?= (float)$s['remaining_balance'] > 0 ? 'var(--gold-solid)' : 'var(--verified-fg)' ?>;">
                                <?= (float)$s['remaining_balance'] > 0 ? View::formatRupiah($s['remaining_balance']) : 'Rp 0 [Lunas]' ?>
                            </td>
                            <td class="text-center">
                                <?php if ($s['payment_status'] === 'lunas'): ?>
                                    <span class="status-pill status-verified">LUNAS</span>
                                <?php elseif ($s['payment_status'] === 'sebagian'): ?>
                                    <span class="status-pill status-pending">SEBAGIAN</span>
                                <?php else: ?>
                                    <span class="status-pill status-void">BELUM BAYAR</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="padding-right: 1.5rem; white-space: nowrap;">
                                <a href="<?= View::url('/students/' . $s['id']) ?>" class="btn btn-secondary btn-sm" title="Lihat Profil">Detail</a>
                                <a href="<?= View::url('/payments/create?student_id=' . $s['id']) ?>" class="btn btn-primary btn-sm" title="Catat Pembayaran">+ Bayar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
