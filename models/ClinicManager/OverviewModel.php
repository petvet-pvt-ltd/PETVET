<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/ClinicData.php';
class OverviewModel extends BaseModel {
    public function fetchOverviewData(): array {
        // Load configuration
        $cfgPath = __DIR__ . '/../../config/clinic_manager.php';
        $cfg = file_exists($cfgPath) ? require $cfgPath : [
            'timezone' => 'Asia/Colombo',
            'slot_duration_minutes' => 60
        ];
        date_default_timezone_set($cfg['timezone'] ?? 'Asia/Colombo');
        $vets = ClinicData::getVets();
        $vetsById = [];
        foreach($vets as $v){ $vetsById[$v['id']] = $v; }
        $schedule = ClinicData::getAppointmentsSchedule();
        $today = date('Y-m-d');
        $todayAppointmentsRaw = $schedule[$today] ?? [];
        $appointments = [];
        foreach($todayAppointmentsRaw as $a){
            $vid = $a['vet_id'];
            $appointments[] = [
                'time' => $a['time'],
                'pet' => $a['pet'],
                'animal' => $a['animal'] ?? 'Pet',
                'client' => $a['client'],
                'vet' => $vetsById[$vid]['name'] ?? 'Unknown Vet',
                'status' => $a['status'],
                'type' => $a['type'] ?? 'Checkup'
            ];
        }
        // Build ongoing appointments by vet (current timeslot, 60 min window)
        $nowTs = time();
    $slotMinutes = (int)($cfg['slot_duration_minutes'] ?? 60);
        // Optional debug override: pass ?debugNow=HH:MM or YYYY-MM-DD HH:MM to preview ongoing logic
        if (!empty($_GET['debugNow'])) {
            $debug = trim($_GET['debugNow']);
            $today = date('Y-m-d');
            if (preg_match('/^\d{2}:\d{2}$/', $debug)) {
                $ts = strtotime($today . ' ' . $debug);
                if ($ts !== false) { $nowTs = $ts; }
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/', $debug)) {
                $ts = strtotime($debug);
                if ($ts !== false) { $nowTs = $ts; }
            }
        }
        // Build map for quick lookup: for each vet today's entries
        $byVet = [];
        foreach ($todayAppointmentsRaw as $a) {
            $vid = $a['vet_id'];
            $startTs = strtotime($today . ' ' . $a['time']);
            $endTs = $startTs + ($slotMinutes * 60);
            $isOngoing = ($nowTs >= $startTs && $nowTs < $endTs && $a['status'] !== 'Completed');
            if ($isOngoing) {
                $byVet[$vid] = [
                    'vet' => $vetsById[$vid]['name'] ?? 'Unknown Vet',
                    'animal' => $a['animal'] ?? 'Pet',
                    'client' => $a['client'],
                    'type' => $a['type'] ?? 'Checkup',
                    'time_range' => date('H:i', $startTs) . ' – ' . date('H:i', $endTs)
                ];
            }
        }
        // Determine staff on duty (vets) and compose normalized list
        $activeVets = array_filter($vets, fn($v)=>$v['status']==='Active');
        // Staff on duty vets from on_duty_dates
        $dutyVets = array_filter($vets, fn($v)=>in_array($today, $v['on_duty_dates']));
        $staff = [
            'Veterinarians' => array_map(fn($v)=>[
                'name'=>$v['name'],
                'time'=>'09:00 – 17:00',
                'status'=>'online'
            ], $dutyVets),
            'Veterinary Assistants' => [
                ['name'=>'Lisa','time'=>'08:00 – 16:00','status'=>'online']
            ],
            'Front Desk' => [
                ['name'=>'Sarah','time'=>'08:00 – 14:00','status'=>'online']
            ],
            'Support Staff' => [
                ['name'=>'John','time'=>'11:00 – 19:00','status'=>'online']
            ],
        ];
        $staffCount = 0; foreach($staff as $group){ $staffCount += count($group); }
        $kpis = [
            ['label'=>'Appointments Today','value'=>count($appointments)],
            ['label'=>'Active Vets','value'=>count($activeVets)],
            ['label'=>'Pending Shop Orders','value'=>4],
            ['label'=>'Staff on Duty Today','value'=>$staffCount],
        ];
        $badgeClasses = [
            'Confirmed'=>'badge-confirmed',
            'Completed'=>'badge-completed',
        ];
        // Normalized list of ongoing appointments by vet on duty
        $ongoing = [];
        foreach ($dutyVets as $v) {
            $vid = $v['id'];
            if (isset($byVet[$vid])) {
                $rec = $byVet[$vid];
                $ongoing[] = [
                    'vet' => $rec['vet'],
                    'hasAppointment' => true,
                    'animal' => $rec['animal'],
                    'client' => $rec['client'],
                    'type' => $rec['type'],
                    'time_range' => $rec['time_range']
                ];
            } else {
                $ongoing[] = [
                    'vet' => $v['name'],
                    'hasAppointment' => false
                ];
            }
        }
        return [
            'kpis'=>$kpis,
            'appointments'=>$appointments,
            'ongoingAppointments'=>$ongoing,
            'staff'=>$staff,
            'badgeClasses'=>$badgeClasses,
        ];
    }
}
?>