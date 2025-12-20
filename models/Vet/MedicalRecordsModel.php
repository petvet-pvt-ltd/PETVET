<?php
require_once __DIR__ . '/../BaseModel.php';

class MedicalRecordsModel extends BaseModel
{
    public function getMedicalRecordsForVet(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                mr.*,
                mr.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY mr.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecordsByAppointment(int $appointmentId, int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                mr.*,
                mr.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE mr.appointment_id = :appointment_id
              AND a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY mr.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'appointment_id' => $appointmentId,
            'vet_id' => $vetId,
            'clinic_id' => $clinicId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMedicalRecord(
        int $appointmentId,
        int $vetId,
        int $clinicId,
        string $symptoms,
        string $diagnosis,
        string $treatment
    ): bool {
        // Appointment must belong to this vet+clinic and be ongoing/completed
        $chk = $this->pdo->prepare("
            SELECT id
            FROM appointments
            WHERE id = :id
              AND vet_id = :vet_id
              AND clinic_id = :clinic_id
              AND status IN ('ongoing','completed')
            LIMIT 1
        ");
        $chk->execute([
            'id' => $appointmentId,
            'vet_id' => $vetId,
            'clinic_id' => $clinicId
        ]);
        if (!$chk->fetch()) return false;

        // Optional: prevent duplicates (one record per appointment)
        $dup = $this->pdo->prepare("SELECT id FROM medical_records WHERE appointment_id = ? LIMIT 1");
        $dup->execute([$appointmentId]);
        if ($dup->fetch()) return false;

        $sql = "
            INSERT INTO medical_records (appointment_id, symptoms, diagnosis, treatment, created_at)
            VALUES (:appointment_id, :symptoms, :diagnosis, :treatment, NOW())
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'appointment_id' => $appointmentId,
            'symptoms' => $symptoms,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment
        ]);
    }
}
