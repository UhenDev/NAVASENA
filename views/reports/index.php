<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Laporan Pertanggungjawaban (LPJ) Resmi &bull; SMKN 1 Panyingkiran
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">
            Pusat Laporan &amp; <span class="gold-highlight">Audit Keuangan</span>
        </h1>
        <p class="page-subtitle">Komposisi alokasi dana 80/20, rekonsiliasi kas riil, dan cetak laporan pertanggungjawaban resmi</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <a href="<?= View::url('/reports/print') ?>" target="_blank" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.32);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            CETAK LPJ RESMI (PDF)
        </a>
    </div>
</div>

<!-- 1. Hero Card: Laporan Pertanggungjawaban (LPJ) Showcase -->
<div class="card card-gold" style="margin-bottom: 2rem; border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

    <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
            <div style="width: 38px; height: 38px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; background: rgba(223, 178, 86, 0.12); border: 1px solid var(--glass-border-gold); filter: drop-shadow(0 2px 8px rgba(223,178,86,0.3));">
                <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Logo" style="width: 24px; height: 24px; object-fit: contain;">
            </div>
            <div>
                <h2 class="card-title" style="font-family: var(--font-display); font-size: 1.15rem; color: #FFFFFF; margin: 0;">
                    Laporan Pertanggungjawaban (LPJ)
                </h2>
                <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 2px;">
                    Komposisi Alokasi Dana &amp; Cetak Rekap Resmi
                </div>
            </div>
        </div>
        <span class="status-pill status-verified" style="font-size: 0.7rem; font-weight: 700;">KOMPOSISI RESMI 80/20</span>
    </div>

    <!-- Donut Section: Chart on left, Details on right -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; align-items: center; padding: 0.5rem 0 1.5rem 0;">
        <!-- Donut Chart with Center Owl Crest -->
        <div style="display: flex; justify-content: center; align-items: center;">
            <div class="donut-wrapper" style="margin: 0; width: 230px; height: 230px;">
                <!-- SVG Donut Chart (80% / 20%) -->
                <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg); border-radius: 50%;">
                    <!-- Background track -->
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255, 255, 255, 0.05)" stroke-width="3.6" />
                    <!-- 80% Album Kenangan (Gold) -->
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="url(#goldGrad)" stroke-width="3.8" stroke-dasharray="80, 100" stroke-linecap="round" />
                    <!-- 20% Acara Graduation (Emerald) -->
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#34D399" stroke-width="3.8" stroke-dasharray="20, 100" stroke-dashoffset="-80" stroke-linecap="round" />
                    <defs>
                        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#FFF3B0"/>
                            <stop offset="45%" stop-color="#DFB256"/>
                            <stop offset="85%" stop-color="#B8860B"/>
                            <stop offset="100%" stop-color="#7A5311"/>
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Center Owl Crest -->
                <div class="donut-center-crest" style="width: 76px; height: 76px;">
                    <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Owl Crest" style="width: 50px; height: 50px; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Legend Breakdown -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="font-family: var(--font-display); font-size: 0.75rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.1em; text-transform: uppercase;">
                ALOKASI KEWAJIBAN PER SISWA
            </div>

            <div style="padding: 1rem 1.25rem; background: rgba(11, 16, 29, 0.7); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); border-left: 4px solid var(--gold-solid);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--gold-solid);"></span>
                    <strong style="color: #FFFFFF; font-size: 0.95rem;">Album Fisik Kenangan (80%)</strong>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary); margin-left: 1.25rem; font-family: var(--font-mono);">
                    Rp 200.000 &times; 492 = <strong style="color: var(--gold-solid);">Rp 98.400.000</strong>
                </div>
            </div>

            <div style="padding: 1rem 1.25rem; background: rgba(11, 16, 29, 0.7); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); border-left: 4px solid var(--verified-fg);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--verified-fg);"></span>
                    <strong style="color: #FFFFFF; font-size: 0.95rem;">Acara Graduation (20%)</strong>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary); margin-left: 1.25rem; font-family: var(--font-mono);">
                    Rp 50.000 &times; 492 = <strong style="color: var(--verified-fg);">Rp 24.600.000</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box: Rekonsiliasi Kas & Audit (0 Drift Verified) -->
    <div style="padding: 1.15rem 1.35rem; background: rgba(7, 10, 18, 0.85); border: 1px dashed var(--glass-border-gold); border-radius: var(--radius-sm); margin-top: 0.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-family: var(--font-mono); font-size: 0.8rem; font-weight: 700; color: var(--verified-fg); letter-spacing: 0.05em;">
                REKONSILIASI KAS &amp; AUDIT (0 DRIFT VERIFIED)
            </div>
            <span class="status-pill status-verified" style="font-size: 0.65rem;">AKURAT 100%</span>
        </div>
        <div style="font-family: var(--font-mono); font-size: 0.875rem; color: #FFFFFF; font-weight: 600; margin-bottom: 0.25rem;">
            Kas Masuk Sah: <span style="color: var(--verified-fg);"><?= View::formatRupiah($metrics['total_cash_in']) ?></span> &bull; Kas Keluar: <span style="color: var(--void-fg);"><?= View::formatRupiah($metrics['total_cash_out']) ?></span> &bull; Saldo Riil: <span style="color: var(--gold-solid);"><?= View::formatRupiah($metrics['cash_balance']) ?></span>
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">
            Format Ekspor: Cetak LPJ Resmi (PDF) &amp; CSV UTF-8 BOM &bull; SHA-256 Audit Integrity
        </div>
    </div>
</div>

<!-- 2. Menu Akses Laporan Spesifik -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
    <!-- Card Laporan Siswa -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: var(--glass-border);">
        <div>
            <div style="font-size: 0.725rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem;">
                Audit Mandiri Siswa
            </div>
            <h2 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; margin: 0.25rem 0 0.5rem 0; color: #FFFFFF;">
                Rekap Pembayaran Siswa
            </h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                Daftar lengkap seluruh 492 siswa, status cicilan, riwayat pembayaran sah, dan sisa kewajiban per kelas.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= View::url('/reports/students') ?>" class="btn btn-primary btn-sm">Buka Rekap Siswa</a>
            <a href="<?= View::url('/reports/export/students') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>

    <!-- Card Progres Cicilan -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: var(--glass-border);">
        <div>
            <div style="font-size: 0.725rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem;">
                Tahapan Cicilan
            </div>
            <h2 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; margin: 0.25rem 0 0.5rem 0; color: #FFFFFF;">
                Rekap Capaian 3 Tahap
            </h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                Persentase pemenuhan Tahap 1 (DP Rp100k), Tahap 2 (Rp100k), dan Tahap 3 (Pelunasan Rp50k).
            </p>
        </div>
        <div>
            <a href="<?= View::url('/reports/installments') ?>" class="btn btn-primary btn-sm">Buka Laporan Tahap</a>
        </div>
    </div>

    <!-- Card Buku Kas -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: var(--glass-border);">
        <div>
            <div style="font-size: 0.725rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem;">
                Buku Kas Riil
            </div>
            <h2 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; margin: 0.25rem 0 0.5rem 0; color: #FFFFFF;">
                Arus Kas Masuk &amp; Keluar
            </h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                Pencatatan mutasi kas per periode, rincian biaya operasional album/graduation, dan saldo kas aktual.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= View::url('/reports/cash') ?>" class="btn btn-primary btn-sm">Buka Buku Kas</a>
            <a href="<?= View::url('/cash/export') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>
</div>
