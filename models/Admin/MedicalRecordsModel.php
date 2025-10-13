<?php
require_once __DIR__ . '/../BaseModel.php';

class MedicalRecordsModel extends BaseModel {
    public function fetchMedicalRecordsData(): array {
        return [
            'stats' => [
                'totalRecords' => 120,
                'newThisWeek' => '10 new this week',
                'vaccinations' => 45,
                'vaccinationNote' => 'Updated regularly',
                'chronicConditions' => 12,
                'chronicNote' => 'Require ongoing care',
                'lastUpdated' => '2 hrs ago',
                'updateNote' => 'Latest entry logged'
            ],
            'records' => [
                [
                    'id' => 'MR-1001',
                    'petName' => 'Buddy',
                    'ownerName' => 'John Smith',
                    'condition' => 'Routine Check-up',
                    'date' => '2023-07-15',
                    'vet' => 'Dr. Sarah Johnson',
                    'status' => 'Completed'
                ],
                [
                    'id' => 'MR-1002',
                    'petName' => 'Luna',
                    'ownerName' => 'Emma Davis',
                    'condition' => 'Vaccination',
                    'date' => '2023-07-14',
                    'vet' => 'Dr. Michael Brown',
                    'status' => 'Completed'
                ],
                [
                    'id' => 'MR-1003',
                    'petName' => 'Max',
                    'ownerName' => 'Robert Wilson',
                    'condition' => 'Dental Cleaning',
                    'date' => '2023-07-13',
                    'vet' => 'Dr. Sarah Johnson',
                    'status' => 'Completed'
                ]
            ]
        ];
    }
}
?>