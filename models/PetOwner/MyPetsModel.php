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
}
