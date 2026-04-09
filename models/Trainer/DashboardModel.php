<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerDashboardModel extends BaseModel {

    private function tableExists(string $tableName): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT 1
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                LIMIT 1");
            $stmt->execute([$tableName]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log('trainer tableExists check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getStats($trainerId) {
        $trainerId = (int)$trainerId;
        if ($trainerId <= 0) {
            return [
                'active_sessions' => 0,
                'total_pets_trained' => 0,
                'completed_sessions' => 0,
                'pending_requests' => 0
            ];
        }

        if (!$this->tableExists('trainer_training_requests')) {
            return [
                'active_sessions' => 0,
                'total_pets_trained' => 0,
                'completed_sessions' => 0,
                'pending_requests' => 0
            ];
        }

        try {
            $pdo = $this->pdo;

            $stmt = $pdo->prepare("SELECT
                    SUM(status = 'pending') AS pending_requests,
                    SUM(status = 'accepted') AS active_sessions,
                    SUM(status = 'completed') AS completed_sessions,
                    COUNT(DISTINCT CASE
                        WHEN status IN ('accepted','completed') THEN CONCAT(pet_owner_id, '-', pet_name)
                        ELSE NULL
                    END) AS total_pets_trained
                FROM trainer_training_requests
                WHERE trainer_id = ?");
            $stmt->execute([$trainerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            return [
                'active_sessions' => (int)($row['active_sessions'] ?? 0),
                'total_pets_trained' => (int)($row['total_pets_trained'] ?? 0),
                'completed_sessions' => (int)($row['completed_sessions'] ?? 0),
                'pending_requests' => (int)($row['pending_requests'] ?? 0)
            ];
        } catch (Throwable $e) {
            error_log('TrainerDashboardModel getStats error: ' . $e->getMessage());
            return [
                'active_sessions' => 0,
                'total_pets_trained' => 0,
                'completed_sessions' => 0,
                'pending_requests' => 0
            ];
        }
    }

    public function getUpcomingAppointments($trainerId, $limit = 5) {
        $trainerId = (int)$trainerId;
        $limit = (int)$limit;
        if ($trainerId <= 0 || $limit <= 0) return [];

        if (!$this->tableExists('trainer_training_requests')) {
            return [];
        }

        try {
            $pdo = $this->pdo;
            $sql = "SELECT
                        r.preferred_date,
                        TIME_FORMAT(r.preferred_time, '%h:%i %p') AS preferred_time,
                        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                        r.location_type,
                        r.location_address,
                        r.location_district
                    FROM trainer_training_requests r
                    JOIN users u ON u.id = r.pet_owner_id
                    WHERE r.trainer_id = ?
                        AND r.status = 'accepted'
                        AND r.preferred_date >= CURDATE()
                    ORDER BY r.preferred_date ASC, r.preferred_time ASC
                    LIMIT $limit";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$trainerId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $out = [];
            foreach ($rows as $r) {
                $date = (string)($r['preferred_date'] ?? '');
                $time = (string)($r['preferred_time'] ?? '');
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

                $locationType = (string)($r['location_type'] ?? '');
                if ($locationType === 'trainer') {
                    $location = "At Trainer's Location";
                } else {
                    $location = trim((string)($r['location_address'] ?? ''));
                    if ($location === '') {
                        $location = trim((string)($r['location_district'] ?? ''));
                    }
                    if ($location === '') {
                        $location = 'Selected location';
                    }
                }

                $out[] = [
                    'time' => $when,
                    'customer_name' => (string)($r['customer_name'] ?? ''),
                    'location' => $location
                ];
            }

            return $out;
        } catch (Throwable $e) {
            error_log('TrainerDashboardModel getUpcomingAppointments error: ' . $e->getMessage());
            return [];
        }
    }

    public function getRecentClients($trainerId) {
        return [];
    }
}
