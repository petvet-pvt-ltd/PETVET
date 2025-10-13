<?php
// app/models/ReportsModel.php
require_once __DIR__ . '/ClinicData.php';
class ReportsModel
{
    // ---------- STATIC MOCK DATA (2 years) ----------
    // date, time, pet, client, vet, status, fee (LKR)
    private array $appointments = [
        // ===== 2023 =====
        ["2023-09-05","09:00","Rocky","John","Dr. Silva","Completed",3200],
        ["2023-09-12","10:30","Luna","Liam","Dr. Perera","Confirmed",3000],
        ["2023-10-08","11:00","Milo","Noah","Dr. Nuwan","Completed",2800],
        ["2023-10-20","14:00","Bella","Sarah","Dr. Silva","Cancelled",0],
        ["2023-11-03","09:15","Max","David","Dr. Perera","Completed",3500],
        ["2023-11-19","16:00","Buddy","Mia","Dr. Silva","No-show",0],
        ["2023-12-06","13:45","Coco","Ava","Dr. Perera","Completed",4000],
        ["2023-12-22","08:30","Duke","Zoe","Dr. Nuwan","Confirmed",3000],

        // ===== 2024 =====
        ["2024-01-07","09:30","Rosie","Emma","Dr. Nuwan","Completed",3100],
        ["2024-01-21","15:10","Lola","James","Dr. Silva","Completed",3300],
        ["2024-02-04","10:00","Rocky","John","Dr. Silva","Cancelled",0],
        ["2024-02-18","12:20","Luna","Liam","Dr. Perera","Completed",3600],
        ["2024-03-05","09:00","Milo","Noah","Dr. Nuwan","Completed",2900],
        ["2024-03-26","11:30","Bella","Sarah","Dr. Silva","Confirmed",3000],
        ["2024-04-02","13:10","Max","David","Dr. Perera","Completed",3700],
        ["2024-04-19","16:45","Buddy","Mia","Dr. Silva","No-show",0],
        ["2024-05-03","08:50","Coco","Ava","Dr. Perera","Completed",4200],
        ["2024-05-28","10:15","Duke","Zoe","Dr. Nuwan","Completed",3000],
        ["2024-06-06","11:40","Rosie","Emma","Dr. Nuwan","Completed",3150],
        ["2024-06-21","14:05","Lola","James","Dr. Silva","Cancelled",0],
        ["2024-07-04","09:25","Rocky","John","Dr. Silva","Completed",3350],
        ["2024-07-23","15:55","Luna","Liam","Dr. Perera","Completed",3650],
        ["2024-08-01","10:10","Milo","Noah","Dr. Nuwan","Confirmed",3000],
        ["2024-08-18","12:50","Bella","Sarah","Dr. Silva","Completed",3450],
        ["2024-09-02","09:05","Max","David","Dr. Perera","Completed",3750],
        ["2024-09-27","16:20","Buddy","Mia","Dr. Silva","Completed",3050],
        ["2024-10-09","11:35","Coco","Ava","Dr. Perera","Completed",4250],
        ["2024-10-25","13:15","Duke","Zoe","Dr. Nuwan","No-show",0],
        ["2024-11-07","08:40","Rosie","Emma","Dr. Nuwan","Completed",3200],
        ["2024-11-22","10:55","Lola","James","Dr. Silva","Completed",3400],
        ["2024-12-05","09:10","Rocky","John","Dr. Silva","Completed",3500],
        ["2024-12-19","15:45","Luna","Liam","Dr. Perera","Cancelled",0],

        // ===== 2025 =====
        ["2025-01-03","09:00","Milo","Noah","Dr. Nuwan","Completed",2950],
        ["2025-01-21","14:30","Bella","Sarah","Dr. Silva","Completed",3550],
        ["2025-02-06","10:20","Max","David","Dr. Perera","Completed",3800],
        ["2025-02-17","16:10","Buddy","Mia","Dr. Silva","Confirmed",3000],
        ["2025-03-04","08:35","Coco","Ava","Dr. Perera","Completed",4300],
        ["2025-03-23","11:25","Duke","Zoe","Dr. Nuwan","Completed",3100],
        ["2025-04-07","13:55","Rosie","Emma","Dr. Nuwan","Completed",3250],
        ["2025-04-24","09:45","Lola","James","Dr. Silva","No-show",0],
        ["2025-05-05","10:05","Rocky","John","Dr. Silva","Completed",3550],
        ["2025-05-20","12:40","Luna","Liam","Dr. Perera","Completed",3700],
        ["2025-06-02","09:30","Milo","Noah","Dr. Nuwan","Completed",3000],
        ["2025-06-18","15:15","Bella","Sarah","Dr. Silva","Cancelled",0],
        ["2025-07-03","11:50","Max","David","Dr. Perera","Completed",3850],
        ["2025-07-22","14:20","Buddy","Mia","Dr. Silva","Completed",3150],
        ["2025-08-05","10:15","Coco","Ava","Dr. Perera","Completed",4350],
        ["2025-08-19","13:35","Duke","Zoe","Dr. Nuwan","Completed",3200],
        ["2025-08-28","16:00","Buddy","Mia","Dr. Silva","No-show",0],
        ["2025-08-29","10:15","Coco","Ava","Dr. Perera","Completed",4000],
        ["2025-08-30","13:45","Milo","Noah","Dr. Silva","Completed",2800],
        ["2025-08-31","08:30","Luna","Liam","Dr. Nuwan","Completed",3000],
        ["2025-08-31","11:00","Duke","Zoe","Dr. Perera","Confirmed",3000],
        ["2025-09-01","11:00","Bella","Sarah","Dr. Silva","Completed",3500],
        ["2025-09-02","10:00","Max","David","Dr. Perera","Completed",3000],
        ["2025-09-03","09:30","Lola","James","Dr. Nuwan","Completed",3200],
        ["2025-09-04","16:00","Buddy","Mia","Dr. Silva","No-show",0],
        ["2025-09-05","10:15","Coco","Ava","Dr. Perera","Completed",4000],
        ["2025-09-06","13:45","Milo","Noah","Dr. Silva","Completed",2800],
        ["2025-09-07","08:30","Luna","Liam","Dr. Nuwan","Completed",3000],
        ["2025-09-07","11:00","Duke","Zoe","Dr. Perera","Confirmed",3000],
    ];

    private array $vets = [];

    // order_date, product, unit_price (LKR), qty, discount (LKR), refund (LKR)
    private array $orders = [
        // ===== 2023 =====
        ["2023-09-06","Dog Food Premium",3500,2,0,0],
        ["2023-09-18","Cat Toy Mouse",700,6,0,0],
        ["2023-10-10","Flea Collar",1200,3,100,0],
        ["2023-10-27","Shampoo",950,4,0,0],
        ["2023-11-08","Dog Food Premium",3500,2,0,0],
        ["2023-11-25","Cat Toy Mouse",700,5,0,0],
        ["2023-12-07","Flea Collar",1200,2,0,0],
        ["2023-12-20","Dog Food Premium",3500,1,0,350],  // refund

        // ===== 2024 =====
        ["2024-01-09","Shampoo",950,5,0,0],
        ["2024-01-26","Dog Food Premium",3500,3,0,0],
        ["2024-02-05","Cat Toy Mouse",700,7,0,0],
        ["2024-02-22","Flea Collar",1200,2,100,0],
        ["2024-03-08","Dog Food Premium",3500,2,0,0],
        ["2024-03-24","Shampoo",950,4,0,0],
        ["2024-04-04","Flea Collar",1200,3,0,0],
        ["2024-04-21","Cat Toy Mouse",700,6,0,0],
        ["2024-05-06","Dog Food Premium",3500,3,0,0],
        ["2024-05-29","Shampoo",950,4,0,0],
        ["2024-06-07","Flea Collar",1200,2,0,0],
        ["2024-06-25","Dog Food Premium",3500,2,0,350], // refund
        ["2024-07-05","Cat Toy Mouse",700,6,0,0],
        ["2024-07-24","Shampoo",950,5,0,0],
        ["2024-08-03","Flea Collar",1200,2,0,0],
        ["2024-08-19","Dog Food Premium",3500,3,0,0],
        ["2024-09-04","Cat Toy Mouse",700,6,0,0],
        ["2024-09-28","Flea Collar",1200,2,100,0],
        ["2024-10-12","Dog Food Premium",3500,2,0,0],
        ["2024-10-29","Shampoo",950,4,0,0],
        ["2024-11-10","Flea Collar",1200,3,0,0],
        ["2024-11-23","Cat Toy Mouse",700,5,0,0],
        ["2024-12-06","Dog Food Premium",3500,2,0,0],
        ["2024-12-21","Shampoo",950,4,0,0],

        // ===== 2025 =====
        ["2025-01-08","Flea Collar",1200,3,100,0],
        ["2025-01-25","Dog Food Premium",3500,3,0,0],
        ["2025-02-07","Cat Toy Mouse",700,7,0,0],
        ["2025-02-20","Shampoo",950,5,0,0],
        ["2025-03-06","Flea Collar",1200,2,0,0],
        ["2025-03-25","Dog Food Premium",3500,2,0,0],
        ["2025-04-09","Cat Toy Mouse",700,6,0,0],
        ["2025-04-26","Flea Collar",1200,3,0,0],
        ["2025-05-07","Dog Food Premium",3500,3,0,0],
        ["2025-05-23","Shampoo",950,4,0,0],
        ["2025-06-05","Flea Collar",1200,2,0,0],
        ["2025-06-24","Dog Food Premium",3500,2,0,350], // refund
        ["2025-07-09","Cat Toy Mouse",700,6,0,0],
        ["2025-07-26","Shampoo",950,5,0,0],
        ["2025-08-06","Flea Collar",1200,2,0,0],
        ["2025-08-19","Dog Food Premium",3500,3,0,0],
        ["2025-09-01","Cat Toy Mouse",700,5,0,0],
        ["2025-09-02","Dog Food Premium",3500,2,0,0],
        ["2025-09-03","Shampoo",950,4,0,0],
        ["2025-09-07","Flea Collar",1200,1,0,0],
    ];

    // date, category, amount (LKR)
    private array $expenses = [
        // monthly baseline across 2023–2025
        ["2023-09-15","Utilities",8000], ["2023-09-28","Supplies",4200],
        ["2023-10-15","Utilities",8100], ["2023-10-28","Supplies",3900],
        ["2023-11-15","Utilities",8200], ["2023-11-28","Supplies",4100],
        ["2023-12-15","Utilities",8300], ["2023-12-28","Supplies",4300],

        ["2024-01-15","Utilities",8400], ["2024-01-28","Supplies",4000],
        ["2024-02-15","Utilities",8450], ["2024-02-28","Supplies",4050],
        ["2024-03-15","Utilities",8500], ["2024-03-28","Supplies",4200],
        ["2024-04-15","Utilities",8550], ["2024-04-28","Supplies",4150],
        ["2024-05-15","Utilities",8600], ["2024-05-28","Supplies",4250],
        ["2024-06-15","Utilities",8650], ["2024-06-28","Supplies",4350],
        ["2024-07-15","Utilities",8700], ["2024-07-28","Supplies",4400],
        ["2024-08-15","Utilities",8750], ["2024-08-28","Supplies",4450],
        ["2024-09-15","Utilities",8800], ["2024-09-28","Supplies",4500],
        ["2024-10-15","Utilities",8850], ["2024-10-28","Supplies",4550],
        ["2024-11-15","Utilities",8900], ["2024-11-28","Supplies",4600],
        ["2024-12-15","Utilities",8950], ["2024-12-28","Supplies",4650],

        ["2025-01-15","Utilities",9000], ["2025-01-28","Supplies",4700],
        ["2025-02-15","Utilities",9050], ["2025-02-28","Supplies",4750],
        ["2025-03-15","Utilities",9100], ["2025-03-28","Supplies",4800],
        ["2025-04-15","Utilities",9150], ["2025-04-28","Supplies",4850],
        ["2025-05-15","Utilities",9200], ["2025-05-28","Supplies",4900],
        ["2025-06-15","Utilities",9250], ["2025-06-28","Supplies",4950],
        ["2025-07-15","Utilities",9300], ["2025-07-28","Supplies",5000],
        ["2025-08-15","Utilities",9350], ["2025-08-28","Supplies",5050],
        ["2025-09-15","Utilities",9400], ["2025-09-28","Supplies",5100],
    ];
    // ------------------------------------------------

    /** Optional: keep for compatibility */
    public function getWeeklyReport(string $today): array
    {
        $start = $this->startOfWeek($today);
        $end   = $this->endOfWeek($today);
        return $this->buildReport($start, $end, 'week');
    }

    /** Main entry used by controller: $mode in ['week','month','year','custom'] */
    public function getReport(string $from, string $to, string $mode = 'custom'): array
    {
        return $this->buildReport($from, $to, $mode);
    }

    // ------------------------- Core aggregation -------------------------
    private function buildReport(string $rangeStart, string $rangeEnd, string $mode = 'week'): array
    {
        $mode = in_array($mode, ['week','month','year','custom']) ? $mode : 'week';

        // prepare chart buckets
        [$bucketKeys, $bucketLabels] = $this->makeBuckets($rangeStart, $rangeEnd, $mode);

        // Sync vets list from centralized ClinicData for consistency
        if (empty($this->vets)) {
            $this->vets = array_map(fn($v)=>$v['name'], ClinicData::getVets());
        }
        $apptStatus = ["Confirmed"=>0,"Completed"=>0,"Cancelled"=>0,"No-show"=>0];
        $workload   = array_fill_keys($this->vets, 0);
        $appointmentsRevenue = 0;

        $apptByBucket = [];
        foreach ($bucketKeys as $k) { $apptByBucket[$k] = 0; }

        foreach ($this->appointments as [$d,$t,$pet,$client,$vet,$status,$fee]) {
            if (!$this->inRange($d,$rangeStart,$rangeEnd)) continue;

            if (!isset($apptStatus[$status])) $apptStatus[$status] = 0;
            $apptStatus[$status]++;
            if (!isset($workload[$vet])) {
                // In case historical data contains a vet no longer in current list
                $workload[$vet] = 0;
            }
            $workload[$vet]++;

            if ($status === "Completed") {
                $appointmentsRevenue += $fee;
                $k = $this->bucketKeyForDate($d, $mode);
                if (isset($apptByBucket[$k])) $apptByBucket[$k] += $fee;
            }
        }

        $productTotals = [];
        $shopRevenue = 0; $totalDiscounts = 0; $totalRefunds = 0;

        foreach ($this->orders as [$od,$product,$price,$qty,$discount,$refund]) {
            if (!$this->inRange($od,$rangeStart,$rangeEnd)) continue;
            $lineTotal = ($price * $qty) - $discount - $refund;
            $shopRevenue     += $lineTotal;
            $totalDiscounts  += $discount;
            $totalRefunds    += $refund;
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

        $labels = [];
        $bars   = [];
        foreach ($bucketKeys as $i => $key) {
            $labels[] = $bucketLabels[$i];
            $bars[]   = $apptByBucket[$key] ?? 0;
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
            'mode'         => $mode,
        ];
    }

    // -------------------------- Bucketing logic -------------------------
    private function makeBuckets(string $start, string $end, string $mode): array
    {
        $keys = []; $labels = [];

        if ($mode === 'year') {
            $cur = strtotime($this->startOfYear($start));
            for ($m = 1; $m <= 12; $m++) {
                $keys[]   = date('Y-m', $cur);
                $labels[] = date('M', $cur);
                $cur = strtotime('+1 month', $cur);
            }
            return [$keys, $labels];
        }

        if ($mode === 'week') {
            $s = strtotime($this->startOfWeek($start));
            for ($i=0; $i<7; $i++) {
                $d = date('Y-m-d', strtotime("+$i day", $s));
                $keys[]   = $d;
                $labels[] = date('D', strtotime($d));
            }
            return [$keys, $labels];
        }

        // month | custom -> per-day buckets
        $cur  = strtotime($start);
        $last = strtotime($end);
        while ($cur <= $last) {
            $d = date('Y-m-d', $cur);
            $keys[]   = $d;
            $labels[] = date('j', $cur); // 1..31
            $cur = strtotime('+1 day', $cur);
        }
        return [$keys, $labels];
    }

    private function bucketKeyForDate(string $date, string $mode): string
    {
        if ($mode === 'year')  return date('Y-m', strtotime($date));
        return date('Y-m-d', strtotime($date)); // week/month/custom -> per-day
    }

    // ------------------------------- Utils -------------------------------
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
    private function startOfYear(string $anyDate): string { return date('Y-01-01', strtotime($anyDate)); }
    private function endOfYear(string $anyDate): string   { return date('Y-12-31', strtotime($anyDate)); }
}
