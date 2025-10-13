<?php
require_once __DIR__ . '/../BaseModel.php';

class PrescriptionsModel extends BaseModel {
    public function fetchPrescriptionsData(): array {
        return [
            'appointments' => [
                ['id' => 1, 'pet' => 'Bella', 'owner' => 'John Doe', 'status' => 'Ongoing', 'date' => '2025-10-10'],
                ['id' => 2, 'pet' => 'Max', 'owner' => 'Jane Smith', 'status' => 'Completed', 'date' => '2025-09-28'],
                ['id' => 3, 'pet' => 'Luna', 'owner' => 'Chris Brown', 'status' => 'Cancelled', 'date' => '2025-09-15'],
                ['id' => 4, 'pet' => 'Charlie', 'owner' => 'Emily Clark', 'status' => 'Upcoming', 'date' => '2025-10-20'],
                ['id' => 5, 'pet' => 'Lucy', 'owner' => 'Michael Adams', 'status' => 'Completed', 'date' => '2025-09-25'],
                ['id' => 6, 'pet' => 'Cooper', 'owner' => 'Olivia Harris', 'status' => 'Completed', 'date' => '2025-09-22']
            ],
            'prescriptions' => [
                ['id' => 1, 'appointmentId' => 2, 'pet' => 'Max', 'owner' => 'Jane Smith', 'medication' => 'Antibiotics', 'dosage' => '250mg', 'notes' => 'Take twice daily', 'date' => '2025-09-28'],
                ['id' => 2, 'appointmentId' => 5, 'pet' => 'Lucy', 'owner' => 'Michael Adams', 'medication' => 'Pain reliever', 'dosage' => '100mg', 'notes' => 'Once daily with food', 'date' => '2025-09-25'],
                ['id' => 3, 'appointmentId' => 6, 'pet' => 'Cooper', 'owner' => 'Olivia Harris', 'medication' => 'Anti-inflammatory', 'dosage' => '50mg', 'notes' => 'Morning and evening', 'date' => '2025-09-22']
            ]
        ];
    }
}
?>