<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

/**
 * StaffModel: manages non-veterinary clinic staff (assistants, front desk, support)
 * Uses database for persistence with CRUD operations
 */
class StaffModel extends BaseModel {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = db();
    }

    /**
     * Get all staff members for a clinic
     * @param int $clinicId - Clinic ID (defaults to 1 for now)
     * @return array - Array of staff members
     */
    public function all(int $clinicId = 1): array {
        try {
            $sql = "SELECT * FROM clinic_staff WHERE clinic_id = ? ORDER BY role, name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$clinicId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all staff error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single staff member by ID
     * @param int $id - Staff member ID
     * @param int $clinicId - Clinic ID
     * @return array|null - Staff member data or null
     */
    public function findById(int $id, int $clinicId = 1): ?array {
        try {
            $sql = "SELECT * FROM clinic_staff WHERE id = ? AND clinic_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $clinicId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Get staff by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Add a new staff member
     * @param array $data - Staff member data
     * @param int $clinicId - Clinic ID
     * @return int|false - New staff ID or false on failure
     */
    public function add(array $data, int $clinicId = 1): int|false {
        try {
            // Set default status
            $data['status'] = $data['status'] ?? 'Active';
            
            $sql = "INSERT INTO clinic_staff (clinic_id, name, role, email, phone, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $clinicId,
                $data['name'],
                $data['role'],
                $data['email'],
                $data['phone'],
                $data['status']
            ]);
            
            if ($result) {
                return (int)$this->db->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Add staff error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing staff member
     * @param int $id - Staff member ID
     * @param array $data - Updated data
     * @param int $clinicId - Clinic ID
     * @return bool - True on success, false on failure
     */
    public function update(int $id, array $data, int $clinicId = 1): bool {
        try {
            $sql = "UPDATE clinic_staff 
                    SET name = ?, role = ?, email = ?, phone = ?, status = ?
                    WHERE id = ? AND clinic_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['name'],
                $data['role'],
                $data['email'],
                $data['phone'],
                $data['status'],
                $id,
                $clinicId
            ]);
        } catch (PDOException $e) {
            error_log("Update staff error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a staff member
     * @param int $id - Staff member ID
     * @param int $clinicId - Clinic ID
     * @return bool - True on success, false on failure
     */
    public function delete(int $id, int $clinicId = 1): bool {
        try {
            $sql = "DELETE FROM clinic_staff WHERE id = ? AND clinic_id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id, $clinicId]);
        } catch (PDOException $e) {
            error_log("Delete staff error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update staff status
     * @param int $id - Staff member ID
     * @param string $status - New status (Active/Inactive)
     * @param int $clinicId - Clinic ID
     * @return bool - True on success
     */
    public function updateStatus(int $id, string $status, int $clinicId = 1): bool {
        try {
            $sql = "UPDATE clinic_staff SET status = ? WHERE id = ? AND clinic_id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$status, $id, $clinicId]);
        } catch (PDOException $e) {
            error_log("Update staff status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total staff count for a clinic
     * @param int $clinicId - Clinic ID
     * @return int - Total staff count
     */
    private function getStaffCount(int $clinicId): int {
        try {
            $sql = "SELECT COUNT(*) as count FROM clinic_staff WHERE clinic_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$clinicId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get staff count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if email already exists for another staff member
     * @param string $email - Email to check
     * @param int|null $excludeId - Staff ID to exclude (for updates)
     * @param int $clinicId - Clinic ID
     * @return bool - True if exists, false otherwise
     */
    public function emailExists(string $email, ?int $excludeId = null, int $clinicId = 1): bool {
        try {
            if ($excludeId) {
                $sql = "SELECT COUNT(*) as count FROM clinic_staff 
                        WHERE email = ? AND id != ? AND clinic_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$email, $excludeId, $clinicId]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM clinic_staff 
                        WHERE email = ? AND clinic_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$email, $clinicId]);
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Check email exists error: " . $e->getMessage());
            return false;
        }
    }
}
