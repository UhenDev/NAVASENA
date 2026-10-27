<?php
/**
 * Script untuk Menghapus Semua Data Siswa
 * NAVASENA MANAGEMENT — SMKN 1 PANYINGKIRAN
 */

require_once __DIR__ . '/../app/autoload.php';

use App\Helpers\Database;
use App\Helpers\Logger;

echo "====================================================================\n";
echo "   NAVASENA MANAGEMENT - PENGHAPUSAN SELURUH DATA SISWA\n";
echo "====================================================================\n\n";

$db = Database::getConnection();

try {
    // 1. Hitung data sebelum dihapus
    $countStudents = (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $countUsers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();
    $countDues = (int)$db->query("SELECT COUNT(*) FROM dues")->fetchColumn();
    $countPayments = (int)$db->query("SELECT COUNT(*) FROM payments")->fetchColumn();

    echo "Data yang terdeteksi sebelum penghapusan:\n";
    echo "- Siswa (students)        : $countStudents data\n";
    echo "- Akun Siswa (users)      : $countUsers akun\n";
    echo "- Kewajiban Iuran (dues)  : $countDues data\n";
    echo "- Transaksi (payments)    : $countPayments data\n\n";

    Database::beginTransaction();

    // 2. Hapus alokasi pembayaran & pembayaran siswa
    $db->exec("DELETE FROM payment_allocations");
    $db->exec("DELETE FROM payments");
    $db->exec("DELETE FROM cash_transactions WHERE payment_id IS NOT NULL");

    // 3. Hapus kewajiban iuran siswa
    $db->exec("DELETE FROM dues");

    // 4. Hapus akun portal mandiri siswa di tabel users
    $db->exec("DELETE FROM users WHERE role = 'siswa'");

    // 5. Hapus semua data siswa
    $db->exec("DELETE FROM students");

    // 6. Catat audit log
    Logger::log(
        'STUDENT_PURGE',
        'students',
        'ALL',
        "Seluruh data siswa ($countStudents siswa) dan akun login portal ($countUsers akun) berhasil dihapus bersih."
    );

    if ($db->inTransaction()) {
        Database::commit();
    }

    // 7. Reset auto-increment (DDL causes implicit commit, executed outside transaction)
    $db->exec("ALTER TABLE students AUTO_INCREMENT = 1");
    $db->exec("ALTER TABLE dues AUTO_INCREMENT = 1");
    $db->exec("ALTER TABLE payments AUTO_INCREMENT = 1");
    $db->exec("ALTER TABLE payment_allocations AUTO_INCREMENT = 1");

    echo "[SUKSES] Seluruh data siswa berhasil dihapus bersih dari database!\n";
    echo "- Sisa siswa aktif: 0 data\n";
    echo "- Akun Master Ketua (uhen) & Bendahara (bendahara) tetap aman dan aktif.\n\n";

} catch (Exception $e) {
    if ($db->inTransaction()) {
        Database::rollBack();
    }
    echo "[GAGAL] Terjadi kesalahan: " . $e->getMessage() . "\n";
    exit(1);
}
