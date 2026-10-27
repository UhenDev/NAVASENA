<?php
use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Helpers\View;

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle ?? 'Portal Keuangan Siswa') ?> — GUARDIAN OF NAVASENA 27</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= View::url('/assets/img/logo.png') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="print">
    <style>
        .portal-header-bar {
            background: rgba(7, 10, 18, 0.85);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--glass-border-gold);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .portal-brand-block {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .portal-crest-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(223, 178, 86, 0.35));
        }
        .portal-container-shell {
            max-width: 1080px;
            margin: 2rem auto;
            padding: 0 1.25rem 6rem 1.25rem;
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>

<header class="portal-header-bar">
    <div class="portal-brand-block">
        <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Owl Crest" class="portal-crest-img">
        <div>
            <div style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 800; background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: 0.06em;">
                GUARDIAN OF NAVASENA 27
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.1em; text-transform: uppercase;">
                Portal Mandiri Siswa &bull; SMKN 1 Panyingkiran
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span class="status-pill status-verified" style="font-size:0.7rem; font-family:var(--font-mono);">
            <?= View::e($user['username'] ?? '') ?>
        </span>
        <a href="<?= View::url('/change-password') ?>" class="btn btn-secondary btn-sm">Ganti Password</a>
        <a href="<?= View::url('/logout') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
    </div>
</header>

<div class="portal-container-shell">
    <?= Flash::render() ?>
