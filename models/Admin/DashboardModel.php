<?php
require_once __DIR__ . '/../BaseModel.php';

class DashboardModel extends BaseModel {
    public function fetchDashboardData(): array {
        return [
            'totalUsers' => 12548,
            'usersGrowth' => '+12%',
            'totalAppointments' => 1352,
            'appointmentsChange' => '-8%',
            'totalRevenue' => 48260,
            'revenueGrowth' => '+18%',
            'totalPets' => 832,
            'petsChange' => '+5%',
            'chartData' => [
                'barChart' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'data' => [65, 78, 90, 81, 56, 85]
                ],
                'pieChart' => [
                    'labels' => ['Pet Owners', 'Vets', 'Clinic Managers'],
                    'data' => [765, 48, 29]
                ]
            ]
        ];
    }
}
?>