<?php
use App\Helpers\CSRF;
use App\Helpers\View;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Catat Pembayaran Iuran</h1>
        <p class="page-subtitle">Pencatatan transaksi pembayaran tunai atau transfer dari siswa</p>
    </div>
    <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">&larr; Kembali ke Daftar Pembayaran</a>
</div>

<div style="max-width: 820px; margin: 0 auto;">
    <div class="card">
        <form method="POST" action="<?= View::url('/payments/create') ?>" id="paymentForm">
            <?= CSRF::field() ?>

            <!-- Preview No Kuitansi -->
            <div style="background: var(--bg-subtle); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border); margin-bottom: 1.25rem; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Nomor Kuitansi Transaksi:</span>
                    <div style="font-size: 1.15rem; font-weight: 700; color: var(--brand);"><?= View::e($receiptNumber) ?></div>
                </div>
                <div style="text-align: right; font-size: 0.8rem; color: var(--text-muted);">
                    Status Awal: <span class="badge badge-warning"><span class="badge-dot"></span>Menunggu Verifikasi</span>
                </div>
            </div>

            <!-- Pilih Siswa -->
            <div class="form-group">
                <label class="form-label" for="studentSelect">Pilih Siswa <span style="color:red">*</span></label>
                <select name="student_id" id="studentSelect" class="form-control" required style="font-size:0.95rem;">
                    <option value="">-- Pilih Siswa Berdasarkan Kelas / Nama / NIS --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>" 
                                data-remaining="<?= (float)$st['remaining_balance'] ?>" 
                                data-total-paid="<?= (float)$st['total_paid'] ?>"
                                <?= $selectedStudentId === (int)$st['id'] ? 'selected' : '' ?>>
                            <?= View::e($st['class_name']) ?> &mdash; <?= View::e($st['full_name']) ?> (<?= View::e($st['student_number']) ?>) &mdash; Sisa: <?= View::formatRupiah($st['remaining_balance']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="studentRemainingNotice" class="form-text" style="font-weight: 600;">
                    Silakan pilih siswa untuk melihat sisa tagihan.
                </div>
            </div>

            <!-- Nominal & Tanggal -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="paymentAmount">Nominal Pembayaran (Rp) <span style="color:red">*</span></label>
                    <input type="text" name="amount" id="paymentAmount" class="form-control input-rupiah" required placeholder="Contoh: 100.000" style="font-size:1.15rem; font-weight:700;">
                    <div class="form-text">Masukkan nominal pembayaran yang diterima.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment_date">Tanggal Pembayaran <span style="color:red">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <!-- Metode & Keterangan -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_method">Metode Pembayaran <span style="color:red">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="tunai">Tunai (Langsung ke Bendahara)</option>
                        <option value="transfer">Transfer Bank / E-Wallet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reference_note">Referensi Transfer / Catatan Pembayaran</label>
                    <input type="text" name="reference_note" id="reference_note" class="form-control" placeholder="Contoh: Transfer BCA an. Budi / Rek No. 123456">
                </div>
            </div>

            <!-- Alokasi Cicilan -->
            <div class="card" style="border: 1px solid var(--border); background: #FAFBFD; margin-top: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--primary);">Alokasi Pembayaran ke Tahap Cicilan</h3>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Total kewajiban Rp250.000 terbagi atas 3 tahap cicilan resmi:
                </p>

                <div style="margin-bottom: 0.75rem;">
                    <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer; margin-bottom:0.4rem;">
                        <input type="radio" name="allocation_type" value="auto" checked id="allocAutoRadio" onchange="toggleAllocMode()">
                        <span><strong>Alokasi Otomatis (Direkomendasikan)</strong> &mdash; Sistem mendistribusikan nominal secara berurutan ke tahap tertua yang belum lunas (DP Pertama &rarr; Cicilan Kedua &rarr; Pelunasan).</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer;">
                        <input type="radio" name="allocation_type" value="custom" id="allocCustomRadio" onchange="toggleAllocMode()">
                        <span><strong>Atur Alokasi Khusus Secara Manual</strong> &mdash; Tentukan pembagian nominal spesifik untuk masing-masing tahap.</span>
                    </label>
                </div>

                <!-- Input Khusus Alokasi Manual (Hidden by default) -->
                <div id="customAllocInputs" style="display:none; padding-top: 0.75rem; border-top: 1px solid var(--border);">
                    <div class="form-row">
                        <?php foreach ($stages as $stg): ?>
                            <div class="form-group" style="margin-bottom:0.5rem;">
                                <label class="form-label" style="font-size:0.8rem;">
                                    <?= View::e($stg['stage_name']) ?> <span style="color:var(--text-muted);">(Target <?= View::formatRupiah($stg['target_amount']) ?>)</span>
                                </label>
                                <input type="text" name="allocations[<?= $stg['id'] ?>]" class="form-control input-rupiah stage-alloc-input" placeholder="Rp 0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="<?= View::url('/payments') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Transaksi Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAllocMode() {
    const isCustom = document.getElementById('allocCustomRadio').checked;
    const customDiv = document.getElementById('customAllocInputs');
    if (customDiv) {
        customDiv.style.display = isCustom ? 'block' : 'none';
    }
}
</script>
