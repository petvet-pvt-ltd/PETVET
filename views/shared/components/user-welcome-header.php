<!-- User Welcome Header Component - Compact Version -->
<?php
// Get current user info
require_once __DIR__ . '/../../../config/auth_helper.php';

if (isLoggedIn()) {
    $user = currentUser();
    $userName = userDisplayName($user);
    $userRole = $_SESSION['current_role_display'] ?? '';
    $userAvatar = userAvatar($user);
?>
<style>
.user-welcome-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    color: #fff;
}

.user-welcome-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: #fff;
    flex-shrink: 0;
}

.user-welcome-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.user-welcome-name {
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.user-welcome-role {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: fit-content;
}

@media (max-width: 768px) {
    .user-welcome-header {
        padding: 12px 16px;
    }
    
    .user-welcome-avatar {
        width: 42px;
        height: 42px;
    }
    
    .user-welcome-name {
        font-size: 15px;
    }
    
    .user-welcome-role {
        font-size: 10px;
        padding: 2px 8px;
    }
}
</style>

<div class="user-welcome-header">
    <img src="<?= htmlspecialchars($userAvatar) ?>" alt="User Avatar" class="user-welcome-avatar">
    <div class="user-welcome-info">
        <h1 class="user-welcome-name"><?= htmlspecialchars($userName) ?></h1>
        <span class="user-welcome-role"><?= htmlspecialchars($userRole) ?></span>
    </div>
</div>

<?php
}
?>
