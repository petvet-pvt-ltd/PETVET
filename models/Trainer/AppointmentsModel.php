<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerAppointmentsModel extends BaseModel {
    
    /**
     * Get all pending training requests for the trainer
     */
    public function getPendingRequests($trainerId) {
        $trainerId = (int)$trainerId;
        if ($trainerId <= 0) return [];

        $pdo = $this->pdo;

        $sql = "SELECT
                    r.id AS request_id,
                    r.training_type,
                    r.pet_name,
                    r.pet_breed,
                    r.preferred_date,
                    TIME_FORMAT(r.preferred_time, '%h:%i %p') AS preferred_time,
                    r.location_type,
                    r.location_address,
                    r.location_lat,
                    r.location_lng,
                    r.location_district,
                    r.additional_notes,
                    r.created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS pet_owner_name,
                    COALESCE(u.phone, '') AS pet_owner_phone,
                    spp.location_latitude AS trainer_lat,
                    spp.location_longitude AS trainer_lng
                FROM trainer_training_requests r
                JOIN users u ON u.id = r.pet_owner_id
                LEFT JOIN service_provider_profiles spp
                    ON spp.user_id = r.trainer_id AND spp.role_type = 'trainer'
                WHERE r.trainer_id = ? AND r.status = 'pending'
                ORDER BY r.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$trainerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $locationType = (string)($row['location_type'] ?? '');

            $trainerLat = isset($row['trainer_lat']) ? (float)$row['trainer_lat'] : null;
            $trainerLng = isset($row['trainer_lng']) ? (float)$row['trainer_lng'] : null;

            $lat = $row['location_lat'] !== null ? (float)$row['location_lat'] : null;
            $lng = $row['location_lng'] !== null ? (float)$row['location_lng'] : null;

            if ($locationType === 'trainer') {
                $row['location'] = "At Trainer's Location";
                // For navigation, use trainer coordinates
                if ($trainerLat && $trainerLng) {
                    $row['location_lat'] = $trainerLat;
                    $row['location_lng'] = $trainerLng;
                }
                $row['distance_km'] = '0.0';
            } else {
                $row['location'] = (string)($row['location_address'] ?? '');
                if ($row['location'] === '') {
                    $row['location'] = 'Selected location';
                }

                // Distance from trainer to selected location
                if ($trainerLat && $trainerLng && $lat && $lng) {
                    $dist = $this->haversineKm($trainerLat, $trainerLng, $lat, $lng);
                    $row['distance_km'] = number_format($dist, 1);
                } else {
                    $row['distance_km'] = null;
                }
            }
        }

        return $rows;
    }

    private function haversineKm($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
    
    /**
     * Get all confirmed/active training sessions for the trainer
     */
    public function getConfirmedSessions($trainerId) {
        // No session table implemented yet; avoid showing dummy data.
        return [];
    }
    
    /**
     * Get all completed training sessions for the trainer
     */
    public function getCompletedSessions($trainerId) {
        // No session table implemented yet; avoid showing dummy data.
        return [];
    }

    
    /**
     * Accept a training request
     */
    public function acceptRequest($requestId, $trainerId) {
        $requestId = (int)$requestId;
        $trainerId = (int)$trainerId;
        if ($requestId <= 0 || $trainerId <= 0) {
            return ['success' => false, 'message' => 'Invalid request'];
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE trainer_training_requests
                SET status = 'accepted', trainer_response_at = NOW(), decline_reason = NULL
                WHERE id = ? AND trainer_id = ? AND status = 'pending'");
            $stmt->execute([$requestId, $trainerId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Request not found or already processed'];
            }

            return ['success' => true, 'message' => 'Training request accepted successfully'];
        } catch (Throwable $e) {
            error_log('acceptRequest error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }
    
    /**
     * Decline a training request
     */
    public function declineRequest($requestId, $trainerId, $reason = '') {
        $requestId = (int)$requestId;
        $trainerId = (int)$trainerId;
        $reason = trim((string)$reason);
        if ($requestId <= 0 || $trainerId <= 0) {
            return ['success' => false, 'message' => 'Invalid request'];
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE trainer_training_requests
                SET status = 'declined', trainer_response_at = NOW(), decline_reason = ?
                WHERE id = ? AND trainer_id = ? AND status = 'pending'");
            $stmt->execute([$reason !== '' ? $reason : null, $requestId, $trainerId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Request not found or already processed'];
            }

            return ['success' => true, 'message' => 'Training request declined'];
        } catch (Throwable $e) {
            error_log('declineRequest error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }
    
    /**
     * Complete a training session and save notes
     */
    public function completeSession($sessionId, $trainerId, $notes, $nextSessionDate = null, $nextSessionTime = null, $nextSessionGoals = '') {
        $sessionId = (int)$sessionId;
        $trainerId = (int)$trainerId;
        $notes = trim((string)$notes);

        if ($sessionId <= 0 || $trainerId <= 0) {
            return ['success' => false, 'message' => 'Invalid session'];
        }

        try {
            // Treat “session” as the accepted request record.
            // Try to persist notes if a column exists; fall back to status-only.
            try {
                $stmt = $this->pdo->prepare("UPDATE trainer_training_requests
                    SET status = 'completed', completed_at = NOW(), completion_notes = ?
                    WHERE id = ? AND trainer_id = ? AND status = 'accepted'");
                $stmt->execute([$notes !== '' ? $notes : null, $sessionId, $trainerId]);
            } catch (PDOException $e) {
                if (($e->errorInfo[0] ?? null) === '42S22') {
                    // Unknown column(s) (completed_at/completion_notes)
                    try {
                        $stmt = $this->pdo->prepare("UPDATE trainer_training_requests
                            SET status = 'completed', completed_at = NOW()
                            WHERE id = ? AND trainer_id = ? AND status = 'accepted'");
                        $stmt->execute([$sessionId, $trainerId]);
                    } catch (PDOException $e2) {
                        if (($e2->errorInfo[0] ?? null) === '42S22') {
                            $stmt = $this->pdo->prepare("UPDATE trainer_training_requests
                                SET status = 'completed'
                                WHERE id = ? AND trainer_id = ? AND status = 'accepted'");
                            $stmt->execute([$sessionId, $trainerId]);
                        } else {
                            throw $e2;
                        }
                    }
                } else {
                    throw $e;
                }
            }

            if (($stmt->rowCount() ?? 0) === 0) {
                return ['success' => false, 'message' => 'Session not found or not active'];
            }

            return ['success' => true, 'message' => 'Training marked as completed'];
        } catch (Throwable $e) {
            error_log('completeSession error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }
    
    /**
     * Mark entire training program as completed
     */
    public function markProgramComplete($sessionId, $trainerId, $finalNotes) {
        // Same persistence strategy as completeSession.
        return $this->completeSession($sessionId, $trainerId, $finalNotes);
    }
    
    /**
     * Get session by ID
     */
    public function getSessionById($sessionId) {
        $sessionId = (int)$sessionId;
        if ($sessionId <= 0) return null;

        // No dedicated sessions table; treat as a request record.
        try {
            $stmt = $this->pdo->prepare("SELECT
                    r.id AS request_id,
                    r.trainer_id,
                    r.pet_owner_id,
                    r.training_type,
                    r.pet_name,
                    r.pet_breed,
                    r.preferred_date,
                    TIME_FORMAT(r.preferred_time, '%h:%i %p') AS preferred_time,
                    r.location_type,
                    r.location_address,
                    r.location_lat,
                    r.location_lng,
                    r.location_district,
                    r.additional_notes,
                    r.status,
                    r.created_at
                FROM trainer_training_requests r
                WHERE r.id = ?
                LIMIT 1");
            $stmt->execute([$sessionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log('getSessionById error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get request by ID
     */
    public function getRequestById($requestId) {
        $requestId = (int)$requestId;
        if ($requestId <= 0) return null;

        try {
            $stmt = $this->pdo->prepare("SELECT
                    r.id AS request_id,
                    r.trainer_id,
                    r.pet_owner_id,
                    r.training_type,
                    r.pet_name,
                    r.pet_breed,
                    r.preferred_date,
                    TIME_FORMAT(r.preferred_time, '%h:%i %p') AS preferred_time,
                    r.location_type,
                    r.location_address,
                    r.location_lat,
                    r.location_lng,
                    r.location_district,
                    r.additional_notes,
                    r.status,
                    r.created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS pet_owner_name,
                    COALESCE(u.phone, '') AS pet_owner_phone,
                    spp.location_latitude AS trainer_lat,
                    spp.location_longitude AS trainer_lng
                FROM trainer_training_requests r
                JOIN users u ON u.id = r.pet_owner_id
                LEFT JOIN service_provider_profiles spp
                    ON spp.user_id = r.trainer_id AND spp.role_type = 'trainer'
                WHERE r.id = ?
                LIMIT 1");
            $stmt->execute([$requestId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            // Normalize location + distance similar to getPendingRequests
            $locationType = (string)($row['location_type'] ?? '');
            $trainerLat = isset($row['trainer_lat']) ? (float)$row['trainer_lat'] : null;
            $trainerLng = isset($row['trainer_lng']) ? (float)$row['trainer_lng'] : null;
            $lat = $row['location_lat'] !== null ? (float)$row['location_lat'] : null;
            $lng = $row['location_lng'] !== null ? (float)$row['location_lng'] : null;

            if ($locationType === 'trainer') {
                $row['location'] = "At Trainer's Location";
                if ($trainerLat && $trainerLng) {
                    $row['location_lat'] = $trainerLat;
                    $row['location_lng'] = $trainerLng;
                }
                $row['distance_km'] = '0.0';
            } else {
                $row['location'] = (string)($row['location_address'] ?? '');
                if ($row['location'] === '') {
                    $row['location'] = 'Selected location';
                }

                if ($trainerLat && $trainerLng && $lat && $lng) {
                    $dist = $this->haversineKm($trainerLat, $trainerLng, $lat, $lng);
                    $row['distance_km'] = number_format($dist, 1);
                } else {
                    $row['distance_km'] = null;
                }
            }

            return $row;
        } catch (Throwable $e) {
            error_log('getRequestById error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get session history (all previous session notes) for a training program
     */
    public function getSessionHistory($sessionId) {
        // No session history storage implemented yet.
        return [];
    }
}
