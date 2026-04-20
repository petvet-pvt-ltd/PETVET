<?php
require_once __DIR__ . '/../BaseModel.php';

class AppointmentsModel extends BaseModel
{
    public function getUpcomingAppointmentsForVet(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                a.*,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.pet_owner_id = u.id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
              AND a.status = 'approved'
              AND a.appointment_date >= CURDATE()
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch current ongoing appointment for vet or null if none exists
    public function getOngoingAppointmentForVet(int $vetId, int $clinicId): ?array
    {
        $sql = "
            SELECT 
                a.*,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.pet_owner_id = u.id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
              AND a.status = 'ongoing'
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Fetch completed appointments for vet sorted by most recent first
    public function getCompletedAppointments(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                a.*,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.pet_owner_id = u.id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
              AND a.status = 'completed'
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch cancelled appointments for vet sorted by most recent first
    public function getCancelledAppointments(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                a.*,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.pet_owner_id = u.id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
              AND a.status = 'cancelled'
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all appointments for vet in all statuses sorted by most recent first
    public function getAllAppointmentsForVet(int $vetId, int $clinicId): array
    {
        $sql = "
            SELECT 
                a.*,
                COALESCE(p.name, a.guest_pet_name) AS pet_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
                a.guest_phone AS guest_phone
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.pet_owner_id = u.id
            WHERE a.vet_id = :vet_id
              AND a.clinic_id = :clinic_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update appointment status with validation of allowed transitions
    public function updateAppointmentStatus(int $appointmentId, string $newStatus, int $vetId, int $clinicId): bool
    {
        $allowedNew = ['ongoing', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedNew, true)) return false;

        // Find current status & ownership
        $stmt = $this->pdo->prepare("
            SELECT status
            FROM appointments
            WHERE id = :id
              AND vet_id = :vet_id
              AND clinic_id = :clinic_id
            LIMIT 1
        ");
        $stmt->execute([
            'id' => $appointmentId,
            'vet_id' => $vetId,
            'clinic_id' => $clinicId
        ]);

        $current = $stmt->fetchColumn();
        if (!$current) return false;

        $valid = false;
        if ($newStatus === 'ongoing' && $current === 'approved') $valid = true;
        if ($newStatus === 'completed' && $current === 'ongoing') $valid = true;
        if ($newStatus === 'cancelled' && in_array($current, ['approved', 'ongoing'], true)) $valid = true;

        if (!$valid) return false;

        $stmt = $this->pdo->prepare("
            UPDATE appointments
            SET status = :status, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute([
            'status' => $newStatus,
            'id' => $appointmentId
        ]);
    }
}
