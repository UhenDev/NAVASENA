<?php
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="badge" style="background: rgba(223, 178, 86, 0.12); color: var(--gold-text); border: 1px solid var(--glass-border-gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                LAPORAN EKSEKUSI IMPOR
            </span>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">IMPORT-EXEC-LOG</span>
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">Ringkasan Hasil Impor Siswa</h1>
        <p class="page-subtitle">Laporan eksekusi validasi, parsing struktur baris, dan penulisan database</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">&larr; Kembali ke Data Siswa</a>
</div>

<div class="metrics-grid" style="margin-bottom: 2rem;">
    <div class="metric-card" style="border-left: 3px solid var(--verified-fg);">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Data Berhasil Dimasukkan</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.8rem; font-weight: 800; color: var(--verified-fg); margin: 0.35rem 0;">
            <?= (int)$result['imported'] ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Siswa aktif baru dengan iuran angkatan Rp250.000</div>
    </div>

    <div class="metric-card" style="border-left: 3px solid <?= (int)$result['skipped'] > 0 ? 'var(--gold-solid)' : 'var(--text-muted)' ?>;">
        <div class="metric-label" style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Data Dilewati / Duplikasi</div>
        <div class="metric-value" style="font-family: var(--font-mono); font-size: 1.8rem; font-weight: 800; color: <?= (int)$result['skipped'] > 0 ? 'var(--gold-solid)' : 'var(--text-muted)' ?>; margin: 0.35rem 0;">
            <?= (int)$result['skipped'] ?>
        </div>
        <div class="metric-sub" style="font-size: 0.75rem; color: var(--text-secondary);">Mencegah data ganda pada nomor NIS yang sama</div>
    </div>
</div>

<?php if (!empty($result['errors'])): ?>
    <div class="card" style="margin-bottom: 2rem; border-color: rgba(239, 68, 68, 0.4);">
        <div class="card-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <h2 class="card-title" style="color: var(--void-fg); font-family: var(--font-display); font-size: 1.05rem;">
                Catatan Baris yang Dilewati (<?= count($result['errors']) ?> Catatan)
            </h2>
        </div>
        <div style="max-height: 320px; overflow-y: auto; font-size: 0.825rem; font-family: var(--font-mono); background: rgba(7, 10, 18, 0.8); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
            <?php foreach ($result['errors'] as $err): ?>
                <div style="margin-bottom: 0.4rem; color: #FCA5A5;">&bull; <?= View::e($err) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= View::url('/students') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">Lihat Seluruh Data Siswa (492)</a>
    <a href="<?= View::url('/students/import') ?>" class="btn btn-secondary">Impor Berkas Lain</a>
</div>
