<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterDashboardModel extends BaseModel {

    private function tableExists(string $tableName): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT 1
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                LIMIT 1");
            $stmt->execute([$tableName]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log('sitter tableExists check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getStats($sitterId) {
        $sitterId = (int)$sitterId;
        if ($sitterId <= 0) {
            return [
                'active_bookings' => 0,
                'total_pets_cared' => 0,
                'completed_bookings' => 0,
                'pending_requests' => 0
            ];
        }

        if (!$this->tableExists('sitter_service_requests')) {
            return [
                'active_bookings' => 0,
                'total_pets_cared' => 0,
                'completed_bookings' => 0,
                'pending_requests' => 0
            ];
        }

        try {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare("SELECT
                    SUM(status = 'pending') AS pending_requests,
                    SUM(status = 'accepted') AS active_bookings,
                    SUM(status = 'completed') AS completed_bookings,
                    COUNT(DISTINCT CASE
                        WHEN status IN ('accepted','completed') THEN CONCAT(pet_owner_id, '-', pet_name)
                        ELSE NULL
                    END) AS total_pets_cared
                FROM sitter_service_requests
                WHERE sitter_id = ?");
            $stmt->execute([$sitterId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            return [
                'active_bookings' => (int)($row['active_bookings'] ?? 0),
                'total_pets_cared' => (int)($row['total_pets_cared'] ?? 0),
                'completed_bookings' => (int)($row['completed_bookings'] ?? 0),
                'pending_requests' => (int)($row['pending_requests'] ?? 0)
            ];
        } catch (Throwable $e) {
            error_log('SitterDashboardModel getStats error: ' . $e->getMessage());
            return [
                'active_bookings' => 0,
                'total_pets_cared' => 0,
                'completed_bookings' => 0,
                'pending_requests' => 0
            ];
        }
    }

    public function getActiveBookings($sitterId) {
        // Dashboard doesn't render an active list; keep this empty to avoid dummy data.
        return [];
    }

    public function getUpcomingBookings($sitterId, $limit = 5) {
        $sitterId = (int)$sitterId;
        $limit = (int)$limit;
        if ($sitterId <= 0 || $limit <= 0) return [];

        if (!$this->tableExists('sitter_service_requests')) {
            return [];
        }

        try {
            $pdo = $this->pdo;
            $sql = "SELECT
                        r.start_date,
                        TIME_FORMAT(r.start_time, '%h:%i %p') AS start_time,
                        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                        r.service_type
                    FROM sitter_service_requests r
                    JOIN users u ON u.id = r.pet_owner_id
                    WHERE r.sitter_id = ?
                        AND r.status = 'accepted'
                        AND r.start_date >= CURDATE()
                    ORDER BY r.start_date ASC, r.start_time ASC
                    LIMIT $limit";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sitterId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $out = [];
            foreach ($rows as $r) {
                $date = (string)($r['start_date'] ?? '');
                $time = (string)($r['start_time'] ?? '');
                $when = '';
                if ($date !== '' && $time !== '') {
                    $today = date('Y-m-d');
                    $tomorrow = date('Y-m-d', strtotime('+1 day'));
                    if ($date === $today) {
                        $when = 'Today ' . $time;
                    } elseif ($date === $tomorrow) {
                        $when = 'Tomorrow ' . $time;
                    } else {
                        $when = date('M j, Y', strtotime($date)) . ', ' . $time;
                    }
                }

                $out[] = [
                    'time' => $when,
                    'customer_name' => (string)($r['customer_name'] ?? ''),
                    'category' => (string)($r['service_type'] ?? '')
                ];
            }

            return $out;
        } catch (Throwable $e) {
            error_log('SitterDashboardModel getUpcomingBookings error: ' . $e->getMessage());
            return [];
        }
    }
}
