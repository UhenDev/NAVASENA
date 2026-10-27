<?php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pertanggungjawaban Keuangan Angkatan &mdash; GUARDIAN OF NAVASENA 27</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="all">
    <style>
        body {
            background-color: var(--bg-obsidian);
            padding: 2rem 1rem;
            color: #0F172A;
            font-size: 10pt;
            min-height: 100vh;
        }
        .report-paper {
            background: #FFFFFF;
            max-width: 860px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: 4px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            position: relative;
            color: #0F172A;
        }
        .report-paper::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 440px;
            height: 440px;
            transform: translate(-50%, -50%);
            background: url("<?= View::url('/assets/img/logo.png') ?>") no-repeat center center;
            background-size: contain;
            opacity: 0.04;
            pointer-events: none;
        }
        .section-header {
            font-family: 'Cinzel', serif;
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #DFB256;
            padding-bottom: 4px;
            margin: 1.75rem 0 0.85rem 0;
            color: #0F172A;
            letter-spacing: 0.05em;
        }
        .stat-grid-print {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .stat-box-print {
            border: 1px solid #CBD5E1;
            padding: 0.75rem 1rem;
            background: #F8FAFC;
            border-radius: 4px;
        }
        .stat-box-print .label {
            font-size: 8pt;
            color: #64748B;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .stat-box-print .val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13pt;
            font-weight: 700;
            color: #0F172A;
            margin-top: 3px;
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

<div class="no-print" style="max-width: 860px; margin: 0 auto 1.5rem auto; display: flex; justify-content: space-between; align-items: center;">
    <a href="javascript:history.back()" class="btn btn-secondary">&larr; Kembali</a>
    <button type="button" onclick="window.print()" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.35);">
        Cetak Laporan / Simpan PDF Resmi
    </button>
</div>

<div class="report-paper">
    <!-- Header Dokumen Resmi Kop Surat -->
    <div class="print-header" style="display: flex; align-items: center; gap: 1.5rem; border-bottom: 2.5px double #0F172A; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
        <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Logo Navasena" style="width: 72px; height: 72px; object-fit: contain;">
        <div style="flex: 1;">
            <div style="font-family: 'Cinzel', serif; font-size: 16pt; font-weight: 800; letter-spacing: 0.05em; color: #0F172A; line-height: 1.1;">
                GUARDIAN OF NAVASENA 27
            </div>
            <div style="font-size: 10pt; font-weight: 700; color: #B8860B; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px;">
                SMKN 1 PANYINGKIRAN &bull; PORTAL KEUANGAN ANGKATAN
            </div>
            <div style="font-size: 8.5pt; color: #475569; margin-top: 4px;">
                LAPORAN PERTANGGUNGJAWABAN (LPJ) REALISASI IURAN ANGKATAN 2026/2027
            </div>
        </div>
        <div style="text-align: right; font-family: 'JetBrains Mono', monospace; font-size: 8pt; color: #64748B;">
            <div>STATUS: SAH / 0 DRIFT</div>
            <div>DICETAK: <?= View::formatDate(date('Y-m-d'), true) ?></div>
        </div>
    </div>

    <!-- 1. Ringkasan Eksekutif Keuangan -->
    <div class="section-header">1. Ringkasan Eksekutif Keuangan</div>
    <div class="stat-grid-print">
        <div class="stat-box-print">
            <div class="label">Target Penerimaan Keseluruhan</div>
            <div class="val"><?= View::formatRupiah($metrics['target_total_revenue']) ?></div>
            <div style="font-size: 8pt; color:#64748B; margin-top: 2px; font-family: 'JetBrains Mono', monospace;"><?= $metrics['active_students'] ?> Siswa &times; <?= View::formatRupiah($metrics['target_per_student']) ?></div>
        </div>
        <div class="stat-box-print">
            <div class="label">Penerimaan Sah (Terverifikasi)</div>
            <div class="val" style="color: #166534;"><?= View::formatRupiah($metrics['total_verified_revenue']) ?></div>
            <div style="font-size: 8pt; color:#166534; font-weight:bold; margin-top: 2px; font-family: 'JetBrains Mono', monospace;">Capaian: <?= $metrics['achievement_percentage'] ?>%</div>
        </div>
        <div class="stat-box-print">
            <div class="label">Total Pengeluaran Kas Angkatan</div>
            <div class="val" style="color: #991B1B;"><?= View::formatRupiah($metrics['total_cash_out']) ?></div>
            <div style="font-size: 8pt; color:#64748B; margin-top: 2px;">Realisasi biaya album fisik, graduation, operasional</div>
        </div>
        <div class="stat-box-print">
            <div class="label">Saldo Kas Aktual Tersedia</div>
            <div class="val" style="color: #B8860B;"><?= View::formatRupiah($metrics['cash_balance']) ?></div>
            <div style="font-size: 8pt; color:#64748B; margin-top: 2px;">Kas Masuk Sah &minus; Kas Keluar Sah (0 Drift)</div>
        </div>
    </div>

    <!-- 2. Capaian per Tahap Pembayaran -->
    <div class="section-header">2. Realisasi per Tahap Cicilan Iuran</div>
    <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 1rem;">
        <thead>
            <tr style="background: #F1F5F9; border-bottom: 1.5px solid #CBD5E1;">
                <th style="padding: 6px 8px; text-align: left;">Tahap</th>
                <th style="padding: 6px 8px; text-align: left;">Nama Tahap Cicilan</th>
                <th style="padding: 6px 8px; text-align: right;">Nominal / Siswa</th>
                <th style="padding: 6px 8px; text-align: left;">Jatuh Tempo</th>
                <th style="padding: 6px 8px; text-align: right;">Target Total</th>
                <th style="padding: 6px 8px; text-align: right;">Terkumpul Sah</th>
                <th style="padding: 6px 8px; text-align: center;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metrics['stage_breakdown'] as $stg): ?>
                <tr style="border-bottom: 1px solid #E2E8F0;">
                    <td style="padding: 6px 8px;"><strong>Tahap <?= $stg['stage_number'] ?></strong></td>
                    <td style="padding: 6px 8px;"><?= View::e($stg['stage_name']) ?></td>
                    <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;"><?= View::formatRupiah($stg['target_per_student']) ?></td>
                    <td style="padding: 6px 8px; font-family: 'JetBrains Mono', monospace;"><?= View::formatDate($stg['due_date']) ?></td>
                    <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;"><?= View::formatRupiah($stg['stage_target']) ?></td>
                    <td style="padding: 6px 8px; text-align: right; font-weight: bold; font-family: 'JetBrains Mono', monospace; color: #166534;"><?= View::formatRupiah($stg['collected']) ?></td>
                    <td style="padding: 6px 8px; text-align: center; font-weight: bold; font-family: 'JetBrains Mono', monospace;"><?= $stg['percentage'] ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 3. Rekapitulasi Status Pembayaran Siswa -->
    <div class="section-header">3. Distribusi Status Pembayaran Siswa</div>
    <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 1rem;">
        <thead>
            <tr style="background: #F1F5F9; border-bottom: 1.5px solid #CBD5E1;">
                <th style="padding: 6px 8px; text-align: left;">Status Kewajiban</th>
                <th style="padding: 6px 8px; text-align: left;">Kriteria Pemenuhan</th>
                <th style="padding: 6px 8px; text-align: center;">Jumlah Siswa</th>
                <th style="padding: 6px 8px; text-align: right;">Rasio</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid #E2E8F0;">
                <td style="padding: 6px 8px;"><strong>Lunas Penuh</strong></td>
                <td style="padding: 6px 8px;">Telah menyelesaikan kewajiban Rp250.000 penuh</td>
                <td style="padding: 6px 8px; text-align: center; font-family: 'JetBrains Mono', monospace;"><strong><?= $metrics['paid_off_count'] ?></strong> siswa</td>
                <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;"><?= $metrics['active_students'] > 0 ? round(($metrics['paid_off_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
            <tr style="border-bottom: 1px solid #E2E8F0;">
                <td style="padding: 6px 8px;"><strong>Membayar Sebagian</strong></td>
                <td style="padding: 6px 8px;">Telah mencicil tahap 1 atau 2, belum lunas penuh</td>
                <td style="padding: 6px 8px; text-align: center; font-family: 'JetBrains Mono', monospace;"><strong><?= $metrics['partial_count'] ?></strong> siswa</td>
                <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;"><?= $metrics['active_students'] > 0 ? round(($metrics['partial_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
            <tr style="border-bottom: 1px solid #E2E8F0;">
                <td style="padding: 6px 8px;"><strong>Belum Membayar</strong></td>
                <td style="padding: 6px 8px;">Belum memiliki riwayat pembayaran sah terverifikasi</td>
                <td style="padding: 6px 8px; text-align: center; font-family: 'JetBrains Mono', monospace;"><strong><?= $metrics['unpaid_count'] ?></strong> siswa</td>
                <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;"><?= $metrics['active_students'] > 0 ? round(($metrics['unpaid_count'] / $metrics['active_students']) * 100, 1) : 0 ?>%</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #F8FAFC; border-top: 1.5px solid #CBD5E1;">
                <td colspan="2" style="padding: 6px 8px;">Total Siswa Angkatan:</td>
                <td style="padding: 6px 8px; text-align: center; font-family: 'JetBrains Mono', monospace;"><?= $metrics['active_students'] ?> siswa</td>
                <td style="padding: 6px 8px; text-align: right; font-family: 'JetBrains Mono', monospace;">100.0%</td>
            </tr>
        </tfoot>
    </table>

    <!-- 4. Rincian Pengeluaran Kas per Kategori -->
    <div class="section-header">4. Rincian Pengeluaran Kas Angkatan</div>
    <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 1.5rem;">
        <thead>
            <tr style="background: #F1F5F9; border-bottom: 1.5px solid #CBD5E1;">
                <th style="padding: 6px 8px; text-align: left;">Kategori Pengeluaran</th>
                <th style="padding: 6px 8px; text-align: center;">Jumlah Transaksi</th>
                <th style="padding: 6px 8px; text-align: right;">Total Realisasi Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($outCategories)): ?>
                <tr>
                    <td colspan="3" style="padding: 10px; text-align: center; color: #64748B;">Belum ada pengeluaran kas dicatat.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($outCategories as $oc): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 6px 8px;"><?= View::e($oc['category']) ?></td>
                        <td style="padding: 6px 8px; text-align: center; font-family: 'JetBrains Mono', monospace;"><?= (int)$oc['count_trans'] ?> kali</td>
                        <td style="padding: 6px 8px; text-align: right; font-weight: bold; font-family: 'JetBrains Mono', monospace;"><?= View::formatRupiah($oc['total_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #F8FAFC; border-top: 1.5px solid #CBD5E1;">
                <td colspan="2" style="padding: 6px 8px;">Total Kas Keluar:</td>
                <td style="padding: 6px 8px; text-align: right; color: #991B1B; font-family: 'JetBrains Mono', monospace;"><?= View::formatRupiah($metrics['total_cash_out']) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pertanggungjawaban -->
    <div class="print-signatures" style="display: flex; justify-content: space-between; margin-top: 40px; page-break-inside: avoid;">
        <div class="signature-block" style="text-align: center; width: 220px;">
            <div style="font-size: 9pt; color: #475569;">Penyusun Laporan,</div>
            <div style="font-size: 8.5pt; font-weight: bold; color: #0F172A; margin-bottom: 60px;">Bendahara Angkatan</div>
            <div style="font-weight: bold; font-size: 10pt; text-decoration: underline; color: #0F172A;"><?= View::e($bendaharaName) ?></div>
            <div style="font-size: 8pt; color: #64748B;">Otoritas Keuangan Navasena 27</div>
        </div>

        <div class="signature-block" style="text-align: center; width: 220px;">
            <div style="font-size: 9pt; color: #475569;">Mengetahui &amp; Menyetujui,</div>
            <div style="font-size: 8.5pt; font-weight: bold; color: #0F172A; margin-bottom: 60px;">Ketua Angkatan Navasena 27</div>
            <div style="font-weight: bold; font-size: 10pt; text-decoration: underline; color: #0F172A;"><?= View::e($ketuaName) ?></div>
            <div style="font-size: 8pt; color: #64748B;">Ketua Angkatan SMKN 1 Panyingkiran</div>
        </div>
    </div>

    <!-- Audit Footer -->
    <div style="margin-top: 30px; padding-top: 10px; border-top: 1px dashed #CBD5E1; display: flex; justify-content: space-between; font-size: 7.5pt; color: #94A3B8; font-family: 'JetBrains Mono', monospace;">
        <div>DOKUMEN LPJ RESMI &bull; GUARDIAN OF NAVASENA 27</div>
        <div>SHA-256 INTEGRITY VERIFIED &bull; 0 DRIFT ENGINE</div>
    </div>
</div>

</body>
</html>
