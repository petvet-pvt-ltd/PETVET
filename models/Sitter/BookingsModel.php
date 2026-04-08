<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterBookingsModel extends BaseModel {

    private function tableExists(string $tableName): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT 1
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                LIMIT 1");
            $stmt->execute([$tableName]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            // If information_schema is not accessible for some reason,
            // don't hard-crash the UI; assume table doesn't exist.
            error_log('tableExists check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getAllBookings($sitterId) {
        return array_merge(
            $this->getPendingBookings($sitterId),
            $this->getActiveBookings($sitterId),
            $this->getCompletedBookings($sitterId)
        );
    }

    public function getPendingBookings($sitterId) {
        $sitterId = (int)$sitterId;
        if ($sitterId <= 0) return [];

        if (!$this->tableExists('sitter_service_requests')) {
            return [];
        }

        $pdo = $this->pdo;

        $sql = "SELECT
                    r.id,
                    r.pet_name,
                    r.pet_type,
                    r.pet_breed,
                    r.service_type,
                    r.start_date,
                    r.end_date,
                    TIME_FORMAT(r.start_time, '%h:%i %p') AS start_time,
                    TIME_FORMAT(r.end_time, '%h:%i %p') AS end_time,
                    r.location_type,
                    r.location_address,
                    r.location_lat,
                    r.location_lng,
                    r.location_district,
                    r.special_notes,
                    r.created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
                    COALESCE(u.phone, '') AS owner_phone,
                    '' AS owner_phone_2,
                    spp.location_latitude AS sitter_lat,
                    spp.location_longitude AS sitter_lng
                FROM sitter_service_requests r
                JOIN users u ON u.id = r.pet_owner_id
                LEFT JOIN service_provider_profiles spp
                    ON spp.user_id = r.sitter_id AND spp.role_type = 'sitter'
                WHERE r.sitter_id = ? AND r.status = 'pending'
                ORDER BY r.created_at DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sitterId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If the table is missing (migration not run yet), avoid fatal error.
            if (($e->errorInfo[0] ?? null) === '42S02') {
                error_log('sitter_service_requests missing; returning empty pending list');
                return [];
            }
            throw $e;
        }

        foreach ($rows as &$row) {
            $locationType = (string)($row['location_type'] ?? '');

            $sitterLat = isset($row['sitter_lat']) ? (float)$row['sitter_lat'] : null;
            $sitterLng = isset($row['sitter_lng']) ? (float)$row['sitter_lng'] : null;

            $lat = $row['location_lat'] !== null ? (float)$row['location_lat'] : null;
            $lng = $row['location_lng'] !== null ? (float)$row['location_lng'] : null;

            if ($locationType === 'sitter') {
                $row['location'] = "At sitter's place";
                if ($sitterLat && $sitterLng) {
                    $row['location_lat'] = $sitterLat;
                    $row['location_lng'] = $sitterLng;
                }
                $row['distance_km'] = '0.0';
            } else {
                $row['location'] = (string)($row['location_address'] ?? '');
                if ($row['location'] === '') {
                    $row['location'] = 'Selected location';
                }

                if ($sitterLat && $sitterLng && $lat && $lng) {
                    $dist = $this->haversineKm($sitterLat, $sitterLng, $lat, $lng);
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

    public function getActiveBookings($sitterId) {
        return [
            [
                'id' => 4,
                'pet_name' => 'Luna & Shadow',
                'pet_type' => 'Dog',
                'pet_breed' => 'Golden Retrievers',
                'owner_name' => 'Maria Garcia',
                'owner_phone' => '555-0202',
                'owner_phone_2' => '555-0212',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-20',
                'start_time' => '9:00 AM',
                'end_time' => '10:00 AM',
                'service_type' => 'Dog Walking',
                'status' => 'confirmed',
                'location' => 'Central Park Area',
                'daily_rate' => 25,
                'special_notes' => 'Two friendly Golden Retrievers need their daily walk. They\'re well-trained and love meeting other dogs at the park.'
            ],
            [
                'id' => 5,
                'pet_name' => 'Whiskers',
                'pet_type' => 'Cat',
                'pet_breed' => 'Tabby',
                'owner_name' => 'Tom Wilson',
                'owner_phone' => '555-0203',
                'owner_phone_2' => '',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-22',
                'start_time' => '12:00 PM',
                'end_time' => '6:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'confirmed',
                'location' => 'Riverside District',
                'daily_rate' => 60,
                'special_notes' => 'Indoor cat needs daily feeding, litter cleaning, and companionship while owner is away on business trip.'
            ],
            [
                'id' => 6,
                'pet_name' => 'Rocky',
                'pet_type' => 'Dog',
                'pet_breed' => 'German Shepherd',
                'owner_name' => 'James Miller',
                'owner_phone' => '555-0206',
                'owner_phone_2' => '',
                'start_date' => '2025-10-19',
                'end_date' => '2025-10-21',
                'start_time' => '8:00 AM',
                'end_time' => '8:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'confirmed',
                'location' => 'North Hill',
                'daily_rate' => 70,
                'special_notes' => 'Active dog needs plenty of exercise. Has special diet - food provided by owner.'
            ]
        ];
    }

    public function getCompletedBookings($sitterId) {
        return [
            [
                'id' => 7,
                'pet_name' => 'Max',
                'pet_type' => 'Dog',
                'pet_breed' => 'Poodle',
                'owner_name' => 'Linda Green',
                'owner_phone' => '555-0207',
                'start_date' => '2025-10-05',
                'end_date' => '2025-10-10',
                'start_time' => '9:00 AM',
                'end_time' => '6:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'completed',
                'location' => 'South Bay',
                'daily_rate' => 50,
                'special_notes' => 'Very friendly, loves toys. Completed successfully.'
            ],
            [
                'id' => 8,
                'pet_name' => 'Bella',
                'pet_type' => 'Dog',
                'pet_breed' => 'Chihuahua',
                'owner_name' => 'Emily Davis',
                'owner_phone' => '555-0208',
                'start_date' => '2025-10-12',
                'end_date' => '2025-10-14',
                'start_time' => '10:00 AM',
                'end_time' => '5:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'completed',
                'location' => 'Downtown',
                'daily_rate' => 40,
                'special_notes' => 'Small dog, easy to care for. Owner very satisfied.'
            ]
        ];
    }

    // Action Methods
    public function acceptBooking($bookingId, $sitterId) {
        $bookingId = (int)$bookingId;
        $sitterId = (int)$sitterId;
        if ($bookingId <= 0 || $sitterId <= 0) {
            return ['success' => false, 'message' => 'Invalid booking'];
        }

        try {
            if (!$this->tableExists('sitter_service_requests')) {
                return ['success' => false, 'message' => 'Bookings table is not set up yet'];
            }
            $stmt = $this->pdo->prepare("UPDATE sitter_service_requests
                SET status = 'accepted', sitter_response_at = NOW(), decline_reason = NULL
                WHERE id = ? AND sitter_id = ? AND status = 'pending'");
            $stmt->execute([$bookingId, $sitterId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Booking not found or already processed'];
            }

            return ['success' => true, 'message' => 'Booking accepted successfully!', 'booking_id' => $bookingId];
        } catch (Throwable $e) {
            error_log('acceptBooking error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function declineBooking($bookingId, $sitterId) {
        $bookingId = (int)$bookingId;
        $sitterId = (int)$sitterId;
        if ($bookingId <= 0 || $sitterId <= 0) {
            return ['success' => false, 'message' => 'Invalid booking'];
        }

        try {
            if (!$this->tableExists('sitter_service_requests')) {
                return ['success' => false, 'message' => 'Bookings table is not set up yet'];
            }
            $stmt = $this->pdo->prepare("UPDATE sitter_service_requests
                SET status = 'declined', sitter_response_at = NOW()
                WHERE id = ? AND sitter_id = ? AND status = 'pending'");
            $stmt->execute([$bookingId, $sitterId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Booking not found or already processed'];
            }

            return ['success' => true, 'message' => 'Booking declined', 'booking_id' => $bookingId];
        } catch (Throwable $e) {
            error_log('declineBooking error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function completeBooking($bookingId, $sitterId) {
        // In production, update database:
        // UPDATE bookings SET status = 'completed', completed_at = NOW() WHERE id = ? AND sitter_id = ?
        
        // Mock response
        return [
            'success' => true,
            'message' => 'Booking marked as complete!',
            'booking_id' => $bookingId
        ];
    }

    public function getBookingById($bookingId, $sitterId) {
        // In production: SELECT * FROM bookings WHERE id = ? AND sitter_id = ?
        
        // Mock: search in all booking arrays
        $allBookings = $this->getAllBookings($sitterId);
        foreach ($allBookings as $booking) {
            if ($booking['id'] == $bookingId) {
                return $booking;
            }
        }
        return null;
    }
}
