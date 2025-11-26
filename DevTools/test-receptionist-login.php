<?php
require_once __DIR__ . '/config/connect.php';

$email = 'receptionist@petvet.com';
$password = 'password123';

try {
    $db = db();
    $stmt = $db->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found: {$user['first_name']} {$user['last_name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Password hash in DB: {$user['password']}\n\n";
        
        if (password_verify($password, $user['password'])) {
            echo "✅ PASSWORD VERIFICATION: SUCCESS!\n";
            echo "Login should work now.\n";
        } else {
            echo "❌ PASSWORD VERIFICATION: FAILED!\n";
            echo "The password 'password123' does not match the hash in database.\n";
        }
    } else {
        echo "User not found!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
