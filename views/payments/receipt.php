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
$auditHash = strtoupper(substr(hash('sha256', $payment['receipt_number'] . $payment['amount'] . $payment['payment_date'] . 'NAVASENA27'), 0, 24));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi <?= View::e($payment['receipt_number']) ?> &mdash; GUARDIAN OF NAVASENA 27</title>
    <link rel="icon" type="image/png" href="<?= View::url('/assets/img/logo.png') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="print">
    <style>
        .screen-receipt-wrapper {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .screen-receipt-card {
            background: rgba(11, 16, 29, 0.9);
            border: 1px solid var(--glass-border-gold);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(0,0,0,0.8), var(--gold-glow);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .screen-receipt-card::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 380px;
            height: 380px;
            background: url('<?= View::url('/assets/img/logo.png') ?>') center center no-repeat;
            background-size: contain;
            opacity: 0.045;
            pointer-events: none;
        }
    </style>
</head>
<body style="background-color: var(--bg-obsidian); padding-bottom: 2rem;">

<div class="no-print screen-receipt-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <a href="javascript:history.back()" class="btn btn-secondary">&larr; Kembali</a>
    <button type="button" onclick="window.print()" class="btn btn-primary" style="gap:0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        CETAK KUITANSI RESMI (PDF)
    </button>
</div>

<div class="screen-receipt-wrapper">
    <div class="screen-receipt-card receipt-card">
        <div class="receipt-watermark-print"></div>

        <!-- Kop Surat Resmi Navasena -->
        <div class="print-kop" style="display:flex; align-items:center; gap:1.25rem; border-bottom: 2px solid var(--gold-solid); padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
            <div class="print-kop-crest" style="width:72px; height:72px; flex-shrink:0;">
                <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Official Crest" style="width:100%; height:100%; object-fit:contain; filter:drop-shadow(0 4px 12px rgba(223,178,86,0.35));">
            </div>
            <div class="print-kop-text" style="flex:1; text-align:center;">
                <h1 style="font-family:var(--font-display); font-size:1.35rem; font-weight:800; letter-spacing:0.08em; background:var(--gold-gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin:0;">
                    GUARDIAN OF NAVASENA 27
                </h1>
                <h2 style="font-family:var(--font-display); font-size:0.95rem; font-weight:700; color:var(--gold-solid); margin:0.15rem 0;">
                    KUITANSI RESMI PEMBAYARAN IURAN ANGKATAN
                </h2>
                <p style="font-size:0.775rem; color:var(--text-muted); margin:0;">
                    SMKN 1 Panyingkiran &bull; Angkatan 2026/2027 &bull; Total Kewajiban Siswa Rp250.000
                </p>
            </div>
        </div>

        <!-- Meta Kuitansi -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; font-size:0.875rem;" class="tabular-nums">
            <div>
                Nomor Kuitansi: <strong class="receipt-code" style="color:var(--gold-solid); font-size:1.05rem;"><?= View::e($payment['receipt_number']) ?></strong>
            </div>
            <div>
                Tanggal Transaksi: <strong><?= View::formatDate($payment['payment_date']) ?></strong>
            </div>
        </div>

        <!-- Data Penyetor & Pembayaran -->
        <div class="receipt-row">
            <div class="receipt-col-label">Telah Terima Dari</div>
            <div class="receipt-col-dots">:</div>
            <div class="receipt-col-val" style="font-size: 1.1rem; font-weight: 800; color:#FFF;">
                <?= View::e($payment['student_name']) ?>
            </div>
        </div>

        <div class="receipt-row">
            <div class="receipt-col-label">NIS &bull; Kelas</div>
            <div class="receipt-col-dots">:</div>
            <div class="receipt-col-val tabular-nums">
                <?= View::e($payment['student_number']) ?> &bull; <?= View::e($payment['class_name']) ?>
            </div>
        </div>

        <div class="receipt-row">
            <div class="receipt-col-label">Uang Sejumlah</div>
            <div class="receipt-col-dots">:</div>
            <div class="receipt-col-val">
                <div class="terbilang-box" style="background:rgba(223,178,86,0.08); border:1px dashed var(--glass-border-gold); padding:0.65rem 1rem; border-radius:var(--radius-sm); color:var(--gold-text); font-style:italic; font-weight:700;">
                    # <?= View::e($terbilangText) ?> #
                </div>
            </div>
        </div>

        <div class="receipt-row">
            <div class="receipt-col-label">Untuk Pembayaran</div>
            <div class="receipt-col-dots">:</div>
            <div class="receipt-col-val">
                <div>Iuran Angkatan Guardian of Navasena 27 (Album Fisik &amp; Graduation)</div>
                <?php if (!empty($allocations)): ?>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-secondary);">
                        <strong style="color:var(--gold-text);">Rincian Alokasi Tahap Cicilan:</strong>
                        <ul style="margin: 0.25rem 0 0 1.25rem; line-height: 1.6;" class="tabular-nums">
                            <?php foreach ($allocations as $al): ?>
                                <li><?= View::e($al['stage_name']) ?>: <strong><?= View::formatRupiah($al['allocated_amount']) ?></strong></li>
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
                <span class="badge badge-neutral" style="text-transform:uppercase; font-size:0.7rem;"><?= ucfirst($payment['payment_method']) ?></span>
                <?= !empty($payment['reference_note']) ? ' &bull; ' . View::e($payment['reference_note']) : '' ?>
            </div>
        </div>

        <div class="receipt-row" style="margin-top: 1.25rem; align-items: center;">
            <div class="receipt-col-label" style="font-weight: 800; color: var(--gold-solid); font-size: 1rem;">TERBILANG RP</div>
            <div class="receipt-col-dots">:</div>
            <div class="receipt-col-val">
                <div class="tabular-nums" style="font-size: 1.65rem; font-weight: 800; color: #FFF; background: rgba(223,178,86,0.15); border: 1px solid var(--glass-border-gold); padding: 0.4rem 1.25rem; border-radius: var(--radius-sm); display: inline-block;">
                    <?= View::formatRupiah($payment['amount']) ?>
                </div>
                <span class="tabular-nums" style="margin-left: 0.85rem; font-size: 0.825rem; color: var(--text-muted);">
                    (Sisa Tagihan: <strong style="color:var(--gold-solid);"><?= View::formatRupiah($payment['remaining_balance']) ?></strong>)
                </span>
            </div>
        </div>

        <!-- Tanda Tangan & Verifikasi Resmi -->
        <div class="print-signatures" style="display: flex; justify-content: space-between; margin-top: 2.75rem;">
            <div class="signature-box sig-box" style="width: 220px; text-align: center;">
                <div class="signature-title" style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 3.5rem;">Penyetor / Siswa,</div>
                <div class="signature-name" style="font-weight: 800; color: #FFF; border-bottom: 1px solid var(--glass-border); padding-bottom: 2px;">
                    <?= View::e($payment['student_name']) ?>
                </div>
                <div class="signature-nip tabular-nums" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                    NIS: <?= View::e($payment['student_number']) ?>
                </div>
            </div>

            <div class="signature-box sig-box" style="width: 220px; text-align: center;">
                <div class="signature-title" style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 3.5rem;">Penerima / Bendahara Angkatan,</div>
                <div class="signature-name" style="font-weight: 800; color: #FFF; border-bottom: 1px solid var(--glass-border); padding-bottom: 2px;">
                    <?= View::e($payment['verifier_name'] ?? $payment['recorder_name']) ?>
                </div>
                <div class="signature-nip" style="font-size: 0.75rem; color: var(--gold-solid); margin-top: 2px;">
                    <?= $payment['status'] === 'terverifikasi' ? 'Diverifikasi Sah &bull; Bendahara' : 'Petugas Pencatat' ?>
                </div>
            </div>
        </div>

        <!-- Audit Hash Bar -->
        <div class="print-audit-footer" style="margin-top: 2rem; padding-top: 0.75rem; border-top: 1px solid var(--glass-border); display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--text-muted); font-family: var(--font-mono);">
            <span>SECURITY AUDIT HASH: <?= $auditHash ?></span>
            <span>VERIFIED ZERO DRIFT ENGINE &bull; SMKN 1 PANYINGKIRAN</span>
        </div>
    </div>
</div>

</body>
</html>
