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
                WHERE r.sitter_id = ? AND r.status = 'accepted'
                ORDER BY r.created_at DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sitterId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === '42S02') {
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

    public function getCompletedBookings($sitterId) {
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
                WHERE r.sitter_id = ? AND r.status = 'completed'
                ORDER BY r.created_at DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sitterId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === '42S02') {
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
        $bookingId = (int)$bookingId;
        $sitterId = (int)$sitterId;
        if ($bookingId <= 0 || $sitterId <= 0) {
            return ['success' => false, 'message' => 'Invalid booking'];
        }

        try {
            if (!$this->tableExists('sitter_service_requests')) {
                return ['success' => false, 'message' => 'Bookings table is not set up yet'];
            }

            // Try to set completed_at if the column exists; if not, fall back to status-only.
            try {
                $stmt = $this->pdo->prepare("UPDATE sitter_service_requests
                    SET status = 'completed', completed_at = NOW()
                    WHERE id = ? AND sitter_id = ? AND status = 'accepted'");
                $stmt->execute([$bookingId, $sitterId]);
            } catch (PDOException $e) {
                if (($e->errorInfo[0] ?? null) === '42S22') {
                    $stmt = $this->pdo->prepare("UPDATE sitter_service_requests
                        SET status = 'completed'
                        WHERE id = ? AND sitter_id = ? AND status = 'accepted'");
                    $stmt->execute([$bookingId, $sitterId]);
                } else {
                    throw $e;
                }
            }

            if (($stmt->rowCount() ?? 0) === 0) {
                return ['success' => false, 'message' => 'Booking not found or not active'];
            }

            return ['success' => true, 'message' => 'Booking marked as complete!', 'booking_id' => $bookingId];
        } catch (Throwable $e) {
            error_log('completeBooking error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function getBookingById($bookingId, $sitterId) {
        $bookingId = (int)$bookingId;
        $sitterId = (int)$sitterId;
        if ($bookingId <= 0 || $sitterId <= 0) return null;

        if (!$this->tableExists('sitter_service_requests')) {
            return null;
        }

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
                    r.status,
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
                WHERE r.id = ? AND r.sitter_id = ?
                LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$bookingId, $sitterId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === '42S02') {
                return null;
            }
            throw $e;
        }

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

        return $row;
    }
}
