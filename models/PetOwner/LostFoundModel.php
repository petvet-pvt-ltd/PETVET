<?php
require_once __DIR__ . '/../BaseModel.php';

class LostFoundModel extends BaseModel {
    
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
     * Format database reports to match view expectations
     */
    private function formatReports($dbReports) {
        $formatted = [];
        
        foreach ($dbReports as $report) {
            // Parse JSON description field
            $description = json_decode($report['description'], true);
            
            // Build photo array - handle both string and array
            $photos = $description['photos'] ?? [];
            if (empty($photos)) {
                $photos = ['/PETVET/public/img/default-pet.jpg']; // Fallback image
            }
            
            $formatted[] = [
                'id' => $report['report_id'],
                'type' => $report['type'],
                'name' => $description['name'] ?? null,
                'species' => $description['species'] ?? 'Unknown',
                'breed' => $description['breed'] ?? 'Unknown',
                'age' => $description['age'] ?? 'Unknown',
                'color' => $description['color'] ?? '',
                'photo' => $photos, // Array of photo URLs
                'last_seen' => $report['location'],
                'date' => $report['date_reported'],
                'notes' => $description['notes'] ?? '',
                'contact' => $description['contact'] ?? [
                    'name' => 'Anonymous',
                    'email' => '',
                    'phone' => '',
                    'phone2' => ''
                ]
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