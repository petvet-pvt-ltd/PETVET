<?php
/**
 * Centralized mock data repository for Clinic Manager module.
 * Replace with real DB queries later; ensures consistency across pages.
 */
class ClinicData {
    public static function getVets(): array {
        $today = date('Y-m-d');
        return [
            [
                'id'=>1,'name'=>'Dr. Robert Fox','photo'=>'https://i.pravatar.cc/64?img=11',
                'specialization'=>'General','phone'=>'02 9445 6721','email'=>'rob@clinic.lk',
                'status'=>'Active','next_slot'=> date('Y-m-d 09:00:00', strtotime('+1 day')),
                'on_duty_dates'=>[$today, date('Y-m-d', strtotime('+2 day'))]
            ],
            [
                'id'=>2,'name'=>'Theresa Webb','photo'=>'https://i.pravatar.cc/64?img=48',
                'specialization'=>'Surgery','phone'=>'031 452 4910','email'=>'theresa@clinic.lk',
                'status'=>'On Leave','next_slot'=>'',
                'on_duty_dates'=>[]
            ],
            [
                'id'=>3,'name'=>'Marvin McKinney','photo'=>'https://i.pravatar.cc/64?img=34',
                'specialization'=>'Dental','phone'=>'02 9445 6721','email'=>'marvin@clinic.lk',
                'status'=>'Active','next_slot'=> date('Y-m-d 13:30:00', strtotime('+2 day')),
                'on_duty_dates'=>[$today]
            ],
            [
                'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
                'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
                'status'=>'Suspended','next_slot'=>'',
                'on_duty_dates'=>[]
            ],
        ];
    }

    /**
     * Returns mapping date => appointments with vet_id references.
     */
    public static function getAppointmentsSchedule(): array {
        $cfgPath = __DIR__ . '/../../config/clinic_manager.php';
        $cfg = file_exists($cfgPath) ? require $cfgPath : [
            'timezone' => 'Asia/Colombo',
            'slot_duration_minutes' => 20,
            'slots' => ['09:00','10:30','13:00','15:30']
        ];
        // Ensure timezone consistency across pages
        date_default_timezone_set($cfg['timezone'] ?? 'Asia/Colombo');
        $schedule = [];
        $pets = ['Rocky','Bella','Max','Rosie','Lola','Coco','Milo','Shadow','Oreo','Daisy','Buddy','Mochi','Simba','Ginger','Snowy','Pablo','Loki','Pepper','Zara'];
        $animalTypes = ['Dog','Cat','Rabbit','Bird','Hamster','Turtle'];
        $apptTypes = ['Checkup','Vaccination','Dental Cleaning','Surgery Consult','Follow-up'];
        $clients = ['John','Sarah','David','Emma','James','Kevin','Ravi','Dilani','Tharindu','Sanduni','Isuru','Harsha','Rashmi','Anushka','Priyan','Vishva','Janani','Mahesh','Madhavi'];
        $statuses = ['Confirmed','Confirmed','Confirmed','Confirmed','Confirmed','Confirmed','Confirmed','Confirmed','Completed']; // Only ~11% completed (3 out of 28 appointments)
        $vetIds = array_column(self::getVets(),'id');
        $dayCount = 7; // next 7 days including today
        $pi = 0;
        $slots = $cfg['slots'] ?? ['09:00','10:30','13:00','15:30'];
        $slotMinutes = (int)($cfg['slot_duration_minutes'] ?? 20);
        $nowTs = time();
        $today = date('Y-m-d');
        for($d=0;$d<$dayCount;$d++){
            $date = date('Y-m-d', strtotime("+{$d} day"));
            $daily = [];
            // Deterministic: always include all slots for consistency
            foreach($slots as $time){
                $pet = $pets[$pi % count($pets)];
                $animal = $animalTypes[$pi % count($animalTypes)];
                $client = $clients[$pi % count($clients)];
                $vetId = $vetIds[$pi % count($vetIds)];
                $type = $apptTypes[$pi % count($apptTypes)];
                $status = $statuses[$pi % count($statuses)];
                $daily[] = [
                    'pet'=>$pet,
                    'animal'=>$animal,
                    'client'=>$client,
                    'vet_id'=>$vetId,
                    'time'=>$time,
                    'type'=>$type,
                    'status'=>$status
                ];
                $pi++;
            }
            // Insert a dynamic "ongoing now" appointment for today to aid testing
            if ($date === $today) {
                $hour = (int)date('H', $nowTs);
                $minute = (int)date('i', $nowTs);
                $rounded = (int)floor($minute / max(1,$slotMinutes)) * max(1,$slotMinutes);
                if ($rounded > 59) { $rounded = 59; } // safety
                $startTimeStr = sprintf('%02d:%02d', $hour, $rounded);
                $pet = $pets[$pi % count($pets)];
                $animal = $animalTypes[$pi % count($animalTypes)];
                $client = $clients[$pi % count($clients)];
                $vetId = $vetIds[$pi % count($vetIds)];
                $type = $apptTypes[$pi % count($apptTypes)];
                $daily[] = [
                    'pet'=>$pet,
                    'animal'=>$animal,
                    'client'=>$client,
                    'vet_id'=>$vetId,
                    'time'=>$startTimeStr,
                    'type'=>$type,
                    'status'=>'Confirmed'
                ];
                $pi++;
            }
            $schedule[$date] = $daily;
        }
        return $schedule;
    }

    /**
     * Returns pending veterinarian registration requests
     */
    public static function getPendingVetRequests(): array {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Emily Carter',
                'photo' => 'https://i.pravatar.cc/64?img=47',
                'specialization' => 'Dermatology',
                'license' => 'LIC-2024-4521',
                'experience' => '5 years experience',
                'email' => 'emily.carter@email.com',
                'phone' => '077 123 4567',
                'docs' => [
                    ['label' => 'License Certificate', 'url' => '#'],
                    ['label' => 'Resume', 'url' => '#'],
                    ['label' => 'Degree', 'url' => '#']
                ]
            ],
            [
                'id' => 2,
                'name' => 'Dr. Michael Zhang',
                'photo' => 'https://i.pravatar.cc/64?img=33',
                'specialization' => 'Radiology',
                'license' => 'LIC-2024-7832',
                'experience' => '8 years experience',
                'email' => 'michael.zhang@email.com',
                'phone' => '071 987 6543',
                'docs' => [
                    ['label' => 'License Certificate', 'url' => '#'],
                    ['label' => 'Resume', 'url' => '#'],
                    ['label' => 'Work References', 'url' => '#']
                ]
            ],
            [
                'id' => 3,
                'name' => 'Dr. Sarah Johnson',
                'photo' => 'https://i.pravatar.cc/64?img=45',
                'specialization' => 'Surgery',
                'license' => 'LIC-2024-9156',
                'experience' => '12 years experience',
                'email' => 'sarah.j@email.com',
                'phone' => '076 555 1234',
                'docs' => [
                    ['label' => 'License Certificate', 'url' => '#'],
                    ['label' => 'Resume', 'url' => '#'],
                    ['label' => 'Specialization Certificate', 'url' => '#']
                ]
            ]
        ];
    }
}
?>
