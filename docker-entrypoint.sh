#!/bin/bash
set -e

# ==============================================================================
# NAVASENA MANAGEMENT - DOCKER ENTRYPOINT
# Handles database connectivity verification, automatic migrations, and permissions
# ==============================================================================

echo ">> [NAVASENA] Starting container entrypoint initialization..."

# 1. Ensure storage permissions
mkdir -p /var/www/html/storage/logs /var/www/html/storage/exports
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# 2. Database Auto-Migration / Verification
if [ -n "$DB_HOST" ]; then
    echo ">> [NAVASENA] DB_HOST detected: $DB_HOST. Testing connection and checking schema..."

    php -r '
    $host = getenv("DB_HOST") ?: "127.0.0.1";
    $port = getenv("DB_PORT") ?: 3306;
    $db   = getenv("DB_NAME") ?: "navasena_keuangan";
    $user = getenv("DB_USER") ?: "root";
    $pass = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";

    $maxRetries = 25;
    $pdo = null;

    for ($i = 1; $i <= $maxRetries; $i++) {
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);
            echo ">> [NAVASENA] Database connection established successfully on attempt $i.\n";
            break;
        } catch (Exception $e) {
            echo ">> [NAVASENA] Waiting for database at $host:$port (attempt $i/$maxRetries)... \n";
            sleep(2);
        }
    }

    if (!$pdo) {
        echo ">> [NAVASENA WARNING] Could not connect to database after $maxRetries attempts. Skipping auto-migration.\n";
        exit(0);
    }

    // Ensure database exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    // Check if tables already exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) === 0) {
        echo ">> [NAVASENA] Database is empty. Importing schema from database/schema.sql...\n";
        $sqlFile = "/var/www/html/database/schema.sql";
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            // Remove CREATE DATABASE and USE statements to allow custom DB_NAME
            $sql = preg_replace("/CREATE DATABASE [^;]+;/i", "", $sql);
            $sql = preg_replace("/USE [^;]+;/i", "", $sql);
            $pdo->exec($sql);
            echo ">> [NAVASENA] Schema and master accounts created successfully!\n";
        }
    } else {
        echo ">> [NAVASENA] Database schema already initialized (" . count($tables) . " tables detected).\n";
    }
    ' || true
fi

echo ">> [NAVASENA] Initialization complete. Starting Apache web server..."
exec "$@"
