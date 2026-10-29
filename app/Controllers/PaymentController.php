<?php
/**
 * Payment Controller - Pengelolaan Transaksi Pembayaran Iuran Siswa
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
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
use PDO;
use Exception;

class PaymentController
{
    public function index(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $status        = trim($_GET['status'] ?? 'all');
        $method        = trim($_GET['method'] ?? 'all');
        $search        = trim($_GET['q'] ?? '');
        $startDate     = trim($_GET['start_date'] ?? '');
        $endDate       = trim($_GET['end_date'] ?? '');

        $sql = "
            SELECT p.*, s.student_number, s.full_name as student_name, s.class_name,
                   u.full_name as recorder_name, v.full_name as verifier_name
            FROM payments p
            JOIN students s ON s.id = p.student_id
            JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            WHERE 1=1
        ";
        $params = [];

        if ($status !== '' && $status !== 'all') {
            $sql .= " AND p.status = :status";
            $params[':status'] = $status;
        }

        if ($method !== '' && $method !== 'all') {
            $sql .= " AND p.payment_method = :method";
            $params[':method'] = $method;
        }

        if ($search !== '') {
            $sql .= " AND (p.receipt_number LIKE :search1 OR s.student_number LIKE :search2 OR s.full_name LIKE :search3)";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        if ($startDate !== '') {
            $sql .= " AND p.payment_date >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate !== '') {
            $sql .= " AND p.payment_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $payments = $stmt->fetchAll();

        // Hitung total menunggu verifikasi
        $stmtPending = $db->query("SELECT COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'");
        $pendingCount = (int)$stmtPending->fetchColumn();

        View::render('payments/index', [
            'pageTitle'    => 'Pengelolaan Pembayaran Iuran',
            'activeNav'    => 'payments',
            'payments'     => $payments,
            'status'       => $status,
            'method'       => $method,
            'search'       => $search,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'pendingCount' => $pendingCount,
            'canVerify'    => Auth::canVerify()
        ], 'main');
    }

    public function create(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        // Ambil daftar siswa aktif beserta sisa tagihan untuk dropdown
        $stmtStudents = $db->query("
            SELECT s.id, s.student_number, s.full_name, s.class_name,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.status = 'aktif'
            ORDER BY s.class_name ASC, s.full_name ASC
        ");
        $students = $stmtStudents->fetchAll();

        // Ambil tahapan cicilan
        $stmtStages = $db->query("SELECT id, stage_number, stage_name, target_amount, due_date FROM installments WHERE is_active = 1 ORDER BY stage_number ASC");
        $stages = $stmtStages->fetchAll();

        // Siswa yang dipilih jika ada parameter ?student_id=
        $selectedStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

        // Generate nomor kuitansi pratinjau
        $previewReceipt = self::generateReceiptNumber();

        View::render('payments/create', [
            'pageTitle'         => 'Catat Pembayaran Iuran',
            'activeNav'         => 'payments',
            'students'          => $students,
            'stages'            => $stages,
            'selectedStudentId' => $selectedStudentId,
            'receiptNumber'     => $previewReceipt
        ], 'main');
    }

    public function store(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $studentId      = (int)($_POST['student_id'] ?? 0);
        $amount         = (float)str_replace(['.', ','], ['', '.'], $_POST['amount'] ?? '0');
        $paymentMethod  = trim($_POST['payment_method'] ?? 'tunai');
        $paymentDate    = trim($_POST['payment_date'] ?? date('Y-m-d'));
        $referenceNote  = trim($_POST['reference_note'] ?? '');
        $allocationType = trim($_POST['allocation_type'] ?? 'auto'); // 'auto' atau 'custom'

        if ($studentId <= 0) {
            Flash::setError("Silakan pilih siswa yang melakukan pembayaran.");
            Response::redirect('/payments/create');
        }

        if ($amount <= 0) {
            Flash::setError("Nominal pembayaran harus lebih besar dari Rp 0.");
            Response::redirect('/payments/create');
        }

        if (!in_array($paymentMethod, ['tunai', 'transfer'], true)) {
            Flash::setError("Metode pembayaran harus berupa Tunai atau Transfer.");
            Response::redirect('/payments/create');
        }

        $db = Database::getConnection();

        // Cek data siswa dan kewajiban
        $stmtStudent = $db->prepare("
            SELECT s.*, COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.total_obligation, 250000.00) as total_obligation
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.id = :id AND s.status = 'aktif'
            LIMIT 1
        ");
        $stmtStudent->execute([':id' => $studentId]);
        $student = $stmtStudent->fetch();

        if (!$student) {
            Flash::setError("Siswa tidak ditemukan atau berstatus nonaktif.");
            Response::redirect('/payments/create');
        }

        // Cek batasan sisa tagihan
        $remaining = (float)$student['remaining_balance'];
        $stmtOver = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_overpayment' LIMIT 1");
        $allowOver = (bool)($stmtOver->fetchColumn() ?: 0);

        if (!$allowOver && $amount > $remaining) {
            Flash::setError("Nominal pembayaran (" . View::formatRupiah($amount) . ") melebihi sisa tagihan siswa (" . View::formatRupiah($remaining) . ").");
            Response::redirect('/payments/create?student_id=' . $studentId);
        }

        // Hitung alokasi cicilan
        $allocations = [];
        if ($allocationType === 'custom' && isset($_POST['allocations']) && is_array($_POST['allocations'])) {
            $totalAlloc = 0.00;
            foreach ($_POST['allocations'] as $stageId => $stageAmount) {
                $parsed = (float)str_replace(['.', ','], ['', '.'], $stageAmount);
                if ($parsed > 0) {
                    $allocations[(int)$stageId] = $parsed;
                    $totalAlloc += $parsed;
                }
            }
            if (abs($totalAlloc - $amount) > 0.01) {
                Flash::setError("Total alokasi tahap (" . View::formatRupiah($totalAlloc) . ") harus sama persis dengan total nominal pembayaran (" . View::formatRupiah($amount) . ").");
                Response::redirect('/payments/create?student_id=' . $studentId);
            }
        } else {
            // Otomatisasi alokasi ke tahap tertua yang belum lunas
            $allocations = AllocationService::computeAutoAllocation($studentId, $amount);
        }

        Database::beginTransaction();
        try {
            // Generate nomor kuitansi unik yang terjamin unik
            $receiptNumber = self::generateReceiptNumber();

            $stmtInsert = $db->prepare("
                INSERT INTO payments (
                    receipt_number, student_id, amount, payment_method, payment_date, 
                    reference_note, status, recorded_by, created_at
                ) VALUES (
                    :receipt, :student_id, :amount, :method, :pay_date,
                    :ref_note, 'menunggu_verifikasi', :recorded_by, NOW()
                )
            ");
            $stmtInsert->execute([
                ':receipt'     => $receiptNumber,
                ':student_id'  => $studentId,
                ':amount'      => $amount,
                ':method'      => $paymentMethod,
                ':pay_date'    => $paymentDate,
                ':ref_note'    => $referenceNote ?: null,
                ':recorded_by' => Auth::id()
            ]);
            $paymentId = (int)$db->lastInsertId();

            // Simpan alokasi cicilan
            AllocationService::saveAllocations($paymentId, $allocations);

            Database::commit();

            Logger::log(
                'PAYMENT_RECORD',
                'payment',
                (string)$paymentId,
                "Pembayaran dicatat: $receiptNumber sejumlah " . View::formatRupiah($amount) . " untuk siswa {$student['full_name']} ({$student['student_number']}). Status: Menunggu Verifikasi."
            );

            Flash::setSuccess("Pembayaran dengan kuitansi $receiptNumber (" . View::formatRupiah($amount) . ") berhasil dicatat dan sedang menunggu verifikasi.", "Pencatatan Pembayaran");
            Response::redirect('/payments/' . $paymentId);
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error recording payment: " . $e->getMessage());
            Flash::setError("Gagal mencatat transaksi pembayaran: " . $e->getMessage(), "Kesalahan Pencatatan");
            Response::redirect('/payments/create');
        }
    }

    public function show(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT p.*, s.student_number, s.full_name as student_name, s.class_name, s.phone as student_phone,
                   u.full_name as recorder_name, u.role as recorder_role,
                   v.full_name as verifier_name, v.role as verifier_role,
                   c.full_name as canceller_name
            FROM payments p
            JOIN students s ON s.id = p.student_id
            JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            LEFT JOIN users c ON c.id = p.cancelled_by
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $payment = $stmt->fetch();

        if (!$payment) {
            Flash::setError("Data pembayaran tidak ditemukan.");
            Response::redirect('/payments');
        }

        // Ambil alokasi cicilan
        $allocations = AllocationService::getAllocationsForPayment($id);

        View::render('payments/detail', [
            'pageTitle'   => 'Rincian Kuitansi ' . $payment['receipt_number'],
            'activeNav'   => 'payments',
            'payment'     => $payment,
            'allocations' => $allocations,
            'canVerify'   => Auth::canVerify()
        ], 'main');
    }

    public function receipt(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT p.*, s.student_number, s.full_name as student_name, s.class_name,
                   u.full_name as recorder_name, v.full_name as verifier_name,
                   d.total_obligation, d.total_paid, d.remaining_balance
            FROM payments p
            JOIN students s ON s.id = p.student_id
            JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $payment = $stmt->fetch();

        if (!$payment) {
            Flash::setError("Data pembayaran tidak ditemukan.");
            Response::redirect('/payments');
        }

        $allocations = AllocationService::getAllocationsForPayment($id);

        View::render('payments/receipt', [
            'pageTitle'   => 'Kuitansi Pembayaran ' . $payment['receipt_number'],
            'payment'     => $payment,
            'allocations' => $allocations
        ], 'none');
    }

    public function verify(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        if (!Auth::canVerify()) {
            Flash::setError("Anda tidak memiliki wewenang untuk menyetujui/memverifikasi pembayaran ini.");
            Response::redirect('/payments/' . $id);
        }

        $db = Database::getConnection();

        Database::beginTransaction();
        try {
            // Kunci baris transaksi pembayaran
            $stmt = $db->prepare("
                SELECT p.*, s.student_number, s.full_name as student_name
                FROM payments p
                JOIN students s ON s.id = p.student_id
                WHERE p.id = :id FOR UPDATE
            ");
            $stmt->execute([':id' => $id]);
            $payment = $stmt->fetch();

            if (!$payment) {
                Database::rollBack();
                Flash::setError("Transaksi tidak ditemukan.");
                Response::redirect('/payments');
            }

            if ($payment['status'] === 'terverifikasi') {
                Database::rollBack();
                Flash::setInfo("Transaksi ini sudah diverifikasi sebelumnya.");
                Response::redirect('/payments/' . $id);
            }

            if ($payment['status'] === 'dibatalkan') {
                Database::rollBack();
                Flash::setError("Transaksi yang telah dibatalkan tidak dapat diverifikasi kembali.");
                Response::redirect('/payments/' . $id);
            }

            // 1. Update status payment menjadi 'terverifikasi'
            $stmtVerify = $db->prepare("
                UPDATE payments 
                SET status = 'terverifikasi', verified_by = :verifier, verified_at = NOW(), rejection_reason = NULL, updated_at = NOW()
                WHERE id = :id
            ");
            $stmtVerify->execute([
                ':verifier' => Auth::id(),
                ':id'       => $id
            ]);

            // 2. Sinkronisasi ke Buku Kas (cash_transactions)
            // Pastikan tidak ada transaksi kas ganda untuk pembayaran ini
            $stmtCheckCash = $db->prepare("SELECT id FROM cash_transactions WHERE payment_id = :payment_id LIMIT 1");
            $stmtCheckCash->execute([':payment_id' => $id]);
            $existingCash = $stmtCheckCash->fetch();

            if (!$existingCash) {
                $description = "Penerimaan Iuran Siswa [" . $payment['student_number'] . "] " . $payment['student_name'] . " (" . $payment['receipt_number'] . ")";
                $stmtCash = $db->prepare("
                    INSERT INTO cash_transactions (
                        transaction_type, category, amount, transaction_date, 
                        description, payment_id, recorded_by, is_cancelled, created_at
                    ) VALUES (
                        'masuk', 'Iuran Siswa', :amount, :date,
                        :desc, :payment_id, :recorded_by, 0, NOW()
                    )
                ");
                $stmtCash->execute([
                    ':amount'      => $payment['amount'],
                    ':date'        => $payment['payment_date'],
                    ':desc'        => $description,
                    ':payment_id'  => $id,
                    ':recorded_by' => Auth::id()
                ]);
            } else {
                // Aktifkan kembali kas jika sebelumnya di-cancel
                $stmtReactivate = $db->prepare("UPDATE cash_transactions SET is_cancelled = 0, cancellation_note = NULL WHERE payment_id = :payment_id");
                $stmtReactivate->execute([':payment_id' => $id]);
            }

            // 3. Rekalkulasi kewajiban dan status lunas siswa
            CalculationService::recalculateStudentDues((int)$payment['student_id']);

            Database::commit();

            Logger::log(
                'PAYMENT_VERIFY',
                'payment',
                (string)$id,
                "Pembayaran {$payment['receipt_number']} sejumlah " . View::formatRupiah($payment['amount']) . " berhasil diverifikasi oleh " . Auth::fullName()
            );

            Flash::setSuccess("Pembayaran {$payment['receipt_number']} telah berhasil diverifikasi dan otomatis tercatat dalam kas masuk angkatan.", "Verifikasi Pembayaran Sah");
            Response::redirect('/payments/' . $id);
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error verifying payment: " . $e->getMessage());
            Flash::setError("Gagal memverifikasi transaksi: " . $e->getMessage(), "Gagal Verifikasi");
            Response::redirect('/payments/' . $id);
        }
    }

    public function reject(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        if (!Auth::canVerify()) {
            Flash::setError("Anda tidak memiliki izin untuk menolak pembayaran ini.");
            Response::redirect('/payments/' . $id);
        }

        $reason = trim($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            Flash::setError("Alasan penolakan pembayaran wajib diisi.");
            Response::redirect('/payments/' . $id);
        }

        $db = Database::getConnection();

        Database::beginTransaction();
        try {
            $stmt = $db->prepare("SELECT * FROM payments WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $payment = $stmt->fetch();

            if (!$payment) {
                Database::rollBack();
                Flash::setError("Transaksi tidak ditemukan.");
                Response::redirect('/payments');
            }

            if ($payment['status'] === 'terverifikasi') {
                Database::rollBack();
                Flash::setError("Transaksi yang sudah terverifikasi tidak bisa ditolak. Gunakan mekanisme pembatalan.");
                Response::redirect('/payments/' . $id);
            }

            $stmtReject = $db->prepare("
                UPDATE payments 
                SET status = 'ditolak', verified_by = :verifier, verified_at = NOW(), rejection_reason = :reason, updated_at = NOW()
                WHERE id = :id
            ");
            $stmtReject->execute([
                ':verifier' => Auth::id(),
                ':reason'   => $reason,
                ':id'       => $id
            ]);

            // Pastikan kewajiban siswa tersinkronisasi
            CalculationService::recalculateStudentDues((int)$payment['student_id']);

            Database::commit();

            Logger::log(
                'PAYMENT_REJECT',
                'payment',
                (string)$id,
                "Pembayaran {$payment['receipt_number']} ditolak. Alasan: $reason"
            );

            Flash::setSuccess("Pembayaran {$payment['receipt_number']} telah ditolak dan tidak dihitung sebagai penerimaan sah.", "Penolakan Transaksi");
            Response::redirect('/payments/' . $id);
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error rejecting payment: " . $e->getMessage());
            Flash::setError("Gagal menolak transaksi: " . $e->getMessage(), "Gagal Menolak");
            Response::redirect('/payments/' . $id);
        }
    }

    public function cancel(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $reason = trim($_POST['cancellation_reason'] ?? '');
        if (empty($reason)) {
            Flash::setError("Alasan pembatalan transaksi wajib diisi.");
            Response::redirect('/payments/' . $id);
        }

        $db = Database::getConnection();

        Database::beginTransaction();
        try {
            $stmt = $db->prepare("SELECT * FROM payments WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $payment = $stmt->fetch();

            if (!$payment) {
                Database::rollBack();
                Flash::setError("Transaksi tidak ditemukan.");
                Response::redirect('/payments');
            }

            if ($payment['status'] === 'dibatalkan') {
                Database::rollBack();
                Flash::setInfo("Transaksi ini sudah berstatus dibatalkan sebelumnya.");
                Response::redirect('/payments/' . $id);
            }

            $wasVerified = ($payment['status'] === 'terverifikasi');

            // 1. Update status pembayaran menjadi 'dibatalkan'
            $stmtCancel = $db->prepare("
                UPDATE payments 
                SET status = 'dibatalkan', cancelled_by = :canceller, cancelled_at = NOW(), cancellation_reason = :reason, updated_at = NOW()
                WHERE id = :id
            ");
            $stmtCancel->execute([
                ':canceller' => Auth::id(),
                ':reason'    => $reason,
                ':id'        => $id
            ]);

            // 2. Jika sebelumnya pernah terverifikasi, batalkan pencatatan kas terkait
            if ($wasVerified) {
                $stmtCashCancel = $db->prepare("
                    UPDATE cash_transactions 
                    SET is_cancelled = 1, cancellation_note = :note, updated_at = NOW() 
                    WHERE payment_id = :payment_id
                ");
                $stmtCashCancel->execute([
                    ':note'       => "Pembatalan transaksi pembayaran kuitansi {$payment['receipt_number']}: $reason",
                    ':payment_id' => $id
                ]);
            }

            // 3. Sinkronkan ulang kewajiban siswa sehingga penerimaan sah berkurang
            CalculationService::recalculateStudentDues((int)$payment['student_id']);

            Database::commit();

            Logger::log(
                'PAYMENT_CANCEL',
                'payment',
                (string)$id,
                "Transaksi {$payment['receipt_number']} sejumlah " . View::formatRupiah($payment['amount']) . " dibatalkan oleh " . Auth::fullName() . ". Alasan: $reason"
            );

            Flash::setSuccess("Transaksi {$payment['receipt_number']} berhasil dibatalkan dengan jejak audit reversal tersimpan.", "Audit Reversal (VOID)");
            Response::redirect('/payments/' . $id);
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error cancelling payment: " . $e->getMessage());
            Flash::setError("Gagal membatalkan transaksi: " . $e->getMessage(), "Gagal Pembatalan");
            Response::redirect('/payments/' . $id);
        }
    }

    /**
     * Menghasilkan nomor kuitansi unik berformat KW-YYYYMMDD-XXXX
     */
    private static function generateReceiptNumber(): string
    {
        $db = Database::getConnection();
        $datePrefix = date('Ymd');
        $prefix = "KW-" . $datePrefix . "-";

        $stmt = $db->prepare("
            SELECT receipt_number 
            FROM payments 
            WHERE receipt_number LIKE :prefix 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([':prefix' => $prefix . '%']);
        $lastReceipt = $stmt->fetchColumn();

        if ($lastReceipt) {
            $parts = explode('-', $lastReceipt);
            $lastSeq = (int)end($parts);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
