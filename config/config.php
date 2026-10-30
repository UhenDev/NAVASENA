<?php
/**
 * Konfigurasi Utama Aplikasi
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

// Set Timezone default Indonesia Western Time (WIB)
date_default_timezone_set('Asia/Jakarta');

// Start secure session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Konfigurasi direktori penyimpanan sesi agar selalu dapat ditulis oleh webserver (Docker/Dokploy/XAMPP)
    $sessionSavePath = dirname(__DIR__) . '/storage/sessions';
    if (!is_dir($sessionSavePath)) {
        @mkdir($sessionSavePath, 0775, true);
    }
    if (is_dir($sessionSavePath) && is_writable($sessionSavePath)) {
        @session_save_path($sessionSavePath);
    }

    // secure = false memastikan cookie TIDAK diabaikan/di-drop browser baik saat diakses via HTTP maupun HTTPS
    @ini_set('session.gc_maxlifetime', '2592000'); // 30 hari
    @ini_set('session.cookie_lifetime', '2592000');
    session_set_cookie_params([
        'lifetime' => 86400 * 30, // 30 hari
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_name('NAVASENA_SESSID');
    session_start();
}

// Deteksi Base Dir & URL secara otomatis
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$baseDir = dirname($scriptName);
$baseDir = ($baseDir === '/' || $baseDir === '\\') ? '' : rtrim($baseDir, '/');

// Jika diakses lewat root folder tanpa menyertakan /public di REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (!str_contains($requestUri, '/public') && str_ends_with($baseDir, '/public')) {
    $parent = dirname($baseDir);
    $baseDir = ($parent === '/' || $parent === '\\') ? '' : rtrim($parent, '/');
}

// Deteksi HTTPS
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && (int)$_SERVER['HTTP_X_FORWARDED_PORT'] === 443);

$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');

$appUrlEnv = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? null);
$baseUrl = !empty($appUrlEnv) ? rtrim($appUrlEnv, '/') : ($protocol . $host . $baseDir);

return [
    'app_name'    => 'NAVASENA MANAGEMENT',
    'app_title'   => 'Sistem Informasi Keuangan Angkatan',
    'app_version' => '1.0.0',
    'base_url'    => $baseUrl,
    'base_dir'    => $baseDir,
    'base_path'   => dirname(__DIR__),
    'storage_path'=> dirname(__DIR__) . '/storage',
    'default_dues'=> 250000.00,
    'stages'      => [
        1 => ['name' => 'DP Pertama', 'amount' => 100000.00, 'due' => '2026-12-01'],
        2 => ['name' => 'Cicilan Kedua', 'amount' => 100000.00, 'due' => '2027-04-30'],
        3 => ['name' => 'Pelunasan', 'amount' => 50000.00, 'due' => '2027-06-15']
    ]
];
