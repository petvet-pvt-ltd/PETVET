<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerAppointmentsModel extends BaseModel {
    
    public function getAllAppointments($trainerId) {
        return array_merge($this->getUpcomingAppointments($trainerId), $this->getCompletedAppointments($trainerId));
    }

    public function getUpcomingAppointments($trainerId) {
        return [
            [
                'id' => 1,
                'client_name' => 'Sarah Johnson',
                'pet_name' => 'Max',
                'pet_breed' => 'Golden Retriever',
                'session_type' => 'Basic Obedience',
                'date' => '2025-10-16',
                'time' => '10:00 AM',
                'duration' => '60 min',
                'status' => 'confirmed',
                'notes' => 'Working on sit and stay commands'
            ],
            [
                'id' => 2,
                'client_name' => 'Mike Davis',
                'pet_name' => 'Bella',
                'pet_breed' => 'German Shepherd',
                'session_type' => 'Advanced Training',
                'date' => '2025-10-17',
                'time' => '2:00 PM',
                'duration' => '90 min',
                'status' => 'confirmed',
                'notes' => 'Agility training continuation'
            ]
        ];
    }

    public function getCompletedAppointments($trainerId) {
        return [
            [
                'id' => 3,
                'client_name' => 'Emma Wilson',
                'pet_name' => 'Charlie',
                'pet_breed' => 'Labrador',
                'session_type' => 'Puppy Training',
                'date' => '2025-10-10',
                'time' => '11:00 AM',
                'duration' => '45 min',
                'status' => 'completed',
                'notes' => 'Great progress with leash training'
            ]
        ];
    }
}
