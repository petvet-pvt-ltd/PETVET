<?php
/**
 * Notification Helper
 * Functions to create and manage notifications
 */

require_once __DIR__ . '/../config/connect.php';

class NotificationHelper {
    
    /**
     * Create appointment notification
     */
    public static function createAppointmentNotification($appointment_id, $status, $reason = null) {
        $pdo = db();
        
        try {
            // Get appointment details
            $sql = "SELECT 
                        a.id,
                        a.pet_owner_id,
                        a.clinic_id,
                        a.vet_id,
                        a.appointment_date,
                        a.appointment_time,
                        p.name as pet_name,
                        v.years_experience,
                        u.first_name as vet_first_name,
                        u.last_name as vet_last_name,
                        c.clinic_name
                    FROM appointments a
                    INNER JOIN pets p ON a.pet_id = p.id
                    LEFT JOIN vets v ON a.vet_id = v.user_id
                    LEFT JOIN users u ON a.vet_id = u.id
                    INNER JOIN clinics c ON a.clinic_id = c.id
                    WHERE a.id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$appointment_id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                return false;
            }
            
            $pet_owner_id = $appointment['pet_owner_id'];
            $clinic_id = $appointment['clinic_id'];
            $clinic_name = $appointment['clinic_name'];
            $pet_name = $appointment['pet_name'];
            $appointment_date = date('F j, Y', strtotime($appointment['appointment_date']));
            $appointment_time = date('g:i A', strtotime($appointment['appointment_time']));
            $vet_name = $appointment['vet_first_name'] . ' ' . $appointment['vet_last_name'];
            
            // Determine message based on status
            $title = '';
            $message = '';
            
            if ($status === 'approved') {
                $title = 'Appointment Confirmed';
                $message = "Appointment for <strong>$pet_name</strong> with <strong>Dr. $vet_name</strong> has been confirmed for <strong>$appointment_date, at $appointment_time</strong> at <strong>$clinic_name</strong>";
            } elseif ($status === 'declined') {
                $title = 'Appointment Declined';
                $reason_text = $reason ? "Reason: <em>$reason</em>" : 'Appointment declined';
                $message = "Appointment declined for <strong>$pet_name</strong> at <strong>$clinic_name</strong>. $reason_text";
            } elseif ($status === 'rescheduled') {
                $title = 'Appointment Rescheduled';
                $message = "Your appointment for <strong>$pet_name</strong> with <strong>Dr. $vet_name</strong> at <strong>$clinic_name</strong> has been rescheduled to <strong>$appointment_date, at $appointment_time</strong>";
            } elseif ($status === 'cancelled') {
                $title = 'Appointment Cancelled';
                if ($reason) {
                    $message = "Your appointment for <strong>$pet_name</strong> at <strong>$clinic_name</strong> has been cancelled. Reason: <em>$reason</em>. Sorry for the inconvenience.";
                } else {
                    $message = "Your appointment for <strong>$pet_name</strong> at <strong>$clinic_name</strong> has been cancelled due to unavoidable reasons. Sorry for the inconvenience.";
                }
            }
            
            // Insert notification
            $sql = "INSERT INTO notifications (pet_owner_id, type, title, message, clinic_id, clinic_name, entity_id, entity_type, action_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $action_data = json_encode([
                'appointment_id' => $appointment_id,
                'date' => $appointment_date,
                'time' => $appointment_time,
                'reason' => $reason
            ]);
            
            $stmt->execute([
                $pet_owner_id,
                'appointment',
                $title,
                $message,
                $clinic_id,
                $clinic_name,
                $appointment_id,
                'appointment',
                $action_data
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log('Error creating appointment notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create sitter notification
     */
    public static function createSitterNotification($pet_owner_id, $sitter_id, $sitter_name, $pet_name, $status, $reason = null) {
        $pdo = db();
        
        try {
            $title = '';
            $message = '';
            
            if ($status === 'accepted') {
                $title = 'Sitter Request Accepted';
                $message = "Sitter <strong>$sitter_name</strong> has accepted your request for <strong>$pet_name</strong>";
            } elseif ($status === 'declined') {
                $title = 'Sitter Request Declined';
                $reason_text = $reason ? ' <span class="notification-reason-inline">Reason:&nbsp;<em>"' . $reason . '"</em></span>' : '';
                $message = "Sitter <strong>$sitter_name</strong> declined your request." . $reason_text;
            } elseif ($status === 'completed') {
                $title = 'Sitter Booking Completed';
                $message = "Sitter <strong>$sitter_name</strong> marked your booking for <strong>$pet_name</strong> as completed.";
            }
            
            $sql = "INSERT INTO notifications (pet_owner_id, type, title, message, entity_id, entity_type, action_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $action_data = json_encode([
                'sitter_id' => $sitter_id,
                'sitter_name' => $sitter_name,
                'pet_name' => $pet_name,
                'reason' => $reason
            ]);
            
            $stmt->execute([
                $pet_owner_id,
                'sitter',
                $title,
                $message,
                $sitter_id,
                'sitter',
                $action_data
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log('Error creating sitter notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create trainer notification
     */
    public static function createTrainerNotification($pet_owner_id, $trainer_id, $trainer_name, $pet_name, $status, $reason = null, $session_date = null, $session_time = null, $location_label = null, $training_type = null, $session_number = null, $next_session_date = null, $next_session_time = null) {
        $pdo = db();
        
        try {
            $title = '';
            $message = '';
            
            if ($status === 'accepted') {
                $title = 'Trainer Request Accepted';
                $dateText = $session_date ? " on <strong>$session_date</strong>" : '';
                $timeText = $session_time ? " at <strong>$session_time</strong>" : '';
                $venueText = $location_label ? " at <strong>$location_label</strong>" : '';
                $message = "Trainer <strong>$trainer_name</strong> has accepted your request for <strong>$pet_name</strong>";
                if ($dateText || $timeText || $venueText) {
                    $message .= ". Training scheduled$dateText$timeText$venueText";
                }
            } elseif ($status === 'declined') {
                $title = 'Trainer Request Declined';
                $reason_text = $reason ? ' <span class="notification-reason-inline">Reason:&nbsp;<em>"' . $reason . '"</em></span>' : '';
                $message = "Trainer <strong>$trainer_name</strong> declined your request." . $reason_text;
            } elseif ($status === 'session_completed') {
                $title = 'Training Session Completed';
                $sessionText = $session_number ? "Session <strong>$session_number</strong>" : 'A training session';
                $typeText = $training_type ? " ($training_type)" : '';
                $message = "$sessionText$typeText for <strong>$pet_name</strong> with <strong>$trainer_name</strong> has been completed.";
                if ($next_session_date || $next_session_time) {
                    $dateText = $next_session_date ? " on <strong>$next_session_date</strong>" : '';
                    $timeText = $next_session_time ? " at <strong>$next_session_time</strong>" : '';
                    $message .= " Next session scheduled$dateText$timeText.";
                }
            } elseif ($status === 'program_completed') {
                $title = 'Training Program Completed';
                $typeText = $training_type ? " ($training_type)" : '';
                $message = "Training program$typeText for <strong>$pet_name</strong> with <strong>$trainer_name</strong> has been completed.";
            }
            
            $sql = "INSERT INTO notifications (pet_owner_id, type, title, message, entity_id, entity_type, action_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $action_data = json_encode([
                'trainer_id' => $trainer_id,
                'trainer_name' => $trainer_name,
                'pet_name' => $pet_name,
                'reason' => $reason,
                'session_date' => $session_date,
                'session_time' => $session_time,
                'location' => $location_label,
                'training_type' => $training_type,
                'session_number' => $session_number,
                'next_session_date' => $next_session_date,
                'next_session_time' => $next_session_time
            ]);
            
            $stmt->execute([
                $pet_owner_id,
                'trainer',
                $title,
                $message,
                $trainer_id,
                'trainer',
                $action_data
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log('Error creating trainer notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create breeder notification
     */
    public static function createBreederNotification($pet_owner_id, $breeder_id, $breeder_name, $breeder_pet_name, $pet_name, $status, $reason = null) {
        $pdo = db();
        
        try {
            $title = '';
            $message = '';
            
            if ($status === 'accepted') {
                $title = 'Breeder Request Accepted';
                $message = "Breeder <strong>$breeder_name</strong> accepted your request for <strong>$pet_name</strong>. Breeder's Pet: <strong>$breeder_pet_name</strong>";
            } elseif ($status === 'declined') {
                $title = 'Breeder Request Declined';
                $reason_text = $reason ? ' <span class="notification-reason-inline">Reason:&nbsp;<em>"' . $reason . '"</em></span>' : '';
                $message = "Breeder <strong>$breeder_name</strong> declined your request." . $reason_text;
            } elseif ($status === 'completed') {
                $title = 'Breeding Completed';
                $breederPetText = $breeder_pet_name ? " Breeder's Pet: <strong>$breeder_pet_name</strong>." : '';
                $message = "Breeder <strong>$breeder_name</strong> marked the breeding request for <strong>$pet_name</strong> as completed." . $breederPetText;
            }
            
            $sql = "INSERT INTO notifications (pet_owner_id, type, title, message, entity_id, entity_type, action_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $action_data = json_encode([
                'breeder_id' => $breeder_id,
                'breeder_name' => $breeder_name,
                'pet_name' => $pet_name,
                'breeder_pet_name' => $breeder_pet_name,
                'reason' => $reason
            ]);
            
            $stmt->execute([
                $pet_owner_id,
                'breeder',
                $title,
                $message,
                $breeder_id,
                'breeder',
                $action_data
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log('Error creating breeder notification: ' . $e->getMessage());
            return false;
        }
    }
}

