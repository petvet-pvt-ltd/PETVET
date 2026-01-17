<?php
require_once __DIR__ . '/../BaseModel.php';

class ShopModel extends BaseModel {
    
    private function getClinicId() {
        $userId = $_SESSION['user_id'] ?? 0;
        if (!$userId) return 0;

        $stmt = $this->db->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function fetchShopData(): array {
        $clinicId = $this->getClinicId();
        if (!$clinicId) return [];

        $stmt = $this->db->prepare("
            SELECT 
                id, 
                name as title, 
                category, 
                stock, 
                price, 
                description, 
                image_url 
            FROM products 
            WHERE clinic_id = ? AND is_active = 1
            ORDER BY created_at DESC
        ");
        $stmt->execute([$clinicId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare statement for fetching images
        $imgStmt = $this->db->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY display_order ASC");

        // Format images as array to match view expectation
        foreach ($products as &$product) {
            // Fetch all images for this product
            $imgStmt->execute([$product['id']]);
            $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($images)) {
                $product['images'] = $images;
            } else {
                // Fallback to the main image_url if no entries in product_images (legacy support)
                $product['images'] = !empty($product['image_url']) ? [$product['image_url']] : [];
            }
            
            // Ensure numeric types
            $product['price'] = (float)$product['price'];
            $product['stock'] = (int)$product['stock'];
        }

        return $products;
    }

    public function fetchPendingOrders(): array {
        $clinicId = $this->getClinicId();
        if (!$clinicId) return [];

        $stmt = $this->db->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.user_id,
                o.total_amount,
                o.delivery_charge,
                o.created_at,
                u.first_name,
                u.last_name,
                u.phone,
                u.email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.clinic_id = ? AND o.status = 'Confirmed'
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$clinicId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get items for each order
        $itemsStmt = $this->db->prepare("
            SELECT 
                product_name,
                quantity,
                price
            FROM order_items
            WHERE order_id = ?
        ");

        foreach ($orders as &$order) {
            $itemsStmt->execute([$order['id']]);
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
            $order['item_count'] = count($order['items']);
        }

        return $orders;
    }
}
?>