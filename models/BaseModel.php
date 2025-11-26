<?php
require_once __DIR__ . '/../config/connect.php';

abstract class BaseModel {
    protected $pdo;
    protected $db; // Alias for compatibility
    
    public function __construct() { 
        // Base constructor - can be overridden by child classes
        $this->pdo = db();
        $this->db = $this->pdo; // Alias
    }
}
?>