<?php
require_once __DIR__ . '/../BaseModel.php';

class GuestShopModel extends BaseModel {
    
    public function getAllProducts(): array {
        // This data would typically come from a database
        // For now, using the same structure as in the working files
        return [
            1 => [
                'id' => 1,
                'name' => 'Denta Fun Veggie Jaw Bone',
                'price' => 500,
                'image' => '/PETVET/views/shared/images/fproduct1.png',
                'description' => 'A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar',
                'category' => 'food',
                'stock' => 25,
                'seller' => 'PetVet Official Store',
                'sold' => 340
            ],
            2 => [
                'id' => 2,
                'name' => 'Trixie Litter Scoop',
                'price' => 900,
                'image' => '/PETVET/views/shared/images/fproduct2.png',
                'description' => 'High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.',
                'category' => 'litter',
                'stock' => 15,
                'seller' => 'Trixie Official',
                'sold' => 185
            ],
            3 => [
                'id' => 3,
                'name' => 'Dog Toy Tug Rope',
                'price' => 2100,
                'image' => '/PETVET/views/shared/images/fproduct3.png',
                'description' => 'Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.',
                'category' => 'toys',
                'stock' => 8,
                'seller' => 'PlayTime Pets',
                'sold' => 95
            ],
            4 => [
                'id' => 4,
                'name' => 'Trixie Aloe Vera Shampoo',
                'price' => 1900,
                'image' => '/PETVET/views/shared/images/fproduct4.png',
                'description' => 'Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.',
                'category' => 'grooming',
                'stock' => 12,
                'seller' => 'Trixie Official',
                'sold' => 220
            ],
            5 => [
                'id' => 5,
                'name' => 'Premium Dog Food - Chicken & Rice',
                'price' => 3500,
                'image' => 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?q=80&w=400&auto=format&fit=crop',
                'description' => 'Complete nutrition for adult dogs with real chicken as the first ingredient. Contains rice, vegetables, and essential vitamins for optimal health.',
                'category' => 'food',
                'stock' => 45,
                'seller' => 'NutriPet',
                'sold' => 567
            ],
            6 => [
                'id' => 6,
                'name' => 'Cat Scratching Post Tower',
                'price' => 4200,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Multi-level scratching post with sisal rope surface. Perfect for cats to sharpen claws, exercise, and play. Includes hanging toys and cozy perches.',
                'category' => 'toys',
                'stock' => 18,
                'seller' => 'FelinePlay',
                'sold' => 123
            ],
            7 => [
                'id' => 7,
                'name' => 'Adjustable Dog Collar - Leather',
                'price' => 1250,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Genuine leather collar with adjustable buckle. Durable, comfortable, and stylish. Available in multiple sizes for small to large dogs.',
                'category' => 'accessories',
                'stock' => 32,
                'seller' => 'PetStyle',
                'sold' => 289
            ],
            8 => [
                'id' => 8,
                'name' => 'Clumping Cat Litter - 10kg',
                'price' => 2800,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Premium clumping clay litter with odor control technology. Easy to scoop, long-lasting, and dust-free formula for healthier environment.',
                'category' => 'litter',
                'stock' => 22,
                'seller' => 'CleanPaws',
                'sold' => 445
            ],
            9 => [
                'id' => 9,
                'name' => 'Dog Nail Clippers Professional',
                'price' => 1650,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Professional-grade nail clippers with sharp stainless steel blades. Ergonomic handle for safe and precise trimming. Includes safety guard.',
                'category' => 'grooming',
                'stock' => 28,
                'seller' => 'GroomMaster',
                'sold' => 156
            ],
            10 => [
                'id' => 10,
                'name' => 'Interactive Puzzle Feeder',
                'price' => 1850,
                'image' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=400&auto=format&fit=crop',
                'description' => 'Slow-feeding puzzle bowl that challenges pets mentally while preventing fast eating. Promotes healthy digestion and reduces bloating.',
                'category' => 'accessories',
                'stock' => 16,
                'seller' => 'SmartFeeding',
                'sold' => 203
            ],
            11 => [
                'id' => 11,
                'name' => 'Cat Treats - Salmon Flavor',
                'price' => 750,
                'image' => 'https://images.unsplash.com/photo-1606491956689-2ea866880c84?q=80&w=400&auto=format&fit=crop',
                'description' => 'Delicious salmon-flavored treats made with real fish. Rich in omega-3 fatty acids for healthy skin and coat. Perfect for training rewards.',
                'category' => 'food',
                'stock' => 41,
                'seller' => 'TastyTreats',
                'sold' => 324
            ],
            12 => [
                'id' => 12,
                'name' => 'Dog Leash - Retractable 5m',
                'price' => 2250,
                'image' => 'https://images.unsplash.com/photo-1605568427561-40dd23c2acea?q=80&w=400&auto=format&fit=crop',
                'description' => 'Retractable leash with 5-meter range and comfortable grip handle. Features one-button brake and lock system for safety and control.',
                'category' => 'accessories',
                'stock' => 35,
                'seller' => 'WalkSafe',
                'sold' => 178
            ],
            13 => [
                'id' => 13,
                'name' => 'Pet Carrier - Medium Size',
                'price' => 3750,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Airline-approved pet carrier with ventilation on all sides. Comfortable for cats and small dogs. Features secure latches and carrying handles.',
                'category' => 'accessories',
                'stock' => 12,
                'seller' => 'TravelPet',
                'sold' => 89
            ],
            14 => [
                'id' => 14,
                'name' => 'Flea & Tick Spray - Natural',
                'price' => 1350,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Natural flea and tick repellent spray with essential oils. Safe for pets and effective protection against parasites. Pleasant lavender scent.',
                'category' => 'grooming',
                'stock' => 26,
                'seller' => 'NaturalCare',
                'sold' => 267
            ],
            15 => [
                'id' => 15,
                'name' => 'Squeaky Duck Toy Set',
                'price' => 950,
                'image' => 'https://images.unsplash.com/photo-1601979031925-424e53b6caaa?q=80&w=400&auto=format&fit=crop',
                'description' => 'Set of 3 colorful squeaky duck toys made from safe rubber. Perfect for bath time and interactive play. Helps reduce anxiety and boredom.',
                'category' => 'toys',
                'stock' => 29,
                'seller' => 'FunToys',
                'sold' => 412
            ],
            16 => [
                'id' => 16,
                'name' => 'Stainless Steel Food Bowl Set',
                'price' => 1450,
                'image' => 'https://images.unsplash.com/photo-1544568100-847a948585b9?q=80&w=400&auto=format&fit=crop',
                'description' => 'Set of 2 stainless steel bowls with non-slip rubber base. Dishwasher safe, rust-resistant, and perfect size for medium pets.',
                'category' => 'accessories',
                'stock' => 38,
                'seller' => 'MealTime',
                'sold' => 356
            ],
            17 => [
                'id' => 17,
                'name' => 'Cat Litter Box - Self-Cleaning',
                'price' => 6750,
                'image' => 'https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?q=80&w=400&auto=format&fit=crop',
                'description' => 'Automated self-cleaning litter box with odor control system. Reduces maintenance time and keeps litter area fresh. Suitable for cats up to 15 lbs.',
                'category' => 'litter',
                'stock' => 8,
                'seller' => 'AutoClean',
                'sold' => 67
            ],
            18 => [
                'id' => 18,
                'name' => 'Dog Dental Chews - Large',
                'price' => 1150,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Dental chews for large dogs that help reduce tartar and freshen breath. Made with natural ingredients and designed for powerful chewers.',
                'category' => 'food',
                'stock' => 33,
                'seller' => 'HealthyChew',
                'sold' => 287
            ],
            19 => [
                'id' => 19,
                'name' => 'Pet Grooming Brush Set',
                'price' => 1750,
                'image' => 'https://images.unsplash.com/photo-1629198688000-71f23e745b6e?q=80&w=400&auto=format&fit=crop',
                'description' => 'Complete grooming set with slicker brush, pin brush, and de-shedding tool. Reduces shedding by up to 90% and keeps coat healthy.',
                'category' => 'grooming',
                'stock' => 24,
                'seller' => 'GroomPro',
                'sold' => 198
            ],
            20 => [
                'id' => 20,
                'name' => 'Catnip Mice Toys - 6 Pack',
                'price' => 650,
                'image' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=400&auto=format&fit=crop',
                'description' => 'Pack of 6 soft mice toys filled with premium catnip. Stimulates natural hunting instincts and provides hours of entertainment for cats.',
                'category' => 'toys',
                'stock' => 47,
                'seller' => 'CatPlay',
                'sold' => 521
            ],
            21 => [
                'id' => 21,
                'name' => 'Puppy Training Pads - 100 Pack',
                'price' => 2450,
                'image' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?q=80&w=400&auto=format&fit=crop',
                'description' => 'Super absorbent training pads with leak-proof backing. Perfect for house training puppies or elderly dogs. Quick-dry technology prevents tracking.',
                'category' => 'litter',
                'stock' => 19,
                'seller' => 'TrainingAid',
                'sold' => 334
            ],
            22 => [
                'id' => 22,
                'name' => 'Dog Bed - Orthopedic Memory Foam',
                'price' => 4850,
                'image' => 'https://images.unsplash.com/photo-1583512603805-3cc6b41f3edb?q=80&w=400&auto=format&fit=crop',
                'description' => 'Orthopedic dog bed with memory foam support for joint health. Machine washable cover and non-slip bottom. Perfect for senior dogs.',
                'category' => 'accessories',
                'stock' => 14,
                'seller' => 'ComfortPet',
                'sold' => 156
            ],
            23 => [
                'id' => 23,
                'name' => 'Bird Seed Mix - Premium Blend',
                'price' => 850,
                'image' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=400&auto=format&fit=crop',
                'description' => 'Nutritious seed mix for pet birds with sunflower seeds, millet, and dried fruits. Supports healthy feathers and energy levels.',
                'category' => 'food',
                'stock' => 36,
                'seller' => 'AvianNutrition',
                'sold' => 145
            ],
            24 => [
                'id' => 24,
                'name' => 'Pet Hair Remover Roller',
                'price' => 750,
                'image' => 'https://img.freepik.com/premium-photo/graphic-design-pet-products-brand-packaging-psd-mockup_919910-2216.jpg',
                'description' => 'Reusable lint roller for removing pet hair from furniture and clothing. No sticky sheets needed - just roll and rinse clean.',
                'category' => 'grooming',
                'stock' => 42,
                'seller' => 'CleanHome',
                'sold' => 378
            ]
        ];
    }

    public function getProductById(int $id): ?array {
        $products = $this->getAllProducts();
        return $products[$id] ?? null;
    }

    public function getRelatedProducts(int $excludeId, string $category = '', int $limit = 3): array {
        $products = $this->getAllProducts();
        $related = [];
        
        foreach ($products as $id => $product) {
            if ($id != $excludeId) {
                $related[] = $product;
            }
        }
        
        // Limit the results
        return array_slice($related, 0, $limit);
    }

    public function getCategories(): array {
        return [
            'food' => 'Food & Treats',
            'toys' => 'Toys & Games', 
            'litter' => 'Litter & Training',
            'accessories' => 'Accessories & Supplies',
            'grooming' => 'Grooming & Health'
        ];
    }

    public function getProductsByCategory(string $category): array {
        $products = $this->getAllProducts();
        $filtered = [];
        
        foreach ($products as $product) {
            if ($product['category'] === $category) {
                $filtered[] = $product;
            }
        }
        
        return $filtered;
    }
}
?>