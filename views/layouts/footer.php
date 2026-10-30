<?php
use App\Helpers\Auth;
use App\Helpers\CSRF;
use App\Helpers\View;
?>
        </main>
    </div>
</div>

<!-- Floating Frosted Glass Bottom Navigation Dock for Mobile View -->
<nav class="mobile-dock" aria-label="Navigasi Cepat Mobile">
    <a href="<?= View::url('/dashboard') ?>" class="dock-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Beranda</span>
    </a>
    <a href="<?= View::url('/students') ?>" class="dock-item <?= ($activeNav ?? '') === 'students' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
        <span>Siswa</span>
    </a>
    <a href="<?= View::url('/payments') ?>" class="dock-item <?= ($activeNav ?? '') === 'payments' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <span>Kuitansi</span>
    </a>
    <a href="<?= View::url('/change-password') ?>" class="dock-item <?= ($activeNav ?? '') === 'account' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Akun</span>
    </a>
</nav>

<!-- Modal Penolakan Pembayaran -->
<div class="modal-overlay" id="rejectPaymentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tolak Transaksi Pembayaran</h3>
            <button type="button" class="modal-close" onclick="closeModal('rejectPaymentModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <?= CSRF::field() ?>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                Anda akan menolak pembayaran <strong class="modal-target-name" style="color:var(--void-fg);"></strong>. Transaksi ini tidak akan dihitung sebagai penerimaan sah.
            </p>
            <div class="form-group">
                <label class="form-label" for="rejection_reason">Alasan Penolakan <span style="color:var(--void-fg)">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" required placeholder="Contoh: Bukti transfer tidak valid atau dana belum masuk rekening"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectPaymentModal')">Batal</button>
                <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembatalan Transaksi (Void/Cancel) -->
<div class="modal-overlay" id="cancelTransactionModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Batalkan Transaksi (Audit Reversal)</h3>
            <button type="button" class="modal-close" onclick="closeModal('cancelTransactionModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <?= CSRF::field() ?>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                Transaksi <strong class="modal-target-name" style="color:var(--gold-solid);"></strong> akan dibatalkan (VOID). Catatan kas dan status iuran siswa akan disesuaikan otomatis dengan jejak audit reversal.
            </p>
            <div class="form-group">
                <label class="form-label" for="cancellation_reason">Alasan Pembatalan <span style="color:var(--void-fg)">*</span></label>
                <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="form-control" required placeholder="Contoh: Terjadi kesalahan input nominal atau dobel transfer"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelTransactionModal')">Tutup</button>
                <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan (Reversal)</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= View::url('/assets/js/app.js') ?>"></script>
</body>
</html>
