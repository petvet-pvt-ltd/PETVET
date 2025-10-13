<?php
require_once __DIR__ . '/../BaseModel.php';

class VaccinationsModel extends BaseModel {
    public function fetchVaccinationsData(): array {
        return [
            'appointments' => [
                ['id' => 1, 'pet' => 'Bella', 'owner' => 'John Doe', 'status' => 'Ongoing', 'date' => '2025-10-10'],
                ['id' => 2, 'pet' => 'Max', 'owner' => 'Jane Smith', 'status' => 'Completed', 'date' => '2025-09-28'],
                ['id' => 3, 'pet' => 'Luna', 'owner' => 'Chris Brown', 'status' => 'Cancelled', 'date' => '2025-09-15'],
                ['id' => 4, 'pet' => 'Charlie', 'owner' => 'Emily Clark', 'status' => 'Upcoming', 'date' => '2025-10-20'],
                ['id' => 5, 'pet' => 'Lucy', 'owner' => 'Michael Adams', 'status' => 'Completed', 'date' => '2025-09-25'],
                ['id' => 6, 'pet' => 'Cooper', 'owner' => 'Olivia Harris', 'status' => 'Completed', 'date' => '2025-09-22']
            ],
            'vaccinations' => [
                ['id' => 1, 'appointmentId' => 2, 'pet' => 'Max', 'owner' => 'Jane Smith', 'vaccine' => 'Rabies', 'nextDue' => '2026-09-28', 'date' => '2025-09-28'],
                ['id' => 2, 'appointmentId' => 5, 'pet' => 'Lucy', 'owner' => 'Michael Adams', 'vaccine' => 'DHPP', 'nextDue' => '2026-09-25', 'date' => '2025-09-25'],
                ['id' => 3, 'appointmentId' => 6, 'pet' => 'Cooper', 'owner' => 'Olivia Harris', 'vaccine' => 'Bordetella', 'nextDue' => '2026-03-22', 'date' => '2025-09-22']
            ]
        ];
    }
}
?>