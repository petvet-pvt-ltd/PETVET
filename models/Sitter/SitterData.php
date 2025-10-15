<?php
/**
 * Centralized mock data for Sitter module
 * All sitter models should reference this file for consistent data
 */
class SitterData {
    
    /**
     * Get all sitter bookings data
     */
    public static function getAllBookings() {
        return [
            // Pending Bookings (3)
            'pending' => [
                [
                    'id' => 1,
                    'pet_name' => 'Buddy',
                    'pet_type' => 'Dog',
                    'owner_name' => 'Lisa Chen',
                    'owner_email' => 'lisa.chen@email.com',
                    'owner_phone' => '+1 (555) 111-2222',
                    'start_date' => '2025-10-20',
                    'end_date' => '2025-10-20',
                    'category' => 'Dog Walk',
                    'status' => 'pending',
                    'special_notes' => 'Needs 30-minute walk in the morning'
                ],
                [
                    'id' => 2,
                    'pet_name' => 'Charlie',
                    'pet_type' => 'Dog',
                    'owner_name' => 'David Smith',
                    'owner_email' => 'david.smith@email.com',
                    'owner_phone' => '+1 (555) 222-3333',
                    'start_date' => '2025-10-22',
                    'end_date' => '2025-10-24',
                    'category' => 'Pet Sitting',
                    'status' => 'pending',
                    'special_notes' => 'Needs to be fed twice daily'
                ],
                [
                    'id' => 3,
                    'pet_name' => 'Mittens',
                    'pet_type' => 'Cat',
                    'owner_name' => 'Sarah Johnson',
                    'owner_email' => 'sarah.j@email.com',
                    'owner_phone' => '+1 (555) 333-4444',
                    'start_date' => '2025-10-25',
                    'end_date' => '2025-10-27',
                    'category' => 'Cat Care',
                    'status' => 'pending',
                    'special_notes' => 'Indoor cat, needs litter cleaning'
                ]
            ],
            
            // Confirmed/Active Bookings (3)
            'confirmed' => [
                [
                    'id' => 4,
                    'pet_name' => 'Luna & Shadow',
                    'pet_type' => 'Dog',
                    'owner_name' => 'Maria Garcia',
                    'owner_email' => 'maria.g@email.com',
                    'owner_phone' => '+1 (555) 444-5555',
                    'start_date' => '2025-10-16',
                    'end_date' => '2025-10-16',
                    'start_time' => '10:00 AM',
                    'category' => 'Dog Walk',
                    'status' => 'confirmed',
                    'special_notes' => 'Two friendly Golden Retrievers need their daily walk.'
                ],
                [
                    'id' => 5,
                    'pet_name' => 'Whiskers',
                    'pet_type' => 'Cat',
                    'owner_name' => 'Tom Wilson',
                    'owner_email' => 'tom.w@email.com',
                    'owner_phone' => '+1 (555) 555-6666',
                    'start_date' => '2025-10-16',
                    'end_date' => '2025-10-22',
                    'start_time' => '3:00 PM',
                    'category' => 'Pet Sitting',
                    'status' => 'confirmed',
                    'special_notes' => 'Indoor cat needs daily feeding, litter cleaning, and companionship.'
                ],
                [
                    'id' => 6,
                    'pet_name' => 'Rocky',
                    'pet_type' => 'Dog',
                    'owner_name' => 'James Miller',
                    'owner_email' => 'james.m@email.com',
                    'owner_phone' => '+1 (555) 666-7777',
                    'start_date' => '2025-10-17',
                    'end_date' => '2025-10-21',
                    'start_time' => '9:30 AM',
                    'category' => 'Cat Care',
                    'status' => 'confirmed',
                    'special_notes' => 'Active dog needs plenty of exercise. Has special diet.'
                ]
            ],
            
            // Completed Bookings (sample of total)
            'completed' => [
                [
                    'id' => 201,
                    'pet_name' => 'Max',
                    'pet_type' => 'Dog',
                    'owner_name' => 'John Davis',
                    'start_date' => '2025-10-10',
                    'end_date' => '2025-10-12',
                    'category' => 'Pet Sitting',
                    'status' => 'completed',
                    'completed_date' => '2025-10-12'
                ],
                [
                    'id' => 202,
                    'pet_name' => 'Bella',
                    'pet_type' => 'Dog',
                    'owner_name' => 'Emma Wilson',
                    'start_date' => '2025-10-08',
                    'end_date' => '2025-10-09',
                    'category' => 'Dog Walk',
                    'status' => 'completed',
                    'completed_date' => '2025-10-09'
                ]
            ]
        ];
    }
    
    /**
     * Get sitter statistics
     */
    public static function getStats() {
        $data = self::getAllBookings();
        return [
            'active_bookings' => count($data['confirmed']),
            'total_pets_cared' => 87, // Historical count
            'completed_bookings' => 2, // Recently completed
            'pending_requests' => count($data['pending'])
        ];
    }
    
    /**
     * Get upcoming bookings for dashboard
     */
    public static function getUpcomingBookings($limit = 5) {
        $data = self::getAllBookings();
        $upcoming = [];
        
        foreach ($data['confirmed'] as $booking) {
            $upcoming[] = [
                'time' => self::formatDateTime($booking['start_date'], $booking['start_time'] ?? '12:00 PM'),
                'customer_name' => $booking['owner_name'],
                'category' => $booking['category']
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
