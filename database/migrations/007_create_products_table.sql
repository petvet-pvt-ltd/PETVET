-- Shop Products Table for PetVet
-- This table stores all products available in the shop

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('food', 'toys', 'litter', 'grooming', 'accessories', 'medicine') NOT NULL,
    image_url VARCHAR(500),
    stock INT DEFAULT 0,
    seller VARCHAR(255) DEFAULT 'PetVet Official Store',
    sold INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert existing mock data
INSERT INTO products (id, name, description, price, category, image_url, stock, seller, sold) VALUES
(1, 'Denta Fun Veggie Jaw Bone', 'A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar', 500.00, 'food', '/PETVET/views/shared/images/fproduct1.png', 25, 'PetVet Official Store', 340),
(2, 'Trixie Litter Scoop', 'High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.', 900.00, 'litter', '/PETVET/views/shared/images/fproduct2.png', 15, 'Trixie Official', 185),
(3, 'Dog Toy Tug Rope', 'Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.', 2100.00, 'toys', '/PETVET/views/shared/images/fproduct3.png', 8, 'PlayTime Pets', 95),
(4, 'Trixie Aloe Vera Shampoo', 'Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.', 1900.00, 'grooming', '/PETVET/views/shared/images/fproduct4.png', 12, 'Trixie Official', 220),
(5, 'Premium Cat Food Mix', 'Nutritionally balanced cat food with real chicken and fish. Contains essential vitamins and minerals for healthy growth. No artificial colors or preservatives.', 3500.00, 'food', '/PETVET/views/shared/images/fproduct5.png', 30, 'PetVet Official Store', 450),
(6, 'Interactive Puzzle Toy', 'Mental stimulation toy for intelligent pets. Hide treats inside and watch your pet figure out how to get them. Improves problem-solving skills.', 2500.00, 'toys', '/PETVET/views/shared/images/fproduct6.png', 18, 'PlayTime Pets', 156),
(7, 'Flea & Tick Collar', 'Long-lasting protection against fleas and ticks for up to 8 months. Water-resistant and adjustable for comfortable fit. Safe and effective formula.', 1500.00, 'medicine', '/PETVET/views/shared/images/fproduct7.png', 22, 'HealthyPet Solutions', 278),
(8, 'Comfortable Pet Bed', 'Ultra-soft pet bed with orthopedic support. Machine washable cover with non-slip bottom. Perfect for pets of all sizes.', 4500.00, 'accessories', '/PETVET/views/shared/images/fproduct8.png', 10, 'PetVet Official Store', 198);
