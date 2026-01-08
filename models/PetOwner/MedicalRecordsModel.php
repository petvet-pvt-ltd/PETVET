<?php
// models/PetOwner/MedicalRecordsModel.php
require_once __DIR__ . '/../BaseModel.php';

class MedicalRecordsModel extends BaseModel {

    /** Get pet info by ID with owner verification */
    public function getPetById(int $petId, int $ownerId): ?array {
        try {
            $sql = "SELECT p.*, 
                           TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age
                    FROM pets p
                    WHERE p.id = :pet_id AND p.user_id = :user_id
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['pet_id' => $petId, 'user_id' => $ownerId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("MedicalRecordsModel::getPetById Error: " . $e->getMessage());
            return null;
        }
    }

    /** Get all appointments (clinic visits) for a pet */
    public function getClinicVisitsByPetId(int $petId): array {
        try {
            $sql = "SELECT 
                        a.*,
                        a.appointment_date,
                        a.appointment_time,
                        a.appointment_type,
                        a.status,
                        a.symptoms,
                        CONCAT(uv.first_name, ' ', uv.last_name) AS vet_name,
                        c.clinic_name,
                        mr.diagnosis,
                        mr.treatment
                    FROM appointments a
                    LEFT JOIN users uv ON uv.id = a.vet_id
                    LEFT JOIN clinics c ON c.id = a.clinic_id
                    LEFT JOIN medical_records mr ON mr.appointment_id = a.id
                    WHERE a.pet_id = :pet_id
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['pet_id' => $petId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("getClinicVisitsByPetId: Pet $petId returned " . count($result) . " visits");
            return $result;
        } catch (Exception $e) {
            error_log("MedicalRecordsModel::getClinicVisitsByPetId Error: " . $e->getMessage());
            return [];
        }
    }

    /** Get all medical records for a pet */
    public function getMedicalRecordsByPetId(int $petId): array {
        try {
            $sql = "SELECT 
                        mr.*,
                        a.appointment_date AS date,
                        p.name AS pet_name,
                        CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
                        CONCAT(uv.first_name, ' ', uv.last_name) AS vet_name
                    FROM medical_records mr
                    JOIN appointments a ON a.id = mr.appointment_id
                    JOIN pets p ON p.id = a.pet_id
                    JOIN users u ON u.id = p.user_id
                    LEFT JOIN users uv ON uv.id = a.vet_id
                    WHERE a.pet_id = :pet_id
                    ORDER BY mr.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['pet_id' => $petId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("getMedicalRecordsByPetId: Pet $petId returned " . count($result) . " records");
            return $result;
        } catch (Exception $e) {
            error_log("MedicalRecordsModel::getMedicalRecordsByPetId Error: " . $e->getMessage());
            return [];
        }
    }

    /** Get all vaccinations for a pet */
    public function getVaccinationsByPetId(int $petId): array {
        try {
            $sql = "SELECT 
                        vax.*,
                        a.appointment_date AS date,
                        p.name AS pet_name,
                        CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
                        CONCAT(uv.first_name, ' ', uv.last_name) AS vet_name
                    FROM vaccinations vax
                    JOIN appointments a ON a.id = vax.appointment_id
                    JOIN pets p ON p.id = a.pet_id
                    JOIN users u ON u.id = p.user_id
                    LEFT JOIN users uv ON uv.id = a.vet_id
                    WHERE a.pet_id = :pet_id
                    ORDER BY vax.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['pet_id' => $petId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("getVaccinationsByPetId: Pet $petId returned " . count($result) . " vaccinations");
            return $result;
        } catch (Exception $e) {
            error_log("MedicalRecordsModel::getVaccinationsByPetId Error: " . $e->getMessage());
            return [];
        }
    }

    /** Get all prescriptions for a pet */
    public function getPrescriptionsByPetId(int $petId): array {
        try {
            $sql = "SELECT 
                        pr.*,
                        a.appointment_date AS date,
                        p.name AS pet_name,
                        CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
                        CONCAT(uv.first_name, ' ', uv.last_name) AS vet_name
                    FROM prescriptions pr
                    JOIN appointments a ON a.id = pr.appointment_id
                    JOIN pets p ON p.id = a.pet_id
                    JOIN users u ON u.id = p.user_id
                    LEFT JOIN users uv ON uv.id = a.vet_id
                    WHERE a.pet_id = :pet_id
                    ORDER BY pr.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['pet_id' => $petId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("getPrescriptionsByPetId: Pet $petId returned " . count($result) . " prescriptions");
            return $result;
        } catch (Exception $e) {
            error_log("MedicalRecordsModel::getPrescriptionsByPetId Error: " . $e->getMessage());
            return [];
        }
    }

    /** Returns a full record set for the medical-records page */
    public function getFullMedicalRecordByPetId(int $petId, int $ownerId): ?array {
        $pet = $this->getPetById($petId, $ownerId);
        if (!$pet) {
            error_log("MedicalRecordsModel: Pet not found - Pet ID: $petId, Owner ID: $ownerId");
            return null;
        }

        $clinic_visits = $this->getClinicVisitsByPetId($petId);
        $medical_records = $this->getMedicalRecordsByPetId($petId);
        $vaccinations = $this->getVaccinationsByPetId($petId);
        $prescriptions = $this->getPrescriptionsByPetId($petId);

        // DEBUG LOG
        error_log("MedicalRecordsModel: Pet ID $petId - Clinic Visits: " . count($clinic_visits) . ", Medical Records: " . count($medical_records) . ", Vaccinations: " . count($vaccinations) . ", Prescriptions: " . count($prescriptions));

        return [
            'pet'               => $pet,
            'clinic_visits'     => $clinic_visits,
            'medical_records'   => $medical_records,
            'vaccinations'      => $vaccinations,
            'prescriptions'     => $prescriptions,
        ];
    }
}
