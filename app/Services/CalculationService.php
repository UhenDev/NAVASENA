<?php
/**
 * Calculation Service - Core Financial Engine
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

namespace App\Services;

use App\Helpers\Database;
use PDO;

class CalculationService
{
    /**
     * Sinkronisasi data kewajiban dan status pembayaran siswa berdasarkan transaksi terverifikasi
     */
    public static function recalculateStudentDues(int $studentId): array
    {
        $db = Database::getConnection();

        // 1. Ambil total pembayaran yang berstatus TERVERIFIKASI saja
        // Pembayaran menunggu_verifikasi, ditolak, atau dibatalkan TIDAK BOLEH dihitung!
        $stmtPay = $db->prepare("
            SELECT COALESCE(SUM(amount), 0) as verified_total 
            FROM payments 
            WHERE student_id = :student_id AND status = 'terverifikasi'
        ");
        $stmtPay->execute([':student_id' => $studentId]);
        $verifiedTotal = (float)$stmtPay->fetchColumn();

        // 2. Ambil total kewajiban dari tabel dues
        $stmtDue = $db->prepare("SELECT total_obligation FROM dues WHERE student_id = :student_id LIMIT 1");
        $stmtDue->execute([':student_id' => $studentId]);
        $totalObligation = $stmtDue->fetchColumn();

        // Jika belum ada record dues, buat secara otomatis dengan nilai default
        if ($totalObligation === false) {
            $totalObligation = 250000.00;
            $stmtInsert = $db->prepare("
                INSERT INTO dues (student_id, total_obligation, album_portion, graduation_portion, total_paid, remaining_balance, payment_status)
                VALUES (:student_id, 250000.00, 200000.00, 50000.00, 0.00, 250000.00, 'belum_bayar')
            ");
            $stmtInsert->execute([':student_id' => $studentId]);
        } else {
            $totalObligation = (float)$totalObligation;
        }

        // 3. Perhitungan sisa tagihan: sisa = MAX(0, kewajiban - pembayaran_sah)
        $remaining = max(0.00, $totalObligation - $verifiedTotal);

        // 4. Penentuan status pembayaran
        if ($verifiedTotal >= $totalObligation) {
            $status = 'lunas';
        } elseif ($verifiedTotal > 0) {
            $status = 'sebagian';
        } else {
            $status = 'belum_bayar';
        }

        // 5. Update data dues siswa
        $stmtUpdate = $db->prepare("
            UPDATE dues 
            SET total_paid = :total_paid,
                remaining_balance = :remaining,
                payment_status = :status,
                updated_at = NOW()
            WHERE student_id = :student_id
        ");
        $stmtUpdate->execute([
            ':total_paid' => $verifiedTotal,
            ':remaining'  => $remaining,
            ':status'     => $status,
            ':student_id' => $studentId
        ]);

        return [
            'total_obligation' => $totalObligation,
            'total_paid'       => $verifiedTotal,
            'remaining_balance'=> $remaining,
            'payment_status'   => $status
        ];
    }

    /**
     * Mengambil metrik dinamis untuk Dashboard dan Laporan Ringkasan
     */
    public static function getDashboardMetrics(): array
    {
        $db = Database::getConnection();

        // 1. Jumlah siswa aktif
        $stmtActive = $db->query("SELECT COUNT(*) FROM students WHERE status = 'aktif'");
        $activeStudentsCount = (int)$stmtActive->fetchColumn();

        // Jumlah siswa nonaktif
        $stmtInactive = $db->query("SELECT COUNT(*) FROM students WHERE status = 'nonaktif'");
        $inactiveStudentsCount = (int)$stmtInactive->fetchColumn();

        // 2. Ambil nilai kewajiban per siswa dari settings
        $stmtSetting = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'total_dues_per_student' LIMIT 1");
        $duePerStudent = (float)($stmtSetting->fetchColumn() ?: 250000.00);

        // Target Total Penerimaan = Jumlah Siswa Aktif x Rp 250.000
        $targetTotalRevenue = $activeStudentsCount * $duePerStudent;

        // 3. Total Pembayaran Sah (Terverifikasi)
        $stmtVerified = $db->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'terverifikasi'");
        $totalVerifiedRevenue = (float)$stmtVerified->fetchColumn();

        // Total Pembayaran Menunggu Verifikasi
        $stmtPending = $db->query("SELECT COALESCE(SUM(amount), 0), COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'");
        $pendingRow = $stmtPending->fetch(PDO::FETCH_NUM);
        $totalPendingAmount = (float)$pendingRow[0];
        $pendingCount = (int)$pendingRow[1];

        // 4. Persentase Pencapaian Target
        $achievementPercentage = $targetTotalRevenue > 0 
            ? round(($totalVerifiedRevenue / $targetTotalRevenue) * 100, 2) 
            : 0.00;

        // 5. Saldo Kas Aktual: Kas Masuk Sah - Kas Keluar Sah
        $stmtCashIn = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'masuk' AND is_cancelled = 0");
        $totalCashIn = (float)$stmtCashIn->fetchColumn();

        $stmtCashOut = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'keluar' AND is_cancelled = 0");
        $totalCashOut = (float)$stmtCashOut->fetchColumn();

        $currentCashBalance = $totalCashIn - $totalCashOut;

        // 6. Ringkasan Status Siswa Aktif (Lunas, Sebagian, Belum Bayar)
        $stmtStatusCounts = $db->query("
            SELECT d.payment_status, COUNT(*) as cnt
            FROM dues d
            JOIN students s ON s.id = d.student_id
            WHERE s.status = 'aktif'
            GROUP BY d.payment_status
        ");
        $statusCounts = [
            'lunas'       => 0,
            'sebagian'    => 0,
            'belum_bayar' => 0
        ];
        while ($row = $stmtStatusCounts->fetch()) {
            if (isset($statusCounts[$row['payment_status']])) {
                $statusCounts[$row['payment_status']] = (int)$row['cnt'];
            }
        }

        // 7. Transaksi Terbaru (5 transaksi terakhir)
        $stmtRecent = $db->query("
            SELECT p.*, s.student_number, s.full_name as student_name, s.class_name, u.full_name as recorder_name
            FROM payments p
            JOIN students s ON s.id = p.student_id
            JOIN users u ON u.id = p.recorded_by
            ORDER BY p.id DESC
            LIMIT 5
        ");
        $recentTransactions = $stmtRecent->fetchAll();

        // 8. Ringkasan Pembayaran Berdasarkan Tahap Cicilan
        $stageBreakdown = self::getStageBreakdown($activeStudentsCount);

        return [
            'active_students'        => $activeStudentsCount,
            'inactive_students'      => $inactiveStudentsCount,
            'target_per_student'     => $duePerStudent,
            'target_total_revenue'   => $targetTotalRevenue,
            'total_verified_revenue' => $totalVerifiedRevenue,
            'total_pending_amount'   => $totalPendingAmount,
            'pending_count'          => $pendingCount,
            'achievement_percentage' => $achievementPercentage,
            'total_cash_in'          => $totalCashIn,
            'total_cash_out'         => $totalCashOut,
            'cash_balance'           => $currentCashBalance,
            'paid_off_count'         => $statusCounts['lunas'],
            'partial_count'          => $statusCounts['sebagian'],
            'unpaid_count'           => $statusCounts['belum_bayar'],
            'recent_transactions'    => $recentTransactions,
            'stage_breakdown'        => $stageBreakdown
        ];
    }

    /**
     * Mengambil ringkasan progres per tahap cicilan
     */
    public static function getStageBreakdown(int $activeStudentsCount): array
    {
        $db = Database::getConnection();

        $stmtStages = $db->query("
            SELECT id, stage_number, stage_name, target_amount, due_date 
            FROM installments 
            WHERE is_active = 1 
            ORDER BY stage_number ASC
        ");
        $stages = $stmtStages->fetchAll();

        $breakdown = [];
        foreach ($stages as $stage) {
            $stageTarget = $activeStudentsCount * (float)$stage['target_amount'];

            // Hitung hanya alokasi dari transaksi yang statusnya 'terverifikasi'
            $stmtAlloc = $db->prepare("
                SELECT COALESCE(SUM(pa.allocated_amount), 0)
                FROM payment_allocations pa
                JOIN payments p ON p.id = pa.payment_id
                WHERE pa.installment_id = :installment_id AND p.status = 'terverifikasi'
            ");
            $stmtAlloc->execute([':installment_id' => $stage['id']]);
            $collected = (float)$stmtAlloc->fetchColumn();

            $percentage = $stageTarget > 0 ? round(($collected / $stageTarget) * 100, 2) : 0.00;

            $breakdown[] = [
                'id'            => (int)$stage['id'],
                'stage_number'  => (int)$stage['stage_number'],
                'stage_name'    => $stage['stage_name'],
                'target_per_student' => (float)$stage['target_amount'],
                'stage_target'  => $stageTarget,
                'collected'     => $collected,
                'percentage'    => $percentage,
                'due_date'      => $stage['due_date']
            ];
        }

        return $breakdown;
    }
}
