<?php
require_once __DIR__ . '/../BaseModel.php';

class FinancePanelModel extends BaseModel {
    public function fetchFinanceData(): array {
        return [
            'stats' => [
                'totalRevenue' => '$48,260',
                'revenueGrowth' => '↑ +18%',
                'expenses' => '$23,580',
                'expensesGrowth' => '↑ +5%',
                'netProfit' => '$24,680',
                'profitGrowth' => '↑ +32%',
                'pendingPayments' => '$3,450',
                'pendingGrowth' => '↓ -8%'
            ],
            'transactions' => [
                [
                    'invoiceId' => 'INV-1023',
                    'customer' => 'John Smith',
                    'service' => 'Pet Checkup',
                    'amount' => '$120',
                    'date' => '2023-07-15',
                    'status' => 'Paid'
                ],
                [
                    'invoiceId' => 'INV-1022',
                    'customer' => 'Sarah Johnson',
                    'service' => 'Dental Cleaning',
                    'amount' => '$185.50',
                    'date' => '2023-07-15',
                    'status' => 'Paid'
                ],
                [
                    'invoiceId' => 'INV-1021',
                    'customer' => 'Michael Brown',
                    'service' => 'Surgery',
                    'amount' => '$350',
                    'date' => '2023-07-14',
                    'status' => 'Pending'
                ]
            ],
            'expenses' => [
                [
                    'expenseId' => 'EXP-1023',
                    'category' => 'Medical Supplies',
                    'vendor' => 'PetMed Inc',
                    'amount' => '$1,250',
                    'date' => '2023-07-10',
                    'status' => 'Paid'
                ],
                [
                    'expenseId' => 'EXP-1022',
                    'category' => 'Staff Salaries',
                    'vendor' => 'Payroll',
                    'amount' => '$8,500',
                    'date' => '2023-07-01',
                    'status' => 'Paid'
                ],
                [
                    'expenseId' => 'EXP-1021',
                    'category' => 'Rent',
                    'vendor' => 'Property LLC',
                    'amount' => '$3,200',
                    'date' => '2023-07-01',
                    'status' => 'Paid'
                ]
            ]
        ];
    }
}
?>