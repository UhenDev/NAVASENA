<?php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Kesalahan Sistem &mdash; NAVASENA MANAGEMENT</title>
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <style>
        body {
            background-color: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 1.5rem;
        }
        .error-card {
            max-width: 520px;
            background: #FFFFFF;
            padding: 2.5rem 2rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div style="font-size: 3.5rem; font-weight: 800; color: #D97706; line-height: 1;">500</div>
        <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0.75rem 0 0.5rem 0;">Terjadi Kesalahan Server</h1>
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
            Sistem mendeteksi kendala operasional. Rincian error telah dicatat ke dalam log server untuk penelusuran.
        </p>
        <?php if (!empty($message)): ?>
            <div style="font-family: monospace; font-size: 0.75rem; background: #FFFBEB; border: 1px solid #FDE68A; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem; color: #92400E; text-align: left; overflow-x: auto;">
                <?= View::e($message) ?>
            </div>
        <?php endif; ?>
        <a href="<?= View::url('/') ?>" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
