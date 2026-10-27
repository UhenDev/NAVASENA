<?php
/**
 * Allocation Service - Handles Installment Allocations per Stage
 * NAVASENA MANAGEMENT
 */

namespace App\Services;

use App\Helpers\Database;
use PDO;
use Exception;

class AllocationService
{
    /**
     * Mengambil status progres ketiga tahap cicilan untuk seorang siswa
     */
    public static function getStudentStagesStatus(int $studentId): array
    {
        $db = Database::getConnection();

        $stmtStages = $db->query("
            SELECT id, stage_number, stage_name, target_amount, due_date, description 
            FROM installments 
            WHERE is_active = 1 
            ORDER BY stage_number ASC
        ");
        $stages = $stmtStages->fetchAll();

        $result = [];
        foreach ($stages as $stage) {
            $target = (float)$stage['target_amount'];

            // Hitung hanya pembayaran yang sudah diverifikasi untuk siswa ini
            $stmtPaid = $db->prepare("
                SELECT COALESCE(SUM(pa.allocated_amount), 0)
                FROM payment_allocations pa
                JOIN payments p ON p.id = pa.payment_id
                WHERE p.student_id = :student_id 
                  AND pa.installment_id = :installment_id 
                  AND p.status = 'terverifikasi'
            ");
            $stmtPaid->execute([
                ':student_id'      => $studentId,
                ':installment_id'  => $stage['id']
            ]);
            $paid = (float)$stmtPaid->fetchColumn();

            $remaining = max(0.00, $target - $paid);
            if ($paid >= $target) {
                $status = 'lunas';
            } elseif ($paid > 0) {
                $status = 'sebagian';
            } else {
                $status = 'belum_bayar';
            }

            $percentage = $target > 0 ? round(($paid / $target) * 100, 1) : 0.0;

            $result[] = [
                'installment_id' => (int)$stage['id'],
                'stage_number'   => (int)$stage['stage_number'],
                'stage_name'     => $stage['stage_name'],
                'target_amount'  => $target,
                'paid_amount'    => $paid,
                'remaining_amount' => $remaining,
                'status'         => $status,
                'percentage'     => $percentage,
                'due_date'       => $stage['due_date'],
                'description'    => $stage['description']
            ];
        }

        return $result;
    }

    /**
     * Menghitung alokasi otomatis berurutan dari tahap tertua yang belum lunas
     * (DP Pertama -> Cicilan Kedua -> Pelunasan)
     */
    public static function computeAutoAllocation(int $studentId, float $paymentAmount): array
    {
        $stages = self::getStudentStagesStatus($studentId);
        $allocations = [];
        $remainingToAllocate = $paymentAmount;

        foreach ($stages as $stage) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            $stageRemaining = $stage['remaining_amount'];
            if ($stageRemaining > 0) {
                $alloc = min($remainingToAllocate, $stageRemaining);
                $allocations[$stage['installment_id']] = $alloc;
                $remainingToAllocate -= $alloc;
            }
        }

        // Jika masih ada sisa pembayaran (misal overpayment), masukkan ke tahap terakhir
        if ($remainingToAllocate > 0 && !empty($stages)) {
            $lastStageId = end($stages)['installment_id'];
            if (isset($allocations[$lastStageId])) {
                $allocations[$lastStageId] += $remainingToAllocate;
            } else {
                $allocations[$lastStageId] = $remainingToAllocate;
            }
        }

        return $allocations;
    }

    /**
     * Menyimpan alokasi pembayaran ke database
     */
    public static function saveAllocations(int $paymentId, array $allocations): void
    {
        $db = Database::getConnection();

        // Hapus alokasi lama jika ada (mencegah duplikasi)
        $stmtDelete = $db->prepare("DELETE FROM payment_allocations WHERE payment_id = :payment_id");
        $stmtDelete->execute([':payment_id' => $paymentId]);

        $stmtInsert = $db->prepare("
            INSERT INTO payment_allocations (payment_id, installment_id, allocated_amount)
            VALUES (:payment_id, :installment_id, :allocated_amount)
        ");

        foreach ($allocations as $installmentId => $amount) {
            if ((float)$amount > 0) {
                $stmtInsert->execute([
                    ':payment_id'        => $paymentId,
                    ':installment_id'    => (int)$installmentId,
                    ':allocated_amount'  => (float)$amount
                ]);
            }
        }
    }

    /**
     * Mengambil alokasi untuk kuitansi / detail pembayaran
     */
    public static function getAllocationsForPayment(int $paymentId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT pa.*, i.stage_number, i.stage_name, i.target_amount
            FROM payment_allocations pa
            JOIN installments i ON i.id = pa.installment_id
            WHERE pa.payment_id = :payment_id
            ORDER BY i.stage_number ASC
        ");
        $stmt->execute([':payment_id' => $paymentId]);
        return $stmt->fetchAll();
    }
}
