<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/connect.php';

class ProductModel extends BaseModel {
    
    private $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = db();
    }
    
    /**
     * Get all products (including inactive for admin management)
     */
    public function getAllProducts(bool $includeInactive = false): array {
        try {
            $sql = "
                SELECT id, name, description, price, category, image_url, 
                       stock, seller, sold, is_active, created_at, updated_at
                FROM products
            ";
            
            if (!$includeInactive) {
                $sql .= " WHERE is_active = TRUE";
            }
            
            $sql .= " ORDER BY id DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     */
    public function getProductById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, description, price, category, image_url, 
                       stock, seller, sold, is_active, created_at, updated_at
                FROM products 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $product ?: null;
        } catch (PDOException $e) {
            error_log("Get product by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new product
     */
    public function createProduct(array $data): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO products 
                (name, description, price, category, image_url, stock, seller, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['image_url'] ?? null,
                $data['stock'] ?? 0,
                $data['seller'] ?? 'PetVet Store',
                $data['is_active'] ?? true
            ]);
        } catch (PDOException $e) {
            error_log("Create product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing product
     */
    public function updateProduct(int $id, array $data): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = ?, 
                    description = ?, 
                    price = ?, 
                    category = ?, 
                    image_url = ?, 
                    stock = ?, 
                    seller = ?,
                    is_active = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['image_url'] ?? null,
                $data['stock'] ?? 0,
                $data['seller'] ?? 'PetVet Store',
                $data['is_active'] ?? true,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete a product (set is_active = false)
     */
    public function deleteProduct(int $id): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Permanently delete a product from database
     */
    public function permanentlyDeleteProduct(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Permanent delete product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Restore a soft-deleted product
     */
    public function restoreProduct(int $id): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET is_active = TRUE, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Restore product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update product stock
     */
    public function updateStock(int $id, int $quantity): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([$quantity, $id]);
        } catch (PDOException $e) {
            error_log("Update stock error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment sold count (when a product is purchased)
     */
    public function incrementSold(int $id, int $quantity = 1): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET sold = sold + ?, 
                    stock = stock - ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND stock >= ?
            ");
            
            return $stmt->execute([$quantity, $quantity, $id, $quantity]);
        } catch (PDOException $e) {
            error_log("Increment sold error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product categories
     */
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
    
    /**
     * Get all images for a product
     */
    public function getProductImages(int $productId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, image_url, display_order
                FROM product_images
                WHERE product_id = ?
                ORDER BY display_order ASC, id ASC
            ");
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get product images error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add an image to a product
     */
    public function addProductImage(int $productId, string $imageUrl, int $displayOrder = 0): bool {
        try {
            // Check if product has 5 images already
            $count = $this->db->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
            $count->execute([$productId]);
            if ($count->fetchColumn() >= 5) {
                error_log("Product already has 5 images");
                return false;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO product_images (product_id, image_url, display_order)
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$productId, $imageUrl, $displayOrder]);
        } catch (PDOException $e) {
            error_log("Add product image error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a product image
     */
    public function deleteProductImage(int $imageId): ?string {
        try {
            // Get image URL before deleting
            $stmt = $this->db->prepare("SELECT image_url FROM product_images WHERE id = ?");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$image) return null;
            
            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM product_images WHERE id = ?");
            $stmt->execute([$imageId]);
            
            return $image['image_url'];
        } catch (PDOException $e) {
            error_log("Delete product image error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get last inserted product ID
     */
    public function getLastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }
}
?>
