<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <div style="font-family: var(--font-display); font-size: 0.775rem; font-weight: 700; color: var(--gold-text); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.25rem;">
            Modul Bendahara Angkatan &bull; Quick Input
        </div>
        <h1 class="page-title">
            Catat Pembayaran <span class="gold-highlight">Iuran Siswa</span>
        </h1>
        <p class="page-subtitle">Pencatatan transaksi tunai atau transfer bank dengan alokasi otomatis 3 tahap cicilan</p>
    </div>
    <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Pembayaran</a>
</div>

<div style="max-width: 860px; margin: 0 auto;">
    <div class="card card-gold">
        <!-- Header Identitas Transaksi -->
        <div style="display:flex; justify-content:space-between; align-items:center; background: var(--glass-inner); padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--glass-border-gold); margin-bottom: 1.5rem;">
            <div>
                <span style="font-size: 0.725rem; color: var(--gold-text); text-transform: uppercase; font-weight: 700; letter-spacing: 0.08em;">No. Kuitansi Otomatis:</span>
                <div class="receipt-code tabular-nums" style="font-size: 1.25rem; font-weight: 800; color: #FFF;"><?= View::e($receiptNumber) ?></div>
            </div>
            <div style="text-align: right;">
                <span class="status-pill status-pending" style="font-size: 0.7rem;">STATUS AWAL: MENUNGGU VERIFIKASI</span>
                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 3px;">Audit Reversal aktif jika koreksi catat</div>
            </div>
        </div>

        <form method="POST" action="<?= View::url('/payments/create') ?>" id="paymentForm">
            <?= CSRF::field() ?>

            <!-- 1. Pilih Siswa (Live Remaining Notice) -->
            <div class="form-group">
                <label class="form-label" for="studentSelect">Pilih Siswa (492 Anggota Angkatan) <span style="color:var(--void-fg)">*</span></label>
                <select name="student_id" id="studentSelect" class="form-control" required style="font-size:0.95rem;">
                    <option value="">-- Pilih Siswa Berdasarkan Kelas / Nama / NIS --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>" 
                                data-remaining="<?= (float)$st['remaining_balance'] ?>" 
                                data-total-paid="<?= (float)$st['total_paid'] ?>"
                                <?= $selectedStudentId === (int)$st['id'] ? 'selected' : '' ?>>
                            <?= View::e($st['class_name']) ?> &bull; <?= View::e($st['full_name']) ?> (<?= View::e($st['student_number']) ?>) &bull; Sisa: <?= View::formatRupiah($st['remaining_balance']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="studentRemainingNotice" class="form-text" style="font-size:0.825rem; font-weight: 700; margin-top:0.4rem; color:var(--gold-solid);">
                    Silakan pilih siswa untuk melihat sisa tagihan.
                </div>
            </div>

            <!-- 2. Nominal & Quick Chips -->
            <div class="form-row">
                <div class="form-group" style="flex: 1.5;">
                    <label class="form-label" for="paymentAmount">Nominal Diterima (Rp) <span style="color:var(--void-fg)">*</span></label>
                    <div style="position:relative;">
                        <input type="text" name="amount" id="paymentAmount" class="form-control input-rupiah tabular-nums" required placeholder="Contoh: 100.000" style="font-size:1.4rem; font-weight:800; color:var(--gold-solid); padding-left:1.25rem;">
                    </div>
                    
                    <!-- Quick-fill Chips -->
                    <div class="quick-nominal-chips">
                        <span style="font-size: 0.725rem; color: var(--text-muted); align-self: center; margin-right: 0.25rem;">Pilih Cepat:</span>
                        <button type="button" class="chip-btn" data-value="50000">Rp 50.000</button>
                        <button type="button" class="chip-btn" data-value="100000">Rp 100.000 (DP)</button>
                        <button type="button" class="chip-btn" data-value="150000">Rp 150.000</button>
                        <button type="button" class="chip-btn" id="chipPayFull" data-value="250000" style="border-color:var(--gold-solid); font-weight:800;">Pelunasan Sisa</button>
                    </div>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label class="form-label" for="payment_date">Tanggal Transaksi <span style="color:var(--void-fg)">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control tabular-nums" value="<?= date('Y-m-d') ?>" required>
                    <div class="form-text">Tanggal dana masuk kas angkatan.</div>
                </div>
            </div>

            <!-- 3. Metode Pembayaran & Catatan -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran <span style="color:var(--void-fg)">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required style="display:none;">
                        <option value="transfer" selected>Transfer Bank / E-Wallet</option>
                        <option value="tunai">Setor Tunai (Langsung ke Bendahara)</option>
                    </select>

                    <div class="method-toggle-group">
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

                <div class="form-group">
                    <label class="form-label" for="reference_note">Referensi / Nomor Bukti Transfer</label>
                    <input type="text" name="reference_note" id="reference_note" class="form-control" placeholder="Contoh: Transfer BCA an. Wali Siswa / ID: 948271">
                </div>
            </div>

            <!-- 4. Tactile Glass Keypad (Responsive Thumbs for Mobile) -->
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Keypad Input Sentuh (Cepat)</span>
                    <span style="font-size:0.7rem; color:var(--text-muted);">Khusus Layar Sentuh / Handphone</span>
                </label>
                <div class="glass-keypad">
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
                    <button type="button" class="keypad-btn" data-key="backspace" style="color:var(--void-fg);">⌫</button>
                </div>
            </div>

            <!-- 5. Opsi Alokasi Cicilan 3 Tahap -->
            <div class="card" style="background: var(--glass-inner); border-color: var(--glass-border); margin: 1.5rem 0;">
                <div style="font-weight: 700; color: var(--gold-text); margin-bottom: 0.35rem; font-size: 0.95rem;">
                    Alokasi Otomatis Cicilan Resmi
                </div>
                <p style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 0.85rem;">
                    Sistem mendistribusikan pembayaran secara otomatis: Tahap 1 (DP Rp100.000) &rarr; Tahap 2 (Rp100.000) &rarr; Tahap 3 (Pelunasan Rp50.000).
                </p>

                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    <label style="display:flex; align-items:center; gap:0.65rem; font-size:0.875rem; cursor:pointer;">
                        <input type="radio" name="allocation_type" value="auto" checked id="allocAutoRadio" onchange="toggleAllocMode()">
                        <span><strong>Alokasi Otomatis (Direkomendasikan)</strong> &mdash; Mencegah kesalahan drift nominal.</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:0.65rem; font-size:0.875rem; cursor:pointer;">
                        <input type="radio" name="allocation_type" value="custom" id="allocCustomRadio" onchange="toggleAllocMode()">
                        <span><strong>Alokasi Khusus Manual</strong> &mdash; Tentukan pembagian tahap secara spesifik.</span>
                    </label>
                </div>

                <div id="customAllocInputs" style="display:none; padding-top: 1rem; margin-top: 1rem; border-top: 1px solid var(--glass-border);">
                    <div class="form-row">
                        <?php foreach ($stages as $stg): ?>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.775rem;">Tahap <?= $stg['stage_number'] ?>: <?= View::e($stg['stage_name']) ?></label>
                                <input type="text" name="custom_allocations[<?= $stg['stage_number'] ?>]" class="form-control input-rupiah tabular-nums" placeholder="0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; letter-spacing: 0.05em; font-size: 1rem;">
                SIMPAN TRANSAKSI PEMBAYARAN
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
