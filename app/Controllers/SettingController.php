<?php
/**
 * Setting Controller - Konfigurasi Aplikasi & Jadwal Cicilan
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

class SettingController
{
    public function index(): void
    {
        Auth::requireRole('ketua');

        $db = Database::getConnection();

        // Ambil semua settings
        $stmtSettings = $db->query("SELECT setting_key, setting_value, description FROM settings");
        $settingsRaw = $stmtSettings->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }

        // Ambil tahapan cicilan
        $stmtStages = $db->query("SELECT * FROM installments ORDER BY stage_number ASC");
        $stages = $stmtStages->fetchAll();

        View::render('settings/index', [
            'pageTitle' => 'Pengaturan Sistem & Jadwal Cicilan',
            'activeNav' => 'settings',
            'settings'  => $settings,
            'stages'    => $stages
        ], 'main');
    }

    public function update(): void
    {
        Auth::requireRole('ketua');
        CSRF::checkRequest();

        $db = Database::getConnection();

        Database::beginTransaction();
        try {
            // 1. Simpan pengaturan umum
            $treasurerCanVerify = isset($_POST['treasurer_can_verify']) && $_POST['treasurer_can_verify'] == '1' ? '1' : '0';
            $allowOverpayment   = isset($_POST['allow_overpayment']) && $_POST['allow_overpayment'] == '1' ? '1' : '0';
            $announcement       = trim($_POST['announcement'] ?? '');
            $academicYear       = trim($_POST['academic_year'] ?? '2026/2027');

            $stmtSet = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            
            $stmtSet->execute([':k' => 'treasurer_can_verify', ':v' => $treasurerCanVerify]);
            $stmtSet->execute([':k' => 'allow_overpayment', ':v' => $allowOverpayment]);
            $stmtSet->execute([':k' => 'announcement', ':v' => $announcement]);
            $stmtSet->execute([':k' => 'academic_year', ':v' => $academicYear]);

            // 2. Simpan jadwal dan nominal setiap tahap cicilan
            if (isset($_POST['stages']) && is_array($_POST['stages'])) {
                $stmtStageUpdate = $db->prepare("
                    UPDATE installments 
                    SET stage_name = :name, target_amount = :amount, due_date = :due, description = :desc, updated_at = NOW()
                    WHERE id = :id
                ");

                foreach ($_POST['stages'] as $stageId => $data) {
                    $amount = (float)str_replace(['.', ','], ['', '.'], $data['target_amount'] ?? '0');
                    $dueDate = !empty($data['due_date']) ? $data['due_date'] : null;
                    $desc = trim($data['description'] ?? '');
                    $name = trim($data['stage_name'] ?? '');

                    $stmtStageUpdate->execute([
                        ':name'   => $name,
                        ':amount' => $amount,
                        ':due'    => $dueDate,
                        ':desc'   => $desc,
                        ':id'     => (int)$stageId
                    ]);
                }
            }

            Database::commit();

            Logger::log('SETTING_CHANGE', 'settings', null, "Ketua Angkatan memperbarui pengaturan sistem dan jadwal cicilan.");
            Flash::setSuccess("Pengaturan aplikasi dan jadwal cicilan berhasil diperbarui.", "Pengaturan Disimpan");
            Response::redirect('/settings');
        } catch (Exception $e) {
            Database::rollBack();
            error_log("Error updating settings: " . $e->getMessage());
            Flash::setError("Gagal memperbarui pengaturan: " . $e->getMessage(), "Gagal Simpan Pengaturan");
            Response::redirect('/settings');
        }
    }
}
