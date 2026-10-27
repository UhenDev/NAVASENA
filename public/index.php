<?php
/**
 * Front Controller & Main Application Router
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

require_once dirname(__DIR__) . '/app/autoload.php';
$config = require dirname(__DIR__) . '/config/config.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\StudentController;
use App\Controllers\PaymentController;
use App\Controllers\CashController;
use App\Controllers\ReportController;
use App\Controllers\PortalController;
use App\Controllers\SettingController;
use App\Controllers\UserController;
use App\Helpers\Auth;
use App\Helpers\Response;
use App\Helpers\View;

// Tentukan Path dari URI atau fallback query ?r=
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir  = dirname($scriptName);
$scriptDir  = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');

// Hapus query string dari requestUri
if (($pos = strpos($requestUri, '?')) !== false) {
    $path = substr($requestUri, 0, $pos);
} else {
    $path = $requestUri;
}

// Hapus script dir dari path jika ada
if (!empty($scriptDir) && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
} else {
    // Jika diakses melalui parent dir via root .htaccess (misal /navasena)
    $parentDir = dirname($scriptDir);
    $parentDir = ($parentDir === '/' || $parentDir === '\\') ? '' : rtrim($parentDir, '/');
    if (!empty($parentDir) && str_starts_with($path, $parentDir)) {
        $path = substr($path, strlen($parentDir));
    }
}

// Hapus 'index.php' jika ada di URL
if (str_starts_with($path, '/index.php')) {
    $path = substr($path, strlen('/index.php'));
}

// Cek fallback query parameter ?r=
if (isset($_GET['r']) && !empty($_GET['r'])) {
    $path = '/' . ltrim($_GET['r'], '/');
}

$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Router Dispatcher
try {
    // 1. Root / Halaman Depan
    if ($path === '/' || $path === '') {
        if (!Auth::check()) {
            Response::redirect('/login');
        } elseif (Auth::isSiswa()) {
            Response::redirect('/portal');
        } else {
            Response::redirect('/dashboard');
        }
    }

    // 2. Autentikasi
    elseif ($path === '/login') {
        $controller = new AuthController();
        if ($method === 'POST') {
            $controller->processLogin();
        } else {
            $controller->showLogin();
        }
    }
    elseif ($path === '/logout') {
        (new AuthController())->logout();
    }
    elseif ($path === '/change-password') {
        $controller = new AuthController();
        if ($method === 'POST') {
            $controller->processChangePassword();
        } else {
            $controller->showChangePassword();
        }
    }

    // 3. Dashboard Pengurus
    elseif ($path === '/dashboard') {
        (new DashboardController())->index();
    }

    // 4. Manajemen Siswa
    elseif ($path === '/students') {
        (new StudentController())->index();
    }
    elseif ($path === '/students/create') {
        $controller = new StudentController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
    }
    elseif ($path === '/students/import') {
        $controller = new StudentController();
        if ($method === 'POST') {
            $controller->processImport();
        } else {
            $controller->showImport();
        }
    }
    elseif ($path === '/students/export') {
        (new StudentController())->exportCsv();
    }
    elseif (preg_match('#^/students/([0-9]+)$#', $path, $matches)) {
        (new StudentController())->show((int)$matches[1]);
    }
    elseif (preg_match('#^/students/([0-9]+)/edit$#', $path, $matches)) {
        $controller = new StudentController();
        if ($method === 'POST') {
            $controller->update((int)$matches[1]);
        } else {
            $controller->edit((int)$matches[1]);
        }
    }
    elseif (preg_match('#^/students/([0-9]+)/toggle-status$#', $path, $matches)) {
        if ($method === 'POST') {
            (new StudentController())->toggleStatus((int)$matches[1]);
        } else {
            Response::redirect('/students/' . $matches[1]);
        }
    }

    // 5. Pengelolaan Pembayaran
    elseif ($path === '/payments') {
        (new PaymentController())->index();
    }
    elseif ($path === '/payments/create') {
        $controller = new PaymentController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
    }
    elseif (preg_match('#^/payments/([0-9]+)$#', $path, $matches)) {
        (new PaymentController())->show((int)$matches[1]);
    }
    elseif (preg_match('#^/payments/([0-9]+)/receipt$#', $path, $matches)) {
        (new PaymentController())->receipt((int)$matches[1]);
    }
    elseif (preg_match('#^/payments/([0-9]+)/verify$#', $path, $matches)) {
        if ($method === 'POST') {
            (new PaymentController())->verify((int)$matches[1]);
        } else {
            Response::redirect('/payments/' . $matches[1]);
        }
    }
    elseif (preg_match('#^/payments/([0-9]+)/reject$#', $path, $matches)) {
        if ($method === 'POST') {
            (new PaymentController())->reject((int)$matches[1]);
        } else {
            Response::redirect('/payments/' . $matches[1]);
        }
    }
    elseif (preg_match('#^/payments/([0-9]+)/cancel$#', $path, $matches)) {
        if ($method === 'POST') {
            (new PaymentController())->cancel((int)$matches[1]);
        } else {
            Response::redirect('/payments/' . $matches[1]);
        }
    }

    // 6. Buku Kas
    elseif ($path === '/cash') {
        (new CashController())->index();
    }
    elseif ($path === '/cash/create') {
        $controller = new CashController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
    }
    elseif ($path === '/cash/export') {
        (new CashController())->exportCsv();
    }
    elseif (preg_match('#^/cash/([0-9]+)/cancel$#', $path, $matches)) {
        if ($method === 'POST') {
            (new CashController())->cancel((int)$matches[1]);
        } else {
            Response::redirect('/cash');
        }
    }

    // 7. Laporan Keuangan
    elseif ($path === '/reports') {
        (new ReportController())->index();
    }
    elseif ($path === '/reports/students') {
        (new ReportController())->students();
    }
    elseif ($path === '/reports/installments') {
        (new ReportController())->installments();
    }
    elseif ($path === '/reports/cash') {
        (new ReportController())->cash();
    }
    elseif ($path === '/reports/print') {
        (new ReportController())->printRecap();
    }
    elseif ($path === '/reports/export/students') {
        (new ReportController())->exportStudentsCsv();
    }

    // 8. Portal Siswa Mandiri
    elseif ($path === '/portal') {
        (new PortalController())->index();
    }
    elseif (preg_match('#^/portal/receipt/([0-9]+)$#', $path, $matches)) {
        (new PortalController())->receipt((int)$matches[1]);
    }

    // 9. Pengaturan & Jadwal Cicilan
    elseif ($path === '/settings') {
        $controller = new SettingController();
        if ($method === 'POST') {
            $controller->update();
        } else {
            $controller->index();
        }
    }

    // 10. Manajemen Pengguna & Hak Akses
    elseif ($path === '/users') {
        (new UserController())->index();
    }
    elseif ($path === '/users/create') {
        $controller = new UserController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
    }
    elseif (preg_match('#^/users/([0-9]+)/reset-password$#', $path, $matches)) {
        if ($method === 'POST') {
            (new UserController())->resetPassword((int)$matches[1]);
        } else {
            Response::redirect('/users');
        }
    }
    elseif (preg_match('#^/users/([0-9]+)/toggle-status$#', $path, $matches)) {
        if ($method === 'POST') {
            (new UserController())->toggleStatus((int)$matches[1]);
        } else {
            Response::redirect('/users');
        }
    }

    // 11. 404 Not Found
    else {
        http_response_code(404);
        View::render('errors/404', [
            'pageTitle' => '404 - Halaman Tidak Ditemukan'
        ], 'none');
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("Unhandled Application Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    View::render('errors/500', [
        'pageTitle' => '500 - Terjadi Kesalahan Sistem',
        'message'   => $e->getMessage()
    ], 'none');
}
