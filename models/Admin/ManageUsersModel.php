<?php
require_once __DIR__ . '/../BaseModel.php';

class ManageUsersModel extends BaseModel {
    public function fetchUsersData(): array {
        return [
            'stats' => [
                'totalUsers' => 842,
                'usersGrowth' => '+12%',
                'activeUsers' => 765,
                'activePercent' => '91%',
                'vets' => 48,
                'clinicManagers' => 29
            ],
            'users' => [
                [
                    'id' => 'USR001',
                    'name' => 'Dr. Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'role' => 'Vet',
                    'joinDate' => '2023-01-15',
                    'lastLogin' => '2023-07-12',
                    'status' => 'Active'
                ],
                [
                    'id' => 'USR002', 
                    'name' => 'Mike Wilson',
                    'email' => 'mike.wilson@example.com',
                    'role' => 'Pet Owner',
                    'joinDate' => '2023-02-20',
                    'lastLogin' => '2023-07-15',
                    'status' => 'Active'
                ],
                [
                    'id' => 'USR003',
                    'name' => 'Lisa Garcia',
                    'email' => 'lisa.garcia@example.com', 
                    'role' => 'Clinic Manager',
                    'joinDate' => '2023-03-10',
                    'lastLogin' => '2023-07-14',
                    'status' => 'Active'
                ],
                [
                    'id' => 'USR004',
                    'name' => 'John Smith',
                    'email' => 'john.smith@example.com',
                    'role' => 'Pet Owner',
                    'joinDate' => '2023-04-05',
                    'lastLogin' => '2023-06-28',
                    'status' => 'Inactive'
                ]
            ]
        ];
    }
}
?>