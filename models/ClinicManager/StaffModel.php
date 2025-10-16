<?php
require_once __DIR__ . '/../BaseModel.php';
/**
 * StaffModel: manages non-veterinary clinic staff (assistants, front desk, support)
 * Using session for mock persistence similar to vets model.
 */
class StaffModel extends BaseModel {
    private string $sessionKey = 'clinic_staff';
    private array $avatarPool = [
        'https://i.pravatar.cc/64?img=21',
        'https://i.pravatar.cc/64?img=32',
        'https://i.pravatar.cc/64?img=55',
        'https://i.pravatar.cc/64?img=47',
        'https://i.pravatar.cc/64?img=11',
        'https://i.pravatar.cc/64?img=48'
    ];

    public function __construct(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        if(!isset($_SESSION[$this->sessionKey])){
            $_SESSION[$this->sessionKey] = $this->seed();
        } else {
            // Migrate legacy rows that still use the placeholder pets.png icon
            $this->migrateLegacyPhotos();
        }
    }

    private function seed(): array {
        return [
            // Veterinary Assistants
            [
                'id'=>1,
                'name'=>'Anushka Perera',
                'role'=>'Veterinary Assistant',
                'email'=>'anushka.assist@petvet.lk',
                'phone'=>'+94 71 234 5678',
                'photo'=>'https://i.pravatar.cc/64?img=21',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>2,
                'name'=>'Nimasha De Silva',
                'role'=>'Veterinary Assistant',
                'email'=>'nimasha.assist@petvet.lk',
                'phone'=>'+94 71 444 8899',
                'photo'=>'https://i.pravatar.cc/64?img=47',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>3,
                'name'=>'Kavinda Fernando',
                'role'=>'Veterinary Assistant',
                'email'=>'kavinda.assist@petvet.lk',
                'phone'=>'+94 77 555 1234',
                'photo'=>'https://i.pravatar.cc/64?img=12',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>4,
                'name'=>'Sachini Wijesinghe',
                'role'=>'Veterinary Assistant',
                'email'=>'sachini.assist@petvet.lk',
                'phone'=>'+94 76 888 9999',
                'photo'=>'https://i.pravatar.cc/64?img=45',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            
            // Front Desk Staff
            [
                'id'=>5,
                'name'=>'Malini Silva',
                'role'=>'Front Desk',
                'email'=>'malini.front@petvet.lk',
                'phone'=>'+94 77 987 6543',
                'photo'=>'https://i.pravatar.cc/64?img=32',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>6,
                'name'=>'Tharindu Gamage',
                'role'=>'Front Desk',
                'email'=>'tharindu.front@petvet.lk',
                'phone'=>'+94 71 222 3333',
                'photo'=>'https://i.pravatar.cc/64?img=13',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            
            // Support Staff
            [
                'id'=>7,
                'name'=>'Ruwan Jayasuriya',
                'role'=>'Support Staff',
                'email'=>'ruwan.support@petvet.lk',
                'phone'=>'+94 76 111 2244',
                'photo'=>'https://i.pravatar.cc/64?img=55',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>8,
                'name'=>'Dilani Rathnayake',
                'role'=>'Support Staff',
                'email'=>'dilani.support@petvet.lk',
                'phone'=>'+94 77 444 5555',
                'photo'=>'https://i.pravatar.cc/64?img=38',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>9,
                'name'=>'Kasun Bandara',
                'role'=>'Support Staff',
                'email'=>'kasun.support@petvet.lk',
                'phone'=>'+94 71 666 7777',
                'photo'=>'https://i.pravatar.cc/64?img=33',
                'status'=>'Active',
                'next_shift'=> null,
            ],
            [
                'id'=>10,
                'name'=>'Chamika Herath',
                'role'=>'Support Staff',
                'email'=>'chamika.support@petvet.lk',
                'phone'=>'+94 76 999 1111',
                'photo'=>'https://i.pravatar.cc/64?img=52',
                'status'=>'Active',
                'next_shift'=> null,
            ],
        ];
    }

    public function all(): array {
        return $_SESSION[$this->sessionKey];
    }

    public function add(array $staff): void {
        $list = &$_SESSION[$this->sessionKey];
        $staff['id'] = $this->nextId($list);
        $staff['status'] = $staff['status'] ?? 'Active';
        if (empty($staff['photo'])) {
            // Assign next avatar from pool (round-robin)
            $staff['photo'] = $this->avatarPool[$this->nextAvatarIndex(count($list))];
        }
        $list[] = $staff;
    }

    public function updateStatus(int $id, string $status): void {
        $list = &$_SESSION[$this->sessionKey];
        foreach($list as &$s){
            if($s['id']===$id){
                $s['status']=$status;break;
            }
        }
    }

    private function nextId(array $list): int {
        $max = 0; foreach($list as $s){ if($s['id']>$max) $max=$s['id']; }
        return $max+1;
    }

    private function migrateLegacyPhotos(): void {
        $changed = false;
        $i = 0;
        foreach($_SESSION[$this->sessionKey] as &$row){
            if (!isset($row['photo']) || str_contains($row['photo'], '/sidebar/pets.png')) {
                $row['photo'] = $this->avatarPool[$i % count($this->avatarPool)];
                $changed = true;
                $i++;
            }
        }
        if ($changed) {
            // Persist updated array back (reference already updated, this line explicit for clarity)
            $_SESSION[$this->sessionKey] = array_values($_SESSION[$this->sessionKey]);
        }
    }

    private function nextAvatarIndex(int $offset): int {
        return $offset % count($this->avatarPool);
    }
}
