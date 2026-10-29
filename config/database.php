<?php
/**
 * Konfigurasi Koneksi Database MySQL / MariaDB (XAMPP Default)
 * NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
 */

return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => getenv('DB_PORT') ?: 3306,
    'dbname'   => getenv('DB_NAME') ?: 'navasena_keuangan',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
    ]
];
