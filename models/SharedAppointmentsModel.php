<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Shared Appointments Model
 * Used by both Clinic Manager and Receptionist
 * Handles all appointment data operations following MVC architecture
 */
class SharedAppointmentsModel extends BaseModel {
    
    /**
     * Get all appointments with optional filtering
     * @param string $vetFilter Filter by specific vet ('all' for no filter)
     * @return array Appointments data organized by date
     */
    public function getAppointments($vetFilter = 'all') {
        // TODO: Replace with actual database query
        // This simulates fetching from database
        $appointments = [
            // TODAY - October 19, 2025
            '2025-10-19' => [
                ['pet' => 'Buddy', 'client' => 'John Smith', 'vet' => 'Dr. Miller', 'time' => '09:00:00'],
                ['pet' => 'Whiskers', 'client' => 'Sarah Johnson', 'vet' => 'Dr. Brown', 'time' => '10:30:00'],
                ['pet' => 'Max', 'client' => 'Mike Davis', 'vet' => 'Dr. Wilson', 'time' => '14:00:00'],
                ['pet' => 'Luna', 'client' => 'Emma White', 'vet' => 'Dr. Miller', 'time' => '15:30:00'],
            ],
            // THIS WEEK
            '2025-10-20' => [
                ['pet' => 'Charlie', 'client' => 'Tom Brown', 'vet' => 'Dr. Brown', 'time' => '09:30:00'],
                ['pet' => 'Bella', 'client' => 'Lisa Wilson', 'vet' => 'Dr. Wilson', 'time' => '11:00:00'],
                ['pet' => 'Rocky', 'client' => 'James Anderson', 'vet' => 'Dr. Miller', 'time' => '13:30:00'],
            ],
            '2025-10-21' => [
                ['pet' => 'Daisy', 'client' => 'Mary Taylor', 'vet' => 'Dr. Brown', 'time' => '08:30:00'],
                ['pet' => 'Cooper', 'client' => 'David Lee', 'vet' => 'Dr. Wilson', 'time' => '10:00:00'],
                ['pet' => 'Molly', 'client' => 'Jennifer Garcia', 'vet' => 'Dr. Miller', 'time' => '14:30:00'],
            ],
            '2025-10-22' => [
                ['pet' => 'Tucker', 'client' => 'Robert Martinez', 'vet' => 'Dr. Brown', 'time' => '09:00:00'],
                ['pet' => 'Bailey', 'client' => 'Karen White', 'vet' => 'Dr. Wilson', 'time' => '11:30:00'],
                ['pet' => 'Sadie', 'client' => 'Steve Johnson', 'vet' => 'Dr. Miller', 'time' => '15:00:00'],
            ],
            '2025-10-23' => [
                ['pet' => 'Duke', 'client' => 'Patricia Brown', 'vet' => 'Dr. Brown', 'time' => '10:00:00'],
                ['pet' => 'Coco', 'client' => 'Michael Davis', 'vet' => 'Dr. Wilson', 'time' => '13:00:00'],
                ['pet' => 'Oliver', 'client' => 'Linda Wilson', 'vet' => 'Dr. Miller', 'time' => '16:00:00'],
            ],
            '2025-10-24' => [
                ['pet' => 'Lola', 'client' => 'William Garcia', 'vet' => 'Dr. Brown', 'time' => '09:30:00'],
                ['pet' => 'Bear', 'client' => 'Barbara Martinez', 'vet' => 'Dr. Wilson', 'time' => '11:00:00'],
            ],
            '2025-10-25' => [
                ['pet' => 'Ruby', 'client' => 'Richard Lee', 'vet' => 'Dr. Miller', 'time' => '10:00:00'],
                ['pet' => 'Milo', 'client' => 'Susan Anderson', 'vet' => 'Dr. Brown', 'time' => '14:00:00'],
            ],
            // REST OF THE MONTH
            '2025-10-26' => [
                ['pet' => 'Jack', 'client' => 'Joseph Taylor', 'vet' => 'Dr. Wilson', 'time' => '09:00:00'],
                ['pet' => 'Penny', 'client' => 'Jessica Thomas', 'vet' => 'Dr. Miller', 'time' => '11:30:00'],
            ],
            '2025-10-27' => [
                ['pet' => 'Zoey', 'client' => 'Christopher Moore', 'vet' => 'Dr. Brown', 'time' => '10:00:00'],
                ['pet' => 'Bentley', 'client' => 'Sarah Jackson', 'vet' => 'Dr. Wilson', 'time' => '13:30:00'],
            ],
            '2025-10-28' => [
                ['pet' => 'Roxy', 'client' => 'Daniel White', 'vet' => 'Dr. Miller', 'time' => '09:30:00'],
                ['pet' => 'Zeus', 'client' => 'Nancy Harris', 'vet' => 'Dr. Brown', 'time' => '15:00:00'],
            ],
            '2025-10-29' => [
                ['pet' => 'Ginger', 'client' => 'Mark Martin', 'vet' => 'Dr. Wilson', 'time' => '10:30:00'],
                ['pet' => 'Harley', 'client' => 'Betty Thompson', 'vet' => 'Dr. Miller', 'time' => '14:00:00'],
            ],
            '2025-10-30' => [
                ['pet' => 'Sophie', 'client' => 'Donald Garcia', 'vet' => 'Dr. Brown', 'time' => '09:00:00'],
                ['pet' => 'Leo', 'client' => 'Dorothy Martinez', 'vet' => 'Dr. Wilson', 'time' => '11:00:00'],
                ['pet' => 'Rosie', 'client' => 'Anthony Robinson', 'vet' => 'Dr. Miller', 'time' => '16:00:00'],
            ],
            '2025-10-31' => [
                ['pet' => 'Oscar', 'client' => 'Lisa Clark', 'vet' => 'Dr. Brown', 'time' => '10:00:00'],
                ['pet' => 'Toby', 'client' => 'Paul Rodriguez', 'vet' => 'Dr. Wilson', 'time' => '13:00:00'],
            ],
        ];
        
        // Filter by vet if specified
        if ($vetFilter !== 'all') {
            $filteredAppointments = [];
            foreach ($appointments as $date => $appts) {
                $filteredDay = [];
                foreach ($appts as $appt) {
                    if ($appt['vet'] === $vetFilter) {
                        $filteredDay[] = $appt;
                    }
                }
                if (!empty($filteredDay)) {
                    $filteredAppointments[$date] = $filteredDay;
                }
            }
            return $filteredAppointments;
        }
        
        return $appointments;
    }
    
    /**
     * Get list of all veterinarians
     * @return array List of vet names
     */
    public function getVetNames() {
        // TODO: Replace with actual database query
        $appointments = $this->getAppointments();
        $vetSet = [];
        
        foreach($appointments as $date => $items) {
            foreach($items as $appointment) {
                $vetSet[$appointment['vet']] = true;
            }
        }
        
        $vetNames = array_keys($vetSet);
        sort($vetNames);
        return $vetNames;
    }
    
    /**
     * Generate week dates starting from today
     * @param int $numDays Number of days to generate (default 7)
     * @return array Array of DateTime objects
     */
    public function getWeekDates($numDays = 7) {
        $weekDays = [];
        $today = new DateTime();
        
        for ($i = 0; $i < $numDays; $i++) {
            $date = clone $today;
            $date->modify("+{$i} day");
            $weekDays[] = $date;
        }
        
        return $weekDays;
    }
    
    /**
     * Generate month dates (5 weeks worth)
     * @param int $numWeeks Number of weeks to generate (default 5)
     * @return array Array of weeks, each containing DateTime objects
     */
    public function getMonthDates($numWeeks = 5) {
        $monthDays = [];
        $start = new DateTime();
        
        for ($row = 0; $row < $numWeeks; $row++) {
            $week = [];
            for ($col = 0; $col < 7; $col++) {
                $date = clone $start;
                $date->modify("+" . ($row * 7 + $col) . " day");
                $week[] = $date;
            }
            $monthDays[] = $week;
        }
        
        return $monthDays;
    }
    
    /**
     * Get module name based on user role for routing
     * @param string $userRole The user's role
     * @return string Module name for URLs
     */
    public function getModuleName($userRole) {
        return $userRole === 'receptionist' ? 'receptionist' : 'clinic-manager';
    }
    
    /**
     * Add a new appointment
     * @param array $appointmentData Appointment details
     * @return bool Success status
     */
    public function addAppointment($appointmentData) {
        // TODO: Implement database insertion
        // This would insert into appointments table
        return true;
    }
    
    /**
     * Update an existing appointment
     * @param int $appointmentId Appointment ID
     * @param array $appointmentData Updated appointment details
     * @return bool Success status
     */
    public function updateAppointment($appointmentId, $appointmentData) {
        // TODO: Implement database update
        // This would update the appointments table
        return true;
    }
    
    /**
     * Cancel/delete an appointment
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function cancelAppointment($appointmentId) {
        // TODO: Implement database deletion
        // This would soft delete or remove the appointment
        return true;
    }
    
    /**
     * Get pending appointments awaiting approval
     * @return array Pending appointments list
     */
    public function getPendingAppointments() {
        // TODO: Replace with actual database query
        // SELECT * FROM appointments WHERE status = 'pending' ORDER BY created_at DESC
        return [
            [
                'id' => 1,
                'pet' => 'Rocky',
                'pet_type' => 'Dog',
                'owner' => 'John Doe',
                'phone' => '555-0100',
                'appointment_type' => 'Routine Check-up',
                'symptoms' => 'Annual checkup and vaccination',
                'clinic' => 'PetVet Main Clinic',
                'requested_vet' => 'Dr. Sarah Johnson',
                'requested_date' => '2025-10-21',
                'requested_time' => '09:00',
                'requested_at' => '2025-10-19 08:30:00',
                'status' => 'pending'
            ],
            [
                'id' => 2,
                'pet' => 'Whiskers',
                'pet_type' => 'Cat',
                'owner' => 'Jane Smith',
                'phone' => '555-0200',
                'appointment_type' => 'Illness/Injury',
                'symptoms' => 'Not eating properly for 2 days, seems lethargic',
                'clinic' => 'PetVet Main Clinic',
                'requested_vet' => 'Any Available Vet',
                'requested_date' => '2025-10-20',
                'requested_time' => '14:30',
                'requested_at' => '2025-10-19 10:15:00',
                'status' => 'pending'
            ],
            [
                'id' => 3,
                'pet' => 'Max',
                'pet_type' => 'Dog',
                'owner' => 'Mike Johnson',
                'phone' => '555-0300',
                'appointment_type' => 'Dental Cleaning',
                'symptoms' => 'Routine dental cleaning',
                'clinic' => 'PetVet Kandy Branch',
                'requested_vet' => 'Dr. Priya Perera',
                'requested_date' => '2025-10-22',
                'requested_time' => '11:00',
                'requested_at' => '2025-10-19 11:45:00',
                'status' => 'pending'
            ],
            [
                'id' => 4,
                'pet' => 'Luna',
                'pet_type' => 'Cat',
                'owner' => 'Sarah Williams',
                'phone' => '555-0400',
                'appointment_type' => 'Vaccination',
                'symptoms' => 'Due for annual vaccinations',
                'clinic' => 'PetVet Main Clinic',
                'requested_vet' => 'Dr. Michael Chen',
                'requested_date' => '2025-10-23',
                'requested_time' => '15:00',
                'requested_at' => '2025-10-19 12:20:00',
                'status' => 'pending'
            ]
        ];
    }
    
    /**
     * Approve a pending appointment
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function approveAppointment($appointmentId) {
        // TODO: Implement database update
        // UPDATE appointments SET status = 'confirmed', confirmed_at = NOW() WHERE id = ?
        return true;
    }
    
    /**
     * Decline a pending appointment
     * @param int $appointmentId Appointment ID
     * @param string $reason Reason for decline
     * @return bool Success status
     */
    public function declineAppointment($appointmentId, $reason = '') {
        // TODO: Implement database update
        // UPDATE appointments SET status = 'declined', decline_reason = ?, declined_at = NOW() WHERE id = ?
        return true;
    }
}
?>