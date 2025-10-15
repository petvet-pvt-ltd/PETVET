<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerDashboardModel extends BaseModel {
    
    public function getStats($trainerId) {
        return [
            'total_clients' => 24,
            'active_sessions' => 8,
            'completed_sessions' => 156,
            'monthly_earnings' => 3200
        ];
    }

    public function getUpcomingSessions($trainerId) {
        return [
            [
                'id' => 1,
                'client_name' => 'Sarah Johnson',
                'pet_name' => 'Max',
                'session_type' => 'Basic Obedience',
                'date' => '2025-10-16',
                'time' => '10:00 AM',
                'duration' => '60 min',
                'status' => 'confirmed'
            ],
            [
                'id' => 2,
                'client_name' => 'Mike Davis',
                'pet_name' => 'Bella',
                'session_type' => 'Advanced Training',
                'date' => '2025-10-17',
                'time' => '2:00 PM',
                'duration' => '90 min',
                'status' => 'confirmed'
            ]
        ];
    }

    public function getRecentClients($trainerId) {
        return [
            ['name' => 'Sarah Johnson', 'pet' => 'Max', 'joined' => '2025-09-15'],
            ['name' => 'Mike Davis', 'pet' => 'Bella', 'joined' => '2025-09-20'],
            ['name' => 'Emma Wilson', 'pet' => 'Charlie', 'joined' => '2025-10-01']
        ];
    }
}
