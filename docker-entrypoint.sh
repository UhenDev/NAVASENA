#!/bin/bash
set -e

# ==============================================================================
# NAVASENA MANAGEMENT - DOCKER ENTRYPOINT
# Handles database connectivity verification, automatic migrations, and permissions
# Self-contained: Runs local MariaDB if no external database is configured!
# ==============================================================================

echo ">> [NAVASENA] Starting container entrypoint initialization..."

# 1. Ensure storage permissions
mkdir -p /var/www/html/storage/logs /var/www/html/storage/exports /var/www/html/storage/sessions
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

DB_HOST_VAL="${DB_HOST:-127.0.0.1}"
DB_PORT_VAL="${DB_PORT:-3306}"
DB_NAME_VAL="${DB_NAME:-navasena_keuangan}"
DB_USER_VAL="${DB_USER:-root}"
DB_PASS_VAL="${DB_PASS:-}"

# 2. If DB_HOST is 127.0.0.1 or localhost, start built-in MariaDB
if [ "$DB_HOST_VAL" = "127.0.0.1" ] || [ "$DB_HOST_VAL" = "localhost" ]; then
    echo ">> [NAVASENA] Using built-in MariaDB database (Host: $DB_HOST_VAL)..."

    # Initialize data dir if empty
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo ">> [NAVASENA] Initializing MariaDB data directory..."
        mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1 || true
    fi

    mkdir -p /var/run/mysqld
    chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
    chmod 777 /var/run/mysqld

    # Start MariaDB service
    echo ">> [NAVASENA] Starting MariaDB service..."
    service mariadb start 2>/dev/null || /etc/init.d/mariadb start 2>/dev/null || mysqld_safe --user=mysql &

    # Wait for MariaDB to be ready
    echo ">> [NAVASENA] Waiting for MariaDB service to be ready..."
    for i in $(seq 1 30); do
        if mysqladmin ping --silent 2>/dev/null; then
            echo ">> [NAVASENA] Local MariaDB is UP and accepting connections (attempt $i)."
            break
        fi
        sleep 1
    done

    # Ensure database exists
    echo ">> [NAVASENA] Ensuring database $DB_NAME_VAL exists..."
    mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME_VAL\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true

    # Check and import schema if needed
    TABLE_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME_VAL';" 2>/dev/null || echo "0")
    if [ "$TABLE_COUNT" = "0" ] || [ -z "$TABLE_COUNT" ]; then
        echo ">> [NAVASENA] Database is empty. Importing schema and initial seed..."
        if [ -f "/var/www/html/database/schema.sql" ]; then
            mysql "$DB_NAME_VAL" < /var/www/html/database/schema.sql
        fi
        if [ -f "/var/www/html/database/seed.sql" ]; then
            mysql "$DB_NAME_VAL" < /var/www/html/database/seed.sql
        fi
        if [ -f "/var/www/html/database/seed_students.sql" ]; then
            echo ">> [NAVASENA] Importing 489 real students into database..."
            mysql "$DB_NAME_VAL" < /var/www/html/database/seed_students.sql
        fi
        echo ">> [NAVASENA] Database schema, master accounts, and 489 students successfully created!"
    else
        echo ">> [NAVASENA] Database already has $TABLE_COUNT tables. Preserving existing data."
        STUDENT_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM students;" "$DB_NAME_VAL" 2>/dev/null || echo "0")
        if [ "$STUDENT_COUNT" = "0" ] && [ -f "/var/www/html/database/seed_students.sql" ]; then
            echo ">> [NAVASENA] No students found in database. Automatically seeding 489 real students..."
            mysql "$DB_NAME_VAL" < /var/www/html/database/seed_students.sql || true
        fi
    fi

else
    # Remote DB_HOST detected (external database)
    echo ">> [NAVASENA] External DB_HOST detected: $DB_HOST_VAL:$DB_PORT_VAL. Waiting for connection..."
    php -r "
    \$host = '$DB_HOST_VAL';
    \$port = $DB_PORT_VAL;
    \$db   = '$DB_NAME_VAL';
    \$user = '$DB_USER_VAL';
    \$pass = '$DB_PASS_VAL';

    \$maxRetries = 25;
    \$pdo = null;

    for (\$i = 1; \$i <= \$maxRetries; \$i++) {
        try {
            \$pdo = new PDO(\"mysql:host=\$host;port=\$port;charset=utf8mb4\", \$user, \$pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);
            echo \">> [NAVASENA] Remote database connection established on attempt \$i.\n\";
            break;
        } catch (Exception \$e) {
            echo \">> [NAVASENA] Waiting for remote database at \$host:\$port (\$i/\$maxRetries)...\n\";
            sleep(2);
        }
    }

    if (\$pdo) {
        \$pdo->exec(\"CREATE DATABASE IF NOT EXISTS \`\$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci\");
        \$pdo->exec(\"USE \`\$db\`\");
        \$tables = \$pdo->query(\"SHOW TABLES\")->fetchAll(PDO::FETCH_COLUMN);
        if (count(\$tables) === 0) {
            \$sql = file_get_contents('/var/www/html/database/schema.sql');
            \$sql = preg_replace('/CREATE DATABASE [^;]+;/i', '', \$sql);
            \$sql = preg_replace('/USE [^;]+;/i', '', \$sql);
            \$pdo->exec(\$sql);
            if (file_exists('/var/www/html/database/seed.sql')) {
                \$seed = file_get_contents('/var/www/html/database/seed.sql');
                \$seed = preg_replace('/USE [^;]+;/i', '', \$seed);
                \$pdo->exec(\$seed);
            }
            if (file_exists('/var/www/html/database/seed_students.sql')) {
                \$seedStudents = file_get_contents('/var/www/html/database/seed_students.sql');
                \$seedStudents = preg_replace('/USE [^;]+;/i', '', \$seedStudents);
                \$pdo->exec(\$seedStudents);
            }
            echo \">> [NAVASENA] Remote schema and 489 students initialized successfully!\n\";
        } else {
            \$count = \$pdo->query(\"SELECT COUNT(*) FROM students\")->fetchColumn();
            if ((int)\$count === 0 && file_exists('/var/www/html/database/seed_students.sql')) {
                echo \">> [NAVASENA] Seeding 489 real students into existing remote database...\n\";
                \$seedStudents = file_get_contents('/var/www/html/database/seed_students.sql');
                \$seedStudents = preg_replace('/USE [^;]+;/i', '', \$seedStudents);
                \$pdo->exec(\$seedStudents);
            }
        }
    }
    " || true
fi

echo ">> [NAVASENA] Initialization complete. Starting Apache web server..."
exec "$@"
