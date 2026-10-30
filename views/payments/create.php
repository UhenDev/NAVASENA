<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header" style="margin-bottom: 2rem;">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Modul Bendahara Angkatan &bull; Quick Input
        </div>
        <h1 class="page-title" style="font-family: var(--font-display); letter-spacing: 0.02em;">
            Catat Pembayaran <span class="gold-highlight">Iuran Siswa</span>
        </h1>
        <p class="page-subtitle">Otomatis mengisi tahap cicilan tertua &amp; menerbitkan nomor kuitansi unik KW-YYYYMMDD-XXXX</p>
    </div>
    <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">&larr; Kembali ke Pembayaran</a>
</div>

<div class="payment-form-container">
    <div class="card card-gold" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <!-- Header Identitas Transaksi & No. Kuitansi Otomatis -->
        <div class="payment-receipt-banner">
            <div class="receipt-banner-info">
                <span class="receipt-banner-label">NO. KUITANSI OTOMATIS:</span>
                <div class="receipt-code tabular-nums">
                    <?= View::e($receiptNumber) ?>
                </div>
            </div>
            <div class="receipt-banner-status">
                <span class="status-pill status-verified">STATUS: LANGSUNG SAH &amp; MASUK KAS</span>
                <div class="receipt-banner-sub">
                    Audit Reversal aktif jika terjadi pembatalan
                </div>
            </div>
        </div>

        <form method="POST" action="<?= View::url('/payments/create') ?>" id="paymentForm" onsubmit="return confirm('Apakah Anda yakin data pembayaran sudah benar dan ingin menyimpannya sekarang? (Pembayaran langsung sah dan masuk buku kas)');">
            <?= CSRF::field() ?>

            <!-- Grid 2 Kolom Input Transaksi -->
            <div class="form-two-col-grid">
                <!-- Kolom Kiri: Pilih Siswa & Nominal -->
                <div>
                    <!-- 1. Pilih Siswa -->
                    <div class="form-group">
                        <label class="form-label" for="studentSelect" style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 0.35rem;">
                            <span>Pilih Siswa Aktif <span style="color:var(--void-fg)">*</span></span>
                            <span style="font-size: 0.725rem; color: var(--text-muted); font-weight: normal;">489 Siswa Angkatan</span>
                        </label>
                        <div style="position: relative; margin-bottom: 0.5rem; width: 100%;">
                            <input type="text" id="studentSearchFilter" class="form-control" placeholder="🔍 Ketik nama, NIS, atau kelas untuk memfilter..." style="font-size: 0.85rem; border-color: var(--glass-border-gold); width: 100%; box-sizing: border-box;" autocomplete="off">
                        </div>
                        <select name="student_id" id="studentSelect" class="form-control" required style="font-size: 0.9rem; width: 100%; max-width: 100%; box-sizing: border-box; text-overflow: ellipsis;">
                            <option value="">-- Cari Nama Siswa, NIS, atau Kelas --</option>
                            <?php foreach ($students as $st): ?>
                                <option value="<?= $st['id'] ?>" 
                                        data-remaining="<?= (float)$st['remaining_balance'] ?>" 
                                        data-total-paid="<?= (float)$st['total_paid'] ?>"
                                        <?= $selectedStudentId === (int)$st['id'] ? 'selected' : '' ?>>
                                    <?= View::e($st['student_number']) ?> &mdash; <?= View::e($st['full_name']) ?> (<?= View::e($st['class_name']) ?>) &bull; Sisa: <?= View::formatRupiah($st['remaining_balance']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="studentRemainingNotice" class="student-remaining-notice">
                            Silakan pilih siswa untuk melihat sisa tagihan aktif.
                        </div>
                    </div>

                    <!-- 2. Nominal Diterima & Quick Chips -->
                    <div class="form-group" style="margin-top: 1.25rem;">
                        <label class="form-label" for="paymentAmount">
                            Nominal Diterima (Rp) <span style="color:var(--void-fg)">*</span>
                        </label>
                        <div style="position: relative; width: 100%;">
                            <input type="text" name="amount" id="paymentAmount" class="form-control input-rupiah tabular-nums" required placeholder="Contoh: 100.000" style="font-family: var(--font-mono); font-size: clamp(1.2rem, 5vw, 1.5rem); font-weight: 800; color: var(--verified-fg); padding: 0.75rem 1rem; width: 100%; box-sizing: border-box;">
                        </div>

                        <!-- Quick-fill Chips -->
                        <div class="quick-nominal-chips" style="margin-top: 0.75rem;">
                            <button type="button" class="chip-btn" data-value="50000">Rp 50.000</button>
                            <button type="button" class="chip-btn" data-value="100000">Rp 100.000 (DP 1)</button>
                            <button type="button" class="chip-btn" data-value="150000">Rp 150.000</button>
                            <button type="button" class="chip-btn" id="chipPayFull" data-value="250000" style="border-color: var(--gold-solid); font-weight: 800; color: var(--gold-solid);">Pelunasan Sisa</button>
                        </div>

                        <!-- Keypad Input di bawah kolom nominal untuk perangkat ponsel/mobile -->
                        <div class="keypad-wrapper-box" style="margin-top: 1rem; background: rgba(11, 16, 29, 0.65); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 0.85rem; width: 100%; box-sizing: border-box;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; gap: 0.5rem;">
                                <span style="font-weight: 700; color: var(--gold-text); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Keypad Angka Cepat (Ponsel)</span>
                                <button type="button" class="btn btn-secondary btn-sm" id="toggleKeypadBtn" style="padding: 0.2rem 0.5rem; font-size: 0.7rem; white-space: nowrap;" onclick="var kp = document.getElementById('glassKeypadGrid'); kp.style.display = (kp.style.display === 'none') ? 'grid' : 'none';">
                                    Buka / Tutup
                                </button>
                            </div>
                            <div class="glass-keypad" id="glassKeypadGrid">
                                <button type="button" class="keypad-btn" data-key="1">1</button>
                                <button type="button" class="keypad-btn" data-key="2">2</button>
                                <button type="button" class="keypad-btn" data-key="3">3</button>
                                <button type="button" class="keypad-btn" data-key="4">4</button>
                                <button type="button" class="keypad-btn" data-key="5">5</button>
                                <button type="button" class="keypad-btn" data-key="6">6</button>
                                <button type="button" class="keypad-btn" data-key="7">7</button>
                                <button type="button" class="keypad-btn" data-key="8">8</button>
                                <button type="button" class="keypad-btn" data-key="9">9</button>
                                <button type="button" class="keypad-btn" data-key="000">000</button>
                                <button type="button" class="keypad-btn" data-key="0">0</button>
                                <button type="button" class="keypad-btn" data-key="backspace" style="color: var(--void-fg);">⌫</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Metode, Tanggal, dan Catatan -->
                <div>
                    <!-- 3. Metode Pembayaran -->
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran <span style="color:var(--void-fg)">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control" required style="display:none;">
                            <option value="transfer" selected>Transfer Bank / E-Wallet</option>
                            <option value="tunai">Setor Tunai (Langsung ke Bendahara)</option>
                        </select>

                        <div class="method-toggle-group" style="margin-bottom: 0.75rem; width: 100%; box-sizing: border-box;">
                            <div class="method-toggle-btn active" data-method="transfer">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                <span>Transfer Bank</span>
                            </div>
                            <div class="method-toggle-btn" data-method="tunai">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <span>Setor Tunai</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Tanggal Transaksi -->
                    <div class="form-group">
                        <label class="form-label" for="payment_date">Tanggal Masuk Kas <span style="color:var(--void-fg)">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control tabular-nums" value="<?= date('Y-m-d') ?>" required style="font-family: var(--font-mono); width: 100%; box-sizing: border-box;">
                    </div>

                    <!-- 5. Referensi / Nomor Bukti -->
                    <div class="form-group">
                        <label class="form-label" for="reference_note">Nomor Referensi / Catatan Bukti</label>
                        <input type="text" name="reference_note" id="reference_note" class="form-control" placeholder="Contoh: Transfer BCA an. Wali Siswa / Resi: 83921" style="width: 100%; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <!-- Submit Button (Responsive & No-Overflow) -->
            <button type="submit" class="btn btn-primary btn-submit-payment" id="btnSubmitPayment">
                <span class="btn-submit-main">+ SIMPAN &amp; BAYAR SEKARANG</span>
                <span class="btn-submit-sub">(LANGSUNG SAH &amp; MASUK KAS)</span>
            </button>
        </form>
    </div>
</div>

<script>
function toggleAllocMode() {
    const isCustom = document.getElementById('allocCustomRadio').checked;
    document.getElementById('customAllocInputs').style.display = isCustom ? 'block' : 'none';
}
</script>
