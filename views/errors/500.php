<?php
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Kesalahan Server &mdash; GUARDIAN OF NAVASENA 27</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <style>
        body {
            background-color: var(--bg-obsidian);
            background-image: var(--bg-mesh);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 1.5rem;
            color: #FFFFFF;
            font-family: var(--font-sans);
        }
        .error-card {
            max-width: 540px;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--glass-border-gold);
            box-shadow: var(--glass-shadow);
            position: relative;
        }
        .error-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold-gradient);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div style="margin-bottom: 1.25rem;">
            <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Logo" style="width: 72px; height: 72px; object-fit: contain; filter: drop-shadow(0 4px 14px rgba(223, 178, 86, 0.3));">
        </div>
        <div style="font-family: var(--font-mono); font-size: 3.5rem; font-weight: 800; color: var(--pending-fg); line-height: 1; letter-spacing: -0.03em;">500</div>
        <h1 style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 700; margin: 0.75rem 0 0.5rem 0; color: #FFFFFF; letter-spacing: 0.03em;">Terjadi Kesalahan Server</h1>
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.6;">
            Sistem mendeteksi kendala pada pemrosesan instruksi. Seluruh parameter keamanan telah dicatat ke log internal untuk penanganan teknis.
        </p>
        <?php if (!empty($message)): ?>
            <div style="font-family: var(--font-mono); font-size: 0.775rem; background: rgba(7, 10, 18, 0.85); border: 1px solid var(--glass-border); padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1.75rem; color: var(--pending-fg); text-align: left; overflow-x: auto;">
                <?= View::e($message) ?>
            </div>
        <?php endif; ?>
        <a href="<?= View::url('/') ?>" class="btn btn-primary" style="box-shadow: 0 4px 18px rgba(223, 178, 86, 0.28);">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
