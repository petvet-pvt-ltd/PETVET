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
            '2025-10-14' => [
                ['pet' => 'Buddy', 'client' => 'John Smith', 'vet' => 'Dr. Miller', 'time' => '09:00:00'],
                ['pet' => 'Whiskers', 'client' => 'Sarah Johnson', 'vet' => 'Dr. Brown', 'time' => '10:30:00'],
                ['pet' => 'Max', 'client' => 'Mike Davis', 'vet' => 'Dr. Wilson', 'time' => '14:00:00'],
            ],
            '2025-10-15' => [
                ['pet' => 'Luna', 'client' => 'Emma White', 'vet' => 'Dr. Miller', 'time' => '09:30:00'],
                ['pet' => 'Charlie', 'client' => 'Tom Brown', 'vet' => 'Dr. Brown', 'time' => '11:00:00'],
            ],
            '2025-10-16' => [
                ['pet' => 'Bella', 'client' => 'Lisa Wilson', 'vet' => 'Dr. Wilson', 'time' => '10:00:00'],
                ['pet' => 'Rocky', 'client' => 'James Anderson', 'vet' => 'Dr. Miller', 'time' => '15:30:00'],
            ],
            '2025-10-17' => [
                ['pet' => 'Daisy', 'client' => 'Mary Taylor', 'vet' => 'Dr. Brown', 'time' => '08:30:00'],
                ['pet' => 'Cooper', 'client' => 'David Lee', 'vet' => 'Dr. Wilson', 'time' => '13:00:00'],
            ],
            '2025-10-18' => [
                ['pet' => 'Molly', 'client' => 'Jennifer Garcia', 'vet' => 'Dr. Miller', 'time' => '09:00:00'],
                ['pet' => 'Tucker', 'client' => 'Robert Martinez', 'vet' => 'Dr. Brown', 'time' => '16:00:00'],
            ]
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
}
?>