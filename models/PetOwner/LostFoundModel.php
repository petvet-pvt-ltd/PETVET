<?php
require_once __DIR__ . '/../BaseModel.php';

class LostFoundModel extends BaseModel {
    
    public function getAllReports() {
        // Mock data - in real implementation this would query the database
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
                'color' => 'White/Gray',
                'photo' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Galle Road, Colombo 03',
                'date' => '2025-10-06',
                'notes' => 'Indoor cat, very scared outside. Has a pink collar.',
                'contact' => [
                    'name' => 'Saman',
                    'email' => 'saman@example.com',
                    'phone' => '+94 78 888 9999'
                ]
            ],
            [
                'id' => 206,
                'type' => 'found',
                'name' => null,
                'species' => 'Bird',
                'breed' => 'Parrot',
                'age' => 'Unknown',
                'color' => 'Green/Red',
                'photo' => 'https://images.unsplash.com/photo-1452570053594-1b985d6ea890?q=80&w=300&auto=format&fit=crop',
                'last_seen' => 'Viharamahadevi Park, Colombo 07',
                'date' => '2025-10-07',
                'notes' => 'Can speak a few words. Very friendly.',
                'contact' => [
                    'name' => 'Malini',
                    'email' => 'malini@example.com',
                    'phone' => '+94 72 333 4444'
                ]
            ]
        ];
        
        return $reports;
    }
    
    public function getLostReports() {
        $reports = $this->getAllReports();
        return array_values(array_filter($reports, fn($r) => $r['type'] === 'lost'));
    }
    
    public function getFoundReports() {
        $reports = $this->getAllReports();
        return array_values(array_filter($reports, fn($r) => $r['type'] === 'found'));
    }
    
    public function getReportsByType($type) {
        if ($type === 'lost') {
            return $this->getLostReports();
        } elseif ($type === 'found') {
            return $this->getFoundReports();
        } else {
            return $this->getAllReports();
        }
    }
    
    public function searchReports($query, $species = null, $type = null) {
        $reports = $this->getAllReports();
        
        // Filter by type if specified
        if ($type) {
            $reports = array_filter($reports, fn($r) => $r['type'] === $type);
        }
        
        // Filter by species if specified
        if ($species) {
            $reports = array_filter($reports, fn($r) => 
                strcasecmp($r['species'], $species) === 0
            );
        }
        
        // Search by query if specified
        if ($query) {
            $query = strtolower($query);
            $reports = array_filter($reports, function($r) use ($query) {
                return (
                    stripos($r['name'] ?? '', $query) !== false ||
                    stripos($r['breed'], $query) !== false ||
                    stripos($r['color'], $query) !== false ||
                    stripos($r['last_seen'], $query) !== false ||
                    stripos($r['notes'], $query) !== false
                );
            });
        }
        
        return array_values($reports);
    }
}
?>