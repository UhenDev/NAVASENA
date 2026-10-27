<?php
/**
 * Portal Controller - Portal Mandiri Siswa
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Flash;
use App\Helpers\Response;
use App\Helpers\View;
use App\Services\AllocationService;
use PDO;

class PortalController
{
    public function index(): void
    {
        Auth::requireRole('siswa');

        $studentId = Auth::studentId();
        if (!$studentId) {
            Flash::setError("Akun Anda belum terhubung dengan data siswa. Silakan hubungi pengurus.");
            Auth::logout();
            Response::redirect('/login');
        }

        $db = Database::getConnection();

        // 1. Ambil data profil siswa dan kewajiban iuran
        $stmt = $db->prepare("
            SELECT s.*, 
                   COALESCE(d.total_obligation, 250000.00) as total_obligation,
                   COALESCE(d.album_portion, 200000.00) as album_portion,
                   COALESCE(d.graduation_portion, 50000.00) as graduation_portion,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $studentId]);
        $student = $stmt->fetch();

        if (!$student) {
            Flash::setError("Data siswa tidak ditemukan.");
            Auth::logout();
            Response::redirect('/login');
        }

        // 2. Status progres ketiga tahap cicilan (DP Pertama, Cicilan Kedua, Pelunasan)
        $stages = AllocationService::getStudentStagesStatus($studentId);

        // 3. Riwayat transaksi pembayaran milik siswa bersangkutan
        $stmtPayments = $db->prepare("
            SELECT p.*, u.full_name as recorder_name, v.full_name as verifier_name
            FROM payments p
            JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            WHERE p.student_id = :student_id
            ORDER BY p.payment_date DESC, p.id DESC
        ");
        $stmtPayments->execute([':student_id' => $studentId]);
        $payments = $stmtPayments->fetchAll();

        // 4. Ambil pengumuman resmi pengurus
        $stmtAnnounce = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'announcement' LIMIT 1");
        $announcement = $stmtAnnounce->fetchColumn() ?: '';

        View::render('portal/index', [
            'pageTitle'    => 'Portal Keuangan Siswa',
            'activeNav'    => 'portal',
            'student'      => $student,
            'stages'       => $stages,
            'payments'     => $payments,
            'announcement' => $announcement
        ], 'student');
    }

    public function receipt(int $id): void
    {
        Auth::requireRole('siswa');

        $studentId = Auth::studentId();
        $db = Database::getConnection();

        // Keamanan Ketat: Siswa HANYA dapat melihat kuitansi miliknya sendiri!
        $stmt = $db->prepare("
            SELECT p.*, s.student_number, s.full_name as student_name, s.class_name,
                   u.full_name as recorder_name, v.full_name as verifier_name,
                   d.total_obligation, d.total_paid, d.remaining_balance
            FROM payments p
            JOIN students s ON s.id = p.student_id
            JOIN users u ON u.id = p.recorded_by
            LEFT JOIN users v ON v.id = p.verified_by
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE p.id = :id AND p.student_id = :student_id
            LIMIT 1
        ");
        $stmt->execute([
            ':id'         => $id,
            ':student_id' => $studentId
        ]);
        $payment = $stmt->fetch();

        if (!$payment) {
            Flash::setError("Kuitansi tidak ditemukan atau Anda tidak memiliki akses ke dokumen tersebut.");
            Response::redirect('/portal');
        }

        $allocations = AllocationService::getAllocationsForPayment($id);

        View::render('payments/receipt', [
            'pageTitle'   => 'Kuitansi Pembayaran ' . $payment['receipt_number'],
            'payment'     => $payment,
            'allocations' => $allocations
        ], 'none');
    }
}
