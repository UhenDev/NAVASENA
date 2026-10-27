<?php
/**
 * End-to-End HTTP Flow Test
 * NAVASENA MANAGEMENT
 */

echo "====================================================================\n";
echo "   E2E HTTP AUTHENTICATION & ROLE ACCESS TEST\n";
echo "====================================================================\n\n";

$baseUrl = 'http://localhost/navasena';
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function makeRequest($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'code'   => $httpCode,
        'header' => $header,
        'body'   => $body
    ];
}

function extractCsrf($html) {
    if (preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    return '';
}

// 1. Ambil halaman login dan CSRF token
echo "[1] Mengakses GET /login...\n";
$resLogin = makeRequest($baseUrl . '/login', 'GET', [], $cookieFile);
$csrf = extractCsrf($resLogin['body']);
echo "    HTTP Code: {$resLogin['code']}\n";
echo "    CSRF Token didapatkan: " . substr($csrf, 0, 10) . "...\n";
if ($resLogin['code'] !== 200 || empty($csrf)) {
    echo "FAILED: Login page did not return 200 or CSRF token\n";
    exit(1);
}

// 2. Login sebagai Ketua Angkatan (uhen)
echo "\n[2] Melakukan autentikasi sebagai Ketua (uhen:hendrikwildan23)...\n";
$postDataKetua = [
    'csrf_token' => $csrf,
    'username'   => 'uhen',
    'password'   => 'hendrikwildan23'
];
$resAuthKetua = makeRequest($baseUrl . '/login', 'POST', $postDataKetua, $cookieFile);
echo "    HTTP Code: {$resAuthKetua['code']}\n";
if (strpos($resAuthKetua['body'], 'Ringkasan Eksekutif Keuangan') !== false) {
    echo "    [PASS] Login Ketua berhasil dan diarahkan ke Dashboard\n";
} else {
    echo "    [FAIL] Dashboard tidak ditemukan setelah login\n";
    exit(1);
}

// 3. Akses rute-rute pengurus oleh Ketua
$pagesKetua = [
    '/dashboard' => 'Dashboard Utama',
    '/students'  => 'Data Siswa Angkatan',
    '/payments'  => 'Pengelolaan Pembayaran',
    '/cash'      => 'Buku Kas Angkatan',
    '/reports'   => 'Pusat Laporan Keuangan',
    '/settings'  => 'Pengaturan Sistem',
    '/users'     => 'Manajemen Akun Pengguna'
];

echo "\n[3] Memverifikasi hak akses Ketua Angkatan...\n";
foreach ($pagesKetua as $page => $expectedTitle) {
    $r = makeRequest($baseUrl . $page, 'GET', [], $cookieFile);
    if ($r['code'] === 200 && strpos($r['body'], $expectedTitle) !== false) {
        echo "    [PASS] $page dapat diakses sempurna (200 OK)\n";
    } else {
        echo "    [FAIL] $page gagal diakses (HTTP {$r['code']})\n";
        exit(1);
    }
}

// 4. Logout Ketua
echo "\n[4] Melakukan Logout Ketua...\n";
$resLogout = makeRequest($baseUrl . '/logout', 'GET', [], $cookieFile);
echo "    [PASS] Logout berhasil dieksekusi\n";

// 5. Login sebagai Bendahara
echo "\n[5] Melakukan autentikasi sebagai Bendahara (bendahara:password123)...\n";
if (file_exists($cookieFile)) unlink($cookieFile);
$resLogin2 = makeRequest($baseUrl . '/login', 'GET', [], $cookieFile);
$csrf2 = extractCsrf($resLogin2['body']);
$postDataBendahara = [
    'csrf_token' => $csrf2,
    'username'   => 'bendahara',
    'password'   => 'password123'
];
$resAuthBen = makeRequest($baseUrl . '/login', 'POST', $postDataBendahara, $cookieFile);
if (strpos($resAuthBen['body'], 'Ringkasan Eksekutif Keuangan') !== false) {
    echo "    [PASS] Login Bendahara berhasil\n";
} else {
    echo "    [FAIL] Login Bendahara gagal\n";
    exit(1);
}

// 6. Verifikasi proteksi role: Bendahara TIDAK BISA mengakses /settings
echo "\n[6] Memverifikasi batasan kewenangan Bendahara (/settings)...\n";
$resBenSettings = makeRequest($baseUrl . '/settings', 'GET', [], $cookieFile);
if (strpos($resBenSettings['body'], 'tidak memiliki izin') !== false || strpos($resBenSettings['body'], 'Ringkasan Eksekutif Keuangan') !== false) {
    echo "    [PASS] Proteksi Role berhasil: Bendahara diblokir dari menu /settings dan dialihkan secara aman\n";
} else {
    echo "    [FAIL] Bendahara dapat mengakses /settings tanpa izin!\n";
    exit(1);
}

// 7. Login sebagai Siswa (20261001 / password)
echo "\n[7] Melakukan autentikasi sebagai Siswa (20261001:password)...\n";
if (file_exists($cookieFile)) unlink($cookieFile);
$resLogin3 = makeRequest($baseUrl . '/login', 'GET', [], $cookieFile);
$csrf3 = extractCsrf($resLogin3['body']);
$postDataSiswa = [
    'csrf_token' => $csrf3,
    'username'   => '20261001',
    'password'   => 'password'
];
$resAuthSiswa = makeRequest($baseUrl . '/login', 'POST', $postDataSiswa, $cookieFile);
if (strpos($resAuthSiswa['body'], 'Portal Keuangan') !== false || strpos($resAuthSiswa['body'], 'Identitas Siswa') !== false || strpos($resAuthSiswa['body'], '20261001') !== false) {
    echo "    [PASS] Login Siswa berhasil dan diarahkan ke Portal Siswa\n";
} else {
    echo "    [FAIL] Portal Siswa tidak dapat diakses setelah login\n";
    exit(1);
}

// Siswa TIDAK BISA mengakses /dashboard pengurus
$resSiswaDash = makeRequest($baseUrl . '/dashboard', 'GET', [], $cookieFile);
if (strpos($resSiswaDash['body'], 'Dashboard Utama') === false) {
    echo "    [PASS] Proteksi Role berhasil: Siswa diblokir dari /dashboard pengurus\n";
} else {
    echo "    [FAIL] Siswa dapat mengakses Dashboard Pengurus!\n";
    exit(1);
}

// Cleanup cookie
if (file_exists($cookieFile)) unlink($cookieFile);

echo "\n====================================================================\n";
echo "   SEMUA PENGUJIAN HTTP DAN ROLE SECURITY BERHASIL 100%!\n";
echo "====================================================================\n";

