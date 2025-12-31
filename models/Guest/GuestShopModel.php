<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

class GuestShopModel extends BaseModel {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllProducts(): array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, clinic_id, name, description, price, category, image_url as image, 
                       stock, sold, is_active
                FROM products 
                WHERE is_active = TRUE
                ORDER BY id ASC
            ");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert to associative array with id as key (for compatibility)
            $result = [];
            foreach ($products as $product) {
                $result[$product['id']] = $product;
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Get products error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.clinic_id, p.name, p.description, p.price, p.category, p.image_url as image, 
                       p.stock, p.sold, p.is_active, c.name as clinic_name
                FROM products p
                LEFT JOIN clinics c ON p.clinic_id = c.id
                WHERE p.id = ? AND p.is_active = TRUE
            ");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $product ?: null;
        } catch (PDOException $e) {
            error_log("Get product by ID error: " . $e->getMessage());
            return null;
        }
    }

    public function getProductImages(int $productId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT image_url 
                FROM product_images 
                WHERE product_id = ? 
                ORDER BY display_order ASC
            ");
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Get product images error: " . $e->getMessage());
            return [];
        }
    }

    public function getRelatedProducts(int $excludeId, string $category = '', int $limit = 3): array {
        try {
            if ($category) {
                $stmt = $this->db->prepare("
                    SELECT id, clinic_id, name, description, price, category, image_url as image, 
                           stock, sold
                    FROM products 
                    WHERE id != ? AND category = ? AND is_active = TRUE
                    ORDER BY sold DESC
                    LIMIT ?
                ");
                $stmt->execute([$excludeId, $category, $limit]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT id, clinic_id, name, description, price, category, image_url as image, 
                           stock, sold
                    FROM products 
                    WHERE id != ? AND is_active = TRUE
                    ORDER BY sold DESC
                    LIMIT ?
                ");
                $stmt->execute([$excludeId, $limit]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get related products error: " . $e->getMessage());
            return [];
        }
    }

    public function getCategories(): array {
        return [
            'food' => 'Food & Treats',
            'toys' => 'Toys & Games', 
            'litter' => 'Litter & Training',
            'accessories' => 'Accessories & Supplies',
            'grooming' => 'Grooming & Health',
            'medicine' => 'Medicine & Health'
        ];
    }

    public function getProductsByCategory(string $category): array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, clinic_id, name, description, price, category, image_url as image, 
                       stock, sold
                FROM products 
                WHERE category = ? AND is_active = TRUE
                ORDER BY sold DESC
            ");
            $stmt->execute([$category]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get products by category error: " . $e->getMessage());
            return [];
        }
    }
}
?>