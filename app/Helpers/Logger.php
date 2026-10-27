<?php
/**
 * Logger Helper - Audit Trail System
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

use PDO;
use Exception;

class Logger
{
    public static function log(string $action, ?string $targetType = null, ?string $targetId = null, ?string $details = null): void
    {
        try {
            $db = Database::getConnection();
            $userId = Auth::id();
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, target_type, target_id, details, ip_address, created_at)
                VALUES (:user_id, :action, :target_type, :target_id, :details, :ip_address, NOW())
            ");

            $stmt->execute([
                ':user_id'     => $userId,
                ':action'      => $action,
                ':target_type' => $targetType,
                ':target_id'   => $targetId,
                ':details'     => $details,
                ':ip_address'  => $ipAddress
            ]);
        } catch (Exception $e) {
            // Fail quietly so an audit log error does not block normal transactions, but log to error_log
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}
