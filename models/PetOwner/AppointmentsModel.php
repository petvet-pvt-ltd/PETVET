<?php
require_once __DIR__ . '/../BaseModel.php';

class PetOwnerAppointmentsModel extends BaseModel {
    
    public function getAppointmentsByOwnerId($ownerId) {
        // Mock data - in real implementation this would query the database
        // For now, return realistic appointment data for testing
        
        $pets = [
            1 => ['name' => 'Rocky',    'species' => 'Dog', 'breed' => 'Golden Retriever', 'photo' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=150&auto=format&fit=crop'],
            2 => ['name' => 'Whiskers', 'species' => 'Cat', 'breed' => 'Siamese',           'photo' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=150&auto=format&fit=crop'],
            3 => ['name' => 'Tweety',   'species' => 'Bird','breed' => 'Canary',            'photo' => 'https://images.unsplash.com/photo-1452570053594-1b985d6ea890?q=80&w=150&auto=format&fit=crop'],
            4 => ['name' => 'Buddy',    'species' => 'Dog', 'breed' => 'Labrador',          'photo' => 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=150&auto=format&fit=crop'],
        ];
        
        $appointments = [
            // Upcoming appointments
            ['id'=>101, 'pet_id'=>1, 'date'=>date('Y-m-d', strtotime('+2 days')), 'time'=>'09:30', 'vet'=>'Dr. Williams', 'type'=>'General Checkup', 'status'=>'Confirmed'],
            ['id'=>102, 'pet_id'=>2, 'date'=>date('Y-m-d', strtotime('+3 days')), 'time'=>'14:00', 'vet'=>'Dr. Taylor',   'type'=>'Vaccination',     'status'=>'Pending'],
            ['id'=>103, 'pet_id'=>1, 'date'=>date('Y-m-d', strtotime('+7 days')), 'time'=>'11:15', 'vet'=>'Dr. Lee',      'type'=>'Grooming',        'status'=>'Confirmed'],
            ['id'=>104, 'pet_id'=>3, 'date'=>date('Y-m-d', strtotime('+10 days')), 'time'=>'16:45', 'vet'=>'Dr. Patel',    'type'=>'Nail Trim',       'status'=>'Confirmed'],
            ['id'=>105, 'pet_id'=>2, 'date'=>date('Y-m-d', strtotime('+14 days')), 'time'=>'10:00', 'vet'=>'Dr. Taylor',   'type'=>'Follow-up',       'status'=>'Confirmed'],
            ['id'=>106, 'pet_id'=>4, 'date'=>date('Y-m-d', strtotime('+5 days')), 'time'=>'15:30', 'vet'=>'Dr. Martinez', 'type'=>'Dental Cleaning', 'status'=>'Confirmed'],
            
            // Past appointments
            ['id'=>107, 'pet_id'=>1, 'date'=>date('Y-m-d', strtotime('-2 days')), 'time'=>'08:30', 'vet'=>'Dr. Lee', 'type'=>'Dental', 'status'=>'Completed'],
            ['id'=>108, 'pet_id'=>2, 'date'=>date('Y-m-d', strtotime('-5 days')), 'time'=>'13:15', 'vet'=>'Dr. Taylor', 'type'=>'Checkup', 'status'=>'Completed'],
        ];
        
        return [
            'pets' => $pets,
            'appointments' => $appointments
        ];
    }
    
    public function getUpcomingAppointments($ownerId) {
        $data = $this->getAppointmentsByOwnerId($ownerId);
        $appointments = $data['appointments'];
        $pets = $data['pets'];
        
        // Filter upcoming appointments only
        $nowTs = time();
        $upcoming = array_values(array_filter($appointments, function($a) use ($nowTs) {
            $ts = strtotime($a['date'] . ' ' . $a['time']);
            return $ts >= strtotime(date('Y-m-d 00:00:00', $nowTs));
        }));
        
        // Sort by date and time
        usort($upcoming, function($a, $b) {
            return strtotime($a['date'] . ' ' . $a['time']) <=> strtotime($b['date'] . ' ' . $b['time']);
        });
        
        return [
            'pets' => $pets,
            'appointments' => $upcoming
        ];
    }
    
    public function getPastAppointments($ownerId) {
        $data = $this->getAppointmentsByOwnerId($ownerId);
        $appointments = $data['appointments'];
        $pets = $data['pets'];
        
        // Filter past appointments only
        $nowTs = time();
        $past = array_values(array_filter($appointments, function($a) use ($nowTs) {
            $ts = strtotime($a['date'] . ' ' . $a['time']);
            return $ts < strtotime(date('Y-m-d 00:00:00', $nowTs));
        }));
        
        // Sort by date and time (most recent first)
        usort($past, function($a, $b) {
            return strtotime($b['date'] . ' ' . $b['time']) <=> strtotime($a['date'] . ' ' . $a['time']);
        });
        
        return [
            'pets' => $pets,
            'appointments' => $past
        ];
    }
}
?>