<?php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pertanggungjawaban Keuangan Angkatan &mdash; NAVASENA MANAGEMENT</title>
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="all">
    <style>
        body {
            background-color: #F8FAFC;
            padding: 2rem 1rem;
            color: #0F172A;
            font-size: 10pt;
        }
        .report-paper {
            background: #FFFFFF;
            max-width: 840px;
            margin: 0 auto;
            padding: 2.5rem 3rem;
            border: 1px solid #CBD5E1;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .section-header {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1.5px solid #0F2744;
            padding-bottom: 4px;
            margin: 1.5rem 0 0.75rem 0;
            color: #0F2744;
        }
        .stat-grid-print {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .stat-box-print {
            border: 1px solid #CBD5E1;
            padding: 0.6rem 0.8rem;
            background: #F8FAFC;
        }
        .stat-box-print .label {
            font-size: 8pt;
            color: #64748B;
            text-transform: uppercase;
            font-weight: bold;
        }
        .stat-box-print .val {
            font-size: 12pt;
            font-weight: bold;
            color: #0F172A;
            margin-top: 2px;
        }
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
            }
            .report-paper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 840px; margin: 0 auto 1.25rem auto; display: flex; justify-content: space-between; align-items: center;">
    <a href="javascript:history.back()" class="btn btn-secondary">&larr; Kembali</a>
    <button type="button" onclick="window.print()" class="btn btn-primary">Cetak Laporan / Simpan PDF</button>
</div>

<div class="report-paper">
    <!-- Header Dokumen Resmi -->
    <div class="print-header">
        <h1>NAVASENA MANAGEMENT</h1>
        <h2>LAPORAN PERTANGGUNGJAWABAN PENGELOLAAN KEUANGAN ANGKATAN</h2>
        <p>Tahun Ajaran 2026/2027 &bull; Target Siswa: <?= $metrics['active_students'] ?> Siswa Aktif &bull; Dicetak per: <?= View::formatDate(date('Y-m-d'), true) ?></p>
    </div>

    <!-- 1. Ringkasan Eksekutif Keuangan -->
    <div class="section-header">1. Ringkasan Eksekutif Keuangan</div>
    <div class="stat-grid-print">
        <div class="stat-box-print">
            <div class="label">Target Penerimaan Keseluruhan</div>
            <div class="val"><?= View::formatRupiah($metrics['target_total_revenue']) ?></div>
            <div style="font-size: 8pt; color:#64748B;"><?= $metrics['active_students'] ?> Siswa &times; <?= View::formatRupiah($metrics['target_per_student']) ?></div>
        </div>
        <div class="stat-box-print">
            <div class="label">Penerimaan Sah (Terverifikasi)</div>
            <div class="val" style="color: #166534;"><?= View::formatRupiah($metrics['total_verified_revenue']) ?></div>
            <div style="font-size: 8pt; color:#166534; font-weight:bold;">Pencapaian Target: <?= $metrics['achievement_percentage'] ?>%</div>
        </div>
        <div class="stat-box-print">
            <div class="label">Total Pengeluaran Kas Angkatan</div>
            <div class="val" style="color: #991B1B;"><?= View::formatRupiah($metrics['total_cash_out']) ?></div>
            <div style="font-size: 8pt; color:#64748B;">Realisasi biaya album, graduation, operasional</div>
        </div>
        <div class="stat-box-print">
            <div class="label">Saldo Kas Aktual Tersedia</div>
            <div class="val"><?= View::formatRupiah($metrics['cash_balance']) ?></div>
            <div style="font-size: 8pt; color:#64748B;">Total Kas Masuk Sah &minus; Total Kas Keluar Sah</div>
        </div>
    </div>

    <!-- 2. Capaian per Tahap Pembayaran -->
    <div class="section-header">2. Realisasi per Tahap Cicilan Iuran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tahap</th>
                <th>Nama Tahap Cicilan</th>
                <th class="text-right">Nominal / Siswa</th>
                <th>Jatuh Tempo</th>
                <th class="text-right">Target Total</th>
                <th class="text-right">Terkumpul Sah</th>
                <th class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metrics['stage_breakdown'] as $stg): ?>
                <tr>
                    <td><strong>Tahap <?= $stg['stage_number'] ?></strong></td>
                    <td><?= View::e($stg['stage_name']) ?></td>
                    <td class="text-right"><?= View::formatRupiah($stg['target_per_student']) ?></td>
                    <td><?= View::formatDate($stg['due_date']) ?></td>
                    <td class="text-right"><?= View::formatRupiah($stg['stage_target']) ?></td>
                    <td class="text-right" style="font-weight:bold;"><?= View::formatRupiah($stg['collected']) ?></td>
                    <td class="text-center"><strong><?= $stg['percentage'] ?>%</strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 3. Rekapitulasi Status Pembayaran Siswa -->
    <div class="section-header">3. Distribusi Status Pembayaran Siswa</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status Kewajiban</th>
                <th>Kriteria</th>
                <th class="text-center">Jumlah Siswa</th>
                <th class="text-right">Persentase Siswa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Lunas Penuh</strong></td>
                <td>Telah menyelesaikan iuran Rp250.000 penuh</td>
                <td class="text-center"><strong><?= $metrics['paid_off_count'] ?></strong> siswa</td>
                <td class="text-right"><?= $metrics['active_students'] > 0 ? round(($metrics['paid_off_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
            <tr>
                <td><strong>Membayar Sebagian</strong></td>
                <td>Telah mencicil, belum lunas penuh</td>
                <td class="text-center"><strong><?= $metrics['partial_count'] ?></strong> siswa</td>
                <td class="text-right"><?= $metrics['active_students'] > 0 ? round(($metrics['partial_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
            <tr>
                <td><strong>Belum Membayar</strong></td>
                <td>Belum memiliki riwayat pembayaran sah</td>
                <td class="text-center"><strong><?= $metrics['unpaid_count'] ?></strong> siswa</td>
                <td class="text-right"><?= $metrics['active_students'] > 0 ? round(($metrics['unpaid_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background:#F8FAFC;">
                <td colspan="2">Total Siswa Aktif:</td>
                <td class="text-center"><?= $metrics['active_students'] ?> siswa</td>
                <td class="text-right">100.0%</td>
            </tr>
        </tfoot>
    </table>

    <!-- 4. Rincian Pengeluaran Kas per Kategori -->
    <div class="section-header">4. Rincian Pengeluaran Kas Angkatan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kategori Pengeluaran</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Realisasi Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($outCategories)): ?>
                <tr>
                    <td colspan="3" class="text-center" style="color:#64748B;">Belum ada pengeluaran kas dicatat.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($outCategories as $oc): ?>
                    <tr>
                        <td><?= View::e($oc['category']) ?></td>
                        <td class="text-center"><?= (int)$oc['count_trans'] ?> transaksi</td>
                        <td class="text-right" style="font-weight:bold;"><?= View::formatRupiah($oc['total_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background:#F8FAFC;">
                <td colspan="2">Total Kas Keluar:</td>
                <td class="text-right" style="color: #991B1B;"><?= View::formatRupiah($metrics['total_cash_out']) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pertanggungjawaban -->
    <div class="print-signatures" style="margin-top: 50px;">
        <div class="signature-block">
            <div class="signature-role">Bendahara Utama Angkatan,</div>
            <div class="signature-space"></div>
            <div class="signature-name"><?= View::e($bendaharaName) ?></div>
            <div class="signature-role">NIP / ID: Pengurus Keuangan</div>
        </div>

        <div class="signature-block">
            <div class="signature-role">Mengetahui &amp; Menyetujui,<br>Ketua Angkatan Navasena</div>
            <div class="signature-space"></div>
            <div class="signature-name"><?= View::e($ketuaName) ?></div>
            <div class="signature-role">NIP / ID: Ketua Angkatan</div>
        </div>
    </div>
</div>

</body>
</html>
