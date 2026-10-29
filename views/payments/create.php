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

<div style="max-width: 900px; margin: 0 auto;">
    <div class="card card-gold" style="border-color: var(--glass-border-gold); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gold-gradient);"></div>

        <!-- Header Identitas Transaksi & No. Kuitansi Otomatis -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(11, 16, 29, 0.75); padding: 1.15rem 1.35rem; border-radius: var(--radius-md); border: 1px solid var(--glass-border); margin-bottom: 1.75rem;">
            <div>
                <span style="font-size: 0.725rem; color: var(--gold-text); text-transform: uppercase; font-weight: 700; letter-spacing: 0.08em;">No. Kuitansi Otomatis:</span>
                <div class="receipt-code tabular-nums" style="font-family: var(--font-mono); font-size: 1.35rem; font-weight: 800; color: var(--gold-solid); margin-top: 2px;">
                    <?= View::e($receiptNumber) ?>
                </div>
            </div>
            <div style="text-align: right;">
                <span class="status-pill status-pending" style="font-size: 0.7rem;">STATUS AWAL: MENUNGGU VERIFIKASI</span>
                <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 4px; font-family: var(--font-mono);">
                    Audit Reversal aktif jika terjadi koreksi catat
                </div>
            </div>
        </div>

        <form method="POST" action="<?= View::url('/payments/create') ?>" id="paymentForm">
            <?= CSRF::field() ?>

            <!-- Grid 2 Kolom Input Transaksi -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Kolom Kiri: Pilih Siswa & Nominal -->
                <div>
                    <!-- 1. Pilih Siswa -->
                    <div class="form-group">
                        <label class="form-label" for="studentSelect" style="display: flex; justify-content: space-between;">
                            <span>Pilih Siswa Aktif <span style="color:var(--void-fg)">*</span></span>
                            <span style="font-size: 0.725rem; color: var(--text-muted); font-weight: normal;">489 Siswa Angkatan</span>
                        </label>
                        <div style="position: relative; margin-bottom: 0.5rem;">
                            <input type="text" id="studentSearchFilter" class="form-control" placeholder="🔍 Ketik nama, NIS, atau kelas untuk memfilter..." style="font-size: 0.85rem; border-color: var(--glass-border-gold);" autocomplete="off">
                        </div>
                        <select name="student_id" id="studentSelect" class="form-control" required style="font-size: 0.9rem;">
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
                        <div id="studentRemainingNotice" style="margin-top: 0.5rem; padding: 0.6rem 0.85rem; border-radius: var(--radius-sm); background: rgba(223, 178, 86, 0.08); border: 1px solid var(--glass-border-gold); font-size: 0.8rem; color: var(--gold-text); font-family: var(--font-mono); font-weight: 600;">
                            Silakan pilih siswa untuk melihat sisa tagihan aktif.
                        </div>
                    </div>

                    <!-- 2. Nominal Diterima & Quick Chips -->
                    <div class="form-group" style="margin-top: 1.25rem;">
                        <label class="form-label" for="paymentAmount">
                            Nominal Diterima (Rp) <span style="color:var(--void-fg)">*</span>
                        </label>
                        <div style="position: relative;">
                            <input type="text" name="amount" id="paymentAmount" class="form-control input-rupiah tabular-nums" required placeholder="Contoh: 100.000" style="font-family: var(--font-mono); font-size: 1.5rem; font-weight: 800; color: var(--verified-fg); padding: 0.75rem 1rem;">
                        </div>

                        <!-- Quick-fill Chips -->
                        <div class="quick-nominal-chips" style="margin-top: 0.75rem;">
                            <button type="button" class="chip-btn" data-value="50000">Rp 50.000</button>
                            <button type="button" class="chip-btn" data-value="100000">Rp 100.000 (DP 1)</button>
                            <button type="button" class="chip-btn" data-value="150000">Rp 150.000</button>
                            <button type="button" class="chip-btn" id="chipPayFull" data-value="250000" style="border-color: var(--gold-solid); font-weight: 800; color: var(--gold-solid);">Pelunasan Sisa</button>
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

                        <div class="method-toggle-group" style="margin-bottom: 0.75rem;">
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
                        <input type="date" name="payment_date" id="payment_date" class="form-control tabular-nums" value="<?= date('Y-m-d') ?>" required style="font-family: var(--font-mono);">
                    </div>

                    <!-- 5. Referensi / Nomor Bukti -->
                    <div class="form-group">
                        <label class="form-label" for="reference_note">Nomor Referensi / Catatan Bukti</label>
                        <input type="text" name="reference_note" id="reference_note" class="form-control" placeholder="Contoh: Transfer BCA an. Wali Siswa / Resi: 83921">
                    </div>
                </div>
            </div>

            <!-- 6. Tactile Glass Keypad (Responsive Mobile / Toggleable) -->
            <div class="card" style="background: rgba(11, 16, 29, 0.5); border-color: var(--glass-border); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <div>
                        <span style="font-weight: 700; color: #FFFFFF; font-size: 0.9rem;">Keypad Input Sentuh (Tactile Glass)</span>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Mendukung penginputan cepat jari di layar sentuh mobile</div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" id="toggleKeypadBtn" onclick="var kp = document.getElementById('glassKeypadGrid'); kp.style.display = (kp.style.display === 'none') ? 'grid' : 'none';">
                        Buka / Tutup Keypad
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

            <!-- 7. Opsi Alokasi Cicilan 3 Tahap -->
            <div class="card" style="background: rgba(11, 16, 29, 0.6); border-color: var(--glass-border); margin-bottom: 1.75rem; padding: 1.25rem;">
                <div style="font-weight: 700; color: var(--gold-text); margin-bottom: 0.35rem; font-size: 0.95rem;">
                    Mode Alokasi Cicilan Resmi (0 Drift Engine)
                </div>
                <p style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 0.85rem;">
                    Sistem mendistribusikan pembayaran secara otomatis: Tahap 1 (DP Rp100.000) &rarr; Tahap 2 (Rp100.000) &rarr; Tahap 3 (Pelunasan Rp50.000).
                </p>

                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    <label style="display:flex; align-items:center; gap:0.65rem; font-size:0.875rem; cursor:pointer;">
                        <input type="radio" name="allocation_type" value="auto" checked id="allocAutoRadio" onchange="toggleAllocMode()" style="accent-color: var(--gold-solid);">
                        <span style="color: #FFFFFF;"><strong>Alokasi Otomatis (Direkomendasikan)</strong> &mdash; Mencegah kesalahan drift nominal.</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:0.65rem; font-size:0.875rem; cursor:pointer;">
                        <input type="radio" name="allocation_type" value="custom" id="allocCustomRadio" onchange="toggleAllocMode()" style="accent-color: var(--gold-solid);">
                        <span style="color: var(--text-secondary);"><strong>Alokasi Khusus Manual</strong> &mdash; Tentukan pembagian tahap secara spesifik.</span>
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

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; letter-spacing: 0.06em; font-size: 1.05rem; padding: 1rem; box-shadow: 0 4px 20px rgba(223, 178, 86, 0.35);">
                + SIMPAN &amp; AJUKAN VERIFIKASI PEMBAYARAN
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
