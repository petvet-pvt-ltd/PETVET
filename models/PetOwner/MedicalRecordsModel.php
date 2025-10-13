<?php
// models/PetOwner/MedicalRecordsModel.php

class MedicalRecordsModel {

    // ---- Mock data store (replace with DB queries later) ----
    private array $pets = [
        1 => [
            'id' => 1,
            'name' => 'Rocky',
            'species' => 'Dog',
            'breed' => 'Golden Retriever',
            'age' => 3,
            'microchip' => true,
            'vaccinated' => true,
            'last_vaccination' => '2025-05-10',
            'vet_contact' => 'Dr. Smith (555-1234)',
            'allergies' => 'Chicken',
            'blood_type' => 'B+',
        ],
        2 => [
            'id' => 2,
            'name' => 'Whiskers',
            'species' => 'Cat',
            'breed' => 'Siamese',
            'age' => 2,
            'microchip' => false,
            'vaccinated' => true,
            'last_vaccination' => '2025-06-15',
            'vet_contact' => 'Dr. Lee (555-5678)',
            'allergies' => 'Fish',
            'blood_type' => 'A-',
        ],
        3 => [
            'id' => 3,
            'name' => 'Tweety',
            'species' => 'Bird',
            'breed' => 'Canary',
            'age' => 1,
            'microchip' => false,
            'vaccinated' => false,
            'last_vaccination' => 'N/A',
            'vet_contact' => 'Dr. Brown (555-9012)',
            'allergies' => 'None',
            'blood_type' => 'O+',
        ],
    ];

    private array $clinicVisits = [
        1 => [
            ['date' => '2025-04-12', 'title' => 'General Checkup',      'details' => 'Healthy, routine check.'],
            ['date' => '2025-03-05', 'title' => 'Allergy Treatment',   'details' => 'Prescribed antihistamines.'],
        ],
        2 => [
            ['date' => '2025-05-10', 'title' => 'Diet Consultation',   'details' => 'Recommended special diet.'],
        ],
        3 => [
            ['date' => '2025-07-01', 'title' => 'Wing Injury',         'details' => 'Bandaged and healed.'],
        ],
    ];

    private array $vaccinations = [
        1 => [
            ['vaccine' => 'Rabies', 'date' => '2025-05-10', 'nextDue' => '2026-05-10', 'vet' => 'Dr. Smith'],
        ],
        2 => [
            ['vaccine' => 'Feline Leukemia', 'date' => '2025-06-15', 'nextDue' => '2026-06-15', 'vet' => 'Dr. Lee'],
        ],
        3 => [
            // none
        ],
    ];

    private array $reports = [
        1 => [
            [
                'date' => '2025-04-20',
                'title' => 'X-Ray',
                'details' => 'Fracture healing well.',
                'images' => [
                    'https://www.nylabone.com/-/media/project/oneweb/nylabone/images/dog101/activities-fun/10-great-small-dog-breeds/maltese-portrait.jpg?h=448&w=740&hash=B111F1998758CA0ED2442A4928D5105D',
                    'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=800&q=80'
                ],
            ],
        ],
        2 => [
            [
                'date' => '2025-06-20',
                'title' => 'Ultrasound',
                'details' => 'No abnormalities detected.',
                'images' => [],
            ],
        ],
        3 => [
            // none
        ],
    ];
    // --------------------------------------------------------

    public function getPetById(int $petId): ?array {
        return $this->pets[$petId] ?? null;
    }

    /** Returns a full record set for the medical-records page */
    public function getFullMedicalRecordByPetId(int $petId): ?array {
        $pet = $this->getPetById($petId);
        if (!$pet) return null;

        return [
            'pet'            => $pet,
            'clinic_visits'  => $this->clinicVisits[$petId] ?? [],
            'vaccinations'   => $this->vaccinations[$petId] ?? [],
            'reports'        => $this->reports[$petId] ?? [],
        ];
    }
}
