<?php
/**
 * Database Helper - Singleton PDO Manager with Transaction Wrappers
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';

            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // Jika error karena database belum ada (error 1049: Unknown database), coba buat otomatis
                if ($e->getCode() == 1049) {
                    try {
                        $dsnRoot = sprintf("mysql:host=%s;port=%d;charset=%s", $config['host'], $config['port'], $config['charset']);
                        $pdoRoot = new PDO($dsnRoot, $config['username'], $config['password'], $config['options']);
                        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . $config['dbname'] . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                    } catch (PDOException $ex) {
                        // Fallback ke error handler di bawah
                    }
                }

                // Jika masih belum terhubung, tampilkan panduan diagnostik
                if (self::$pdo === null) {
                    error_log("Database connection error: " . $e->getMessage());

                    if (php_sapi_name() === 'cli') {
                        fwrite(STDERR, "[NAVASENA DB ERROR] " . $e->getMessage() . "\n");
                        throw $e;
                    }

                    $host = htmlspecialchars($config['host']);
                    $port = (int)$config['port'];
                    $db   = htmlspecialchars($config['dbname']);
                    $user = htmlspecialchars($config['username']);
                    $errMsg = htmlspecialchars($e->getMessage());

                    http_response_code(500);
                    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
                    echo "<title>Koneksi Database Belum Terhubung — NAVASENA</title>";
                    echo "<style>
                        body { margin: 0; background: #070a12; color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; box-sizing: border-box; }
                        .card { max-width: 620px; width: 100%; background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(223, 178, 86, 0.25); border-radius: 16px; padding: 2.25rem; box-shadow: 0 20px 50px rgba(0,0,0,0.6); backdrop-filter: blur(12px); }
                        h2 { margin-top: 0; color: #dfb256; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
                        p { color: #94a3b8; line-height: 1.6; font-size: 0.95rem; }
                        .info-box { background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1rem; margin: 1.25rem 0; font-family: monospace; font-size: 0.85rem; }
                        .info-row { display: flex; justify-content: space-between; margin-bottom: 0.35rem; }
                        .info-label { color: #64748b; }
                        .info-val { color: #e2e8f0; font-weight: bold; }
                        .err-box { background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.25rem; word-break: break-all; }
                        .step-card { background: rgba(223, 178, 86, 0.08); border-left: 3px solid #dfb256; padding: 0.85rem 1rem; border-radius: 4px; margin-top: 1rem; }
                        .step-title { font-weight: bold; color: #f8fafc; font-size: 0.9rem; margin-bottom: 0.25rem; }
                        code { background: rgba(0,0,0,0.5); padding: 0.15rem 0.4rem; border-radius: 4px; color: #dfb256; }
                    </style></head><body>";
                    echo "<div class='card'>";
                    echo "<h2>⚠️ Koneksi Database Belum Terhubung</h2>";
                    echo "<p>Aplikasi web NAVASENA berhasil berjalan di Dokploy/Server, namun belum dapat tersambung ke database MySQL/MariaDB.</p>";
                    
                    echo "<div class='err-box'><strong>Pesan Error:</strong> {$errMsg}</div>";

                    echo "<div class='info-box'>";
                    echo "<div class='info-row'><span class='info-label'>Host Database:</span><span class='info-val'>{$host}</span></div>";
                    echo "<div class='info-row'><span class='info-label'>Port Database:</span><span class='info-val'>{$port}</span></div>";
                    echo "<div class='info-row'><span class='info-label'>Nama Database:</span><span class='info-val'>{$db}</span></div>";
                    echo "<div class='info-row'><span class='info-label'>Pengguna (User):</span><span class='info-val'>{$user}</span></div>";
                    echo "</div>";

                    echo "<div class='step-card'>";
                    echo "<div class='step-title'>Langkah Penyelesaian di Dokploy:</div>";
                    echo "<p style='margin:0; font-size: 0.88rem; color: #cbd5e1;'>";
                    echo "1. Buka <strong>Dashboard Dokploy</strong> &rarr; Service Aplikasi Anda (<code>peserta58</code>)<br>";
                    echo "2. Masuk ke tab <strong>Environment</strong><br>";
                    echo "3. Isi variabel koneksi database Anda:<br>";
                    echo "<pre style='background:rgba(0,0,0,0.6);padding:0.6rem;border-radius:6px;margin:0.5rem 0;color:#dfb256;'>DB_HOST=nama-host-database-anda\nDB_PORT=3306\nDB_NAME=navasena_keuangan\nDB_USER=root\nDB_PASS=password-database-anda</pre>";
                    echo "4. Klik <strong>Save</strong> lalu klik <strong>Redeploy</strong>.";
                    echo "</p></div>";

                    echo "</div></body></html>";
                    exit;
                }
            }

            // Self-Healing Auto-Initialization:
            // 1. Cek tabel-tabel utama. Jika belum ada, jalankan schema.sql
            try {
                $stmt = self::$pdo->query("SHOW TABLES");
                $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $dbDir = dirname(__DIR__, 2) . '/database';
                
                if (count($existingTables) === 0) {
                    self::executeSqlFile(self::$pdo, $dbDir . '/schema.sql');
                }

                // 2. Cek apakah akun master / users sudah ada. Jika kosong, jalankan seed.sql
                $hasUsersTable = in_array('users', $existingTables, true);
                if (!$hasUsersTable) {
                    $tablesCheck = self::$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    $hasUsersTable = in_array('users', $tablesCheck, true);
                }

                if ($hasUsersTable) {
                    $userCount = (int)self::$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    if ($userCount === 0 && file_exists($dbDir . '/seed.sql')) {
                        self::executeSqlFile(self::$pdo, $dbDir . '/seed.sql');
                    }
                }

                // 3. Cek apakah data siswa sudah ada. Jika kosong, jalankan seed_students.sql
                $hasStudentsTable = in_array('students', $existingTables, true);
                if (!$hasStudentsTable) {
                    $tablesCheck = self::$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    $hasStudentsTable = in_array('students', $tablesCheck, true);
                }

                if ($hasStudentsTable) {
                    $studentCount = (int)self::$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
                    if ($studentCount === 0 && file_exists($dbDir . '/seed_students.sql')) {
                        self::executeSqlFile(self::$pdo, $dbDir . '/seed_students.sql');
                    }
                }
            } catch (\Throwable $t) {
                error_log("Database Auto-Init Warning: " . $t->getMessage());
            }
        }

        return self::$pdo;
    }

    /**
     * Menjalankan berkas query SQL secara aman
     */
    public static function executeSqlFile(PDO $pdo, string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $sql = file_get_contents($filePath);
        if (trim($sql) === '') {
            return true;
        }

        // Hapus pernyataan CREATE DATABASE dan USE agar tetap menggunakan koneksi DB aktif
        $sql = preg_replace('/CREATE DATABASE [^;]+;/i', '', $sql);
        $sql = preg_replace('/USE [^;]+;/i', '', $sql);

        // Hapus komentar SQL
        $lines = explode("\n", $sql);
        $cleanLines = [];
        $inBlockComment = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($inBlockComment) {
                if (str_contains($trimmed, '*/')) {
                    $inBlockComment = false;
                }
                continue;
            }
            if (str_starts_with($trimmed, '/*')) {
                if (!str_contains($trimmed, '*/')) {
                    $inBlockComment = true;
                }
                continue;
            }
            if (str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                continue;
            }
            if ($trimmed !== '') {
                $cleanLines[] = $line;
            }
        }

        $cleanSql = implode("\n", $cleanLines);

        try {
            $pdo->exec($cleanSql);
            return true;
        } catch (\Throwable $e) {
            // Fallback: jalankan pernyataan satu per satu jika driver tidak mendukung multi-statement utuh
            $statements = preg_split('/;\s*(\r\n|\r|\n)/m', $cleanSql);
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (!empty($stmt)) {
                    try {
                        $pdo->exec($stmt);
                    } catch (\Throwable $stEx) {
                        error_log("executeSqlFile warning: " . $stEx->getMessage() . " on statement: " . substr($stmt, 0, 80));
                    }
                }
            }
            return true;
        }
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        if (self::getConnection()->inTransaction()) {
            return self::getConnection()->rollBack();
        }
        return false;
    }
}
