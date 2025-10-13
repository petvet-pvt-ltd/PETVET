<?php
require_once __DIR__ . '/../BaseModel.php';
// app/models/ReportsModel.php
class ReportsModel extends BaseModel
{
    // ---------- MOCK DATA (replace with real DB queries later) ----------
    private array $appointments = [
        // date, time, pet, client, vet, status, fee (LKR)
        ["2025-09-01","09:00","Rocky","John","Dr. Silva","Confirmed",3000],
        ["2025-09-01","11:00","Bella","Sarah","Dr. Silva","Completed",3500],
        ["2025-09-02","10:00","Max","David","Dr. Perera","Completed",3000],
        ["2025-09-02","14:00","Rosie","Emma","Dr. Nuwan","Cancelled",0],
        ["2025-09-03","09:30","Lola","James","Dr. Nuwan","Completed",3200],
        ["2025-09-04","16:00","Buddy","Mia","Dr. Silva","No-show",0],
        ["2025-09-05","10:15","Coco","Ava","Dr. Perera","Completed",4000],
        ["2025-09-06","13:45","Milo","Noah","Dr. Silva","Completed",2800],
        ["2025-09-07","08:30","Luna","Liam","Dr. Nuwan","Completed",3000],
        ["2025-09-07","11:00","Duke","Zoe","Dr. Perera","Confirmed",3000],
        ["2025-08-28","16:00","Buddy","Mia","Dr. Silva","No-show",0],
        ["2025-08-29","10:15","Coco","Ava","Dr. Perera","Completed",4000],
        ["2025-08-30","13:45","Milo","Noah","Dr. Silva","Completed",2800],
        ["2025-08-31","08:30","Luna","Liam","Dr. Nuwan","Completed",3000],
        ["2025-08-31","11:00","Duke","Zoe","Dr. Perera","Confirmed",3000],
    ];

    private array $vets = ["Dr. Silva","Dr. Perera","Dr. Nuwan"];

    private array $orders = [
    // order_date, product, unit_price (LKR), qty, discount (LKR), refund (LKR)
    ["2025-09-01","Dog Food Premium",3500,3,0,0],
    ["2025-09-01","Cat Toy Mouse",700,5,0,0],
    ["2025-09-02","Flea Collar",1200,2,100,0],
    ["2025-09-03","Shampoo",950,4,0,0],
    ["2025-09-04","Dog Food Premium",3500,2,0,0],
    ["2025-09-05","Cat Toy Mouse",700,6,0,0],
    ["2025-09-06","Flea Collar",1200,1,0,0],
    ["2025-09-07","Dog Food Premium",3500,1,0,350], // refund
    ];

    private array $expenses = [
    // date, category, amount (LKR)
    ["2025-09-02","Utilities",8500],
    ["2025-09-04","Supplies",4200],
    ];
    // -------------------------------------------------------------------

    public function getWeeklyReport(string $today): array
    {
        $start = $this->startOfWeek($today);
        $end   = $this->endOfWeek($today);
        return $this->buildReport($start, $end);
    }

    public function getReport(string $from, string $to): array
    {
        return $this->buildReport($from, $to);
    }

    // Core aggregation
    private function buildReport(string $rangeStart, string $rangeEnd): array
    {
        $apptStatus = ["Confirmed"=>0,"Completed"=>0,"Cancelled"=>0,"No-show"=>0];
        $workload   = array_fill_keys($this->vets, 0);
        $apptByDay  = [];
        $appointmentsRevenue = 0;

        foreach ($this->appointments as [$d,$t,$pet,$client,$vet,$status,$fee]) {
            if (!$this->inRange($d,$rangeStart,$rangeEnd)) continue;
            if (!isset($apptStatus[$status])) $apptStatus[$status] = 0;
            $apptStatus[$status]++;
            if (isset($workload[$vet])) $workload[$vet]++;
            if ($status === "Completed") {
                $appointmentsRevenue += $fee;
                $apptByDay[$d] = ($apptByDay[$d] ?? 0) + $fee;
            }
        }

        $productTotals = [];
        $shopRevenue = 0; $totalDiscounts = 0; $totalRefunds = 0;

        foreach ($this->orders as [$od,$product,$price,$qty,$discount,$refund]) {
            if (!$this->inRange($od,$rangeStart,$rangeEnd)) continue;
            $lineTotal = ($price * $qty) - $discount - $refund;
            $shopRevenue += $lineTotal;
            $totalDiscounts += $discount;
            $totalRefunds   += $refund;
            $productTotals[$product] = ($productTotals[$product] ?? 0) + $qty;
        }

        $totalExpenses = 0;
        foreach ($this->expenses as [$ed,$cat,$amt]) {
            if ($this->inRange($ed,$rangeStart,$rangeEnd)) $totalExpenses += $amt;
        }

        arsort($productTotals);
        arsort($workload);

        $grossRevenue = $appointmentsRevenue + $shopRevenue;
        $netIncome    = $grossRevenue - $totalRefunds - $totalExpenses;

        // Bars for 7 days
        $labels = []; $bars = [];
        for ($i=0; $i<7; $i++){
            $d = date('Y-m-d', strtotime("$rangeStart +$i day"));
            $labels[] = date('D', strtotime($d));
            $bars[] = $apptByDay[$d] ?? 0;
        }
        $maxBar = max(1, max($bars));

        $clinicPct = $grossRevenue>0 ? round(($appointmentsRevenue/$grossRevenue)*100) : 0;
        $shopPct   = 100 - $clinicPct;

        return [
            'rangeStart' => $rangeStart,
            'rangeEnd'   => $rangeEnd,
            'apptStatus' => $apptStatus,
            'workload'   => $workload,
            'productTotals' => $productTotals,
            'appointmentsRevenue' => $appointmentsRevenue,
            'shopRevenue' => $shopRevenue,
            'totalRefunds' => $totalRefunds,
            'totalExpenses'=> $totalExpenses,
            'grossRevenue' => $grossRevenue,
            'netIncome'    => $netIncome,
            'labels'       => $labels,
            'bars'         => $bars,
            'maxBar'       => $maxBar,
            'clinicPct'    => $clinicPct,
            'shopPct'      => $shopPct,
        ];
    }

    // Utils
    private function inRange(string $date, string $start, string $end): bool {
        return $date >= $start && $date <= $end;
    }
    private function startOfWeek(string $anyDate): string {
        $ts = strtotime($anyDate); $dow = date('N', $ts);
        return date('Y-m-d', strtotime("-".($dow-1)." days", $ts));
    }
    private function endOfWeek(string $anyDate): string {
        return date('Y-m-d', strtotime($this->startOfWeek($anyDate).' +6 days'));
    }
}
