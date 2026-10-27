<?php
use App\Helpers\View;

function terbilang($angka) {
    $angka = abs((float)$angka);
    $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    $terbilang = '';

    if ($angka < 12) {
        $terbilang = ' ' . $baca[$angka];
    } elseif ($angka < 20) {
        $terbilang = terbilang($angka - 10) . ' Belas';
    } elseif ($angka < 100) {
        $terbilang = terbilang(floor($angka / 10)) . ' Puluh' . terbilang($angka % 10);
    } elseif ($angka < 200) {
        $terbilang = ' Seratus' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $terbilang = terbilang(floor($angka / 100)) . ' Ratus' . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        $terbilang = ' Seribu' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $terbilang = terbilang(floor($angka / 1000)) . ' Ribu' . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        $terbilang = terbilang(floor($angka / 1000000)) . ' Juta' . terbilang($angka % 1000000);
    }

    return trim($terbilang);
}

$terbilangText = terbilang((float)$payment['amount']) . ' Rupiah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi <?= View::e($payment['receipt_number']) ?> &mdash; NAVASENA MANAGEMENT</title>
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="print">
    <style>
        body {
            background-color: #F1F5F9;
            padding: 2rem 1rem;
            color: #0F172A;
        }
        .receipt-card {
            background: #FFFFFF;
            max-width: 760px;
            margin: 0 auto;
            padding: 2.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            border: 2px solid #0F2744;
            position: relative;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #0F2744;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .receipt-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 5.5rem;
            font-weight: 900;
            color: rgba(22, 101, 52, 0.08);
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            white-space: nowrap;
        }
        .receipt-row {
            display: flex;
            margin-bottom: 0.75rem;
            font-size: 0.925rem;
        }
        .receipt-col-label {
            width: 170px;
            color: #475569;
            flex-shrink: 0;
        }
        .receipt-col-dots {
            width: 15px;
            flex-shrink: 0;
        }
        .receipt-col-val {
            flex: 1;
            font-weight: 500;
        }
        .terbilang-box {
            background: #F8FAFC;
            border: 1px dashed #CBD5E1;
            padding: 0.75rem 1rem;
            font-style: italic;
            font-weight: 600;
            color: #1E293B;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .amount-highlight {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0F2744;
            background: #EFF6FF;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            display: inline-block;
            border: 1px solid #BFDBFE;
        }
        .receipt-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 2.5rem;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            width: 220px;
        }
        .sig-line {
            margin-top: 60px;
            border-bottom: 1px solid #0F172A;
            font-weight: 700;
            padding-bottom: 2px;
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 760px; margin: 0 auto 1rem auto; display: flex; justify-content: space-between; align-items: center;">
    <a href="javascript:history.back()" class="btn btn-secondary">&larr; Kembali</a>
    <button type="button" onclick="window.print()" class="btn btn-primary">Cetak Kuitansi Ini</button>
</div>

<div class="receipt-card">
    <?php if ($payment['status'] === 'terverifikasi'): ?>
        <div class="receipt-watermark">TERVERIFIKASI</div>
    <?php elseif ($payment['status'] === 'dibatalkan'): ?>
        <div class="receipt-watermark" style="color: rgba(220, 38, 38, 0.08);">DIBATALKAN</div>
    <?php endif; ?>

    <div class="receipt-header">
        <h1 style="font-size: 1.35rem; font-weight: 800; letter-spacing: 0.05em; margin: 0; color: #0F2744;">NAVASENA MANAGEMENT</h1>
        <h2 style="font-size: 1rem; font-weight: 600; color: #334155; margin: 0.2rem 0;">KUITANSI RESMI PEMBAYARAN IURAN ANGKATAN</h2>
        <p style="font-size: 0.8rem; color: #64748B; margin: 0;">Angkatan Tahun Ajaran 2026/2027 &bull; Total Kewajiban Siswa Rp250.000</p>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.875rem;">
        <div>
            Nomor Kuitansi: <strong style="color: #2563EB;"><?= View::e($payment['receipt_number']) ?></strong>
        </div>
        <div>
            Tanggal: <strong><?= View::formatDate($payment['payment_date']) ?></strong>
        </div>
    </div>

    <div class="receipt-row">
        <div class="receipt-col-label">Telah Terima Dari</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val" style="font-size: 1.05rem; font-weight: 700;">
            <?= View::e($payment['student_name']) ?>
        </div>
    </div>

    <div class="receipt-row">
        <div class="receipt-col-label">NIS &bull; Kelas</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val">
            <?= View::e($payment['student_number']) ?> &bull; Kelas <?= View::e($payment['class_name']) ?>
        </div>
    </div>

    <div class="receipt-row">
        <div class="receipt-col-label">Uang Sejumlah</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val">
            <div class="terbilang-box">
                # <?= View::e($terbilangText) ?> #
            </div>
        </div>
    </div>

    <div class="receipt-row">
        <div class="receipt-col-label">Untuk Pembayaran</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val">
            <div>Pembayaran Iuran Angkatan Navasena (Album Fisik &amp; Graduation)</div>
            <?php if (!empty($allocations)): ?>
                <div style="margin-top: 0.4rem; font-size: 0.825rem; color: #475569;">
                    <strong>Rincian Alokasi Tahap:</strong>
                    <ul style="margin: 0.2rem 0 0 1.25rem;">
                        <?php foreach ($allocations as $al): ?>
                            <li><?= View::e($al['stage_name']) ?>: <?= View::formatRupiah($al['allocated_amount']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="receipt-row" style="margin-top: 0.5rem;">
        <div class="receipt-col-label">Metode &amp; Catatan</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val">
            <?= ucfirst($payment['payment_method']) ?>
            <?= !empty($payment['reference_note']) ? ' &bull; ' . View::e($payment['reference_note']) : '' ?>
        </div>
    </div>

    <div class="receipt-row" style="margin-top: 1rem; align-items: center;">
        <div class="receipt-col-label" style="font-weight: 700; color: #0F2744;">TERBILANG RP</div>
        <div class="receipt-col-dots">:</div>
        <div class="receipt-col-val">
            <div class="amount-highlight">
                <?= View::formatRupiah($payment['amount']) ?>
            </div>
            <span style="margin-left: 0.75rem; font-size: 0.85rem; color: #64748B;">
                (Sisa Tagihan Siswa: <strong><?= View::formatRupiah($payment['remaining_balance']) ?></strong>)
            </span>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="receipt-signatures">
        <div class="sig-box">
            <div style="font-size: 0.85rem; color: #475569;">Penyetor / Siswa,</div>
            <div class="sig-line"><?= View::e($payment['student_name']) ?></div>
            <div style="font-size: 0.75rem; color: #64748B;">NIS: <?= View::e($payment['student_number']) ?></div>
        </div>

        <div class="sig-box">
            <div style="font-size: 0.85rem; color: #475569;">Penerima / Bendahara Angkatan,</div>
            <div class="sig-line"><?= View::e($payment['verifier_name'] ?? $payment['recorder_name']) ?></div>
            <div style="font-size: 0.75rem; color: #64748B;">
                <?= $payment['status'] === 'terverifikasi' ? 'Diverifikasi Sah' : 'Petugas Pencatat' ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
