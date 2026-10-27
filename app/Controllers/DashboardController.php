<?php
/**
 * Dashboard Controller - Pengurus Angkatan (Ketua & Bendahara)
 * NAVASENA MANAGEMENT
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Response;
use App\Helpers\View;
use App\Services\CalculationService;

class DashboardController
{
    public function index(): void
    {
        Auth::requireRole(['ketua', 'bendahara']);

        $metrics = CalculationService::getDashboardMetrics();

        // Ambil 5 aktivitas terbaru dari audit_logs
        $db = Database::getConnection();
        $stmtLogs = $db->query("
            SELECT a.*, u.username, u.full_name as user_full_name, u.role as user_role
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.user_id
            ORDER BY a.id DESC
            LIMIT 6
        ");
        $recentLogs = $stmtLogs->fetchAll();

        View::render('dashboard/index', [
            'pageTitle'  => 'Dashboard Utama',
            'activeNav'  => 'dashboard',
            'metrics'    => $metrics,
            'recentLogs' => $recentLogs,
            'canVerify'  => Auth::canVerify()
        ], 'main');
    }
}
