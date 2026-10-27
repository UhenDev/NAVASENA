<?php
use App\Helpers\CSRF;
use App\Helpers\Flash;
use App\Helpers\View;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem — NAVASENA MANAGEMENT</title>
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #0F2744 0%, #1A3A60 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .login-header {
            padding: 2.25rem 2rem 1.5rem 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .login-brand-logo {
            width: 48px;
            height: 48px;
            background: #2563EB;
            color: #FFFFFF;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 auto 1rem auto;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }
        .login-body {
            padding: 2rem;
        }
        .login-footer-info {
            padding: 1rem 2rem;
            background: var(--bg-subtle);
            border-top: 1px solid var(--border);
            font-size: 0.775rem;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="login-brand-logo">N</div>
        <h1 style="font-size: 1.35rem; font-weight: 700; color: var(--primary);">NAVASENA MANAGEMENT</h1>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Sistem Informasi Keuangan Angkatan</p>
    </div>

    <div class="login-body">
        <?= Flash::render() ?>

        <form method="POST" action="<?= View::url('/login') ?>">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username / Nomor Siswa (NIS)</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username atau NIS" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.95rem; margin-top: 0.5rem;">
                Masuk ke Aplikasi
            </button>
        </form>
    </div>

    <div class="login-footer-info">
        <div><strong>Akses Pengurus & Siswa</strong></div>
        <div>Ketua: <code>uhen</code> | Bendahara: <code>bendahara</code></div>
        <div>Siswa: Username = <strong>NIS</strong> &bull; Password = <code>password</code></div>
    </div>
</div>

</body>
</html>
