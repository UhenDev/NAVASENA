<?php
use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Helpers\View;

$activeNav = $activeNav ?? 'dashboard';
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle ?? 'NAVASENA MANAGEMENT') ?> — GUARDIAN OF NAVASENA 27</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= View::url('/assets/img/logo.png') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/print.css') ?>" media="print">
</head>
<body>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<div class="app-layout">
    <!-- Executive Left Sidebar Navigation -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-crest">
                <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Guardian of Navasena Crest">
            </div>
            <div class="brand-text">
                <h1>NAVASENA 27</h1>
                <p>FINANCE SYSTEM</p>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="<?= View::url('/dashboard') ?>" class="nav-item <?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard Eksekutif</span>
            </a>

            <a href="<?= View::url('/students') ?>" class="nav-item <?= $activeNav === 'students' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Data Siswa (492)</span>
            </a>

            <a href="<?= View::url('/payments') ?>" class="nav-item <?= $activeNav === 'payments' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span>Verifikasi Pembayaran</span>
                <?php if (isset($pendingCount) && $pendingCount > 0): ?>
                    <span class="nav-badge"><?= (int)$pendingCount ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= View::url('/cash') ?>" class="nav-item <?= $activeNav === 'cash' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <span>Buku Kas &amp; Arus Kas</span>
            </a>

            <div class="nav-section-title">Laporan &amp; Audit</div>
            <a href="<?= View::url('/reports') ?>" class="nav-item <?= $activeNav === 'reports' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Laporan &amp; Rekap LPJ</span>
            </a>

            <?php if (Auth::isKetua()): ?>
            <div class="nav-section-title">Manajemen &amp; Konfigurasi</div>
            <a href="<?= View::url('/settings') ?>" class="nav-item <?= $activeNav === 'settings' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Pengaturan Cicilan</span>
            </a>

            <a href="<?= View::url('/users') ?>" class="nav-item <?= $activeNav === 'users' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Manajemen User</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Guardian of Navasena School Emblem Badge -->
        <div class="sidebar-guardian-badge">
            <div class="crest-mini">
                <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Mini Crest" style="width:100%; height:100%; object-fit:contain;">
            </div>
            <div class="badge-title">GUARDIAN OF NAVASENA</div>
            <div class="badge-sub">SMKN 1 Panyingkiran</div>
            <div style="font-family: var(--font-mono); font-size: 0.68rem; font-weight: 700; color: var(--verified-fg); margin-top: 0.25rem; letter-spacing: 0.05em;">
                TA 2026/2027 &bull; 44/44 PASS
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-info-brief">
                <div class="user-name"><?= View::e($user['full_name'] ?? 'Pengguna') ?></div>
                <div class="user-role-tag"><?= View::e(strtoupper($user['role'] ?? '')) ?></div>
            </div>
            <a href="<?= View::url('/logout') ?>" title="Keluar dari Sistem" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.6rem; border-color: rgba(239,68,68,0.3); color: var(--void-fg);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="app-main">
        <header class="app-topbar">
            <div class="topbar-left">
                <button type="button" class="menu-toggle-btn" id="menuToggleBtn" aria-label="Buka Menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="topbar-title">
                    <span><?= View::e($pageTitle ?? 'NAVASENA MANAGEMENT') ?></span>
                    <span class="topbar-engine-tag">0 DRIFT ENGINE</span>
                </div>
            </div>
            <div class="topbar-right">
                <span class="status-pill status-verified" style="font-size:0.7rem;">ROLE: <?= View::e(strtoupper($user['role'] ?? '')) ?></span>
                <a href="<?= View::url('/change-password') ?>" class="btn btn-secondary btn-sm">Ganti Password</a>
                <a href="<?= View::url('/logout') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </header>

        <main class="app-content">
            <div id="flash-container" class="flash-alert-container">
                <?= Flash::render() ?>
            </div>
