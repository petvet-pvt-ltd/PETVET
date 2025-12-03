<!-- User Welcome Header Component - Compact Version -->
<?php
// Get current user info
require_once __DIR__ . '/../../../config/auth_helper.php';

if (isLoggedIn()) {
    $user = currentUser();
    $userName = userDisplayName($user);
    $userRole = $_SESSION['current_role_display'] ?? '';
    $userAvatar = userAvatar($user);
    
    // Get clinic info for receptionist and clinic manager
    $clinicInfo = null;
    if (isset($_SESSION['current_role']) && in_array($_SESSION['current_role'], ['receptionist', 'clinic_manager'])) {
        $userId = currentUserId();
        if ($userId) {
            $pdo = db();
            $clinicStmt = $pdo->prepare("
                SELECT c.clinic_name 
                FROM clinic_staff cs
                JOIN clinics c ON cs.clinic_id = c.id
                WHERE cs.user_id = ?
            ");
            $clinicStmt->execute([$userId]);
            $clinic = $clinicStmt->fetch(PDO::FETCH_ASSOC);
            if ($clinic) {
                $clinicInfo = $clinic['clinic_name'];
            }
        }
    }
?>
<style>
.user-welcome-header {
    background: #17293F;
    border-radius: 16px;
    padding: 16px 24px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(23, 41, 63, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-welcome-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-welcome-avatar {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: #2a3d52;
    flex-shrink: 0;
}

.user-welcome-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.user-welcome-name {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    line-height: 1.3;
    color: #ffffff;
}

.user-welcome-role {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #e0e7ff;
    width: fit-content;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-welcome-role::before {
    content: "●";
    color: #93c5fd;
    font-size: 8px;
}

.user-welcome-center {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-welcome-date {
    color: #ffffff;
    font-size: 15px;
    font-weight: 500;
    opacity: 0.9;
}

.user-welcome-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-clinic-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(139, 92, 246, 0.25));
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #e0e7ff;
    border: 1px solid rgba(99, 102, 241, 0.4);
    white-space: nowrap;
}

.user-clinic-icon {
    font-size: 18px;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}

.user-welcome-actions {
    display: flex !important;
    align-items: center;
    gap: 12px;
    min-height: 40px;
}

.user-welcome-actions > * {
    display: flex !important;
}

@media (max-width: 768px) {
    .user-welcome-header {
        padding: 14px 18px;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .user-welcome-left {
        gap: 12px;
    }
    
    .user-welcome-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
    }
    
    .user-welcome-name {
        font-size: 16px;
    }
    
    .user-welcome-role {
        font-size: 11px;
        padding: 3px 10px;
    }
    
    .user-welcome-center {
        order: 3;
        width: 100%;
        justify-content: flex-start;
        margin-top: 4px;
    }
    
    .user-welcome-date {
        font-size: 13px;
    }
    
    .user-welcome-right {
        order: 2;
        justify-content: flex-end;
    }
    
    .user-clinic-badge {
        font-size: 12px;
        padding: 8px 14px;
    }
    
    .user-clinic-icon {
        font-size: 16px;
    }
}
</style>

<div class="user-welcome-header">
    <div class="user-welcome-left">
        <img src="<?= htmlspecialchars($userAvatar) ?>" alt="User Avatar" class="user-welcome-avatar">
        <div class="user-welcome-info">
            <h1 class="user-welcome-name"><?= htmlspecialchars($userName) ?></h1>
            <span class="user-welcome-role"><?= htmlspecialchars($userRole) ?></span>
        </div>
    </div>
    <div class="user-welcome-center">
        <span class="user-welcome-date"><?= date('l, F j, Y') ?></span>
    </div>
    <div class="user-welcome-right">
        <?php if ($clinicInfo): ?>
            <div class="user-clinic-badge">
                <span class="user-clinic-icon">🏥</span>
                <span><?= htmlspecialchars($clinicInfo) ?></span>
            </div>
        <?php endif; ?>
        <div class="user-welcome-actions" id="headerActionsSlot">
            <!-- Actions will be inserted here from the page -->
        </div>
    </div>
</div>

<?php
}
?>
