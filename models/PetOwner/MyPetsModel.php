<?php
require_once __DIR__ . '/PetProfileModel.php';

class MyPetsModel {
	private $petProfileModel;
	
	public function __construct() {
		$this->petProfileModel = new PetProfileModel();
	}
	
	public function fetchPets() {
		// Get user ID from session
		$userId = $_SESSION['user_id'] ?? null;
		
		if (!$userId) {
			return [];
		}
		
		// Fetch pets from database
		$petsFromDb = $this->petProfileModel->getUserPets($userId);
		
		// Transform database pets to match existing UI structure
		$pets = [];
		foreach ($petsFromDb as $p) {
			// Use photo_url if available, otherwise use placeholder based on species
			$photo = $p['photo_url'];
			if (!$photo) {
				// Default placeholder images based on species
				$placeholders = [
					'Dog' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=800&auto=format&fit=crop',
					'Cat' => 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=800&auto=format&fit=crop',
					'Bird' => 'https://thvnext.bing.com/th/id/OIP.ikE0KSiA5itZmKSZ4koCqAHaFj?w=600&h=450&c=7&r=0&o=5&dpr=1.3&pid=1.7',
					'Rabbit' => 'https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?q=80&w=800&auto=format&fit=crop',
					'Hamster' => 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?q=80&w=800&auto=format&fit=crop',
					'Guinea Pig' => 'https://images.unsplash.com/photo-1548681528-6a5c45b66b42?q=80&w=800&auto=format&fit=crop',
					'Fish' => 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?q=80&w=800&auto=format&fit=crop',
					'Turtle' => 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?q=80&w=800&auto=format&fit=crop'
				];
				$photo = $placeholders[$p['species']] ?? 'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?q=80&w=800&auto=format&fit=crop';
			}
			
			$pets[] = [
				'id' => $p['id'],
				'name' => $p['name'],
				'species' => $p['species'],
				'breed' => $p['breed'] ?? 'Unknown',
				'sex' => $p['sex'] ?? 'Unknown',
				'date_of_birth' => $p['date_of_birth'] ?? '',
				'weight' => $p['weight'] ?? '0',
				'color' => $p['color'] ?? '',
				'allergies' => $p['allergies'] ?? 'None',
				'notes' => $p['notes'] ?? '',
				'photo' => $photo
			];
		}
		
		return $pets;
	}

	public function getClinics() {
		// Fetch real clinics from database
		try {
			require_once __DIR__ . '/../../config/connect.php';
			$db = db();
			
			$query = "
				SELECT 
					id, 
					clinic_name as name, 
					clinic_address as address, 
					clinic_phone as phone
				FROM clinics 
				WHERE is_active = 1 
				ORDER BY clinic_name
			";
			
			$stmt = $db->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		} catch (Exception $e) {
			error_log("Error fetching clinics: " . $e->getMessage());
			return [];
		}
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
