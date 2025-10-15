<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerClientsModel extends BaseModel {
    
    public function getAllClients($trainerId) {
        return [
            [
                'id' => 1,
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@email.com',
                'phone' => '555-0101',
                'pet_name' => 'Max',
                'pet_breed' => 'Golden Retriever',
                'sessions_completed' => 12,
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'Mike Davis',
                'email' => 'mike.d@email.com',
                'phone' => '555-0102',
                'pet_name' => 'Bella',
                'pet_breed' => 'German Shepherd',
                'sessions_completed' => 8,
                'status' => 'active'
            ]
        ];
    }

    public function getActiveClients($trainerId) {
        return array_filter($this->getAllClients($trainerId), fn($c) => $c['status'] === 'active');
    }

    public function getClientStats($trainerId) {
        return [
            'total' => 24,
            'active' => 18,
            'inactive' => 6
        ];
    }
}
