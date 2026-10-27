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
    <title><?= View::e($pageTitle ?? 'Portal Keuangan Siswa') ?> — NAVASENA MANAGEMENT</title>
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="print">
    <style>
        .portal-header {
            background-color: var(--primary);
            color: #FFFFFF;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-md);
        }
        .portal-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .portal-container {
            max-width: 1040px;
            margin: 2rem auto;
            padding: 0 1.25rem;
        }
    </style>
</head>
<body style="background-color: #F8FAFC;">

<header class="portal-header">
    <div class="portal-brand">
        <div class="brand-logo-icon" style="background:#2563EB;">N</div>
        <div>
            <h1 style="font-size: 1.15rem; font-weight: 700; color: #FFFFFF; line-height: 1.2;">NAVASENA MANAGEMENT</h1>
            <p style="font-size: 0.775rem; color: #94A3B8;">Portal Keuangan Mandiri Siswa Angkatan</p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="text-align: right; display: none; @media(min-width:600px){display:block;}">
            <div style="font-weight: 600; font-size: 0.875rem; color:#FFFFFF;"><?= View::e($user['full_name']) ?></div>
            <div style="font-size: 0.725rem; color: #93C5FD;">Siswa Angkatan</div>
        </div>
        <a href="<?= View::url('/change-password') ?>" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.15); border:none; color:#FFFFFF;">Ganti Password</a>
        <a href="<?= View::url('/logout') ?>" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.15); border:none; color:#FFFFFF;" onclick="return confirm('Keluar dari portal siswa?')">Keluar</a>
    </div>
</header>

<div class="portal-container">
    <?= Flash::render() ?>
