<?php
require_once __DIR__ . '/../BaseModel.php';

class PetOwnerAppointmentsModel extends BaseModel {
    
    public function getAppointmentsByOwnerId($ownerId) {
        $db = $this->db;
        
        // Fetch all appointments for this owner with pet and vet details
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.pet_id,
                a.appointment_date as date,
                a.appointment_time as time,
                a.appointment_type as type,
                a.status,
                a.symptoms,
                p.name as pet_name,
                p.species,
                p.breed,
                p.photo_url,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Any Available Vet') as vet
            FROM appointments a
            INNER JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.vet_id = u.id
            WHERE a.pet_owner_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        
        $stmt->execute([$ownerId]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize pets data by pet_id
        $pets = [];
        $appointmentsList = [];
        
        foreach ($appointments as $appt) {
            $petId = $appt['pet_id'];
            
            // Store pet info
            if (!isset($pets[$petId])) {
                $pets[$petId] = [
                    'name' => $appt['pet_name'],
                    'species' => $appt['species'],
                    'breed' => $appt['breed'],
                    'photo' => $appt['photo_url']
                ];
            }
            
            // Store appointment info
            $appointmentsList[] = [
                'id' => $appt['id'],
                'pet_id' => $petId,
                'date' => $appt['date'],
                'time' => $appt['time'],
                'type' => $appt['type'],
                'status' => ucfirst($appt['status']),
                'vet' => $appt['vet']
            ];
        }
        
        return [
            'pets' => $pets,
            'appointments' => $appointmentsList
        ];
    }
    
    public function getUpcomingAppointments($ownerId) {
        $db = $this->db;
        
        // Fetch upcoming appointments for this owner
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.pet_id,
                a.appointment_date as date,
                a.appointment_time as time,
                a.appointment_type as type,
                a.status,
                a.symptoms,
                p.name as pet_name,
                p.species,
                p.breed,
                p.photo_url,
                c.clinic_name,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Any Available Vet') as vet
            FROM appointments a
            INNER JOIN pets p ON a.pet_id = p.id
            LEFT JOIN clinics c ON a.clinic_id = c.id
            LEFT JOIN users u ON a.vet_id = u.id
            WHERE a.pet_owner_id = ?
            AND a.appointment_date >= CURDATE()
            AND a.status NOT IN ('cancelled', 'declined', 'completed')
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        
        $stmt->execute([$ownerId]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize pets data by pet_id
        $pets = [];
        $appointmentsList = [];
        
        foreach ($appointments as $appt) {
            $petId = $appt['pet_id'];
            
            // Store pet info
            if (!isset($pets[$petId])) {
                $pets[$petId] = [
                    'name' => $appt['pet_name'],
                    'species' => $appt['species'],
                    'breed' => $appt['breed'],
                    'photo' => $appt['photo_url']
                ];
            }
            
            // Store appointment info
            $appointmentsList[] = [
                'id' => $appt['id'],
                'pet_id' => $petId,
                'date' => $appt['date'],
                'time' => $appt['time'],
                'type' => $appt['type'],
                'status' => $appt['status'] === 'approved' ? 'Confirmed' : ucfirst($appt['status']),
                'vet' => $appt['vet'],
                'clinic' => $appt['clinic_name']
            ];
        }
        
        return [
            'pets' => $pets,
            'appointments' => $appointmentsList
        ];
    }
    
    public function getPastAppointments($ownerId) {
        $db = $this->db;
        
        // Fetch past appointments for this owner
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.pet_id,
                a.appointment_date as date,
                a.appointment_time as time,
                a.appointment_type as type,
                a.status,
                a.symptoms,
                p.name as pet_name,
                p.species,
                p.breed,
                p.photo_url,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Any Available Vet') as vet
            FROM appointments a
            INNER JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON a.vet_id = u.id
            WHERE a.pet_owner_id = ?
            AND (
                a.appointment_date < CURDATE()
                OR a.status IN ('cancelled', 'declined', 'completed')
            )
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        
        $stmt->execute([$ownerId]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize pets data by pet_id
        $pets = [];
        $appointmentsList = [];
        
        foreach ($appointments as $appt) {
            $petId = $appt['pet_id'];
            
            // Store pet info
            if (!isset($pets[$petId])) {
                $pets[$petId] = [
                    'name' => $appt['pet_name'],
                    'species' => $appt['species'],
                    'breed' => $appt['breed'],
                    'photo' => $appt['photo_url']
                ];
            }
            
            // Store appointment info
            $appointmentsList[] = [
                'id' => $appt['id'],
                'pet_id' => $petId,
                'date' => $appt['date'],
                'time' => $appt['time'],
                'type' => $appt['type'],
                'status' => ucfirst($appt['status']),
                'vet' => $appt['vet']
            ];
        }
        
        return [
            'pets' => $pets,
            'appointments' => $appointmentsList
        ];
    }
}
?>