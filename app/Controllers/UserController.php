<?php
/**
 * User Controller - Manajemen Akun Pengguna & Hak Akses
 * Akses khusus: Ketua Angkatan
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
use Exception;

class UserController
{
    public function index(): void
    {
        Auth::requireRole('ketua');

        $db = Database::getConnection();

        $role   = trim($_GET['role'] ?? 'all');
        $search = trim($_GET['q'] ?? '');

        $sql = "
            SELECT u.*, s.student_number, s.class_name
            FROM users u
            LEFT JOIN students s ON s.id = u.student_id
            WHERE 1=1
        ";
        $params = [];

        if ($role !== '' && $role !== 'all') {
            $sql .= " AND u.role = :role";
            $params[':role'] = $role;
        }

        if ($search !== '') {
            $sql .= " AND (u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY CASE u.role WHEN 'ketua' THEN 1 WHEN 'bendahara' THEN 2 ELSE 3 END, u.id ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        View::render('users/index', [
            'pageTitle' => 'Manajemen Akun Pengguna',
            'activeNav' => 'users',
            'users'     => $users,
            'role'      => $role,
            'search'    => $search
        ], 'main');
    }

    public function create(): void
    {
        Auth::requireRole('ketua');

        View::render('users/create', [
            'pageTitle' => 'Tambah Pengurus / Pengguna',
            'activeNav' => 'users'
        ], 'main');
    }

    public function store(): void
    {
        Auth::requireRole('ketua');
        CSRF::checkRequest();

        $username = trim($_POST['username'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $email    = trim($_POST['email'] ?? '') ?: null;
        $role     = trim($_POST['role'] ?? 'bendahara');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($fullName) || empty($password)) {
            Flash::setError("Username, Nama Lengkap, dan Kata Sandi wajib diisi.");
            Response::redirect('/users/create');
        }

        if (!in_array($role, ['ketua', 'bendahara'], true)) {
            Flash::setError("Role hanya dapat berupa Ketua atau Bendahara.");
            Response::redirect('/users/create');
        }

        if (strlen($password) < 6) {
            Flash::setError("Kata sandi minimal 6 karakter.");
            Response::redirect('/users/create');
        }

        $db = Database::getConnection();

        // Cek username
        $stmtCheck = $db->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmtCheck->execute([':u' => $username]);
        if ($stmtCheck->fetch()) {
            Flash::setError("Username '$username' sudah terdaftar.");
            Response::redirect('/users/create');
        }

        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("
                INSERT INTO users (username, password_hash, full_name, email, role, is_active, force_change_password, created_at)
                VALUES (:u, :p, :name, :email, :role, 1, 0, NOW())
            ");
            $stmt->execute([
                ':u'     => $username,
                ':p'     => $hash,
                ':name'  => $fullName,
                ':email' => $email,
                ':role'  => $role
            ]);
            $userId = (int)$db->lastInsertId();

            Logger::log('USER_CREATE', 'user', (string)$userId, "Akun baru dibuat: $username ($role) oleh Ketua.");
            Flash::setSuccess("Akun pengurus $fullName ($role) berhasil dibuat.", "Pengguna Baru Dibuat");
            Response::redirect('/users');
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            Flash::setError("Gagal membuat akun: " . $e->getMessage(), "Gagal Buat Akun");
            Response::redirect('/users/create');
        }
    }

    public function resetPassword(int $id): void
    {
        Auth::requireRole('ketua');
        CSRF::checkRequest();

        $newPassword = trim($_POST['new_password'] ?? '');
        if (empty($newPassword) || strlen($newPassword) < 6) {
            Flash::setError("Kata sandi baru minimal 6 karakter.", "Validasi Kata Sandi");
            Response::redirect('/users');
        }

        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("SELECT username, full_name FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch();

            if (!$user) {
                Flash::setError("Pengguna tidak ditemukan.", "Pengguna Tidak Ada");
                Response::redirect('/users');
            }

            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmtUpdate = $db->prepare("UPDATE users SET password_hash = :hash, force_change_password = 1, updated_at = NOW() WHERE id = :id");
            $stmtUpdate->execute([':hash' => $hash, ':id' => $id]);

            Logger::log('USER_RESET_PASSWORD', 'user', (string)$id, "Reset password untuk pengguna {$user['username']} oleh Ketua.");
            Flash::setSuccess("Kata sandi untuk pengguna {$user['full_name']} ({$user['username']}) berhasil direset.", "Reset Kata Sandi Berhasil");
            Response::redirect('/users');
        } catch (Exception $e) {
            error_log("Error resetting password: " . $e->getMessage());
            Flash::setError("Gagal mereset kata sandi: " . $e->getMessage(), "Gagal Reset Kata Sandi");
            Response::redirect('/users');
        }
    }

    public function toggleStatus(int $id): void
    {
        Auth::requireRole('ketua');
        CSRF::checkRequest();

        if ($id === Auth::id()) {
            Flash::setError("Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.", "Tindakan Ditolak");
            Response::redirect('/users');
        }

        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("SELECT username, full_name, is_active FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch();

            if (!$user) {
                Flash::setError("Pengguna tidak ditemukan.", "Pengguna Tidak Ada");
                Response::redirect('/users');
            }

            $newActive = ((int)$user['is_active'] === 1) ? 0 : 1;
            $stmtUpdate = $db->prepare("UPDATE users SET is_active = :a, updated_at = NOW() WHERE id = :id");
            $stmtUpdate->execute([':a' => $newActive, ':id' => $id]);

            $statusStr = $newActive === 1 ? 'diaktifkan' : 'dinonaktifkan';
            Logger::log('USER_STATUS', 'user', (string)$id, "Akun {$user['username']} $statusStr oleh Ketua.");
            Flash::setSuccess("Akun {$user['full_name']} berhasil $statusStr.", "Status Akun Diperbarui");
            Response::redirect('/users');
        } catch (Exception $e) {
            error_log("Error toggling user status: " . $e->getMessage());
            Flash::setError("Gagal mengubah status akun: " . $e->getMessage(), "Gagal Mengubah Status");
            Response::redirect('/users');
        }
    }
}
