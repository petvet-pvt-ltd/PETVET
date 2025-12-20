<?php
require_once __DIR__ . '/../BaseModel.php';

class PrescriptionsModel extends BaseModel
{
    public function getPrescriptionsForVet(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                pr.*,
                pr.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY pr.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAppointment(int $appointmentId, int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                pr.*,
                pr.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE pr.appointment_id = :appointment_id
              AND a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY pr.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'appointment_id' => $appointmentId,
            'vet_id' => $vetId,
            'clinic_id' => $clinicId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPrescription(
        int $appointmentId,
        int $vetId,
        int $clinicId,
        string $medication,
        string $dosage,
        ?string $notes
    ): bool {
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

        // Optional: prevent duplicates (one prescription per appointment)
        $dup = $this->pdo->prepare("SELECT id FROM prescriptions WHERE appointment_id = ? LIMIT 1");
        $dup->execute([$appointmentId]);
        if ($dup->fetch()) return false;

        $sql = "
            INSERT INTO prescriptions (appointment_id, medication, dosage, notes, created_at)
            VALUES (:appointment_id, :medication, :dosage, :notes, NOW())
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'appointment_id' => $appointmentId,
            'medication' => $medication,
            'dosage' => $dosage,
            'notes' => ($notes === '' ? null : $notes)
        ]);
    }
}
