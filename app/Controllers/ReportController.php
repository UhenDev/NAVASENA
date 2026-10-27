<?php
/**
 * Report Controller - Laporan Keuangan, Ekspor Excel/CSV, dan Cetak Pertanggungjawaban
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Response;
use App\Helpers\View;
use App\Services\CalculationService;
use PDO;

class ReportController
{
    public function index(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $metrics = CalculationService::getDashboardMetrics();

        $db = Database::getConnection();

        // Rekap Metode Pembayaran (Hanya yang terverifikasi)
        $stmtMethod = $db->query("
            SELECT payment_method, COUNT(*) as count_trans, COALESCE(SUM(amount), 0) as total_amount
            FROM payments
            WHERE status = 'terverifikasi'
            GROUP BY payment_method
        ");
        $methods = $stmtMethod->fetchAll();

        // Rekap Pengeluaran Kas Berdasarkan Kategori
        $stmtOutCategory = $db->query("
            SELECT category, COUNT(*) as count_trans, COALESCE(SUM(amount), 0) as total_amount
            FROM cash_transactions
            WHERE transaction_type = 'keluar' AND is_cancelled = 0
            GROUP BY category
            ORDER BY total_amount DESC
        ");
        $outCategories = $stmtOutCategory->fetchAll();

        View::render('reports/index', [
            'pageTitle'     => 'Pusat Laporan Keuangan Angkatan',
            'activeNav'     => 'reports',
            'metrics'       => $metrics,
            'methods'       => $methods,
            'outCategories' => $outCategories
        ], 'main');
    }

    public function students(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $paymentStatus = trim($_GET['status'] ?? 'all');
        $className     = trim($_GET['class'] ?? 'all');

        $stmtClasses = $db->query("SELECT DISTINCT class_name FROM students WHERE class_name != '' ORDER BY class_name ASC");
        $classes = $stmtClasses->fetchAll(PDO::FETCH_COLUMN);

        $sql = "
            SELECT s.student_number, s.full_name, s.class_name, s.status as student_status,
                   COALESCE(d.total_obligation, 250000.00) as total_obligation,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.status = 'aktif'
        ";
        $params = [];

        if ($paymentStatus !== '' && $paymentStatus !== 'all') {
            $sql .= " AND d.payment_status = :status";
            $params[':status'] = $paymentStatus;
        }

        if ($className !== '' && $className !== 'all') {
            $sql .= " AND s.class_name = :class_name";
            $params[':class_name'] = $className;
        }

        $sql .= " ORDER BY s.class_name ASC, s.full_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        // Total akumulasi untuk footer tabel
        $sumObligation = 0.0;
        $sumPaid       = 0.0;
        $sumRemaining  = 0.0;

        foreach ($students as $s) {
            $sumObligation += (float)$s['total_obligation'];
            $sumPaid       += (float)$s['total_paid'];
            $sumRemaining  += (float)$s['remaining_balance'];
        }

        View::render('reports/students', [
            'pageTitle'     => 'Rekap Pembayaran Siswa',
            'activeNav'     => 'reports',
            'students'      => $students,
            'classes'       => $classes,
            'paymentStatus' => $paymentStatus,
            'className'     => $className,
            'sumObligation' => $sumObligation,
            'sumPaid'       => $sumPaid,
            'sumRemaining'  => $sumRemaining
        ], 'main');
    }

    public function installments(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();
        $stmtActive = $db->query("SELECT COUNT(*) FROM students WHERE status = 'aktif'");
        $activeCount = (int)$stmtActive->fetchColumn();

        $stageBreakdown = CalculationService::getStageBreakdown($activeCount);

        View::render('reports/installments', [
            'pageTitle'      => 'Rekap Pembayaran per Tahap Cicilan',
            'activeNav'      => 'reports',
            'stageBreakdown' => $stageBreakdown,
            'activeCount'    => $activeCount
        ], 'main');
    }

    public function cash(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $startDate = trim($_GET['start_date'] ?? date('Y-m-01'));
        $endDate   = trim($_GET['end_date'] ?? date('Y-m-d'));

        $stmt = $db->prepare("
            SELECT c.*, u.full_name as recorder_name, p.receipt_number
            FROM cash_transactions c
            JOIN users u ON u.id = c.recorded_by
            LEFT JOIN payments p ON p.id = c.payment_id
            WHERE c.transaction_date >= :start_date 
              AND c.transaction_date <= :end_date
              AND c.is_cancelled = 0
            ORDER BY c.transaction_date ASC, c.id ASC
        ");
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date'   => $endDate
        ]);
        $transactions = $stmt->fetchAll();

        // Hitung total periode
        $periodIn = 0.0;
        $periodOut = 0.0;
        foreach ($transactions as $t) {
            if ($t['transaction_type'] === 'masuk') {
                $periodIn += (float)$t['amount'];
            } else {
                $periodOut += (float)$t['amount'];
            }
        }
        $periodNet = $periodIn - $periodOut;

        // Saldo Kas Kumulatif Keseluruhan
        $stmtTotalIn = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'masuk' AND is_cancelled = 0");
        $allIn = (float)$stmtTotalIn->fetchColumn();

        $stmtTotalOut = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'keluar' AND is_cancelled = 0");
        $allOut = (float)$stmtTotalOut->fetchColumn();

        $allBalance = $allIn - $allOut;

        View::render('reports/cash', [
            'pageTitle'    => 'Laporan Arus Kas (Buku Kas Masuk & Keluar)',
            'activeNav'    => 'reports',
            'transactions' => $transactions,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'periodIn'     => $periodIn,
            'periodOut'    => $periodOut,
            'periodNet'    => $periodNet,
            'allBalance'   => $allBalance
        ], 'main');
    }

    public function printRecap(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $metrics = CalculationService::getDashboardMetrics();

        $db = Database::getConnection();

        // Rekap Metode
        $stmtMethod = $db->query("
            SELECT payment_method, COUNT(*) as count_trans, COALESCE(SUM(amount), 0) as total_amount
            FROM payments
            WHERE status = 'terverifikasi'
            GROUP BY payment_method
        ");
        $methods = $stmtMethod->fetchAll();

        // Rekap Pengeluaran Kas
        $stmtOutCategory = $db->query("
            SELECT category, COUNT(*) as count_trans, COALESCE(SUM(amount), 0) as total_amount
            FROM cash_transactions
            WHERE transaction_type = 'keluar' AND is_cancelled = 0
            GROUP BY category
            ORDER BY total_amount DESC
        ");
        $outCategories = $stmtOutCategory->fetchAll();

        // Pengurus penandatangan
        $stmtKetua = $db->query("SELECT full_name FROM users WHERE role = 'ketua' LIMIT 1");
        $ketuaName = $stmtKetua->fetchColumn() ?: 'Ketua Angkatan';

        $stmtBendahara = $db->query("SELECT full_name FROM users WHERE role = 'bendahara' LIMIT 1");
        $bendaharaName = $stmtBendahara->fetchColumn() ?: 'Bendahara Utama';

        View::render('reports/print_recap', [
            'pageTitle'     => 'Cetak Laporan Pertanggungjawaban Keuangan Angkatan',
            'metrics'       => $metrics,
            'methods'       => $methods,
            'outCategories' => $outCategories,
            'ketuaName'     => $ketuaName,
            'bendaharaName' => $bendaharaName
        ], 'none');
    }

    public function exportStudentsCsv(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();
        $status = trim($_GET['status'] ?? 'all');
        $class  = trim($_GET['class'] ?? 'all');

        $sql = "
            SELECT s.student_number, s.full_name, s.class_name,
                   COALESCE(d.total_obligation, 250000.00) as total_obligation,
                   COALESCE(d.total_paid, 0.00) as total_paid,
                   COALESCE(d.remaining_balance, 250000.00) as remaining_balance,
                   COALESCE(d.payment_status, 'belum_bayar') as payment_status
            FROM students s
            LEFT JOIN dues d ON d.student_id = s.id
            WHERE s.status = 'aktif'
        ";
        $params = [];
        if ($status !== 'all' && !empty($status)) {
            $sql .= " AND d.payment_status = :status";
            $params[':status'] = $status;
        }
        if ($class !== 'all' && !empty($class)) {
            $sql .= " AND s.class_name = :class";
            $params[':class'] = $class;
        }
        $sql .= " ORDER BY s.class_name ASC, s.full_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        $headers = [
            'Nomor Siswa (NIS)',
            'Nama Siswa',
            'Kelas',
            'Kewajiban Iuran (Rp)',
            'Terbayar Sah (Rp)',
            'Sisa Tagihan (Rp)',
            'Status Pembayaran'
        ];

        $rows = [];
        foreach ($students as $s) {
            $rows[] = [
                $s['student_number'],
                $s['full_name'],
                $s['class_name'],
                number_format((float)$s['total_obligation'], 0, ',', '.'),
                number_format((float)$s['total_paid'], 0, ',', '.'),
                number_format((float)$s['remaining_balance'], 0, ',', '.'),
                strtoupper(str_replace('_', ' ', $s['payment_status']))
            ];
        }

        $filename = 'Laporan_Rekap_Siswa_' . date('Ymd_His') . '.csv';
        Response::downloadCsv($filename, $headers, $rows);
    }
}
