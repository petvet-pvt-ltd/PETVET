-- Product Images Table for Multiple Images per Product
-- This table allows each product to have up to 5 images

CREATE TABLE IF NOT EXISTS product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing single images to the new table
INSERT INTO product_images (product_id, image_url, display_order)
SELECT id, image_url, 0
FROM products
WHERE image_url IS NOT NULL AND image_url != '';

-- Note: Keep the image_url column in products table for backward compatibility
-- It will store the primary/first image
