<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterBookingsModel extends BaseModel {
    
    public function getAllBookings($sitterId) {
        return array_merge(
            $this->getPendingBookings($sitterId),
            $this->getActiveBookings($sitterId),
            $this->getCompletedBookings($sitterId)
        );
    }

    public function getPendingBookings($sitterId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Buddy',
                'pet_type' => 'Dog',
                'pet_breed' => 'Beagle',
                'owner_name' => 'Lisa Chen',
                'owner_phone' => '555-0201',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-20',
                'start_time' => '4:00 PM',
                'end_time' => '5:00 PM',
                'service_type' => 'Dog Walking',
                'status' => 'pending',
                'location' => 'Downtown Park',
                'daily_rate' => 20,
                'special_notes' => 'Young Beagle needs exercise and socialization. First time client - please review special instructions.'
            ],
            [
                'id' => 2,
                'pet_name' => 'Charlie',
                'pet_type' => 'Dog',
                'pet_breed' => 'Labrador',
                'owner_name' => 'David Smith',
                'owner_phone' => '555-0204',
                'start_date' => '2025-10-22',
                'end_date' => '2025-10-24',
                'start_time' => '10:00 AM',
                'end_time' => '6:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'pending',
                'location' => 'Westside Area',
                'daily_rate' => 60,
                'special_notes' => 'Friendly Lab, needs to be walked twice a day. Owner traveling for business.'
            ],
            [
                'id' => 3,
                'pet_name' => 'Mittens',
                'pet_type' => 'Cat',
                'pet_breed' => 'Siamese',
                'owner_name' => 'Sarah Johnson',
                'owner_phone' => '555-0205',
                'start_date' => '2025-10-25',
                'end_date' => '2025-10-27',
                'start_time' => '9:00 AM',
                'end_time' => '7:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'pending',
                'location' => 'Eastside Heights',
                'daily_rate' => 45,
                'special_notes' => 'Indoor cat, needs daily feeding and litter box cleaning. Very shy with strangers.'
            ]
        ];
    }

    public function getActiveBookings($sitterId) {
        return [
            [
                'id' => 4,
                'pet_name' => 'Luna & Shadow',
                'pet_type' => 'Dog',
                'pet_breed' => 'Golden Retrievers',
                'owner_name' => 'Maria Garcia',
                'owner_phone' => '555-0202',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-20',
                'start_time' => '9:00 AM',
                'end_time' => '10:00 AM',
                'service_type' => 'Dog Walking',
                'status' => 'confirmed',
                'location' => 'Central Park Area',
                'daily_rate' => 25,
                'special_notes' => 'Two friendly Golden Retrievers need their daily walk. They\'re well-trained and love meeting other dogs at the park.'
            ],
            [
                'id' => 5,
                'pet_name' => 'Whiskers',
                'pet_type' => 'Cat',
                'pet_breed' => 'Tabby',
                'owner_name' => 'Tom Wilson',
                'owner_phone' => '555-0203',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-22',
                'start_time' => '12:00 PM',
                'end_time' => '6:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'confirmed',
                'location' => 'Riverside District',
                'daily_rate' => 60,
                'special_notes' => 'Indoor cat needs daily feeding, litter cleaning, and companionship while owner is away on business trip.'
            ],
            [
                'id' => 6,
                'pet_name' => 'Rocky',
                'pet_type' => 'Dog',
                'pet_breed' => 'German Shepherd',
                'owner_name' => 'James Miller',
                'owner_phone' => '555-0206',
                'start_date' => '2025-10-19',
                'end_date' => '2025-10-21',
                'start_time' => '8:00 AM',
                'end_time' => '8:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'confirmed',
                'location' => 'North Hill',
                'daily_rate' => 70,
                'special_notes' => 'Active dog needs plenty of exercise. Has special diet - food provided by owner.'
            ]
        ];
    }

    public function getCompletedBookings($sitterId) {
        return [
            [
                'id' => 7,
                'pet_name' => 'Max',
                'pet_type' => 'Dog',
                'pet_breed' => 'Poodle',
                'owner_name' => 'Linda Green',
                'owner_phone' => '555-0207',
                'start_date' => '2025-10-05',
                'end_date' => '2025-10-10',
                'start_time' => '9:00 AM',
                'end_time' => '6:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'completed',
                'location' => 'South Bay',
                'daily_rate' => 50,
                'special_notes' => 'Very friendly, loves toys. Completed successfully.'
            ],
            [
                'id' => 8,
                'pet_name' => 'Bella',
                'pet_type' => 'Dog',
                'pet_breed' => 'Chihuahua',
                'owner_name' => 'Emily Davis',
                'owner_phone' => '555-0208',
                'start_date' => '2025-10-12',
                'end_date' => '2025-10-14',
                'start_time' => '10:00 AM',
                'end_time' => '5:00 PM',
                'service_type' => 'Pet Sitting',
                'status' => 'completed',
                'location' => 'Downtown',
                'daily_rate' => 40,
                'special_notes' => 'Small dog, easy to care for. Owner very satisfied.'
            ]
        ];
    }
}
