<?php
/**
 * Konfigurasi Utama Aplikasi
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

// Set Timezone default Indonesia Western Time (WIB)
date_default_timezone_set('Asia/Jakarta');

// Start secure session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_name('NAVASENA_SESSID');
    session_start();
}

// Deteksi Base URL secara otomatis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$baseDir = dirname($scriptName);
$baseDir = ($baseDir === '/' || $baseDir === '\\') ? '' : rtrim($baseDir, '/');

// Jika diakses lewat root folder tanpa menyertakan /public di REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (!str_contains($requestUri, '/public') && str_ends_with($baseDir, '/public')) {
    $parent = dirname($baseDir);
    $baseDir = ($parent === '/' || $parent === '\\') ? '' : rtrim($parent, '/');
}

$baseUrl = $protocol . $host . $baseDir;

return [
    'app_name'    => 'NAVASENA MANAGEMENT',
    'app_title'   => 'Sistem Informasi Keuangan Angkatan',
    'app_version' => '1.0.0',
    'base_url'    => $baseUrl,
    'base_path'   => dirname(__DIR__),
    'storage_path'=> dirname(__DIR__) . '/storage',
    'default_dues'=> 250000.00,
    'stages'      => [
        1 => ['name' => 'DP Pertama', 'amount' => 100000.00, 'due' => '2026-12-01'],
        2 => ['name' => 'Cicilan Kedua', 'amount' => 100000.00, 'due' => '2027-04-30'],
        3 => ['name' => 'Pelunasan', 'amount' => 50000.00, 'due' => '2027-06-15']
    ]
];
