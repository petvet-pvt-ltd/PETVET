# Quick Reference: Wishlist System API

## API Endpoint
```
/PETVET/api/pet-owner/shop-wishlist.php
```

## Actions

### 1. Get All Wishlisted Products
```javascript
// Get all wishlisted products
fetch('/PETVET/api/pet-owner/shop-wishlist.php?action=get')
  .then(res => res.json())
  .then(data => console.log(data.wishlist));

// Get wishlisted products for specific clinic
fetch('/PETVET/api/pet-owner/shop-wishlist.php?action=get&clinic_id=123')
  .then(res => res.json())
  .then(data => console.log(data.wishlist));
```

### 2. Get Wishlisted Product IDs Only
```javascript
// Quick lookup for checking multiple products
fetch('/PETVET/api/pet-owner/shop-wishlist.php?action=get_ids&clinic_id=123')
  .then(res => res.json())
  .then(data => {
    const wishlistedIds = new Set(data.product_ids);
    console.log(wishlistedIds.has(456)); // Check if product 456 is wishlisted
  });
```

### 3. Check Single Product
```javascript
// Check if specific product is wishlisted
fetch('/PETVET/api/pet-owner/shop-wishlist.php?action=check&product_id=456')
  .then(res => res.json())
  .then(data => console.log(data.in_wishlist)); // true or false
```

### 4. Add to Wishlist
```javascript
const formData = new FormData();
formData.append('action', 'add');
formData.append('product_id', '456');
formData.append('clinic_id', '123');

fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
  method: 'POST',
  body: formData
})
  .then(res => res.json())
  .then(data => console.log(data.message));
```

### 5. Remove from Wishlist
```javascript
const formData = new FormData();
formData.append('action', 'remove');
formData.append('product_id', '456');

fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
  method: 'POST',
  body: formData
})
  .then(res => res.json())
  .then(data => console.log(data.message));
```

### 6. Toggle Wishlist (Recommended)
```javascript
// Add if not exists, remove if exists
const formData = new FormData();
formData.append('action', 'toggle');
formData.append('product_id', '456');
formData.append('clinic_id', '123');

fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
  method: 'POST',
  body: formData
})
  .then(res => res.json())
  .then(data => {
    console.log(data.action); // 'added' or 'removed'
    console.log(data.in_wishlist); // true or false
  });
```

## Response Formats

### Get Wishlist Response
```json
{
  "success": true,
  "wishlist": [
    {
      "wishlist_id": 1,
      "product_id": 456,
      "clinic_id": 123,
      "wishlisted_at": "2025-12-26 10:30:00",
      "name": "Product Name",
      "description": "Product description",
      "price": 1500,
      "stock": 0,
      "image": "path/to/image.jpg",
      "images": ["image1.jpg", "image2.jpg"],
      "clinic_name": "Clinic Name"
    }
  ],
  "total": 1
}
```

### Get IDs Response
```json
{
  "success": true,
  "product_ids": [456, 789, 101]
}
```

### Check Response
```json
{
  "success": true,
  "in_wishlist": true
}
```

### Toggle Response
```json
{
  "success": true,
  "action": "added",
  "in_wishlist": true,
  "message": "Added to wishlist"
}
```

## Database Schema

### Table: shop_wishlist
```sql
CREATE TABLE shop_wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    clinic_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    INDEX idx_clinic_id (clinic_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE
);
```

## Direct SQL Queries

### Get User's Wishlist
```sql
SELECT 
    w.*,
    p.name,
    p.price,
    p.stock,
    c.clinic_name
FROM shop_wishlist w
JOIN products p ON w.product_id = p.id
JOIN clinics c ON w.clinic_id = c.id
WHERE w.user_id = ?
ORDER BY w.created_at DESC;
```

### Add to Wishlist
```sql
INSERT INTO shop_wishlist (user_id, product_id, clinic_id) 
VALUES (?, ?, ?) 
ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP;
```

### Remove from Wishlist
```sql
DELETE FROM shop_wishlist 
WHERE user_id = ? AND product_id = ?;
```

### Check if in Wishlist
```sql
SELECT EXISTS(
    SELECT 1 FROM shop_wishlist 
    WHERE user_id = ? AND product_id = ?
) as in_wishlist;
```

## CSS Classes Reference

### Wishlist Toggle Button
- `.wishlist-toggle-btn` - Base button style
- `.wishlist-toggle-btn.active` - Active state (yellow background)

### Product Wishlist Icon
- `.product-wishlist-btn` - Icon on shop-clinic products
- `.product-wishlist-btn-detail` - Icon on product detail page
- `.product-wishlist-btn.in-wishlist` - Filled star (yellow)

### Wishlist Action Button
- `.add-to-wishlist` - Button for out-of-stock items (pink #ff00a3)
- `.add-to-wishlist-detail` - Same for product detail page

### Other Elements
- `.out-of-stock-banner` - Red banner indicating no stock

## Example: Complete Integration

```javascript
class WishlistManager {
    constructor(clinicId) {
        this.clinicId = clinicId;
        this.wishlist = new Set();
    }
    
    async init() {
        await this.loadWishlist();
        this.updateUI();
    }
    
    async loadWishlist() {
        const response = await fetch(
            `/PETVET/api/pet-owner/shop-wishlist.php?action=get_ids&clinic_id=${this.clinicId}`
        );
        const data = await response.json();
        this.wishlist = new Set(data.product_ids);
    }
    
    async toggle(productId) {
        const formData = new FormData();
        formData.append('action', 'toggle');
        formData.append('product_id', productId);
        formData.append('clinic_id', this.clinicId);
        
        const response = await fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.in_wishlist) {
                this.wishlist.add(productId);
            } else {
                this.wishlist.delete(productId);
            }
            this.updateUI();
        }
    }
    
    updateUI() {
        document.querySelectorAll('.product-wishlist-btn').forEach(btn => {
            const productId = parseInt(btn.dataset.productId);
            btn.classList.toggle('in-wishlist', this.wishlist.has(productId));
        });
    }
    
    isWishlisted(productId) {
        return this.wishlist.has(productId);
    }
}

// Usage
const manager = new WishlistManager(123);
manager.init();
```

## Security Notes
- All endpoints require user authentication (session-based)
- Users can only access/modify their own wishlist
- SQL injection prevention via prepared statements
- Input validation on all parameters

## Error Handling
All API responses include:
```json
{
  "success": false,
  "error": "Error message here"
}
```

Common errors:
- `"Unauthorized"` - User not logged in
- `"Product ID required"` - Missing required parameter
- `"Product not found"` - Invalid product ID
- `"Database error: ..."` - Server error
