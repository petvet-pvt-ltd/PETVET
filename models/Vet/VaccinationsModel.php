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
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY vax.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        $vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch vaccines for each vaccination
        foreach ($vaccinations as &$vaccination) {
            $itemSql = "SELECT vaccine, next_due FROM vaccination_items WHERE vaccination_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$vaccination['id']]);
            $vaccination['vaccines'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $vaccinations;
    }

    public function getByAppointment(int $appointmentId, int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                vax.*,
                vax.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
            LEFT JOIN pets p   ON p.id = a.pet_id
            LEFT JOIN users u  ON u.id = a.pet_owner_id
            LEFT JOIN users v  ON v.id = a.vet_id
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

    public function getVaccinationsByPetAcrossVets(int $petId): array
    {
        $sql = "
            SELECT
                vax.*,
                vax.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
            LEFT JOIN pets p ON p.id = a.pet_id
            LEFT JOIN users u ON u.id = a.pet_owner_id
            LEFT JOIN users v ON v.id = a.vet_id
            WHERE a.pet_id = :pet_id
            ORDER BY vax.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pet_id' => $petId]);
        $vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vaccinations as &$vaccination) {
            $itemSql = "SELECT vaccine, next_due FROM vaccination_items WHERE vaccination_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$vaccination['id']]);
            $vaccination['vaccines'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $vaccinations;
    }

    public function getVaccinationsByGuestPetAcrossVets(string $guestPetName, ?string $guestClientName = null): array
    {
        $sql = "
            SELECT
                vax.*,
                vax.appointment_id,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone,
                a.vet_id,
                CONCAT(v.first_name, ' ', v.last_name) AS vet_name
            FROM vaccinations vax
            JOIN appointments a ON a.id = vax.appointment_id
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

        $sql .= " ORDER BY vax.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vaccinations as &$vaccination) {
            $itemSql = "SELECT vaccine, next_due FROM vaccination_items WHERE vaccination_id = ? ORDER BY id";
            $itemStmt = $this->pdo->prepare($itemSql);
            $itemStmt->execute([$vaccination['id']]);
            $vaccination['vaccines'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $vaccinations;
    }
}
