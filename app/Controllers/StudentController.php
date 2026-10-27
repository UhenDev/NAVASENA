<?php
/**
 * Student Controller - Pengelolaan Data Siswa Angkatan
 * NAVASENA MANAGEMENT
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\CSRF;
use App\Helpers\Database;
use App\Helpers\Flash;
use App\Helpers\Logger;
use App\Helpers\Response;
use App\Helpers\View;
use App\Services\AllocationService;
use App\Services\CalculationService;
use App\Services\ImportService;
use PDO;
use Exception;

class StudentController
{
    public function index(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $search = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? 'aktif'); // default tampilkan yang aktif
        $paymentStatus = trim($_GET['payment_status'] ?? '');
        $className = trim($_GET['class'] ?? '');

        // Dapatkan daftar kelas unik untuk filter dropdown
        $stmtClasses = $db->query("SELECT DISTINCT class_name FROM students WHERE class_name != '' ORDER BY class_name ASC");
        $classes = $stmtClasses->fetchAll(PDO::FETCH_COLUMN);

        // Bangun query
        $sql = "
            SELECT s.*, 
                   COALESCE(d.total_obligation, 250000.00) as total_obligation,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (s.student_number LIKE :search OR s.full_name LIKE :search OR s.class_name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($status !== '' && $status !== 'all') {
            $sql .= " AND s.status = :status";
            $params[':status'] = $status;
        }

        if ($paymentStatus !== '' && $paymentStatus !== 'all') {
            $sql .= " AND d.payment_status = :payment_status";
            $params[':payment_status'] = $paymentStatus;
        }

        if ($className !== '' && $className !== 'all') {
            $sql .= " AND s.class_name = :class_name";
            $params[':class_name'] = $className;
        }

        $sql .= " ORDER BY s.class_name ASC, s.full_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        // Ringkasan jumlah
        $stmtTotal = $db->query("SELECT COUNT(*) FROM students WHERE status = 'aktif'");
        $totalActive = (int)$stmtTotal->fetchColumn();

        View::render('students/index', [
            'pageTitle'     => 'Data Siswa Angkatan',
            'activeNav'     => 'students',
            'students'      => $students,
            'classes'       => $classes,
            'search'        => $search,
            'status'        => $status,
            'paymentStatus' => $paymentStatus,
            'className'     => $className,
            'totalActive'   => $totalActive
        ], 'main');
    }

    public function create(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        View::render('students/create', [
            'pageTitle' => 'Tambah Data Siswa',
            'activeNav' => 'students'
        ], 'main');
    }

    public function store(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $studentNumber = trim($_POST['student_number'] ?? '');
        $fullName      = trim($_POST['full_name'] ?? '');
        $className     = trim($_POST['class_name'] ?? '');
        $gender        = trim($_POST['gender'] ?? '') ?: null;
        $phone         = trim($_POST['phone'] ?? '') ?: null;
        $email         = trim($_POST['email'] ?? '') ?: null;
        $notes         = trim($_POST['notes'] ?? '') ?: null;

        if (empty($studentNumber) || empty($fullName) || empty($className)) {
            Flash::setError("Nomor Siswa/NIS, Nama Lengkap, dan Kelas wajib diisi.");
            Response::redirect('/students/create');
        }

        $db = Database::getConnection();

        // Cek duplikasi nomor siswa
        $stmtCheck = $db->prepare("SELECT id FROM students WHERE student_number = :number LIMIT 1");
        $stmtCheck->execute([':number' => $studentNumber]);
        if ($stmtCheck->fetch()) {
            Flash::setError("Nomor Siswa/NIS '{$studentNumber}' sudah digunakan oleh siswa lain.");
            Response::redirect('/students/create');
        }

        Database::beginTransaction();
        try {
            // Ambil nominal kewajiban iuran dari settings
            $stmtSet = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'total_dues_per_student' LIMIT 1");
            $totalDues = (float)($stmtSet->fetchColumn() ?: 250000.00);

            $stmtAlbum = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'album_fee' LIMIT 1");
            $albumFee = (float)($stmtAlbum->fetchColumn() ?: 200000.00);

            $stmtGrad = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'graduation_fee' LIMIT 1");
            $gradFee = (float)($stmtGrad->fetchColumn() ?: 50000.00);

            // Insert student
            $stmtInsert = $db->prepare("
                INSERT INTO students (student_number, full_name, class_name, gender, phone, email, notes, status, created_at)
                VALUES (:number, :name, :class, :gender, :phone, :email, :notes, 'aktif', NOW())
            ");
            $stmtInsert->execute([
                ':number' => $studentNumber,
                ':name'   => $fullName,
                ':class'  => $className,
                ':gender' => $gender,
                ':phone'  => $phone,
                ':email'  => $email,
                ':notes'  => $notes
            ]);
            $studentId = (int)$db->lastInsertId();

            // Insert dues
            $stmtDues = $db->prepare("
                INSERT INTO dues (student_id, total_obligation, album_portion, graduation_portion, total_paid, remaining_balance, payment_status)
                VALUES (:student_id, :total_obligation, :album, :grad, 0.00, :remaining, 'belum_bayar')
            ");
            $stmtDues->execute([
                ':student_id'        => $studentId,
                ':total_obligation'  => $totalDues,
                ':album'             => $albumFee,
                ':grad'              => $gradFee,
                ':remaining'         => $totalDues
            ]);

            // Buat akun login siswa secara otomatis (username = NIS, password = password)
            $passwordHash = password_hash('password', PASSWORD_BCRYPT);
            $stmtUser = $db->prepare("
                INSERT INTO users (username, password_hash, full_name, role, student_id, is_active, force_change_password)
                VALUES (:username, :password, :name, 'siswa', :student_id, 1, 0)
            ");
            $stmtUser->execute([
                ':username'   => $studentNumber,
                ':password'   => $passwordHash,
                ':name'       => $fullName,
                ':student_id' => $studentId
            ]);

            Database::commit();

            Logger::log('STUDENT_CREATE', 'student', (string)$studentId, "Siswa baru ditambahkan: $fullName ($studentNumber)");
            Flash::setSuccess("Siswa $fullName berhasil ditambahkan dengan kewajiban iuran " . View::formatRupiah($totalDues) . ".");
            Response::redirect('/students/' . $studentId);
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error creating student: " . $e->getMessage());
            Flash::setError("Gagal menyimpan data siswa: " . $e->getMessage());
            Response::redirect('/students/create');
        }
    }

    public function show(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT s.*, d.total_obligation, d.album_portion, d.graduation_portion, d.total_paid, d.remaining_balance, d.payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        if (!$student) {
            Flash::setError("Data siswa tidak ditemukan.");
            Response::redirect('/students');
        }

        // Status progres 3 tahap cicilan
        $stageProgress = AllocationService::getStudentStagesStatus($id);

        // Riwayat pembayaran siswa
        $stmtPayments = $db->prepare("
            SELECT p.*, u.full_name as recorder_name, v.full_name as verifier_name
            FROM payments p
            LEFT JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            WHERE p.student_id = :student_id
            ORDER BY p.id DESC
        ");
        $stmtPayments->execute([':student_id' => $id]);
        $payments = $stmtPayments->fetchAll();

        View::render('students/detail', [
            'pageTitle'     => 'Profil & Rincian Iuran Siswa',
            'activeNav'     => 'students',
            'student'       => $student,
            'stageProgress' => $stageProgress,
            'payments'      => $payments,
            'canVerify'     => Auth::canVerify()
        ], 'main');
    }

    public function edit(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        if (!$student) {
            Flash::setError("Data siswa tidak ditemukan.");
            Response::redirect('/students');
        }

        View::render('students/edit', [
            'pageTitle' => 'Edit Data Siswa',
            'activeNav' => 'students',
            'student'   => $student
        ], 'main');
    }

    public function update(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $studentNumber = trim($_POST['student_number'] ?? '');
        $fullName      = trim($_POST['full_name'] ?? '');
        $className     = trim($_POST['class_name'] ?? '');
        $gender        = trim($_POST['gender'] ?? '') ?: null;
        $phone         = trim($_POST['phone'] ?? '') ?: null;
        $email         = trim($_POST['email'] ?? '') ?: null;
        $notes         = trim($_POST['notes'] ?? '') ?: null;
        $status        = trim($_POST['status'] ?? 'aktif');

        if (empty($studentNumber) || empty($fullName) || empty($className)) {
            Flash::setError("Nomor Siswa/NIS, Nama Lengkap, dan Kelas wajib diisi.");
            Response::redirect("/students/$id/edit");
        }

        $db = Database::getConnection();

        // Cek duplikasi nomor siswa pada siswa lain
        $stmtCheck = $db->prepare("SELECT id FROM students WHERE student_number = :number AND id != :id LIMIT 1");
        $stmtCheck->execute([':number' => $studentNumber, ':id' => $id]);
        if ($stmtCheck->fetch()) {
            Flash::setError("Nomor Siswa/NIS '{$studentNumber}' sudah digunakan oleh siswa lain.");
            Response::redirect("/students/$id/edit");
        }

        $stmtUpdate = $db->prepare("
            UPDATE students 
            SET student_number = :number, full_name = :name, class_name = :class, gender = :gender, 
                phone = :phone, email = :email, notes = :notes, status = :status, updated_at = NOW()
            WHERE id = :id
        ");
        $stmtUpdate->execute([
            ':number' => $studentNumber,
            ':name'   => $fullName,
            ':class'  => $className,
            ':gender' => $gender,
            ':phone'  => $phone,
            ':email'  => $email,
            ':notes'  => $notes,
            ':status' => $status,
            ':id'     => $id
        ]);

        // Sinkronisasi nama pada tabel users jika ada
        $stmtUser = $db->prepare("UPDATE users SET full_name = :name WHERE student_id = :id");
        $stmtUser->execute([':name' => $fullName, ':id' => $id]);

        Logger::log('STUDENT_UPDATE', 'student', (string)$id, "Data siswa $fullName ($studentNumber) diperbarui.");
        Flash::setSuccess("Data siswa berhasil diperbarui.");
        Response::redirect("/students/$id");
    }

    public function toggleStatus(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, full_name, student_number, status FROM students WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        if (!$student) {
            Flash::setError("Data siswa tidak ditemukan.");
            Response::redirect('/students');
        }

        $newStatus = ($student['status'] === 'aktif') ? 'nonaktif' : 'aktif';

        $stmtToggle = $db->prepare("UPDATE students SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmtToggle->execute([':status' => $newStatus, ':id' => $id]);

        Logger::log('STUDENT_STATUS', 'student', (string)$id, "Status siswa {$student['full_name']} diubah menjadi $newStatus.");
        Flash::setSuccess("Status siswa {$student['full_name']} berhasil diubah menjadi " . strtoupper($newStatus) . ". Riwayat transaksi tetap tersimpan.");
        Response::redirect("/students/$id");
    }

    public function showImport(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        View::render('students/import', [
            'pageTitle' => 'Impor Data Siswa (Excel / CSV)',
            'activeNav' => 'students'
        ], 'main');
    }

    public function processImport(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Flash::setError("Silakan pilih file CSV yang valid untuk diunggah.");
            Response::redirect('/students/import');
        }

        $file = $_FILES['csv_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['csv', 'txt'])) {
            Flash::setError("Format file harus berupa CSV (.csv). Pastikan Anda menyimpan Excel sebagai CSV UTF-8.");
            Response::redirect('/students/import');
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            Flash::setError("Ukuran file maksimal adalah 10MB.");
            Response::redirect('/students/import');
        }

        $createAccounts = isset($_POST['create_accounts']) && $_POST['create_accounts'] == '1';
        $updateExisting = isset($_POST['update_existing']) && $_POST['update_existing'] == '1';

        $result = ImportService::importCsv($file['tmp_name'], $createAccounts, $updateExisting);

        if ($result['success']) {
            Flash::setSuccess($result['message']);
        } else {
            Flash::setError($result['message']);
        }

        // Tampilkan halaman detail hasil impor
        View::render('students/import_result', [
            'pageTitle' => 'Hasil Impor Data Siswa',
            'activeNav' => 'students',
            'result'    => $result
        ], 'main');
    }

    public function exportCsv(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT s.student_number, s.full_name, s.class_name, s.gender, s.phone, s.status,
                   COALESCE(d.total_obligation, 250000.00) as total_obligation,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            ORDER BY s.class_name ASC, s.full_name ASC
        ");
        $students = $stmt->fetchAll();

        $headers = [
            'Nomor Siswa (NIS)',
            'Nama Lengkap',
            'Kelas',
            'Jenis Kelamin',
            'No. HP / WhatsApp',
            'Status Siswa',
            'Total Kewajiban (Rp)',
            'Total Terbayar (Rp)',
            'Sisa Tagihan (Rp)',
            'Status Pembayaran'
        ];

        $rows = [];
        foreach ($students as $s) {
            $rows[] = [
                $s['student_number'],
                $s['full_name'],
                $s['class_name'],
                $s['gender'] ?? '-',
                $s['phone'] ?? '-',
                ucfirst($s['status']),
                number_format((float)$s['total_obligation'], 0, ',', '.'),
                number_format((float)$s['total_paid'], 0, ',', '.'),
                number_format((float)$s['remaining_balance'], 0, ',', '.'),
                strtoupper(str_replace('_', ' ', $s['payment_status']))
            ];
        }

        $filename = 'Data_Siswa_Navasena_' . date('Ymd_His') . '.csv';
        Response::downloadCsv($filename, $headers, $rows);
    }
}
