<?php
/**
 * Import Service - Flexible CSV UTF-8 Import for Student Data
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

namespace App\Services;

use App\Helpers\Database;
use App\Helpers\Logger;
use PDO;
use Exception;

class ImportService
{
    /**
     * Memproses file CSV siswa yang diunggah
     */
    public static function importCsv(string $filePath, bool $createAccounts = true, bool $updateExisting = false): array
    {
        // Alokasikan waktu eksekusi yang cukup untuk impor dataset besar
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [
                'success'  => false,
                'message'  => 'File tidak ditemukan atau tidak dapat dibaca.',
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['File tidak valid']
            ];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [
                'success'  => false,
                'message'  => 'Gagal membuka file.',
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['File gagal dibuka']
            ];
        }

        // Hapus UTF-8 BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($handle);
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        // Baca baris header
        $headerRaw = fgetcsv($handle, 0, $delimiter);
        if (!$headerRaw) {
            fclose($handle);
            return [
                'success'  => false,
                'message'  => 'File CSV kosong atau format tidak sesuai.',
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['Header tidak ditemukan']
            ];
        }

        // Normalisasi header
        $headers = array_map(function ($h) {
            $h = strtolower(trim($h));
            $h = preg_replace('/[^a-z0-9_]/', '_', $h);
            return trim($h, '_');
        }, $headerRaw);

        // Petakan indeks kolom
        $colMap = self::detectColumns($headers);

        if ($colMap['student_number'] === null || $colMap['full_name'] === null || $colMap['class_name'] === null) {
            fclose($handle);
            return [
                'success'  => false,
                'message'  => 'Format CSV tidak valid. Wajib memiliki kolom Nomor Siswa/NIS, Nama Lengkap, dan Kelas.',
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['Kolom wajib tidak ditemukan pada header CSV']
            ];
        }

        $db = Database::getConnection();
        Database::beginTransaction();

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $seenInFile = [];
        $rowNumber = 1;

        try {
            // Ambil setting kewajiban iuran
            $stmtSet = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'total_dues_per_student' LIMIT 1");
            $totalDues = (float)($stmtSet->fetchColumn() ?: 250000.00);

            $stmtAlbum = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'album_fee' LIMIT 1");
            $albumFee = (float)($stmtAlbum->fetchColumn() ?: 200000.00);

            $stmtGrad = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'graduation_fee' LIMIT 1");
            $gradFee = (float)($stmtGrad->fetchColumn() ?: 50000.00);

            $stmtCheck = $db->prepare("SELECT id, status FROM students WHERE student_number = :number LIMIT 1");

            $stmtInsertStudent = $db->prepare("
                INSERT INTO students (student_number, full_name, class_name, gender, phone, email, status, created_at)
                VALUES (:number, :name, :class, :gender, :phone, :email, 'aktif', NOW())
            ");

            $stmtUpdateStudent = $db->prepare("
                UPDATE students 
                SET full_name = :name, class_name = :class, gender = :gender, phone = :phone, email = :email, updated_at = NOW()
                WHERE student_number = :number
            ");

            $stmtInsertDues = $db->prepare("
                INSERT INTO dues (student_id, total_obligation, album_portion, graduation_portion, total_paid, remaining_balance, payment_status)
                VALUES (:student_id, :obligation, :album, :grad, 0.00, :remaining, 'belum_bayar')
                ON DUPLICATE KEY UPDATE total_obligation = VALUES(total_obligation)
            ");

            $stmtInsertUser = $db->prepare("
                INSERT INTO users (username, password_hash, full_name, role, student_id, is_active, force_change_password)
                VALUES (:username, :password, :name, 'siswa', :student_id, 1, 0)
                ON DUPLICATE KEY UPDATE student_id = VALUES(student_id), full_name = VALUES(full_name)
            ");

            // Optimasi Kecepatan Eksekusi Ekstrem:
            // Bcrypt hashing butuh ~100ms per panggilan. Untuk 492 baris, menghitung di dalam loop
            // membutuhkan ~50 detik dan menyebabkan fatal error max_execution_time 30 detik!
            // Karena password default semua akun siswa baru seragam ('password'), hitung 1x di luar loop.
            $defaultPasswordHash = $createAccounts ? password_hash('password', PASSWORD_BCRYPT) : '';

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Abaikan baris kosong
                if (empty(array_filter($row, fn($val) => trim($val) !== ''))) {
                    continue;
                }

                $rawNumber = trim($row[$colMap['student_number']] ?? '');
                $rawName   = trim($row[$colMap['full_name']] ?? '');
                $rawClass  = trim($row[$colMap['class_name']] ?? '');
                $rawGender = isset($colMap['gender']) ? strtoupper(trim($row[$colMap['gender']] ?? '')) : null;
                $rawPhone  = isset($colMap['phone']) ? trim($row[$colMap['phone']] ?? '') : null;
                $rawEmail  = isset($colMap['email']) ? trim($row[$colMap['email']] ?? '') : null;

                // Format gender ke 'L' atau 'P'
                if ($rawGender !== null) {
                    if (str_starts_with($rawGender, 'L') || $rawGender === '1' || str_starts_with($rawGender, 'PRIA')) {
                        $rawGender = 'L';
                    } elseif (str_starts_with($rawGender, 'P') || $rawGender === '2' || str_starts_with($rawGender, 'WANITA')) {
                        $rawGender = 'P';
                    } else {
                        $rawGender = null;
                    }
                }

                // Validasi kolom wajib
                if ($rawNumber === '' || $rawName === '' || $rawClass === '') {
                    $errors[] = "Baris $rowNumber: Kolom NIS, Nama, atau Kelas tidak boleh kosong.";
                    $skipped++;
                    continue;
                }

                // Cek duplikasi di dalam file yang sama
                if (isset($seenInFile[$rawNumber])) {
                    $errors[] = "Baris $rowNumber: Duplikasi nomor siswa '$rawNumber' ditemukan di dalam file CSV.";
                    $skipped++;
                    continue;
                }
                $seenInFile[$rawNumber] = true;

                // Cek apakah siswa sudah ada di database
                $stmtCheck->execute([':number' => $rawNumber]);
                $existing = $stmtCheck->fetch();

                if ($existing) {
                    if ($updateExisting) {
                        $stmtUpdateStudent->execute([
                            ':number' => $rawNumber,
                            ':name'   => $rawName,
                            ':class'  => $rawClass,
                            ':gender' => $rawGender,
                            ':phone'  => $rawPhone,
                            ':email'  => $rawEmail
                        ]);
                        $imported++;
                    } else {
                        $errors[] = "Baris $rowNumber: Siswa dengan NIS '$rawNumber' sudah terdaftar di database (Dilewati).";
                        $skipped++;
                    }
                    continue;
                }

                // Insert siswa baru
                $stmtInsertStudent->execute([
                    ':number' => $rawNumber,
                    ':name'   => $rawName,
                    ':class'  => $rawClass,
                    ':gender' => $rawGender,
                    ':phone'  => $rawPhone,
                    ':email'  => $rawEmail
                ]);
                $studentId = (int)$db->lastInsertId();

                // Buat kewajiban dues Rp 250.000
                $stmtInsertDues->execute([
                    ':student_id'  => $studentId,
                    ':obligation'  => $totalDues,
                    ':album'       => $albumFee,
                    ':grad'        => $gradFee,
                    ':remaining'   => $totalDues
                ]);

                // Buat akun login siswa jika diaktifkan (default: username = NIS, password = password)
                if ($createAccounts) {
                    $stmtInsertUser->execute([
                        ':username'   => $rawNumber,
                        ':password'   => $defaultPasswordHash,
                        ':name'       => $rawName,
                        ':student_id' => $studentId
                    ]);
                }

                $imported++;
            }

            fclose($handle);
            Database::commit();

            Logger::log(
                'STUDENT_IMPORT',
                'students',
                null,
                "Impor CSV berhasil: $imported siswa ditambahkan, $skipped dilewati."
            );

            return [
                'success'  => true,
                'message'  => "Proses impor selesai: $imported data berhasil dimasukkan, $skipped dilewati.",
                'imported' => $imported,
                'skipped'  => $skipped,
                'errors'   => $errors
            ];
        } catch (Exception $e) {
            Database::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }
            error_log("Import error: " . $e->getMessage());
            return [
                'success'  => false,
                'message'  => "Terjadi kesalahan saat memproses impor database: " . $e->getMessage(),
                'imported' => $imported,
                'skipped'  => $skipped,
                'errors'   => array_merge($errors, [$e->getMessage()])
            ];
        }
    }

    /**
     * Otomatisasi pendeteksian kolom header CSV
     */
    private static function detectColumns(array $headers): array
    {
        $map = [
            'student_number' => null,
            'full_name'      => null,
            'class_name'     => null,
            'gender'         => null,
            'phone'          => null,
            'email'          => null
        ];

        foreach ($headers as $idx => $h) {
            // Nomor Siswa / NIS
            if (in_array($h, ['nis', 'no_siswa', 'nomor_siswa', 'nisn', 'nomor_induk', 'id_siswa', 'no_induk', 'student_number'])) {
                $map['student_number'] = $idx;
            }
            // Nama Lengkap
            elseif (in_array($h, ['nama', 'nama_lengkap', 'nama_siswa', 'full_name', 'name'])) {
                $map['full_name'] = $idx;
            }
            // Kelas
            elseif (in_array($h, ['kelas', 'rombel', 'class', 'class_name', 'tingkat'])) {
                $map['class_name'] = $idx;
            }
            // Jenis Kelamin
            elseif (in_array($h, ['jk', 'jenis_kelamin', 'gender', 'sex', 'l_p'])) {
                $map['gender'] = $idx;
            }
            // Telepon / HP
            elseif (in_array($h, ['telepon', 'no_hp', 'no_telp', 'phone', 'whatsapp', 'hp', 'kontak'])) {
                $map['phone'] = $idx;
            }
            // Email
            elseif (in_array($h, ['email', 'surel', 'e_mail'])) {
                $map['email'] = $idx;
            }
        }

        return $map;
    }
}
