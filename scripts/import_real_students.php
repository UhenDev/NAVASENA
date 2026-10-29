<?php
/**
 * CLI Runner: Import 489 Real Students into Navasena Database
 * GUARDIAN OF NAVASENA 27 — SMKN 1 PANYINGKIRAN
 */

require_once dirname(__DIR__) . '/app/autoload.php';

use App\Helpers\Database;
use App\Services\ImportService;

echo "=================================================================\n";
echo "  NAVASENA 27: IMPOR DATA SISWA RESMI ANGKATAN (489 SISWA)       \n";
echo "=================================================================\n\n";

$csvPath = dirname(__DIR__) . '/storage/data_siswa_492.csv';

if (!file_exists($csvPath)) {
    die("ERROR: Berkas CSV tidak ditemukan di: $csvPath\n");
}

echo "Memeriksa koneksi database...\n";
try {
    $db = Database::getConnection();
    echo ">> Database terhubung!\n\n";
} catch (Exception $e) {
    die(">> GAGAL KONEKSI DATABASE: " . $e->getMessage() . "\n");
}

echo "Memulai proses impor data siswa & pembuatan akun...\n";
$start = microtime(true);

$result = ImportService::importCsv($csvPath, true, true);

$elapsed = round(microtime(true) - $start, 3);

echo "\n-----------------------------------------------------------------\n";
echo "HASIL IMPOR:\n";
echo "- Status      : " . ($result['success'] ? 'BERHASIL' : 'GAGAL') . "\n";
echo "- Pesan       : " . $result['message'] . "\n";
echo "- Dimasukkan  : " . $result['imported'] . " siswa\n";
echo "- Dilewati    : " . $result['skipped'] . " siswa\n";
echo "- Waktu Proses: " . $elapsed . " detik\n";
if (!empty($result['errors'])) {
    echo "- Catatan/Error: " . count($result['errors']) . " item\n";
    foreach (array_slice($result['errors'], 0, 5) as $err) {
        echo "  * $err\n";
    }
}
echo "-----------------------------------------------------------------\n";

// Update target_students_count ke 489
try {
    $stmt = $db->prepare("UPDATE settings SET setting_value = '489' WHERE setting_key = 'target_students_count'");
    $stmt->execute();
    echo ">> Target siswa angkatan di settings telah diperbarui menjadi 489 siswa.\n";
} catch (Exception $e) {
    // ignore
}

echo "\nSELESAI!\n";

