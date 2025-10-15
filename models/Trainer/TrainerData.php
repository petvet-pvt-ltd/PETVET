<?php
/**
 * Centralized mock data for Trainer module
 * All trainer models should reference this file for consistent data
 */
class TrainerData {
    
    /**
     * Get all trainer appointments/sessions data
     */
    public static function getAllSessions() {
        return [
            // Pending Requests (3)
            'pending' => [
                [
                    'request_id' => 1,
                    'pet_owner_name' => 'Sarah Johnson',
                    'pet_owner_email' => 'sarah.j@email.com',
                    'pet_owner_phone' => '+1 (555) 123-4567',
                    'pet_name' => 'Max',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Golden Retriever',
                    'pet_age' => '2 years',
                    'training_type' => 'Basic',
                    'training_goals' => 'Basic obedience commands - sit, stay, come, heel. Leash training.',
                    'preferred_date' => '2025-10-20',
                    'preferred_time' => '10:00 AM',
                    'duration' => '1 hour',
                    'location' => 'Central Park, NY',
                    'additional_notes' => 'Max is very energetic and loves treats. He gets distracted easily by other dogs.',
                    'request_date' => '2025-10-14'
                ],
                [
                    'request_id' => 2,
                    'pet_owner_name' => 'Michael Chen',
                    'pet_owner_email' => 'mchen@email.com',
                    'pet_owner_phone' => '+1 (555) 234-5678',
                    'pet_name' => 'Luna',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'German Shepherd',
                    'pet_age' => '1 year',
                    'training_type' => 'Intermediate',
                    'training_goals' => 'Advanced commands, off-leash training, socialization with other dogs.',
                    'preferred_date' => '2025-10-22',
                    'preferred_time' => '2:00 PM',
                    'duration' => '1.5 hours',
                    'location' => 'Downtown Training Center',
                    'additional_notes' => 'Luna knows basic commands but needs work on consistency. Can be reactive to strangers.',
                    'request_date' => '2025-10-15'
                ],
                [
                    'request_id' => 3,
                    'pet_owner_name' => 'Emma Wilson',
                    'pet_owner_email' => 'emma.w@email.com',
                    'pet_owner_phone' => '+1 (555) 345-6789',
                    'pet_name' => 'Bella',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Labrador',
                    'pet_age' => '6 months',
                    'training_type' => 'Basic',
                    'training_goals' => 'Puppy basics - potty training, crate training, basic commands.',
                    'preferred_date' => '2025-10-18',
                    'preferred_time' => '4:00 PM',
                    'duration' => '1 hour',
                    'location' => 'Riverside Dog Park',
                    'additional_notes' => 'First time puppy owner. Bella is playful but nippy.',
                    'request_date' => '2025-10-13'
                ]
            ],
            
            // Confirmed/Active Sessions (8)
            'confirmed' => [
                [
                    'session_id' => 101,
                    'pet_owner_name' => 'Sarah Johnson',
                    'pet_owner_email' => 'sarah.j@email.com',
                    'pet_owner_phone' => '+1 (555) 123-4567',
                    'pet_name' => 'Max',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Golden Retriever',
                    'training_type' => 'Basic',
                    'session_number' => 5,
                    'next_session_date' => '2025-10-16',
                    'next_session_time' => '10:00 AM',
                    'location' => 'Central Park, NY',
                    'training_progress' => 'Good progress on sit and stay commands'
                ],
                [
                    'session_id' => 102,
                    'pet_owner_name' => 'Mike Davis',
                    'pet_owner_email' => 'mike.d@email.com',
                    'pet_owner_phone' => '+1 (555) 567-8901',
                    'pet_name' => 'Charlie',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Beagle',
                    'training_type' => 'Intermediate',
                    'session_number' => 3,
                    'next_session_date' => '2025-10-16',
                    'next_session_time' => '2:30 PM',
                    'location' => 'Downtown Training Center',
                    'training_progress' => 'Working on recall and leash manners'
                ],
                [
                    'session_id' => 103,
                    'pet_owner_name' => 'Emma Wilson',
                    'pet_owner_email' => 'emma.w@email.com',
                    'pet_owner_phone' => '+1 (555) 345-6789',
                    'pet_name' => 'Bella',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Labrador',
                    'training_type' => 'Basic',
                    'session_number' => 2,
                    'next_session_date' => '2025-10-17',
                    'next_session_time' => '9:00 AM',
                    'location' => 'Riverside Dog Park',
                    'training_progress' => 'Learning basic commands'
                ],
                [
                    'session_id' => 104,
                    'pet_owner_name' => 'James Brown',
                    'pet_owner_email' => 'james.b@email.com',
                    'pet_owner_phone' => '+1 (555) 789-0123',
                    'pet_name' => 'Duke',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Rottweiler',
                    'training_type' => 'Advanced',
                    'session_number' => 8,
                    'next_session_date' => '2025-10-17',
                    'next_session_time' => '11:00 AM',
                    'location' => 'Home Visit - Brooklyn',
                    'training_progress' => 'Protection training, excellent progress'
                ],
                [
                    'session_id' => 105,
                    'pet_owner_name' => 'Lisa Martinez',
                    'pet_owner_email' => 'lisa.m@email.com',
                    'pet_owner_phone' => '+1 (555) 890-1234',
                    'pet_name' => 'Buddy',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Poodle',
                    'training_type' => 'Intermediate',
                    'session_number' => 4,
                    'next_session_date' => '2025-10-18',
                    'next_session_time' => '3:00 PM',
                    'location' => 'Greenway Training Facility',
                    'training_progress' => 'Agility training in progress'
                ],
                [
                    'session_id' => 106,
                    'pet_owner_name' => 'Robert Taylor',
                    'pet_owner_email' => 'robert.t@email.com',
                    'pet_owner_phone' => '+1 (555) 901-2345',
                    'pet_name' => 'Zeus',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Husky',
                    'training_type' => 'Basic',
                    'session_number' => 6,
                    'next_session_date' => '2025-10-19',
                    'next_session_time' => '10:30 AM',
                    'location' => 'Northside Park',
                    'training_progress' => 'Good with commands, working on energy control'
                ],
                [
                    'session_id' => 107,
                    'pet_owner_name' => 'Jennifer Lee',
                    'pet_owner_email' => 'jen.lee@email.com',
                    'pet_owner_phone' => '+1 (555) 012-3456',
                    'pet_name' => 'Daisy',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Corgi',
                    'training_type' => 'Basic',
                    'session_number' => 3,
                    'next_session_date' => '2025-10-20',
                    'next_session_time' => '1:00 PM',
                    'location' => 'Home Visit - Queens',
                    'training_progress' => 'Learning to not bark at visitors'
                ],
                [
                    'session_id' => 108,
                    'pet_owner_name' => 'Chris Anderson',
                    'pet_owner_email' => 'chris.a@email.com',
                    'pet_owner_phone' => '+1 (555) 123-4567',
                    'pet_name' => 'Shadow',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Border Collie',
                    'training_type' => 'Advanced',
                    'session_number' => 10,
                    'next_session_date' => '2025-10-21',
                    'next_session_time' => '4:30 PM',
                    'location' => 'Elite Training Center',
                    'training_progress' => 'Competition prep, doing excellently'
                ]
            ],
            
            // Completed Sessions (sample of 156 total)
            'completed' => [
                [
                    'session_id' => 201,
                    'pet_owner_name' => 'Mark Johnson',
                    'pet_name' => 'Rusty',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Boxer',
                    'training_type' => 'Basic',
                    'sessions_completed' => 10,
                    'completed_date' => '2025-09-30',
                    'final_notes' => 'Successfully completed basic obedience program. Dog responds well to commands.',
                    'location' => 'Central Training Facility'
                ],
                [
                    'session_id' => 202,
                    'pet_owner_name' => 'Amanda Smith',
                    'pet_name' => 'Cooper',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Australian Shepherd',
                    'training_type' => 'Intermediate',
                    'sessions_completed' => 12,
                    'completed_date' => '2025-09-25',
                    'final_notes' => 'Excellent progress in agility training. Ready for competitions.',
                    'location' => 'Agility Park'
                ],
                [
                    'session_id' => 203,
                    'pet_owner_name' => 'Kevin Brown',
                    'pet_name' => 'Ace',
                    'pet_type' => 'Dog',
                    'pet_breed' => 'Doberman',
                    'training_type' => 'Advanced',
                    'sessions_completed' => 15,
                    'completed_date' => '2025-09-20',
                    'final_notes' => 'Guard dog training completed. Highly disciplined and responsive.',
                    'location' => 'Security K9 Facility'
                ]
            ]
        ];
    }
    
    /**
     * Get trainer statistics
     */
    public static function getStats() {
        $data = self::getAllSessions();
        return [
            'active_sessions' => count($data['confirmed']),
            'total_pets_trained' => 45, // Historical count
            'completed_sessions' => 156, // Historical count
            'pending_requests' => count($data['pending'])
        ];
    }
    
    /**
     * Get upcoming appointments for dashboard
     */
    public static function getUpcomingAppointments($limit = 5) {
        $data = self::getAllSessions();
        $upcoming = [];
        
        foreach ($data['confirmed'] as $session) {
            $upcoming[] = [
                'time' => self::formatDateTime($session['next_session_date'], $session['next_session_time']),
                'customer_name' => $session['pet_owner_name'],
                'location' => $session['location']
            ];
            
            if (count($upcoming) >= $limit) {
                break;
            }
        }
        
        return $upcoming;
    }
    
    /**
     * Format date time for display
     */
    private static function formatDateTime($date, $time) {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        if ($date == $today) {
            return 'Today ' . $time;
        } elseif ($date == $tomorrow) {
            return 'Tomorrow ' . $time;
        } else {
            return date('M j', strtotime($date)) . ', ' . $time;
        }
    }
}
