<?php
require_once __DIR__ . '/../BaseModel.php';

class VaccinationsModel extends BaseModel
{
    public function getVaccinationsForVet(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                vax.*,
                vax.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY vax.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAppointment(int $appointmentId, int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                vax.*,
                vax.appointment_id,
                p.name AS pet_name,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
            JOIN pets p        ON p.id = a.pet_id
            JOIN users u       ON u.id = a.pet_owner_id
            WHERE vax.appointment_id = :appointment_id
              AND a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY vax.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'appointment_id' => $appointmentId,
            'vet_id' => $vetId,
            'clinic_id' => $clinicId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addVaccination(
        int $appointmentId,
        int $vetId,
        int $clinicId,
        string $vaccine,
        ?string $nextDue
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

        // Optional: prevent duplicates (one vaccination per appointment)
        $dup = $this->pdo->prepare("SELECT id FROM vaccinations WHERE appointment_id = ? LIMIT 1");
        $dup->execute([$appointmentId]);
        if ($dup->fetch()) return false;

        // Normalize next_due
        $nextDue = ($nextDue === '' ? null : $nextDue);

        $sql = "
            INSERT INTO vaccinations (appointment_id, vaccine, next_due, created_at)
            VALUES (:appointment_id, :vaccine, :next_due, NOW())
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'appointment_id' => $appointmentId,
            'vaccine' => $vaccine,
            'next_due' => $nextDue
        ]);
    }
}
