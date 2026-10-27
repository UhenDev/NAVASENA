<?php
/**
 * CSRF Protection Helper
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

class CSRF
{
    public static function getToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function checkRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!self::validate($token)) {
                Flash::setError("Sesi validasi keamanan (CSRF) kadaluarsa. Silakan ulangi kembali.");
                $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                Response::redirect($referer);
            }
        }
    }
}
