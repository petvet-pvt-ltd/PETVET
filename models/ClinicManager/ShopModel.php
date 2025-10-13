<?php
require_once __DIR__ . '/../BaseModel.php';
class ShopModel extends BaseModel {
    // Add methods to interact with the shop-related data in the database
    public function fetchShopData(): array{
        $products = [
            [
                'id' => 1,
                'title' => 'Dog Food Premium',
                'category' => 'Food',
                'stock' => 25,
                'price' => 3500,
                'description' => 'High quality dog food for all breeds.',
                'images' => [
                    'https://bestcarepetshop.lk/web/image/product.product/21628/image_1024/%5BPC03007%5D%20Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg%20%282%29%2C%20Rs%2010%2C900.00?unique=07c8065',
                    'https://bestcarepetshop.lk/web/image/product.image/311/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473',
                    'https://bestcarepetshop.lk/web/image/product.image/313/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473',
                    'https://bestcarepetshop.lk/web/image/product.image/314/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473'
                ]
            ],
            [
                'id' => 2,
                'title' => 'Cat Toy Mouse',
                'category' => 'Toys',
                'stock' => 60,
                'price' => 700,
                'description' => 'Fun mouse toy for cats.',
                'images' => [
                    'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=facearea&w=400&q=80'
                ]
            ],
        ];
        return $products;
    }
    public function fetchPendingOrders(): array {
        $pendingOrders = [
            [
                'id' => 101,
                'customer' => 'Nimal Perera',
                'address' => '123 Main St, Colombo',
                'phone' => '077-1234567',
                'product' => 'Dog Food Premium',
                'qty' => 2,
                'date' => '2025-08-10'
            ],
            [
                'id' => 102,
                'customer' => 'Samanthi Silva',
                'address' => '456 Lake Rd, Kandy',
                'phone' => '071-9876543',
                'product' => 'Cat Toy Mouse',
                'qty' => 1,
                'date' => '2025-08-11'
            ],
        ];
        return $pendingOrders;
    }
}
?>