<?php
/**
 * Cash Controller - Pengelolaan Buku Kas (Pemasukan & Pengeluaran)
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
use PDO;
use Exception;

class CashController
{
    public function index(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();

        $type      = trim($_GET['type'] ?? 'all');
        $category  = trim($_GET['category'] ?? 'all');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate   = trim($_GET['end_date'] ?? '');

        // Kategori-kategori kas baku
        $categories = [
            'Iuran Siswa',
            'Album Fisik',
            'Graduation',
            'Kegiatan Angkatan',
            'Administrasi & Operasional',
            'Pemasukan Lain',
            'Pengeluaran Lain'
        ];

        $sql = "
            SELECT c.*, u.full_name as recorder_name, p.receipt_number
            FROM cash_transactions c
            JOIN users u ON u.id = c.recorded_by
            LEFT JOIN payments p ON p.id = c.payment_id
            WHERE 1=1
        ";
        $params = [];

        if ($type !== '' && $type !== 'all') {
            $sql .= " AND c.transaction_type = :type";
            $params[':type'] = $type;
        }

        if ($category !== '' && $category !== 'all') {
            $sql .= " AND c.category = :category";
            $params[':category'] = $category;
        }

        if ($startDate !== '') {
            $sql .= " AND c.transaction_date >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate !== '') {
            $sql .= " AND c.transaction_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql .= " ORDER BY c.transaction_date DESC, c.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll();

        // Ringkasan Kas Keseluruhan (Hanya transaksi yang tidak dibatalkan)
        $stmtTotalIn = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'masuk' AND is_cancelled = 0");
        $totalIn = (float)$stmtTotalIn->fetchColumn();

        $stmtTotalOut = $db->query("SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE transaction_type = 'keluar' AND is_cancelled = 0");
        $totalOut = (float)$stmtTotalOut->fetchColumn();

        $balance = $totalIn - $totalOut;

        View::render('cash/index', [
            'pageTitle'    => 'Buku Kas Angkatan',
            'activeNav'    => 'cash',
            'transactions' => $transactions,
            'categories'   => $categories,
            'type'         => $type,
            'category'     => $category,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'totalIn'      => $totalIn,
            'totalOut'     => $totalOut,
            'balance'      => $balance
        ], 'main');
    }

    public function create(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $categories = [
            'Album Fisik',
            'Graduation',
            'Kegiatan Angkatan',
            'Administrasi & Operasional',
            'Pemasukan Lain',
            'Pengeluaran Lain'
        ];

        View::render('cash/create', [
            'pageTitle'  => 'Catat Transaksi Kas',
            'activeNav'  => 'cash',
            'categories' => $categories
        ], 'main');
    }

    public function store(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $type            = trim($_POST['transaction_type'] ?? 'keluar');
        $category        = trim($_POST['category'] ?? '');
        $amount          = (float)str_replace(['.', ','], ['', '.'], $_POST['amount'] ?? '0');
        $transactionDate = trim($_POST['transaction_date'] ?? date('Y-m-d'));
        $description     = trim($_POST['description'] ?? '');

        if (!in_array($type, ['masuk', 'keluar'], true)) {
            Flash::setError("Jenis transaksi harus berupa Kas Masuk atau Kas Keluar.");
            Response::redirect('/cash/create');
        }

        if (empty($category)) {
            Flash::setError("Kategori transaksi wajib dipilih.");
            Response::redirect('/cash/create');
        }

        if ($amount <= 0) {
            Flash::setError("Nominal transaksi harus lebih besar dari Rp 0.");
            Response::redirect('/cash/create');
        }

        if (empty($description)) {
            Flash::setError("Keterangan transaksi kas wajib diisi.");
            Response::redirect('/cash/create');
        }

        $db = Database::getConnection();

        try {
            $stmt = $db->prepare("
                INSERT INTO cash_transactions (
                    transaction_type, category, amount, transaction_date, 
                    description, recorded_by, is_cancelled, created_at
                ) VALUES (
                    :type, :category, :amount, :date, 
                    :description, :recorded_by, 0, NOW()
                )
            ");
            $stmt->execute([
                ':type'        => $type,
                ':category'    => $category,
                ':amount'      => $amount,
                ':date'        => $transactionDate,
                ':description' => $description,
                ':recorded_by' => Auth::id()
            ]);
            $cashId = (int)$db->lastInsertId();

            Logger::log(
                'CASH_ENTRY',
                'cash_transaction',
                (string)$cashId,
                "Catat Kas " . ucfirst($type) . " [$category]: " . View::formatRupiah($amount) . " - $description"
            );

            Flash::setSuccess("Transaksi kas " . ucfirst($type) . " sebesar " . View::formatRupiah($amount) . " berhasil dicatat.", "Buku Kas Angkatan");
            Response::redirect('/cash');
        } catch (Exception $e) {
            error_log("Error creating cash transaction: " . $e->getMessage());
            Flash::setError("Gagal menyimpan transaksi kas: " . $e->getMessage(), "Kesalahan Transaksi Kas");
            Response::redirect('/cash/create');
        }
    }

    public function cancel(int $id): void
    {
        Auth::requireRole(['ketua', 'bendahara']);
        CSRF::checkRequest();

        $reason = trim($_POST['cancellation_reason'] ?? '');
        if (empty($reason)) {
            Flash::setError("Alasan pembatalan transaksi kas wajib diisi.");
            Response::redirect('/cash');
        }

        $db = Database::getConnection();

        try {
            $stmt = $db->prepare("SELECT * FROM cash_transactions WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $cash = $stmt->fetch();

            if (!$cash) {
                Flash::setError("Transaksi kas tidak ditemukan.");
                Response::redirect('/cash');
            }

            if ($cash['payment_id'] !== null) {
                Flash::setError("Transaksi kas yang berasal dari pembayaran iuran siswa harus dibatalkan melalui menu Pengelolaan Pembayaran.");
                Response::redirect('/cash');
            }

            $stmtCancel = $db->prepare("
                UPDATE cash_transactions 
                SET is_cancelled = 1, cancellation_note = :note, updated_at = NOW() 
                WHERE id = :id
            ");
            $stmtCancel->execute([
                ':note' => $reason,
                ':id'   => $id
            ]);

            Logger::log(
                'CASH_CANCEL',
                'cash_transaction',
                (string)$id,
                "Transaksi kas #$id (" . View::formatRupiah($cash['amount']) . ") dibatalkan. Alasan: $reason"
            );

            Flash::setSuccess("Transaksi kas berhasil dibatalkan dan saldo kas telah disesuaikan.", "Pembatalan Kas Berhasil");
            Response::redirect('/cash');
        } catch (Exception $e) {
            error_log("Error cancelling cash: " . $e->getMessage());
            Flash::setError("Gagal membatalkan transaksi kas: " . $e->getMessage(), "Gagal Pembatalan Kas");
            Response::redirect('/cash');
        }
    }

    public function exportCsv(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT c.*, u.full_name as recorder_name, p.receipt_number
            FROM cash_transactions c
            JOIN users u ON u.id = c.recorded_by
            LEFT JOIN payments p ON p.id = c.payment_id
            ORDER BY c.transaction_date ASC, c.id ASC
        ");
        $rowsData = $stmt->fetchAll();

        $headers = [
            'ID',
            'Tanggal',
            'Jenis',
            'Kategori',
            'Nomor Kuitansi',
            'Keterangan',
            'Kas Masuk (Rp)',
            'Kas Keluar (Rp)',
            'Dicatat Oleh',
            'Status'
        ];

        $rows = [];
        foreach ($rowsData as $r) {
            $isCancelled = (int)$r['is_cancelled'] === 1;
            $rows[] = [
                $r['id'],
                $r['transaction_date'],
                strtoupper($r['transaction_type']),
                $r['category'],
                $r['receipt_number'] ?: '-',
                $r['description'],
                $r['transaction_type'] === 'masuk' ? number_format((float)$r['amount'], 0, ',', '.') : '0',
                $r['transaction_type'] === 'keluar' ? number_format((float)$r['amount'], 0, ',', '.') : '0',
                $r['recorder_name'],
                $isCancelled ? 'DIBATALKAN (' . $r['cancellation_note'] . ')' : 'SAH'
            ];
        }

        $filename = 'Buku_Kas_Navasena_' . date('Ymd_His') . '.csv';
        Response::downloadCsv($filename, $headers, $rows);
    }
}
