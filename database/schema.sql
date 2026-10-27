-- ==============================================================================
-- DATABASE SCHEMA: NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan
-- Standard: MySQL 8.0+ / MariaDB 10.4+
-- Target: Rp 123.000.000 (492 siswa x Rp 250.000)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `navasena_keuangan` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `navasena_keuangan`;

-- 1. Tabel Settings (Konfigurasi Aplikasi & Iuran)
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Installments (Jadwal & Tahapan Cicilan)
CREATE TABLE IF NOT EXISTS `installments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `stage_number` INT NOT NULL UNIQUE,
    `stage_name` VARCHAR(100) NOT NULL,
    `target_amount` DECIMAL(12,2) NOT NULL,
    `due_date` DATE NULL,
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Students (Data Siswa Angkatan)
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_number` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `class_name` VARCHAR(50) NOT NULL,
    `gender` ENUM('L', 'P') NULL,
    `phone` VARCHAR(25) NULL,
    `email` VARCHAR(100) NULL,
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_student_status` (`status`),
    INDEX `idx_student_class` (`class_name`),
    INDEX `idx_student_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Users (Akun Pengguna: Ketua, Bendahara, Siswa)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NULL,
    `phone` VARCHAR(25) NULL,
    `role` ENUM('ketua', 'bendahara', 'siswa') NOT NULL,
    `student_id` INT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `force_change_password` TINYINT(1) NOT NULL DEFAULT 0,
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Dues (Kewajiban Iuran Per Siswa)
-- Total default: Rp 250.000 (Album fisik Rp 200.000 + Graduation Rp 50.000)
CREATE TABLE IF NOT EXISTS `dues` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL UNIQUE,
    `total_obligation` DECIMAL(12,2) NOT NULL DEFAULT 250000.00,
    `album_portion` DECIMAL(12,2) NOT NULL DEFAULT 200000.00,
    `graduation_portion` DECIMAL(12,2) NOT NULL DEFAULT 50000.00,
    `total_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `remaining_balance` DECIMAL(12,2) NOT NULL DEFAULT 250000.00,
    `payment_status` ENUM('belum_bayar', 'sebagian', 'lunas') NOT NULL DEFAULT 'belum_bayar',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_dues_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    INDEX `idx_dues_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Payments (Transaksi Pembayaran Iuran Siswa)
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `payment_method` ENUM('tunai', 'transfer') NOT NULL,
    `payment_date` DATE NOT NULL,
    `reference_note` VARCHAR(255) NULL,
    `proof_file` VARCHAR(255) NULL,
    `status` ENUM('menunggu_verifikasi', 'terverifikasi', 'ditolak', 'dibatalkan') NOT NULL DEFAULT 'menunggu_verifikasi',
    `recorded_by` INT NOT NULL,
    `verified_by` INT NULL,
    `verified_at` DATETIME NULL,
    `rejection_reason` TEXT NULL,
    `cancelled_by` INT NULL,
    `cancelled_at` DATETIME NULL,
    `cancellation_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_payments_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_payments_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_payments_cancelled_by` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`),
    INDEX `idx_payments_status` (`status`),
    INDEX `idx_payments_date` (`payment_date`),
    INDEX `idx_payments_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel Payment Allocations (Alokasi Pembayaran ke Tahap Cicilan)
CREATE TABLE IF NOT EXISTS `payment_allocations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payment_id` INT NOT NULL,
    `installment_id` INT NOT NULL,
    `allocated_amount` DECIMAL(12,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_allocations_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_allocations_installment` FOREIGN KEY (`installment_id`) REFERENCES `installments` (`id`),
    INDEX `idx_allocations_payment` (`payment_id`),
    INDEX `idx_allocations_installment` (`installment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Cash Transactions (Buku Kas Masuk dan Keluar Angkatan)
CREATE TABLE IF NOT EXISTS `cash_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transaction_type` ENUM('masuk', 'keluar') NOT NULL,
    `category` VARCHAR(60) NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `transaction_date` DATE NOT NULL,
    `description` TEXT NOT NULL,
    `payment_id` INT NULL,
    `recorded_by` INT NOT NULL,
    `is_cancelled` TINYINT(1) NOT NULL DEFAULT 0,
    `cancellation_note` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cash_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_cash_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
    INDEX `idx_cash_type` (`transaction_type`),
    INDEX `idx_cash_date` (`transaction_date`),
    INDEX `idx_cash_category` (`category`),
    INDEX `idx_cash_is_cancelled` (`is_cancelled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Audit Logs (Jejak Rekam Aktivitas Keuangan & Sistem)
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(60) NOT NULL,
    `target_type` VARCHAR(50) NULL,
    `target_id` VARCHAR(50) NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
