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
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
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
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
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

    public function getMedicalRecordsByPetAcrossVets(int $petId): array
    {
        $sql = "
            SELECT
                mr.*,
                mr.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            LEFT JOIN pets p ON p.id = a.pet_id
            LEFT JOIN users u ON u.id = a.pet_owner_id
            LEFT JOIN users v ON v.id = a.vet_id
            WHERE a.pet_id = :pet_id
            ORDER BY mr.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pet_id' => $petId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedicalRecordsByGuestPetAcrossVets(string $guestPetName, ?string $guestClientName = null): array
    {
        $sql = "
            SELECT
                mr.*,
                mr.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM medical_records mr
            JOIN appointments a ON a.id = mr.appointment_id
            LEFT JOIN pets p ON p.id = a.pet_id
            LEFT JOIN users u ON u.id = a.pet_owner_id
            LEFT JOIN users v ON v.id = a.vet_id
            WHERE a.pet_id IS NULL
              AND a.guest_pet_name = :guest_pet_name
        ";

        $params = ['guest_pet_name' => $guestPetName];
        if ($guestClientName !== null && $guestClientName !== '') {
            $sql .= " AND a.guest_client_name = :guest_client_name";
            $params['guest_client_name'] = $guestClientName;
        }

        $sql .= " ORDER BY mr.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
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
