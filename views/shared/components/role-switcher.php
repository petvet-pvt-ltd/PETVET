<?php
/**
 * Shared Role Switching Section Component
 * Displays user's actual roles from database with ability to switch between them
 */

// Get user's actual roles from database
require_once __DIR__ . '/../../../config/auth_helper.php';
require_once __DIR__ . '/../../../config/User.php';

$user = new User();
$userRoles = $user->getUserRoles(currentUserId());
$currentRole = currentRole();

// Role display names and descriptions
$roleDisplayMap = [
    'pet_owner' => ['name' => 'Pet Owner', 'desc' => 'Manage your pets and appointments'],
    'trainer' => ['name' => 'Trainer', 'desc' => 'Provide training services'],
    'sitter' => ['name' => 'Pet Sitter', 'desc' => 'Offer pet sitting services'],
    'breeder' => ['name' => 'Breeder', 'desc' => 'Manage breeding operations'],
    'groomer' => ['name' => 'Groomer', 'desc' => 'Provide grooming services'],
    'vet' => ['name' => 'Veterinarian', 'desc' => 'Provide medical services'],
    'clinic_manager' => ['name' => 'Clinic Manager', 'desc' => 'Manage clinic operations'],
    'receptionist' => ['name' => 'Receptionist', 'desc' => 'Handle appointments and front desk'],
    'admin' => ['name' => 'Administrator', 'desc' => 'System administration']
];

// Count approved roles
$approvedRoles = array_filter($userRoles, fn($r) => $r['verification_status'] === 'approved');
$hasMultipleRoles = count($approvedRoles) > 1;
?>

<section class="card" id="section-role" data-section>
    <div class="card-head">
        <h2>Active Role</h2>
        <p class="muted small">
            <?php if ($hasMultipleRoles): ?>
                Switch between your registered roles
            <?php else: ?>
                Your current role in the system
            <?php endif; ?>
        </p>
    </div>
    <form id="formRole" class="form">
        <div class="role-options">
            <?php if (empty($userRoles)): ?>
                <p class="muted">No roles assigned to your account.</p>
            <?php else: ?>
                <?php foreach ($userRoles as $role): ?>
                    <?php 
                    $roleKey = $role['role_name'];
                    $roleData = $roleDisplayMap[$roleKey] ?? ['name' => ucfirst(str_replace('_', ' ', $roleKey)), 'desc' => ''];
                    $isActive = ($roleKey === $currentRole);
                    $isApproved = ($role['verification_status'] === 'approved');
                    $isPending = ($role['verification_status'] === 'pending');
                    ?>
                    
                    <?php if ($isApproved): ?>
                        <label class="role-option <?= $isActive ? 'active' : '' ?>">
                            <input type="radio" name="active_role" value="<?= $roleKey ?>" <?= $isActive ? 'checked' : '' ?> <?= !$hasMultipleRoles ? 'disabled' : '' ?> />
                            <div class="role-card">
                                <div class="role-header">
                                    <span class="role-name"><?= htmlspecialchars($roleData['name']) ?></span>
                                    <?php if ($isActive): ?>
                                        <span class="role-badge">Active</span>
                                    <?php endif; ?>
                                </div>
                                <p class="role-desc"><?= htmlspecialchars($roleData['desc']) ?></p>
                            </div>
                        </label>
                    <?php elseif ($isPending): ?>
                        <div class="role-option pending">
                            <div class="role-card">
                                <div class="role-header">
                                    <span class="role-name"><?= htmlspecialchars($roleData['name']) ?></span>
                                    <span class="role-badge pending">Pending Approval</span>
                                </div>
                                <p class="role-desc muted">Waiting for admin verification</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($hasMultipleRoles): ?>
            <div class="actions">
                <button class="btn primary" type="submit">Switch Role</button>
            </div>
        <?php endif; ?>
    </form>
</section>
