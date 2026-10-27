<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
        </main>
    </div>
</div>

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
                Anda akan menolak pembayaran <strong class="modal-target-name"></strong>. Transaksi ini tidak akan dihitung sebagai penerimaan sah.
            </p>
            <div class="form-group">
                <label class="form-label" for="rejection_reason">Alasan Penolakan <span style="color:red">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" required placeholder="Contoh: Bukti transfer tidak valid atau dana belum masuk rekening"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectPaymentModal')">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
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
                Transaksi <strong class="modal-target-name"></strong> akan dibatalkan. Jika transaksi ini sebelumnya telah diverifikasi, catatan kas dan status iuran siswa akan disesuaikan otomatis dengan jejak audit.
            </p>
            <div class="form-group">
                <label class="form-label" for="cancellation_reason">Alasan Pembatalan <span style="color:red">*</span></label>
                <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="form-control" required placeholder="Contoh: Terjadi kesalahan input nominal atau dobel transfer"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelTransactionModal')">Tutup</button>
                <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= View::url('/assets/js/app.js') ?>"></script>
</body>
</html>
