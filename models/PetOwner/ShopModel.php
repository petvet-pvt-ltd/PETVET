<?php
/**
 * Pet Owner Shop Model
 * Reuses the exact same shop functionality as Guest Shop
 */
require_once __DIR__ . '/../Guest/GuestShopModel.php';

class PetOwnerShopModel extends GuestShopModel {
    // Inherits all methods from GuestShopModel
    // Uses exact same products, categories, and functionality
    
    // Can add pet-owner specific features here if needed in future
    // For now, everything is exactly the same as guest shop
}
