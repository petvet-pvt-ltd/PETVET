<?php
require_once __DIR__ . '/../BaseModel.php';

class AppointmentsModel extends BaseModel {
    public function fetchAppointmentsData(): array {
        return [
            'stats' => [
                'todayTotal' => 12,
                'todayCompleted' => 3,
                'todayInProgress' => 2,
                'todayUpcoming' => 7,
                'weekTotal' => 48,
                'weekGrowth' => '10 more than last week',
                'cancellations' => 3,
                'cancellationRate' => '5% cancellation rate',
                'avgDuration' => '35 min',
                'durationChange' => '2 min less than last month'
            ],
            'appointments' => [
                [
                    'id' => 'APT001',
                    'time' => '09:00',
                    'petName' => 'Buddy',
                    'ownerName' => 'John Smith',
                    'vet' => 'Dr. Sarah Johnson',
                    'reason' => 'Routine Check-up',
                    'status' => 'Completed'
                ],
                [
                    'id' => 'APT002',
                    'time' => '10:30',
                    'petName' => 'Luna',
                    'ownerName' => 'Emma Davis',
                    'vet' => 'Dr. Michael Brown',
                    'reason' => 'Vaccination',
                    'status' => 'Completed'
                ],
                [
                    'id' => 'APT003',
                    'time' => '14:00',
                    'petName' => 'Max',
                    'ownerName' => 'Robert Wilson',
                    'vet' => 'Dr. Sarah Johnson',
                    'reason' => 'Dental Issue',
                    'status' => 'Pending'
                ]
            ]
        ];
    }
}
?>