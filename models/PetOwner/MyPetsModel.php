<?php
class MyPetsModel {
	public function fetchPets() {
		// Using remote placeholder images (royalty-free Unsplash / Bing) so cards always show photos.
		$pets = [
			[
				'id' => 1,
				'name' => 'Rocky',
				'species' => 'Dog',
				'breed' => 'Golden Retriever',
				'sex' => 'Male',
				'date_of_birth' => '2022-04-15',
				'weight' => '30.5',
				'color' => 'Golden',
				'allergies' => 'None',
				'notes' => 'Loves people. No known allergies.',
				'photo' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=800&auto=format&fit=crop',
				'owner' => 'John Doe'
			],
			[
				'id' => 2,
				'name' => 'Whiskers',
				'species' => 'Cat',
				'breed' => 'Siamese',
				'sex' => 'Female',
				'date_of_birth' => '2023-01-10',
				'weight' => '4.2',
				'color' => 'Cream with brown points',
				'allergies' => 'Fish',
				'notes' => 'Very playful. Needs special diet.',
				'photo' => 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=800&auto=format&fit=crop',
				'owner' => 'Jane Smith'
			],
			[
				'id' => 3,
				'name' => 'Tweety',
				'species' => 'Bird',
				'breed' => 'Canary',
				'sex' => 'Unknown',
				'date_of_birth' => '2024-06-01',
				'weight' => '0.03',
				'color' => 'Yellow',
				'allergies' => 'None',
				'notes' => 'Sings every morning.',
				'photo' => 'https://thvnext.bing.com/th/id/OIP.ikE0KSiA5itZmKSZ4koCqAHaFj?w=600&h=450&c=7&r=0&o=5&dpr=1.3&pid=1.7',
				'owner' => 'Alice Brown'
			]
		];
		return $pets;
	}

	public function getClinics() {
		// Mock clinic data
		return [
			[
				'id' => 1,
				'name' => 'PetVet Main Clinic',
				'address' => '123 Main Street, Colombo',
				'phone' => '011-2345678'
			],
			[
				'id' => 2,
				'name' => 'PetVet Kandy Branch',
				'address' => '456 Peradeniya Road, Kandy',
				'phone' => '081-2234567'
			],
			[
				'id' => 3,
				'name' => 'PetVet Galle Branch',
				'address' => '789 Galle Road, Galle',
				'phone' => '091-2223344'
			],
			[
				'id' => 4,
				'name' => 'PetVet Negombo Branch',
				'address' => '321 Beach Road, Negombo',
				'phone' => '031-2227788'
			]
		];
	}

	public function getVetsByClinic($clinicId) {
		// Mock vet data by clinic
		$vets = [
			1 => [ // Main Clinic
				['id' => 1, 'name' => 'Dr. Sarah Johnson', 'specialization' => 'General Practice', 'avatar' => 'https://i.pravatar.cc/150?img=1'],
				['id' => 2, 'name' => 'Dr. Michael Chen', 'specialization' => 'Surgery', 'avatar' => 'https://i.pravatar.cc/150?img=13'],
				['id' => 3, 'name' => 'Dr. Emily Rodriguez', 'specialization' => 'Exotic Animals', 'avatar' => 'https://i.pravatar.cc/150?img=5'],
				['id' => 4, 'name' => 'Dr. James Wilson', 'specialization' => 'Cardiology', 'avatar' => 'https://i.pravatar.cc/150?img=12']
			],
			2 => [ // Kandy Branch
				['id' => 5, 'name' => 'Dr. Priya Perera', 'specialization' => 'General Practice', 'avatar' => 'https://i.pravatar.cc/150?img=9'],
				['id' => 6, 'name' => 'Dr. Nuwan Silva', 'specialization' => 'Dentistry', 'avatar' => 'https://i.pravatar.cc/150?img=14'],
				['id' => 7, 'name' => 'Dr. Anjali Fernando', 'specialization' => 'Dermatology', 'avatar' => 'https://i.pravatar.cc/150?img=10']
			],
			3 => [ // Galle Branch
				['id' => 8, 'name' => 'Dr. Rajesh Kumar', 'specialization' => 'General Practice', 'avatar' => 'https://i.pravatar.cc/150?img=15'],
				['id' => 9, 'name' => 'Dr. Lisa Thompson', 'specialization' => 'Orthopedics', 'avatar' => 'https://i.pravatar.cc/150?img=20'],
				['id' => 10, 'name' => 'Dr. David Lee', 'specialization' => 'Emergency Care', 'avatar' => 'https://i.pravatar.cc/150?img=33']
			],
			4 => [ // Negombo Branch
				['id' => 11, 'name' => 'Dr. Amanda Costa', 'specialization' => 'General Practice', 'avatar' => 'https://i.pravatar.cc/150?img=24'],
				['id' => 12, 'name' => 'Dr. Robert Brown', 'specialization' => 'Behavior', 'avatar' => 'https://i.pravatar.cc/150?img=52']
			]
		];

		return $vets[$clinicId] ?? [];
	}

	public function getExistingAppointments($date) {
		// Mock existing appointments for time conflict checking
		// Returns array of appointments with time slots (in 24-hour format for easier comparison)
		$appointments = [
			'2025-10-20' => [
				['time' => '09:00', 'vet_id' => 1],
				['time' => '09:30', 'vet_id' => 2],
				['time' => '10:00', 'vet_id' => 1],
				['time' => '14:00', 'vet_id' => 3],
				['time' => '14:30', 'vet_id' => 1],
			],
			'2025-10-21' => [
				['time' => '09:00', 'vet_id' => 2],
				['time' => '11:00', 'vet_id' => 1],
				['time' => '15:00', 'vet_id' => 4],
			]
		];

		return $appointments[$date] ?? [];
	}
}
