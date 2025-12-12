<?php
/**
 * Guest Lost & Found Model
 * Extends PetOwner LostFoundModel to share database access
 */
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../PetOwner/LostFoundModel.php';

class GuestLostFoundModel extends LostFoundModel {
    // Inherit all methods from LostFoundModel
    // Guest users see the same database data as pet owners
}
?>
