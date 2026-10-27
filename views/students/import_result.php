<?php
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Ringkasan Hasil Impor Siswa</h1>
        <p class="page-subtitle">Laporan eksekusi validasi dan impor data siswa ke database</p>
    </div>
    <a href="<?= View::url('/students') ?>" class="btn btn-primary">&larr; Kembali ke Data Siswa</a>
</div>

<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Data Berhasil Dimasukkan</div>
        <div class="metric-value success"><?= (int)$result['imported'] ?></div>
        <div class="metric-sub">Siswa aktif dengan iuran Rp250.000</div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Data Dilewati / Duplikasi</div>
        <div class="metric-value" style="color: <?= (int)$result['skipped'] > 0 ? '#D97706' : 'var(--text-muted)' ?>;">
            <?= (int)$result['skipped'] ?>
        </div>
        <div class="metric-sub">Mencegah data ganda</div>
    </div>
</div>

<?php if (!empty($result['errors'])): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="color: var(--danger-text);">Catatan / Baris yang Dilewati (<?= count($result['errors']) ?> Catatan)</h2>
        </div>
        <div style="max-height: 320px; overflow-y: auto; font-size: 0.825rem; font-family: monospace; background: var(--bg-subtle); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            <?php foreach ($result['errors'] as $err): ?>
                <div style="margin-bottom: 0.35rem; color: #B91C1C;">&bull; <?= View::e($err) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
    <a href="<?= View::url('/students') ?>" class="btn btn-primary">Lihat Seluruh Data Siswa</a>
    <a href="<?= View::url('/students/import') ?>" class="btn btn-secondary">Impor File Lain</a>
</div>
