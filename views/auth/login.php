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
    <title>Masuk ke Sistem — GUARDIAN OF NAVASENA 27</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= View::url('/assets/img/logo.png') ?>">
    <link rel="stylesheet" href="<?= View::url('/assets/css/main.css') ?>">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-crest-header">
            <img src="<?= View::url('/assets/img/logo.png') ?>" alt="Official Crest" class="login-crest-img">
            <h1>GUARDIAN OF NAVASENA 27</h1>
            <p>SMKN 1 PANYINGKIRAN &bull; MANAGEMENT KEUANGAN</p>
        </div>

        <div class="login-body">
            <div id="flash-container" class="flash-alert-container">
                <?= Flash::render() ?>
            </div>

            <form method="POST" action="">
                <?= CSRF::field() ?>

                <div class="form-group">
                    <label class="form-label" for="username">Username / Nomor Induk Siswa (NIS)</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Contoh: NIS Siswa" required autofocus autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi Keamanan</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••••••" required autocomplete="current-password" style="padding-right: 2.75rem;">
                        <button type="button" id="togglePasswordBtn" class="password-toggle-btn" aria-label="Lihat kata sandi" title="Lihat kata sandi">
                            <svg id="eyeIconOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeIconClose" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.75rem; letter-spacing: 0.05em;">
                    MASUK PORTAL NAVASENA
                </button>
            </form>

            <div style="margin-top: 1.5rem; padding: 0.85rem 1rem; background: rgba(7, 10, 18, 0.65); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); font-size: 0.75rem;">
                <div style="font-weight: 700; color: var(--gold-text); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.06em;">
                    Proteksi Keamanan Role &amp; Data
                </div>
                <ul style="list-style: none; padding-left: 0; color: var(--text-muted); line-height: 1.6;">
                    <li>&bull; Enkripsi Bcrypt &amp; Token CSRF Aktif</li>
                    <li>&bull; Isolasi Data Mandiri per Siswa (0 Data Leak)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="<?= View::url('/assets/js/app.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const eyeOpen = document.getElementById('eyeIconOpen');
    const eyeClose = document.getElementById('eyeIconClose');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = pwdInput.getAttribute('type') === 'password';
            pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
            if (eyeOpen && eyeClose) {
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClose.style.display = isPassword ? 'block' : 'none';
            }
            toggleBtn.style.color = isPassword ? 'var(--gold-solid)' : 'var(--text-muted)';
            toggleBtn.setAttribute('title', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
        });
    }
});
</script>
</body>
</html>
