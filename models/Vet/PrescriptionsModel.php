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
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY pr.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch medications for each prescription
        foreach ($prescriptions as &$prescription) {
            $itemSql = "SELECT medication, dosage FROM prescription_items WHERE prescription_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$prescription['id']]);
            $prescription['medications'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $prescriptions;
    }

    // Fetch prescriptions for specific appointment by appointment ID
    public function getByAppointment(int $appointmentId, int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                pr.*,
                pr.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
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

    // Create new prescription for appointment with medication, dosage, and optional notes
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

    // Fetch all prescriptions for a pet from all veterinarians with medications
    public function getPrescriptionsByPetAcrossVets(int $petId): array
    {
        $sql = "
            SELECT
                pr.*,
                pr.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
            LEFT JOIN pets p ON p.id = a.pet_id
            LEFT JOIN users u ON u.id = a.pet_owner_id
            LEFT JOIN users v ON v.id = a.vet_id
            WHERE a.pet_id = :pet_id
            ORDER BY pr.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pet_id' => $petId]);
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prescriptions as &$prescription) {
            $itemSql = "SELECT medication, dosage FROM prescription_items WHERE prescription_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$prescription['id']]);
            $prescription['medications'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $prescriptions;
    }

    // Fetch all prescriptions for guest pet by name and optional owner name
    public function getPrescriptionsByGuestPetAcrossVets(string $guestPetName, ?string $guestClientName = null): array
    {
        $sql = "
            SELECT
                pr.*,
                pr.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM prescriptions pr
            JOIN appointments a ON a.id = pr.appointment_id
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

        $sql .= " ORDER BY pr.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prescriptions as &$prescription) {
            $itemSql = "SELECT medication, dosage FROM prescription_items WHERE prescription_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$prescription['id']]);
            $prescription['medications'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $prescriptions;
    }
}
