<?php
use App\Helpers\View;
?>
    <footer style="margin-top: 3.5rem; padding: 2rem 0; text-align: center; border-top: 1px solid var(--glass-border); font-size: 0.8rem; color: var(--text-muted);">
        <div style="font-family: var(--font-display); font-weight: 700; color: var(--gold-text); margin-bottom: 0.25rem; letter-spacing: 0.05em;">
            GUARDIAN OF NAVASENA 27 &bull; SMKN 1 PANYINGKIRAN
        </div>
        <p>&copy; <?= date('Y') ?> <strong>NAVASENA MANAGEMENT</strong> &mdash; Sistem Informasi Keuangan Angkatan.</p>
        <p style="margin-top: 0.25rem; font-size: 0.75rem;">Jika terdapat ketidaksesuaian data pembayaran, silakan hubungi Bendahara atau Pengurus Angkatan.</p>
    </footer>
</div>

<!-- Fixed Frosted Glass Bottom Navigation Bar for Student Mobile View -->
<nav class="mobile-dock mobile-bottom-nav" aria-label="Navigasi Siswa">
    <a href="<?= View::url('/portal') ?>" class="dock-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Tagihan</span>
    </a>
    <a href="<?= View::url('/change-password') ?>" class="dock-item">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Akun</span>
    </a>
    <a href="<?= View::url('/logout') ?>" class="dock-item" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem Guardian of Navasena 27?')">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Keluar</span>
    </a>
</nav>

<script src="<?= View::url('/assets/js/app.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
