<?php
require_once __DIR__ . '/../BaseModel.php';

class LostFoundModel extends BaseModel {
    
    /**
     * Validate all report form fields
     * Returns array with 'valid' boolean and 'errors' array
     */
    public function validateReportFields($type, $species, $name, $color, $location, $date, $time, $phone, $phone2, $email, $notes, $reward = null,$price = null) {
        $errors = [];
        
        // Validate type
        if (empty($type)) {
            $errors[] = 'Report type is required';
        } elseif (!in_array($type, ['lost', 'found'])) {
            $errors[] = 'Invalid type. Must be "lost" or "found"';
        }
        
        // Validate species (required)
        if (empty($species)) {
            $errors[] = 'Pet species is required';
        } elseif (strlen($species) > 50) {
            $errors[] = 'Pet species must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $species)) {
            $errors[] = 'Pet species must contain only letters and spaces';
        }
        
        // Validate name (required)
        if (empty($name)) {
            $errors[] = 'Pet name is required';
        } elseif (strlen($name) > 50) {
            $errors[] = 'Pet name must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9\s\-\.]+$/', $name)) {
            $errors[] = 'Pet name contains invalid characters';
        }
        
        // Validate color (required)
        if (empty($color)) {
            $errors[] = 'Pet color is required';
        } elseif (strlen($color) > 100) {
            $errors[] = 'Pet color description must not exceed 100 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9\s\-,\.]+$/', $color)) {
            $errors[] = 'Pet color contains invalid characters';
        }
        
        

        // Validate location (required)
        if (empty($location)) {
            $errors[] = 'Location is required';
        } elseif (strlen($location) < 5) {
            $errors[] = 'Location must be at least 5 characters';
        } elseif (strlen($location) > 200) {
            $errors[] = 'Location must not exceed 200 characters';
        }
        
        // Validate date (required)
        if (empty($date)) {
            $errors[] = 'Date is required';
        } else {
            // Check valid date format (YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = 'Date must be in YYYY-MM-DD format';
            } else {
                // Check if date is valid and not in future
                $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
                if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
                    $errors[] = 'Invalid date';
                } elseif ($dateObj > new \DateTime()) {
                    $errors[] = 'Date cannot be in the future';
                }
            }
        }

        
        
        // Validate time (optional but validate if provided)
        if (!empty($time)) {
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                $errors[] = 'Time must be in HH:MM format (24-hour)';
            }
        }
        
        // Validate phone (required)
        if (empty($phone)) {
            $errors[] = 'Phone number is required';
        } elseif (!$this->isValidPhoneNumber($phone)) {
            $errors[] = 'Phone number must be in format +94XXXXXXXXX (9 digits after +94) or 10 digits';
        }
        
        // Validate phone2 (optional but if provided must follow format)
        if (!empty($phone2)) {
            if (!$this->isValidPhoneNumber($phone2)) {
                $errors[] = 'Alternate phone number must be in format +94XXXXXXXXX (9 digits after +94) or 10 digits';
            }
        }
        
        // Validate email (optional but if provided must be valid)
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email address format';
            } elseif (strlen($email) > 100) {
                $errors[] = 'Email must not exceed 100 characters';
            }
        }
        
        // Validate notes (optional but max length)
        if (!empty($notes)) {
            if (strlen($notes) > 1000) {
                $errors[] = 'Notes must not exceed 1000 characters';
            }
        }
        
        // Validate reward (optional but if provided must be numeric and non-negative)
        if ($reward !== null && $reward !== '') {
            if (!is_numeric($reward) || $reward < 0) {
                $errors[] = 'Reward must be a positive number';
            } elseif ($reward > 9999999) {
                $errors[] = 'Reward amount is too large';
            }
        }
        
        if ($price !== null && $price !== '') {
            if (!is_numeric($price) || $price < 0) {
                $errors[] = 'Price must be a positive number';
            } elseif ($price > 9999999) {
                $errors[] = 'Price amount is too large';
            }
        }
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validate phone number format: +94 followed by exactly 9 digits or 10 digits
     */
    private function isValidPhoneNumber($phone) {
        // Check format: +94 followed by exactly 9 digits OR 10 digits
        return preg_match('/^(\d{10}|\+94\d{9})$/', $phone);
    }
    
    /**
     * Fetch all reports from database
     */
    public function getAllReports() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM LostFoundReport 
                ORDER BY date_reported DESC
            ");
            $stmt->execute();
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->formatReports($reports);
        } catch (PDOException $e) {
            error_log("Error fetching reports: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate days missing from report date to today
     */
    private function calculateDaysMissing($dateReported) {
        try {
            $today = new DateTime();
            $reported = new DateTime($dateReported);
            $interval = $today->diff($reported);
            return $interval->days;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Format database reports to match view expectations
     * Reads from individual columns (new schema) with fallback to JSON parsing (old schema)
     */
    private function formatReports($dbReports) {
        $formatted = [];
        
        foreach ($dbReports as $report) {
            // Try to use individual columns first, fallback to JSON parsing
            $photos = [];
            if (!empty($report['photos'])) {
                $decoded = json_decode($report['photos'], true);
                $photos = is_array($decoded) ? $decoded : [$report['photos']];
            }
            
            // Fallback to description JSON if columns are empty
            if (empty($photos) && !empty($report['description'])) {
                $description = json_decode($report['description'], true);
                $photos = $description['photos'] ?? [];
            }
            
            if (empty($photos)) {
                $photos = ['/PETVET/public/img/default-pet.jpg']; // Fallback image
            }
            
            // Calculate days missing
            $daysMissing = $this->calculateDaysMissing($report['date_reported']);
            
            // Get contact info from individual columns
            $contact = [
                'phone' => $report['phone'] ?? '',
                'phone2' => $report['phone2'] ?? '',
                'email' => $report['email'] ?? ''
            ];
            
            // Fallback to description JSON if columns are empty
            if (empty($contact['phone']) && empty($contact['email']) && !empty($report['description'])) {
                $description = json_decode($report['description'], true);
                $contact = $description['contact'] ?? $contact;
            }
            
            $formatted[] = [
                'id' => $report['report_id'],
                'type' => $report['type'],
                'name' => $report['name'] ?? null,
                'species' => $report['species'] ?? 'Unknown',
                'breed' => $report['breed'] ?? 'Unknown',
                'age' => $report['age'] ?? 'Unknown',
                'color' => $report['color'] ?? '',
                'reward' => $report['reward'] ? (float)$report['reward'] : 0,
                'price' => $report['price'] ? (float)$report['price'] : 0,
                'urgency' => $report['urgency'] ?? 'medium',
                'time' => $report['time'] ?? null,
                'photo' => $photos, // Array of photo URLs
                'last_seen' => $report['location'],
                'date' => $report['date_reported'],
                'days_missing' => $daysMissing,
                'notes' => $report['notes'] ?? '',
                'contact' => $contact,
                'latitude' => $report['latitude'] ?? null,
                'longitude' => $report['longitude'] ?? null,
                'user_id' => $report['user_id'] ?? null
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Get only lost pet reports
     */
    public function getLostReports() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM LostFoundReport 
                WHERE type = 'lost'
                ORDER BY date_reported DESC
            ");
            $stmt->execute();
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->formatReports($reports);
        } catch (PDOException $e) {
            error_log("Error fetching lost reports: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get only found pet reports
     */
    public function getFoundReports() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM LostFoundReport 
                WHERE type = 'found'
                ORDER BY date_reported DESC
            ");
            $stmt->execute();
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->formatReports($reports);
        } catch (PDOException $e) {
            error_log("Error fetching found reports: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get reports by type
     */
    public function getReportsByType($type) {
        if ($type === 'lost') {
            return $this->getLostReports();
        } elseif ($type === 'found') {
            return $this->getFoundReports();
        } else {
            return $this->getAllReports();
        }
    }
    
    /**
     * Insert a new lost/found report - accepts data array with individual columns
     */
    public function insertReport($type, $location, $date, $data) {
        try {
            // Support both old JSON format (for backward compatibility) and new array format
            $description = is_array($data) ? json_encode($data) : $data;
            
            // Extract individual fields from array if provided
            $species = isset($data['species']) ? $data['species'] : null;
            $name = isset($data['name']) ? $data['name'] : null;
            $color = isset($data['color']) ? $data['color'] : null;
            $breed = isset($data['breed']) ? $data['breed'] : null;
            $age = isset($data['age']) ? $data['age'] : null;
            $notes = isset($data['notes']) ? $data['notes'] : null;
            $time = isset($data['time']) ? $data['time'] : null;
            $reward = isset($data['reward']) ? (float)$data['reward'] : null;
            $price = isset($data['price']) ? (float)$data['price'] : null;
            $urgency = isset($data['urgency']) ? $data['urgency'] : 'medium';
            $phone = isset($data['contact']['phone']) ? $data['contact']['phone'] : (isset($data['phone']) ? $data['phone'] : null);
            $phone2 = isset($data['contact']['phone2']) ? $data['contact']['phone2'] : (isset($data['phone2']) ? $data['phone2'] : null);
            $email = isset($data['contact']['email']) ? $data['contact']['email'] : (isset($data['email']) ? $data['email'] : null);
            $photos = isset($data['photos']) ? json_encode($data['photos']) : null;
            $latitude = isset($data['latitude']) ? $data['latitude'] : null;
            $longitude = isset($data['longitude']) ? $data['longitude'] : null;
            $user_id = isset($data['user_id']) ? $data['user_id'] : null;
            $submitted_at = isset($data['submitted_at']) ? $data['submitted_at'] : date('Y-m-d H:i:s');
            
            $stmt = $this->db->prepare("
                INSERT INTO LostFoundReport (
                    type, location, date_reported, species, name, color, breed, age, notes, time,
                    reward, price, urgency, phone, phone2, email, photos, latitude, longitude, user_id, submitted_at, description
                ) VALUES (
                    :type, :location, :date_reported, :species, :name, :color, :breed, :age, :notes, :time,
                    :reward, :price, :urgency, :phone, :phone2, :email, :photos, :latitude, :longitude, :user_id, :submitted_at, :description
                )
            ");
            
            $stmt->execute([
                ':type' => $type,
                ':location' => $location,
                ':date_reported' => $date,
                ':species' => $species,
                ':name' => $name,
                ':color' => $color,
                ':breed' => $breed,
                ':age' => $age,
                ':notes' => $notes,
                ':time' => $time,
                ':reward' => $reward,
                ':price' => $price,
                ':urgency' => $urgency,
                ':phone' => $phone,
                ':phone2' => $phone2,
                ':email' => $email,
                ':photos' => $photos,
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':user_id' => $user_id,
                ':submitted_at' => $submitted_at,
                ':description' => $description
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error inserting report: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get a single report by ID
     */
    public function getReportById($reportId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM LostFoundReport WHERE report_id = :report_id");
            $stmt->execute([':report_id' => $reportId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching report: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a lost/found report - accepts data array with individual columns
     */
    public function updateReport($reportId, $type, $location, $date, $data) {
        try {
            // Support both old JSON format (for backward compatibility) and new array format
            $description = is_array($data) ? json_encode($data) : $data;
            
            // Extract individual fields from array if provided
            $species = isset($data['species']) ? $data['species'] : null;
            $name = isset($data['name']) ? $data['name'] : null;
            $color = isset($data['color']) ? $data['color'] : null;
            $breed = isset($data['breed']) ? $data['breed'] : null;
            $age = isset($data['age']) ? $data['age'] : null;
            $notes = isset($data['notes']) ? $data['notes'] : null;
            $time = isset($data['time']) ? $data['time'] : null;
            $reward = isset($data['reward']) ? (float)$data['reward'] : null;
            $price = isset($data['price']) ? (float)$data['price'] : null;
            $urgency = isset($data['urgency']) ? $data['urgency'] : 'medium';
            $phone = isset($data['contact']['phone']) ? $data['contact']['phone'] : (isset($data['phone']) ? $data['phone'] : null);
            $phone2 = isset($data['contact']['phone2']) ? $data['contact']['phone2'] : (isset($data['phone2']) ? $data['phone2'] : null);
            $email = isset($data['contact']['email']) ? $data['contact']['email'] : (isset($data['email']) ? $data['email'] : null);
            $photos = isset($data['photos']) ? json_encode($data['photos']) : null;
            $latitude = isset($data['latitude']) ? $data['latitude'] : null;
            $longitude = isset($data['longitude']) ? $data['longitude'] : null;
            $user_id = isset($data['user_id']) ? $data['user_id'] : null;
            $updated_at = date('Y-m-d H:i:s');
            
            $stmt = $this->db->prepare("
                UPDATE LostFoundReport 
                SET type = :type, 
                    location = :location, 
                    date_reported = :date_reported,
                    species = :species,
                    name = :name,
                    color = :color,
                    breed = :breed,
                    age = :age,
                    notes = :notes,
                    time = :time,
                    reward = :reward,
                    price = :price,
                    urgency = :urgency,
                    phone = :phone,
                    phone2 = :phone2,
                    email = :email,
                    photos = :photos,
                    latitude = :latitude,
                    longitude = :longitude,
                    user_id = :user_id,
                    description = :description,
                    updated_at = :updated_at
                WHERE report_id = :report_id
            ");
            
            $stmt->execute([
                ':type' => $type,
                ':location' => $location,
                ':date_reported' => $date,
                ':species' => $species,
                ':name' => $name,
                ':color' => $color,
                ':breed' => $breed,
                ':age' => $age,
                ':notes' => $notes,
                ':time' => $time,
                ':reward' => $reward,
                ':price' => $price,
                ':urgency' => $urgency,
                ':phone' => $phone,
                ':phone2' => $phone2,
                ':email' => $email,
                ':photos' => $photos,
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':user_id' => $user_id,
                ':description' => $description,
                ':updated_at' => $updated_at,
                ':report_id' => $reportId
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating report: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a lost/found report
     */
    public function deleteReport($reportId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM LostFoundReport WHERE report_id = :report_id");
            $stmt->execute([':report_id' => $reportId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting report: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Search reports with filters
     */
    public function searchReports($query, $species = null, $type = null) {
        try {
            $sql = "SELECT * FROM LostFoundReport WHERE 1=1";
            $params = [];
            
            // Filter by type
            if ($type && in_array($type, ['lost', 'found'])) {
                $sql .= " AND type = :type";
                $params[':type'] = $type;
            }
            
            // Search in description JSON (limited support, better with full-text search)
            if ($query) {
                $sql .= " AND (location LIKE :query OR description LIKE :query2)";
                $params[':query'] = '%' . $query . '%';
                $params[':query2'] = '%' . $query . '%';
            }
            
            $sql .= " ORDER BY date_reported DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $formatted = $this->formatReports($reports);
            
            // Additional client-side filtering for species (since it's in JSON)
            if ($species) {
                $formatted = array_filter($formatted, function($r) use ($species) {
                    return strcasecmp($r['species'], $species) === 0;
                });
                $formatted = array_values($formatted); // Re-index
            }
            
            return $formatted;
        } catch (PDOException $e) {
            error_log("Error searching reports: " . $e->getMessage());
            return [];
        }
    }
}
?>