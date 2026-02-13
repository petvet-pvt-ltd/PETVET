<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/TrainerData.php';

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
        $data = TrainerData::getAllSessions();
        return $data['confirmed'];
    }
    
    /**
     * Get all completed training sessions for the trainer
     */
    public function getCompletedSessions($trainerId) {
        $data = TrainerData::getAllSessions();
        return $data['completed'];
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
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training session completed and notes saved successfully'
        ];
    }
    
    /**
     * Mark entire training program as completed
     */
    public function markProgramComplete($sessionId, $trainerId, $finalNotes) {
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training program marked as complete'
        ];
    }
    
    /**
     * Get session by ID
     */
    public function getSessionById($sessionId) {
        // Mock implementation - in real app, query database
        $confirmedSessions = $this->getConfirmedSessions(null);
        foreach ($confirmedSessions as $session) {
            if ($session['session_id'] == $sessionId) {
                return $session;
            }
        }
        return null;
    }
    
    /**
     * Get request by ID
     */
    public function getRequestById($requestId) {
        // Mock implementation - in real app, query database
        $pendingRequests = $this->getPendingRequests(null);
        foreach ($pendingRequests as $request) {
            if ($request['request_id'] == $requestId) {
                return $request;
            }
        }
        return null;
    }
    
    /**
     * Get session history (all previous session notes) for a training program
     */
    public function getSessionHistory($sessionId) {
        // Mock data - in real app, query database for all previous sessions
        // This would join session_notes table or similar
        return [
            [
                'session_number' => 1,
                'session_date' => '2025-09-26',
                'notes' => 'First session with Rocky. He is very energetic and enthusiastic. Started with basic sit and stay commands. He responds well to treats. Needs work on focus and attention span.',
                'goals_for_next' => 'Continue practicing sit and stay. Introduce heel command.'
            ],
            [
                'session_number' => 2,
                'session_date' => '2025-09-29',
                'notes' => 'Good progress on sit command. Rocky now sits consistently on command. Started heel training - he pulls on leash initially but improving. Practiced recall with distractions.',
                'goals_for_next' => 'Focus on heel work and loose leash walking. Practice commands with more distractions.'
            ],
            [
                'session_number' => 3,
                'session_date' => '2025-10-03',
                'notes' => 'Rocky is doing much better with leash manners. Heel command is improving. Introduced down command. He gets excited when other dogs are around - need to work on impulse control.',
                'goals_for_next' => 'Continue leash work. Practice down-stay. Start impulse control exercises.'
            ],
            [
                'session_number' => 4,
                'session_date' => '2025-10-07',
                'notes' => 'Excellent session! Rocky held down-stay for 30 seconds. Worked on impulse control with food temptations - showing good progress. Owner is also improving with consistent commands and timing.',
                'goals_for_next' => 'Increase duration of stays. Practice all commands with high-level distractions.'
            ],
            [
                'session_number' => 5,
                'session_date' => '2025-10-10',
                'notes' => 'Rocky showed great improvement on recall commands. Distraction training went well. Continue working on impulse control around food. Overall excellent progress - he is becoming very reliable with basic commands.',
                'goals_for_next' => 'Focus on advanced heel work and practicing commands with increased distractions.'
            ]
        ];
    }
}
