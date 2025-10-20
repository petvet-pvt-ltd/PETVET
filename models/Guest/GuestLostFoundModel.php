<?php
require_once __DIR__ . '/../BaseModel.php';

class GuestLostFoundModel extends BaseModel {
    
    public function getAllReports() {
        // Same data as PetOwner version - 100% identical
        $reports = [
            [
                'id' => 201,
                'type' => 'lost',
                'name' => 'Rocky',
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'age' => '3y',
                'color' => 'Golden',
                'photo' => [
                    'https://images.pexels.com/photos/356378/pexels-photo-356378.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500',
                    'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500',
                    'https://images.pexels.com/photos/2253275/pexels-photo-2253275.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500'
                ],
                'last_seen' => 'Madison St., Colombo 06',
                'date' => '2025-10-10',
                'notes' => 'Friendly, wears a red collar. Microchipped.',
                'contact' => [
                    'name' => 'Kasun',
                    'email' => 'kasun@example.com',
                    'phone' => '+94 77 123 4567'
                ]
            ],
            [
                'id' => 202,
                'type' => 'lost',
                'name' => 'Garfield',
                'species' => 'Cat',
                'breed' => 'Siamese',
                'age' => '2y',
                'color' => 'Cream/Seal',
                'photo' => [
                    'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=300&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1573865526739-10c1dd7aa123?q=80&w=300&auto=format&fit=crop'
                ],
                'last_seen' => 'Flower Rd., Colombo 07',
                'date' => '2025-10-08',
                'notes' => 'Needs special diet. Very shy.',
                'contact' => [
                    'name' => 'Nimali',
                    'email' => 'nimali@example.com',
                    'phone' => '+94 76 555 1212',
                    'phone2' => '+94 76 555 1213'
                ]
            ],
            [
                'id' => 203,
                'type' => 'found',
                'name' => null,
                'species' => 'Dog',
                'breed' => 'Rottweiler',
                'age' => 'Unknown',
                'color' => 'Black/Brown',
                'photo' => [
                    'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=300&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1567752881298-894bb81f9379?q=80&w=300&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1568572933382-74d440642117?q=80&w=300&auto=format&fit=crop'
                ],
                'last_seen' => 'Near Green Valley Park, Colombo 04',
                'date' => '2025-10-09',
                'notes' => 'Very tame. Responds to whistling.',
                'contact' => [
                    'name' => 'Tharindu',
                    'email' => 'tharu@example.com',
                    'phone' => '+94 71 987 2345',
                    'phone2' => '+94 71 987 2346'
                ]
            ],
            [
                'id' => 204,
                'type' => 'found',
                'name' => null,
                'species' => 'Dog',
                'breed' => 'Mixed',
                'age' => 'Approx 1y',
                'color' => 'Brown/White',
                'photo' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Marine Dr., Colombo 03',
                'date' => '2025-10-11',
                'notes' => 'No collar. Very calm and friendly.',
                'contact' => [
                    'name' => 'Ishara',
                    'email' => 'ishara@example.com',
                    'phone' => '+94 75 222 9090'
                ]
            ],
            [
                'id' => 205,
                'type' => 'lost',
                'name' => 'Bella',
                'species' => 'Cat',
                'breed' => 'Persian',
                'age' => '4y',
                'color' => 'White/Grey',
                'photo' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Duplication Rd., Colombo 04',
                'date' => '2025-10-12',
                'notes' => 'Very timid. Wears a pink collar with bell.',
                'contact' => [
                    'name' => 'Senuri',
                    'email' => 'senuri@example.com',
                    'phone' => '+94 77 888 4455'
                ]
            ],
            [
                'id' => 206,
                'type' => 'found',
                'name' => null,
                'species' => 'Cat',
                'breed' => 'Tabby',
                'age' => 'Unknown',
                'color' => 'Orange/White',
                'photo' => 'https://images.unsplash.com/photo-1529778873920-4da4926a72c2?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Galle Rd., Colombo 06',
                'date' => '2025-10-13',
                'notes' => 'No collar. Very friendly and well-groomed.',
                'contact' => [
                    'name' => 'Malith',
                    'email' => 'malith@example.com',
                    'phone' => '+94 76 111 2222'
                ]
            ],
            [
                'id' => 207,
                'type' => 'lost',
                'name' => 'Charlie',
                'species' => 'Bird',
                'breed' => 'Cockatiel',
                'age' => '2y',
                'color' => 'Grey/Yellow',
                'photo' => 'https://images.unsplash.com/photo-1535083783855-76ae62b2914e?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Reid Ave., Colombo 07',
                'date' => '2025-10-14',
                'notes' => 'Responds to name "Charlie". Can whistle.',
                'contact' => [
                    'name' => 'Dilshan',
                    'email' => 'dilshan@example.com',
                    'phone' => '+94 75 333 6677'
                ]
            ]
        ];
        
        return $reports;
    }
    
    public function getLostReports() {
        $allReports = $this->getAllReports();
        return array_filter($allReports, function($r) {
            return $r['type'] === 'lost';
        });
    }
    
    public function getFoundReports() {
        $allReports = $this->getAllReports();
        return array_filter($allReports, function($r) {
            return $r['type'] === 'found';
        });
    }
}
?>
