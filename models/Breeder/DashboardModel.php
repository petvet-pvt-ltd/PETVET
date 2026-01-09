<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

class BreederDashboardModel extends BaseModel {
    protected $conn;
    
    public function __construct() {
        parent::__construct();
        global $conn;
        $this->conn = $conn;
    }
    
    public function getStats($breederId) {
        $stats = [
            'pending_requests' => 0,
            'approved_requests' => 0,
            'total_breedings' => 0,
            'active_pets' => 0
        ];
        
        // Get pending requests count
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM breeding_requests WHERE breeder_id = ? AND status = 'pending'");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['pending_requests'] = $result['count'];
        
        // Get approved requests count
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM breeding_requests WHERE breeder_id = ? AND status = 'approved'");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['approved_requests'] = $result['count'];
        
        // Get total completed breedings
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM breeding_requests WHERE breeder_id = ? AND status = 'completed'");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['total_breedings'] = $result['count'];
        
        // Get active pets count
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM breeder_pets WHERE breeder_id = ? AND is_active = 1");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['active_pets'] = $result['count'];
        
        return $stats;
    }

    public function getPendingRequests($breederId, $limit = null) {
        $query = "
            SELECT 
                br.*,
                u.first_name as owner_fname,
                u.last_name as owner_lname,
                u.phone as phone,
                '' as phone_2,
                u.email
            FROM breeding_requests br
            JOIN users u ON br.owner_id = u.id
            WHERE br.breeder_id = ? AND br.status = 'pending'
            ORDER BY br.requested_date DESC
        ";
        
        if ($limit) {
            $query .= " LIMIT ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limit) {
            $stmt->bind_param("ii", $breederId, $limit);
        } else {
            $stmt->bind_param("i", $breederId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $row['owner_name'] = trim($row['owner_fname'] . ' ' . $row['owner_lname']);
            $row['pet_name'] = $row['owner_pet_name'];
            $row['breed'] = $row['owner_pet_breed'];
            $row['pet_breed'] = $row['owner_pet_breed'];
            $row['gender'] = $row['owner_pet_gender'];
            $requests[] = $row;
        }
        
        return $requests;
    }

    public function getApprovedRequests($breederId, $limit = null) {
        $query = "
            SELECT 
                br.*,
                u.first_name as owner_fname,
                u.last_name as owner_lname,
                u.phone as phone,
                '' as phone_2,
                u.email,
                bp.name as breeder_pet_name,
                bp.breed as breeder_pet_breed
            FROM breeding_requests br
            JOIN users u ON br.owner_id = u.id
            LEFT JOIN breeder_pets bp ON br.breeder_pet_id = bp.id
            WHERE br.breeder_id = ? AND br.status = 'approved'
            ORDER BY br.breeding_date ASC
        ";
        
        if ($limit) {
            $query .= " LIMIT ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limit) {
            $stmt->bind_param("ii", $breederId, $limit);
        } else {
            $stmt->bind_param("i", $breederId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $row['owner_name'] = trim($row['owner_fname'] . ' ' . $row['owner_lname']);
            $row['pet_name'] = $row['owner_pet_name'];
            $row['breed'] = $row['owner_pet_breed'];
            $row['pet_breed'] = $row['owner_pet_breed'];
            $row['gender'] = $row['owner_pet_gender'];
            $requests[] = $row;
        }
        
        return $requests;
    }

    public function getCompletedRequests($breederId) {
        $stmt = $this->conn->prepare("
            SELECT 
                br.*,
                u.first_name as owner_fname,
                u.last_name as owner_lname,
                u.phone as phone,
                '' as phone_2,
                u.email,
                bp.name as breeder_pet_name,
                bp.breed as breeder_pet_breed
            FROM breeding_requests br
            JOIN users u ON br.owner_id = u.id
            LEFT JOIN breeder_pets bp ON br.breeder_pet_id = bp.id
            WHERE br.breeder_id = ? AND br.status = 'completed'
            ORDER BY br.completed_date DESC
        ");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $row['owner_name'] = trim($row['owner_fname'] . ' ' . $row['owner_lname']);
            $row['pet_name'] = $row['owner_pet_name'];
            $row['breed'] = $row['owner_pet_breed'];
            $row['pet_breed'] = $row['owner_pet_breed'];
            $row['gender'] = $row['owner_pet_gender'];
            $row['completion_date'] = $row['completed_date'];
            $requests[] = $row;
        }
        
        return $requests;
    }

    public function getUpcomingBreedingDates($breederId, $limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT 
                br.id,
                br.breeding_date,
                bp.name as breeder_pet_name,
                br.owner_pet_name as customer_pet_name,
                br.owner_pet_breed as breed,
                u.first_name as owner_fname,
                u.last_name as owner_lname
            FROM breeding_requests br
            JOIN users u ON br.owner_id = u.id
            LEFT JOIN breeder_pets bp ON br.breeder_pet_id = bp.id
            WHERE br.breeder_id = ? 
                AND br.status = 'approved' 
                AND br.breeding_date >= CURDATE()
            ORDER BY br.breeding_date ASC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $breederId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $dates = [];
        while ($row = $result->fetch_assoc()) {
            $row['owner_name'] = trim($row['owner_fname'] . ' ' . $row['owner_lname']);
            unset($row['owner_fname'], $row['owner_lname']);
            $dates[] = $row;
        }
        
        return $dates;
    }
}
