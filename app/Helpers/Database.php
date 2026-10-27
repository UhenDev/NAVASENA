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
                // Log and show user-friendly error without leaking passwords
                error_log("Database connection error: " . $e->getMessage());
                die("<div style='font-family:sans-serif;padding:2rem;text-align:center;'><h2>Koneksi Database Gagal</h2><p>Pastikan MySQL/MariaDB pada XAMPP sudah berjalan dan database <code>navasena_keuangan</code> telah dibuat.</p></div>");
            }
        }

        return self::$pdo;
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
