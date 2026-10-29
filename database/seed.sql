-- ==============================================================================
-- DATABASE SEED: NAVASENA MANAGEMENT
-- Initial configuration, installment stages, and master accounts.
-- Note: NO fake/dummy student data!
-- ==============================================================================

-- 1. Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('app_name', 'NAVASENA MANAGEMENT', 'Nama Sistem Informasi Pengelolaan Keuangan Angkatan'),
('academic_year', '2026/2027', 'Tahun Ajaran / Angkatan'),
('total_dues_per_student', '250000.00', 'Total kewajiban iuran per siswa'),
('album_fee', '200000.00', 'Porsi iuran untuk album fisik'),
('graduation_fee', '50000.00', 'Porsi iuran untuk tambahan graduation'),
('target_students_count', '492', 'Estimasi jumlah siswa angkatan'),
('treasurer_can_verify', '1', 'Kewenangan bendahara memverifikasi pembayaran (1 = Ya, 0 = Hanya Ketua)'),
('allow_overpayment', '0', 'Izinkan pembayaran melebihi sisa tagihan (0 = Tidak, 1 = Ya)'),
('announcement', 'Selamat datang di NAVASENA MANAGEMENT. Pastikan melakukan pembayaran sesuai jadwal cicilan resmi angkatan.', 'Pengumuman resmi untuk portal siswa')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- 2. Default Installment Stages (3 Tahap)
INSERT INTO `installments` (`stage_number`, `stage_name`, `target_amount`, `due_date`, `description`, `is_active`) VALUES
(1, 'DP Pertama', 100000.00, '2026-12-01', 'DP Pertama sebesar Rp100.000 jatuh tempo 1 Desember 2026', 1),
(2, 'Cicilan Kedua', 100000.00, '2027-04-30', 'Cicilan kedua sebesar Rp100.000 (estimasi April/Mei 2027)', 1),
(3, 'Pelunasan', 50000.00, '2027-06-15', 'Pelunasan sebesar Rp50.000 menjelang kelulusan 2027', 1)
ON DUPLICATE KEY UPDATE 
    `stage_name` = VALUES(`stage_name`),
    `target_amount` = VALUES(`target_amount`),
    `due_date` = VALUES(`due_date`),
    `description` = VALUES(`description`);

-- 3. Master Accounts (Ketua & Bendahara)
-- Passwords:
-- Ketua (uhen): hendrikwildan23
-- Bendahara: password123
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `email`, `role`, `is_active`, `force_change_password`) VALUES
('uhen', '$2y$10$sPZK/v1EGCH7uOknrJiCFeyW7Uv0ygc.CXs4Kk1UNq1YfDb.bObHa', 'Ketua Angkatan Navasena', 'ketua@navasena.internal', 'ketua', 1, 0),
('bendahara', '$2y$10$vIppzTdeSYpT.1G9DmtoO.dInJ.Ao9cLWCUF9Yw704kpqz24OvJjy', 'Bendahara Utama Navasena', 'bendahara@navasena.internal', 'bendahara', 1, 0)
ON DUPLICATE KEY UPDATE 
    `password_hash` = VALUES(`password_hash`),
    `full_name` = VALUES(`full_name`),
    `role` = VALUES(`role`),
    `is_active` = 1,
    `force_change_password` = 0;
