<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Pusat Laporan Keuangan Angkatan</h1>
        <p class="page-subtitle">Rekapitulasi iuran siswa, laporan buku kas, dan dokumen pertanggungjawaban resmi</p>
    </div>
    <a href="<?= View::url('/reports/print') ?>" target="_blank" class="btn btn-primary">
        Cetak Laporan Pertanggungjawaban Resmi
    </a>
</div>

<!-- Navigasi Menu Laporan -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--brand); text-transform:uppercase;">Laporan Siswa</div>
            <h2 style="font-size:1.15rem; font-weight:700; margin: 0.35rem 0;">Rekap Pembayaran Siswa</h2>
            <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                Daftar lengkap kewajiban, pembayaran sah, dan sisa tagihan per siswa. Filter berdasarkan status lunas, sebagian, atau belum bayar.
            </p>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <a href="<?= View::url('/reports/students') ?>" class="btn btn-primary btn-sm">Buka Laporan</a>
            <a href="<?= View::url('/reports/export/students') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>

    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--brand); text-transform:uppercase;">Laporan Tahap</div>
            <h2 style="font-size:1.15rem; font-weight:700; margin: 0.35rem 0;">Rekap Progres Cicilan</h2>
            <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                Pantauan realisasi penerimaan per tahap (DP Pertama Rp100k, Cicilan Kedua Rp100k, Pelunasan Rp50k) dan persentase ketercapaian.
            </p>
        </div>
        <div>
            <a href="<?= View::url('/reports/installments') ?>" class="btn btn-primary btn-sm">Buka Laporan Tahap</a>
        </div>
    </div>

    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--brand); text-transform:uppercase;">Laporan Kas</div>
            <h2 style="font-size:1.15rem; font-weight:700; margin: 0.35rem 0;">Arus Kas Masuk &amp; Keluar</h2>
            <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                Buku kas masuk dan keluar per periode tanggal, rincian biaya pengeluaran angkatan, dan rekonsiliasi saldo kas aktual.
            </p>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <a href="<?= View::url('/reports/cash') ?>" class="btn btn-primary btn-sm">Buka Buku Kas</a>
            <a href="<?= View::url('/cash/export') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>
</div>

<!-- Statistik Laporan Analitik -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- Rekap Metode Pembayaran -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Penerimaan Sah Berdasarkan Metode</h2>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Metode</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-right">Total Sah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($methods)): ?>
                        <tr>
                            <td colspan="3" class="text-center" style="padding: 1.5rem; color: var(--text-muted);">
                                Belum ada pembayaran sah terverifikasi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($methods as $m): ?>
                            <tr>
                                <td><?= View::methodBadge($m['payment_method']) ?></td>
                                <td class="text-center"><strong><?= (int)$m['count_trans'] ?></strong> transaksi</td>
                                <td class="text-right" style="font-weight: 700; color: var(--success-text);">
                                    <?= View::formatRupiah($m['total_amount']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap Pengeluaran Kas per Kategori -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Rincian Pengeluaran Kas per Kategori</h2>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategori Pengeluaran</th>
                        <th class="text-center">Frekuensi</th>
                        <th class="text-right">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($outCategories)): ?>
                        <tr>
                            <td colspan="3" class="text-center" style="padding: 1.5rem; color: var(--text-muted);">
                                Belum ada transaksi pengeluaran kas tercatat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($outCategories as $oc): ?>
                            <tr>
                                <td><strong><?= View::e($oc['category']) ?></strong></td>
                                <td class="text-center"><?= (int)$oc['count_trans'] ?> kali</td>
                                <td class="text-right" style="font-weight: 700; color: var(--danger-text);">
                                    <?= View::formatRupiah($oc['total_amount']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
