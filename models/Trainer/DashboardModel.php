<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/TrainerData.php';

class TrainerDashboardModel extends BaseModel {
    
    public function getStats($trainerId) {
        return TrainerData::getStats();
    }

    public function getUpcomingAppointments($trainerId, $limit = 5) {
        return TrainerData::getUpcomingAppointments($limit);
    }

    public function getRecentClients($trainerId) {
        return [
            ['name' => 'Sarah Johnson', 'pet' => 'Max', 'joined' => '2025-09-15'],
            ['name' => 'Mike Davis', 'pet' => 'Bella', 'joined' => '2025-09-20'],
            ['name' => 'Emma Wilson', 'pet' => 'Charlie', 'joined' => '2025-10-01']
        ];
    }
}
