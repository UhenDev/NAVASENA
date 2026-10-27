<?php
/**
 * Auth Helper - Authentication, Role Checks, and Session Management
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id'                   => $_SESSION['user_id'],
            'username'             => $_SESSION['username'] ?? '',
            'full_name'            => $_SESSION['full_name'] ?? '',
            'role'                 => $_SESSION['role'] ?? '',
            'student_id'           => $_SESSION['student_id'] ?? null,
            'force_change_password'=> $_SESSION['force_change_password'] ?? 0
        ];
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function username(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    public static function fullName(): ?string
    {
        return $_SESSION['full_name'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function isKetua(): bool
    {
        return self::role() === 'ketua';
    }

    public static function isBendahara(): bool
    {
        return self::role() === 'bendahara';
    }

    public static function isSiswa(): bool
    {
        return self::role() === 'siswa';
    }

    public static function isStaff(): bool
    {
        return self::isKetua() || self::isBendahara();
    }

    public static function studentId(): ?int
    {
        return $_SESSION['student_id'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']               = (int)$user['id'];
        $_SESSION['username']             = $user['username'];
        $_SESSION['full_name']            = $user['full_name'];
        $_SESSION['role']                 = $user['role'];
        $_SESSION['student_id']           = $user['student_id'] ? (int)$user['student_id'] : null;
        $_SESSION['force_change_password']= (int)($user['force_change_password'] ?? 0);
        $_SESSION['logged_in_at']         = time();
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Flash::setError("Silakan masuk terlebih dahulu untuk mengakses sistem.");
            Response::redirect('/login');
        }
    }

    public static function requireRole(array|string $roles): void
    {
        self::requireLogin();

        $roles = (array)$roles;
        $currentRole = self::role();

        if (!in_array($currentRole, $roles, true)) {
            Flash::setError("Anda tidak memiliki izin untuk mengakses halaman tersebut.");
            if ($currentRole === 'siswa') {
                Response::redirect('/portal');
            } else {
                Response::redirect('/dashboard');
            }
        }
    }

    public static function canVerify(): bool
    {
        if (self::isKetua()) {
            return true;
        }

        if (self::isBendahara()) {
            // Check setting treasurer_can_verify
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'treasurer_can_verify' LIMIT 1");
                $stmt->execute();
                $val = $stmt->fetchColumn();
                return $val === '1' || $val === 1 || $val === true;
            } catch (\Exception $e) {
                return true; // default fallback
            }
        }

        return false;
    }
}
