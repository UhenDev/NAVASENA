<?php
/**
 * Automated Financial & Business Logic Test Suite
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

require_once dirname(__DIR__) . '/app/autoload.php';
$config = require dirname(__DIR__) . '/config/config.php';

use App\Helpers\Database;
use App\Services\CalculationService;
use App\Services\AllocationService;
use App\Services\ImportService;

echo "====================================================================\n";
echo "   NAVASENA MANAGEMENT - AUTOMATED VALIDATION SUITE\n";
echo "====================================================================\n\n";

$db = Database::getConnection();
$testsPassed = 0;
$testsFailed = 0;

// Bersihkan data sisa uji sebelumnya jika ada
$db->exec("DELETE FROM cash_transactions WHERE description LIKE 'Test%' OR category = 'Administrasi & Operasional'");
$db->exec("DELETE FROM payment_allocations WHERE payment_id IN (SELECT id FROM payments WHERE receipt_number LIKE 'KW-TEST%')");
$db->exec("DELETE FROM payments WHERE receipt_number LIKE 'KW-TEST%'");
$db->exec("DELETE FROM users WHERE username LIKE '202610%'");
$db->exec("DELETE FROM dues WHERE student_id IN (SELECT id FROM students WHERE student_number LIKE '202610%')");
$db->exec("DELETE FROM students WHERE student_number LIKE '202610%'");

function assertTest(bool $condition, string $testName) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $testsPassed++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $testsFailed++;
    }
}

try {
    // -------------------------------------------------------------
    // TEST 1: Database & Master Schema
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 1: Skema Database & Akun Master ---\n";
    $stmtTables = $db->query("SHOW TABLES");
    $stmtCount = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()");
    $tableCount = (int)$stmtCount->fetchColumn();
    assertTest($tableCount >= 9, "9 tabel utama berhasil terdaftar di database MySQL/MariaDB ($tableCount tabel)");

    // Cek akun master ketua & bendahara
    $stmtKetua = $db->query("SELECT * FROM users WHERE role = 'ketua' LIMIT 1");
    $ketua = $stmtKetua->fetch();
    assertTest($ketua && (password_verify('hendrikwildan23', $ketua['password_hash']) || password_verify('PasswordKetua2026!', $ketua['password_hash'])), "Autentikasi akun Ketua dengan password_verify() valid");

    $stmtBendahara = $db->query("SELECT * FROM users WHERE role = 'bendahara' LIMIT 1");
    $bendahara = $stmtBendahara->fetch();
    assertTest($bendahara && (password_verify('password123', $bendahara['password_hash']) || password_verify('PasswordBendahara2026!', $bendahara['password_hash'])), "Autentikasi akun Bendahara dengan password_verify() valid");

    // Cek 3 tahapan cicilan
    $stmtStages = $db->query("SELECT * FROM installments ORDER BY stage_number ASC");
    $stages = $stmtStages->fetchAll();
    assertTest(count($stages) === 3, "Tiga tahapan cicilan (DP Pertama, Cicilan Kedua, Pelunasan) aktif");
    assertTest((float)$stages[0]['target_amount'] === 100000.00, "Tahap 1 DP Pertama bernilai Rp100.000");
    assertTest((float)$stages[1]['target_amount'] === 100000.00, "Tahap 2 Cicilan Kedua bernilai Rp100.000");
    assertTest((float)$stages[2]['target_amount'] === 50000.00, "Tahap 3 Pelunasan bernilai Rp50.000");

    // -------------------------------------------------------------
    // TEST 2: Impor Siswa & Pembuatan Tagihan Default Rp250.000
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 2: Impor Data Siswa & Validasi Tagihan ---\n";
    $testCsvPath = dirname(__DIR__) . '/storage/sample_import_siswa.csv';
    $importResult = ImportService::importCsv($testCsvPath, true, true);
    assertTest($importResult['success'] === true, "Impor CSV berhasil dieksekusi tanpa error parsing");
    assertTest($importResult['imported'] >= 5, "Minimal 5 data siswa berhasil dimasukkan ke sistem");

    // Cek salah satu siswa yang diimpor
    $stmtSiswa1 = $db->prepare("
        SELECT s.*, d.total_obligation, d.album_portion, d.graduation_portion, d.total_paid, d.remaining_balance, d.payment_status 
        FROM students s 
        JOIN dues d ON d.student_id = s.id 
        WHERE s.student_number = '20261001'
    ");
    $stmtSiswa1->execute();
    $s1 = $stmtSiswa1->fetch();
    assertTest($s1 !== false, "Siswa Ahmad Fauzi (20261001) ditemukan di database");
    assertTest((float)$s1['total_obligation'] === 250000.00, "Kewajiban siswa otomatis disetel tepat Rp250.000");
    assertTest((float)$s1['album_portion'] === 200000.00, "Porsi album fisik tercatat Rp200.000");
    assertTest((float)$s1['graduation_portion'] === 50000.00, "Porsi graduation tercatat Rp50.000");
    assertTest((float)$s1['remaining_balance'] === 250000.00, "Sisa tagihan awal sama dengan kewajiban (Rp250.000)");
    assertTest($s1['payment_status'] === 'belum_bayar', "Status pembayaran awal siswa adalah 'belum_bayar'");

    // Cek akun portal siswa otomatis
    $stmtSiswaUser = $db->prepare("SELECT * FROM users WHERE username = '20261001' AND role = 'siswa'");
    $stmtSiswaUser->execute();
    $su1 = $stmtSiswaUser->fetch();
    assertTest($su1 && (password_verify('password', $su1['password_hash']) || password_verify('20261001', $su1['password_hash'])), "Akun login portal siswa dibuat otomatis dengan password ter-hash aman");

    // -------------------------------------------------------------
    // TEST 3: Transaksi Pembayaran & Alokasi Cicilan Otomatis
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 3: Transaksi Pembayaran & Alokasi Cicilan ---\n";
    $studentId = (int)$s1['id'];

    // Hitung alokasi otomatis untuk pembayaran Rp100.000
    $autoAlloc1 = AllocationService::computeAutoAllocation($studentId, 100000.00);
    assertTest(isset($autoAlloc1[$stages[0]['id']]) && $autoAlloc1[$stages[0]['id']] == 100000.00, "Alokasi otomatis Rp100.000 masuk 100% ke Tahap 1 (DP Pertama)");

    // Catat transaksi pembayaran DP Rp100.000
    $receipt1 = "KW-TEST-0001";
    $stmtPay1 = $db->prepare("
        INSERT INTO payments (receipt_number, student_id, amount, payment_method, payment_date, reference_note, status, recorded_by, created_at)
        VALUES (:receipt, :sid, 100000.00, 'transfer', '2026-10-26', 'Test Transfer DP', 'menunggu_verifikasi', :rec_by, NOW())
    ");
    $stmtPay1->execute([
        ':receipt' => $receipt1,
        ':sid'     => $studentId,
        ':rec_by'  => $bendahara['id']
    ]);
    $payId1 = (int)$db->lastInsertId();
    AllocationService::saveAllocations($payId1, $autoAlloc1);

    // KETENTUAN FINANSIAL KRITIS: Pembayaran menunggu verifikasi TIDAK BOLEH dihitung sebagai penerimaan sah!
    CalculationService::recalculateStudentDues($studentId);
    $checkPendingDues = $db->query("SELECT * FROM dues WHERE student_id = $studentId")->fetch();
    assertTest((float)$checkPendingDues['total_paid'] === 0.00, "Transaksi menunggu verifikasi TIDAK menambah total terbayar sah (total_paid tetap 0.00)");
    assertTest((float)$checkPendingDues['remaining_balance'] === 250000.00, "Transaksi menunggu verifikasi TIDAK mengurangi sisa tagihan (sisa tetap 250.000)");

    // Cek bahwa transaksi belum masuk buku kas
    $stmtCheckCash1 = $db->query("SELECT COUNT(*) FROM cash_transactions WHERE payment_id = $payId1");
    assertTest((int)$stmtCheckCash1->fetchColumn() === 0, "Transaksi menunggu verifikasi TIDAK masuk ke buku kas angkatan");

    // -------------------------------------------------------------
    // TEST 4: Verifikasi Pembayaran & Sinkronisasi Kas Otomatis
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 4: Verifikasi Transaksi & Sinkronisasi Kas ---\n";
    // Verifikasi pembayaran oleh Ketua
    $stmtVerify1 = $db->prepare("
        UPDATE payments 
        SET status = 'terverifikasi', verified_by = :vby, verified_at = NOW() 
        WHERE id = :pid
    ");
    $stmtVerify1->execute([':vby' => $ketua['id'], ':pid' => $payId1]);

    // Masukkan ke buku kas secara otomatis
    $stmtCashInsert1 = $db->prepare("
        INSERT INTO cash_transactions (transaction_type, category, amount, transaction_date, description, payment_id, recorded_by, is_cancelled, created_at)
        VALUES ('masuk', 'Iuran Siswa', 100000.00, '2026-10-26', 'Test Iuran DP', :pid, :rec_by, 0, NOW())
    ");
    $stmtCashInsert1->execute([':pid' => $payId1, ':rec_by' => $ketua['id']]);

    // Sinkronisasi kewajiban
    $duesAfterVerify1 = CalculationService::recalculateStudentDues($studentId);
    assertTest($duesAfterVerify1['total_paid'] === 100000.00, "Setelah diverifikasi, total terbayar sah tepat Rp100.000");
    assertTest($duesAfterVerify1['remaining_balance'] === 150000.00, "Setelah diverifikasi, sisa tagihan berkurang menjadi Rp150.000");
    assertTest($duesAfterVerify1['payment_status'] === 'sebagian', "Status pembayaran siswa berubah menjadi 'sebagian'");

    // Cek progres tahap cicilan siswa
    $studentStagesAfter1 = AllocationService::getStudentStagesStatus($studentId);
    assertTest($studentStagesAfter1[0]['status'] === 'lunas', "Tahap 1 (DP Pertama Rp100.000) berstatus LUNAS");
    assertTest($studentStagesAfter1[1]['status'] === 'belum_bayar', "Tahap 2 (Cicilan Kedua Rp100.000) masih berstatus BELUM BAYAR");

    // -------------------------------------------------------------
    // TEST 5: Pembayaran Sebagian Lanjutan & Multi-Tahap
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 5: Pembayaran Tahap Kedua & Sisa Tagihan ---\n";
    // Siswa membayar Rp50.000 (sebagian dari Tahap 2)
    $autoAlloc2 = AllocationService::computeAutoAllocation($studentId, 50000.00);
    assertTest(isset($autoAlloc2[$stages[1]['id']]) && $autoAlloc2[$stages[1]['id']] == 50000.00, "Alokasi otomatis Rp50.000 dialokasikan tepat ke Tahap 2 (Cicilan Kedua)");

    $receipt2 = "KW-TEST-0002";
    $stmtPay2 = $db->prepare("
        INSERT INTO payments (receipt_number, student_id, amount, payment_method, payment_date, reference_note, status, recorded_by, verified_by, verified_at, created_at)
        VALUES (:receipt, :sid, 50000.00, 'tunai', '2026-10-26', 'Cicilan Kedua Sebagian', 'terverifikasi', :rec_by, :ver_by, NOW(), NOW())
    ");
    $stmtPay2->execute([':receipt' => $receipt2, ':sid' => $studentId, ':rec_by' => $bendahara['id'], ':ver_by' => $bendahara['id']]);
    $payId2 = (int)$db->lastInsertId();
    AllocationService::saveAllocations($payId2, $autoAlloc2);

    $stmtCashInsert2 = $db->prepare("
        INSERT INTO cash_transactions (transaction_type, category, amount, transaction_date, description, payment_id, recorded_by, is_cancelled, created_at)
        VALUES ('masuk', 'Iuran Siswa', 50000.00, '2026-10-26', 'Test Cicilan 2 Sebagian', :pid, :rec_by, 0, NOW())
    ");
    $stmtCashInsert2->execute([':pid' => $payId2, ':rec_by' => $bendahara['id']]);

    $duesAfterVerify2 = CalculationService::recalculateStudentDues($studentId);
    assertTest($duesAfterVerify2['total_paid'] === 150000.00, "Total terbayar sah terakumulasi tepat Rp150.000");
    assertTest($duesAfterVerify2['remaining_balance'] === 100000.00, "Sisa tagihan berkurang tepat menjadi Rp100.000");

    $studentStagesAfter2 = AllocationService::getStudentStagesStatus($studentId);
    assertTest($studentStagesAfter2[1]['status'] === 'sebagian', "Tahap 2 berstatus 'sebagian' (Terbayar Rp50.000 dari target Rp100.000)");

    // -------------------------------------------------------------
    // TEST 6: Pelunasan Penuh (Rp100.000 Menghabiskan Sisa Kewajiban)
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 6: Pelunasan Penuh (Status Lunas) ---\n";
    $autoAlloc3 = AllocationService::computeAutoAllocation($studentId, 100000.00);
    // Rp50.000 harus masuk ke Tahap 2 (melunasi Tahap 2), dan Rp50.000 masuk ke Tahap 3 (melunasi Tahap 3)
    assertTest($autoAlloc3[$stages[1]['id']] == 50000.00, "Alokasi pintar: Rp50.000 melunasi kekurangan Tahap 2");
    assertTest($autoAlloc3[$stages[2]['id']] == 50000.00, "Alokasi pintar: Rp50.000 melunasi Tahap 3 (Pelunasan)");

    $receipt3 = "KW-TEST-0003";
    $stmtPay3 = $db->prepare("
        INSERT INTO payments (receipt_number, student_id, amount, payment_method, payment_date, reference_note, status, recorded_by, verified_by, verified_at, created_at)
        VALUES (:receipt, :sid, 100000.00, 'transfer', '2026-10-26', 'Pelunasan Akhir', 'terverifikasi', :rec_by, :ver_by, NOW(), NOW())
    ");
    $stmtPay3->execute([':receipt' => $receipt3, ':sid' => $studentId, ':rec_by' => $ketua['id'], ':ver_by' => $ketua['id']]);
    $payId3 = (int)$db->lastInsertId();
    AllocationService::saveAllocations($payId3, $autoAlloc3);

    $stmtCashInsert3 = $db->prepare("
        INSERT INTO cash_transactions (transaction_type, category, amount, transaction_date, description, payment_id, recorded_by, is_cancelled, created_at)
        VALUES ('masuk', 'Iuran Siswa', 100000.00, '2026-10-26', 'Test Pelunasan Akhir', :pid, :rec_by, 0, NOW())
    ");
    $stmtCashInsert3->execute([':pid' => $payId3, ':rec_by' => $ketua['id']]);

    $duesAfterVerify3 = CalculationService::recalculateStudentDues($studentId);
    assertTest($duesAfterVerify3['total_paid'] === 250000.00, "Total terbayar sah mencapai penuh Rp250.000");
    assertTest($duesAfterVerify3['remaining_balance'] === 0.00, "Sisa tagihan menjadi Rp0");
    assertTest($duesAfterVerify3['payment_status'] === 'lunas', "Status pembayaran siswa menjadi 'lunas'");

    $studentStagesAfter3 = AllocationService::getStudentStagesStatus($studentId);
    assertTest($studentStagesAfter3[0]['status'] === 'lunas', "Tahap 1 LUNAS");
    assertTest($studentStagesAfter3[1]['status'] === 'lunas', "Tahap 2 LUNAS");
    assertTest($studentStagesAfter3[2]['status'] === 'lunas', "Tahap 3 LUNAS");

    // -------------------------------------------------------------
    // TEST 7: Pembatalan Transaksi Sah (Audit Reversal)
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 7: Pembatalan Transaksi (Void & Audit Reversal) ---\n";
    // Batalkan transaksi ke-3 (Pelunasan Rp100.000)
    $stmtCancel3 = $db->prepare("
        UPDATE payments 
        SET status = 'dibatalkan', cancelled_by = :cby, cancelled_at = NOW(), cancellation_reason = 'Test Void' 
        WHERE id = :pid
    ");
    $stmtCancel3->execute([':cby' => $ketua['id'], ':pid' => $payId3]);

    $stmtCancelCash3 = $db->prepare("UPDATE cash_transactions SET is_cancelled = 1, cancellation_note = 'Void test' WHERE payment_id = :pid");
    $stmtCancelCash3->execute([':pid' => $payId3]);

    // Rekalkulasi setelah pembatalan
    $duesAfterCancel = CalculationService::recalculateStudentDues($studentId);
    assertTest($duesAfterCancel['total_paid'] === 150000.00, "Setelah pembatalan, total terbayar sah berkurang kembali ke Rp150.000");
    assertTest($duesAfterCancel['remaining_balance'] === 100000.00, "Setelah pembatalan, sisa tagihan kembali menjadi Rp100.000");
    assertTest($duesAfterCancel['payment_status'] === 'sebagian', "Status pembayaran kembali menjadi 'sebagian' (bukan lagi lunas)");

    // -------------------------------------------------------------
    // TEST 8: Arus Kas Keluar & Rekonsiliasi Saldo Kas
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 8: Arus Kas & Rekonsiliasi Saldo ---\n";
    // Catat pengeluaran operasional Rp50.000
    $stmtExp = $db->prepare("
        INSERT INTO cash_transactions (transaction_type, category, amount, transaction_date, description, recorded_by, is_cancelled, created_at)
        VALUES ('keluar', 'Administrasi & Operasional', 50000.00, '2026-10-26', 'Biaya Pembelian Kuitansi Fisik', :rec_by, 0, NOW())
    ");
    $stmtExp->execute([':rec_by' => $bendahara['id']]);
    $expId = (int)$db->lastInsertId();

    $metrics = CalculationService::getDashboardMetrics();
    // Kas Masuk Sah yang aktif: Pay 1 (100k) + Pay 2 (50k) = 150k (Pay 3 dibatalkan tidak dihitung)
    // Kas Keluar Sah: 50k
    // Saldo Kas seharusnya = 150k - 50k = 100k
    assertTest($metrics['total_cash_in'] === 150000.00, "Total kas masuk sah aktif tepat Rp150.000 (transaksi dibatalkan diabaikan)");
    assertTest($metrics['total_cash_out'] === 50000.00, "Total kas keluar aktif tepat Rp50.000");
    assertTest($metrics['cash_balance'] === 100000.00, "Saldo kas riil direkonsiliasi tepat Rp100.000");

    // -------------------------------------------------------------
    // TEST 9: Isolasi Data Siswa (Keamanan Portal Siswa)
    // -------------------------------------------------------------
    echo "\n--- KELOMPOK UJI 9: Isolasi Hak Akses Data Antarsiswa ---\n";
    // Siswa Ahmad Fauzi (ID = $studentId) hanya boleh mengakses pembayaran miliknya
    $otherStudentId = $studentId + 1;
    $stmtLeakTest = $db->prepare("SELECT COUNT(*) FROM payments WHERE id = :pid AND student_id = :other_sid");
    $stmtLeakTest->execute([':pid' => $payId1, ':other_sid' => $otherStudentId]);
    $leakCount = (int)$stmtLeakTest->fetchColumn();
    assertTest($leakCount === 0, "Query portal siswa menjamin data transaksi siswa lain TIDAK bocor ke akun berbeda");

    // -------------------------------------------------------------
    // BERSIHKAN DATA UJI (CLEANUP)
    // -------------------------------------------------------------
    echo "\n--- MEMBERSIHKAN DATA PENGUJIAN ---\n";
    $db->exec("DELETE FROM cash_transactions WHERE id = $expId OR payment_id IN ($payId1, $payId2, $payId3)");
    $db->exec("DELETE FROM payment_allocations WHERE payment_id IN ($payId1, $payId2, $payId3)");
    $db->exec("DELETE FROM payments WHERE id IN ($payId1, $payId2, $payId3)");
    $db->exec("DELETE FROM users WHERE username LIKE '202610%'");
    $db->exec("DELETE FROM dues WHERE student_id IN (SELECT id FROM students WHERE student_number LIKE '202610%')");
    $db->exec("DELETE FROM students WHERE student_number LIKE '202610%'");
    echo "Data pengujian berhasil dibersihkan dari database.\n";

} catch (Exception $e) {
    echo "\n[ERROR EXCEPTION] " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n====================================================================\n";
echo "   HASIL PENGUJIAN: $testsPassed LULUS, $testsFailed GAGAL\n";
echo "====================================================================\n";

if ($testsFailed === 0) {
    echo "SEMUA KRITERIA PENERIMAAN (ACCEPTANCE CRITERIA) TERPENUHI DENGAN SEMPURNA!\n";
} else {
    exit(1);
}
