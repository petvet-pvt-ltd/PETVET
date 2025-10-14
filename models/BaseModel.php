<?php
// require_once __DIR__ . '/../config/connect.php';
abstract class BaseModel {
    protected $pdo; // Remove PDO type hint for now to avoid issues
    
    public function __construct() { 
        // Base constructor - can be overridden by child classes
        // $this->pdo = db(); // Uncomment when database connection is set up
    }
}
?>