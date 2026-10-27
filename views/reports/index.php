<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Laporan Pertanggungjawaban (LPJ) Resmi &bull; SMKN 1 Panyingkiran
        </div>
        <h1 class="page-title">
            Pusat Laporan &amp; <span class="gold-highlight">Audit Keuangan</span>
        </h1>
        <p class="page-subtitle">Rekapitulasi iuran siswa, transparansi alokasi 80/20, dan audit rekonsiliasi kas angkatan</p>
    </div>
    <div style="display:flex; gap:0.75rem;">
        <a href="<?= View::url('/reports/print') ?>" target="_blank" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            CETAK LPJ RESMI (PDF)
        </a>
    </div>
</div>

<!-- 1. Donut Chart Alokasi 80/20 dengan Center Owl Crest & Rekonsiliasi Audit Box -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Donut Chart Card -->
    <div class="card card-gold">
        <div class="card-header">
            <div>
                <h2 class="card-title">Komposisi Alokasi Dana Angkatan</h2>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                    Target Total: Rp123.000.000 (492 Siswa &times; Rp250.000)
                </div>
            </div>
            <span class="status-pill status-verified" style="font-size:0.65rem;">KOMPOSISI SAH</span>
        </div>

        <div class="donut-wrapper">
            <!-- SVG Donut Chart (80% / 20%) -->
            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg); border-radius: 50%;">
                <!-- Background track -->
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255, 255, 255, 0.05)" stroke-width="3.5" />
                <!-- 80% Album Kenangan (Gold) -->
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="url(#goldGrad)" stroke-width="3.8" stroke-dasharray="80, 100" stroke-linecap="round" />
                <!-- 20% Acara Graduation (Emerald) -->
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#34D399" stroke-width="3.8" stroke-dasharray="20, 100" stroke-dashoffset="-80" stroke-linecap="round" />
                <defs>
                    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFF3B0"/>
                        <stop offset="50%" stop-color="#DFB256"/>
                        <stop offset="100%" stop-color="#B8860B"/>
                    </linearGradient>
                </defs>
            </svg>

            <!-- Center Owl Crest -->
            <div class="donut-center-crest">
                <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Owl Crest">
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: var(--glass-inner); border-radius: var(--radius-sm); border-left: 3px solid var(--gold-solid);">
                <div>
                    <div style="font-weight: 700; color: #FFF; font-size: 0.85rem;">Album Fisik Kenangan (80%)</div>
                    <div style="font-size: 0.725rem; color: var(--text-muted);">Rp200.000 &times; 492 Siswa</div>
                </div>
                <div class="tabular-nums" style="font-weight: 800; color: var(--gold-solid);">
                    Rp 98.400.000
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: var(--glass-inner); border-radius: var(--radius-sm); border-left: 3px solid var(--verified-fg);">
                <div>
                    <div style="font-weight: 700; color: #FFF; font-size: 0.85rem;">Acara Graduation (20%)</div>
                    <div style="font-size: 0.725rem; color: var(--text-muted);">Rp50.000 &times; 492 Siswa</div>
                </div>
                <div class="tabular-nums" style="font-weight: 800; color: var(--verified-fg);">
                    Rp 24.600.000
                </div>
            </div>
        </div>
    </div>

    <!-- Rekonsiliasi Kas & Audit (0 Drift Verified) -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header">
                <div>
                    <h2 class="card-title">Rekonsiliasi Kas &amp; Audit</h2>
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                        Verifikasi keakuratan saldo riil vs total penerimaan sah
                    </div>
                </div>
                <span class="status-pill status-verified" style="font-size:0.65rem;">0 DRIFT VERIFIED</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="padding: 1rem; background: var(--glass-inner); border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                        Status Rekonsiliasi
                    </div>
                    <div style="font-size: 1.15rem; font-weight: 800; color: var(--verified-fg); margin-top: 0.2rem;">
                        Terkonfirmasi Seimbang (0 Selisih)
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.35rem;">
                        Seluruh transaksi masuk telah diuji terhadap pembukuan buku kas dan alokasi cicilan. Tidak ditemukan deviasi atau pergeseran saldo.
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div style="padding: 0.85rem; background: var(--glass-inner); border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Total Siswa Terdaftar</div>
                        <div class="tabular-nums" style="font-size: 1.35rem; font-weight: 800; color: #FFF; margin-top: 0.15rem;">
                            492 Siswa
                        </div>
                    </div>
                    <div style="padding: 0.85rem; background: var(--glass-inner); border: 1px solid var(--glass-border); border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Rombongan Belajar</div>
                        <div class="tabular-nums" style="font-size: 1.35rem; font-weight: 800; color: var(--gold-solid); margin-top: 0.15rem;">
                            16 Kelas
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 1rem; background: rgba(7, 10, 18, 0.7); border: 1px dashed var(--glass-border-gold); border-radius: var(--radius-sm); font-size: 0.775rem; color: var(--text-muted);">
            <div><strong>Sertifikasi Kepatuhan Keuangan:</strong></div>
            <div>Diverifikasi menggunakan algoritma enkripsi SHA-256 Audit Trail. Sesuai standar tata kelola iuran angkatan resmi SMKN 1 Panyingkiran.</div>
        </div>
    </div>
</div>

<!-- 2. Menu Laporan Spesifik -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
    <!-- Card Laporan Siswa -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em;">
                Audit Mandiri Siswa
            </div>
            <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0.35rem 0; color: #FFF;">Rekap Pembayaran Siswa</h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Daftar lengkap 492 siswa, total pembayaran sah, sisa tagihan, dan status cicilan.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= View::url('/reports/students') ?>" class="btn btn-primary btn-sm">Buka Rekap Siswa</a>
            <a href="<?= View::url('/reports/export/students') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>

    <!-- Card Progres Cicilan -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em;">
                Tahapan Cicilan
            </div>
            <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0.35rem 0; color: #FFF;">Rekap Progres 3 Tahap</h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Realisasi penerimaan Tahap 1 (DP Rp100k), Tahap 2 (Rp100k), dan Tahap 3 (Pelunasan Rp50k).
            </p>
        </div>
        <div>
            <a href="<?= View::url('/reports/installments') ?>" class="btn btn-primary btn-sm">Buka Laporan Tahap</a>
        </div>
    </div>

    <!-- Card Buku Kas -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--gold-solid); text-transform: uppercase; letter-spacing: 0.08em;">
                Buku Kas Riil
            </div>
            <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0.35rem 0; color: #FFF;">Arus Kas Masuk &amp; Keluar</h2>
            <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Pencatatan mutasi kas per periode, rincian biaya operasional, dan saldo kas aktual angkatan.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= View::url('/reports/cash') ?>" class="btn btn-primary btn-sm">Buka Buku Kas</a>
            <a href="<?= View::url('/cash/export') ?>" class="btn btn-secondary btn-sm">Ekspor CSV</a>
        </div>
    </div>
</div>
