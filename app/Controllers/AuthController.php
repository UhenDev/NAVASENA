<?php
/**
 * Auth Controller - Handles Authentication, Password Resets, and Sessions
 * NAVASENA MANAGEMENT
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\CSRF;
use App\Helpers\Database;
use App\Helpers\Flash;
use App\Helpers\Logger;
use App\Helpers\Response;
use App\Helpers\View;
use PDO;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            if (Auth::isSiswa()) {
                Response::redirect('/portal');
            } else {
                Response::redirect('/dashboard');
            }
        }

        View::render('auth/login', [
            'pageTitle' => 'Masuk ke Sistem'
        ], 'none');
    }

    public function processLogin(): void
    {
        CSRF::checkRequest();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            Flash::setError("Username dan kata sandi wajib diisi.");
            Response::redirect('/login');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, s.status as student_status, s.full_name as student_name
            FROM users u
            LEFT JOIN students s ON s.id = u.student_id
            WHERE u.username = :username
            LIMIT 1
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            Logger::log('LOGIN_FAILED', 'user', null, "Percobaan login gagal untuk username: $username");
            Flash::setError("Kombinasi username atau kata sandi tidak ditemukan.");
            Response::redirect('/login');
        }

        if ((int)$user['is_active'] !== 1) {
            Flash::setError("Akun Anda telah dinonaktifkan. Silakan hubungi pengurus angkatan.");
            Response::redirect('/login');
        }

        // Jika akun siswa, pastikan data siswa juga aktif
        if ($user['role'] === 'siswa' && $user['student_status'] === 'nonaktif') {
            Flash::setError("Status data siswa Anda saat ini nonaktif. Silakan hubungi pengurus.");
            Response::redirect('/login');
        }

        // Verifikasi password menggunakan password_verify (Bcrypt)
        if (!password_verify($password, $user['password_hash'])) {
            Logger::log('LOGIN_FAILED', 'user', (string)$user['id'], "Password salah untuk username: $username");
            Flash::setError("Kombinasi username atau kata sandi salah.");
            Response::redirect('/login');
        }

        // Update waktu login terakhir
        $stmtUpdate = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmtUpdate->execute([':id' => $user['id']]);

        // Simpan sesi autentikasi
        Auth::login($user);

        Logger::log('LOGIN', 'user', (string)$user['id'], "Pengguna {$user['username']} ({$user['role']}) berhasil masuk.");

        // Jika ada kewajiban ganti password (misal akun default pertama kali)
        if ((int)$user['force_change_password'] === 1) {
            Flash::setInfo("Demi keamanan data, silakan buat kata sandi baru untuk akun Anda.");
            Response::redirect('/change-password');
        }

        Flash::setSuccess("Selamat datang kembali, " . $user['full_name'] . "!");

        if ($user['role'] === 'siswa') {
            Response::redirect('/portal');
        } else {
            Response::redirect('/dashboard');
        }
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Logger::log('LOGOUT', 'user', (string)Auth::id(), "Pengguna " . Auth::username() . " keluar.");
            Auth::logout();
        }
        Flash::setSuccess("Anda telah berhasil keluar dari sistem.");
        Response::redirect('/login');
    }

    public function showChangePassword(): void
    {
        Auth::requireLogin();

        View::render('auth/change_password', [
            'pageTitle' => 'Ganti Kata Sandi',
            'user'      => Auth::user()
        ], Auth::isSiswa() ? 'student' : 'main');
    }

    public function processChangePassword(): void
    {
        Auth::requireLogin();
        CSRF::checkRequest();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Flash::setError("Semua kolom kata sandi wajib diisi.");
            Response::redirect('/change-password');
        }

        if (strlen($newPassword) < 6) {
            Flash::setError("Kata sandi baru minimal 6 karakter.");
            Response::redirect('/change-password');
        }

        if ($newPassword !== $confirmPassword) {
            Flash::setError("Konfirmasi kata sandi baru tidak cocok.");
            Response::redirect('/change-password');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => Auth::id()]);
        $currentHash = $stmt->fetchColumn();

        if (!password_verify($currentPassword, $currentHash)) {
            Flash::setError("Kata sandi lama yang Anda masukkan tidak sesuai.");
            Response::redirect('/change-password');
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmtUpdate = $db->prepare("
            UPDATE users 
            SET password_hash = :hash, force_change_password = 0, updated_at = NOW() 
            WHERE id = :id
        ");
        $stmtUpdate->execute([
            ':hash' => $newHash,
            ':id'   => Auth::id()
        ]);

        $_SESSION['force_change_password'] = 0;

        Logger::log('PASSWORD_CHANGE', 'user', (string)Auth::id(), "Pengguna " . Auth::username() . " berhasil memperbarui kata sandi.");
        Flash::setSuccess("Kata sandi berhasil diperbarui.");

        if (Auth::isSiswa()) {
            Response::redirect('/portal');
        } else {
            Response::redirect('/dashboard');
        }
    }
}
