<?php
/**
 * Get Notifications API - Pet Owner
 * Returns unread notifications for the current pet owner
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

session_start();

$pet_owner_id = $_SESSION['user_id'] ?? null;

if (!$pet_owner_id) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

try {
    $pdo = db();
    $defaultAvatar = '/PETVET/public/images/emptyProfPic.png';

    $normalizeReasonInline = function ($message) {
        if (!is_string($message) || $message === '') {
            return $message;
        }
        if (stripos($message, 'notification-reason-inline') !== false) {
            return $message;
        }
        if (stripos($message, 'Reason:') === false) {
            return $message;
        }

        $updated = preg_replace_callback('/\bReason:\s*<em>(.*?)<\/em>/i', function ($m) {
            $inner = isset($m[1]) ? (string)$m[1] : '';
            $trimmed = trim($inner);
            $hasQuotes = (strlen($trimmed) >= 2 && $trimmed[0] === '"' && $trimmed[strlen($trimmed) - 1] === '"');
            $display = $hasQuotes ? $trimmed : ('"' . $trimmed . '"');
            return '<span class="notification-reason-inline">Reason:&nbsp;<em>' . $display . '</em></span>';
        }, $message, 1);

        return is_string($updated) ? $updated : $message;
    };
    
    // Get unread notifications for this pet owner
    $sql = "SELECT 
                n.id,
                n.type,
                n.title,
                n.message,
                n.clinic_id,
                n.clinic_name,
                COALESCE(c_by_id.clinic_logo, c_by_name.clinic_logo) AS clinic_logo,
                n.entity_id,
                n.entity_type,
                n.action_data,
                n.created_at,
                DATE_FORMAT(n.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at_iso,
                UNIX_TIMESTAMP(n.created_at) AS created_at_ts,
                TIMESTAMPDIFF(SECOND, n.created_at, UTC_TIMESTAMP()) AS age_seconds,
                CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read
            FROM notifications n
            LEFT JOIN clinics c_by_id ON n.clinic_id = c_by_id.id
            LEFT JOIN clinics c_by_name
                ON (n.clinic_id IS NULL OR n.clinic_id = 0)
               AND n.clinic_name IS NOT NULL
               AND n.clinic_name <> ''
               AND c_by_name.clinic_name = n.clinic_name
            LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.pet_owner_id = ?
            WHERE n.pet_owner_id = ?
            ORDER BY n.created_at DESC
            LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pet_owner_id, $pet_owner_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse action_data JSON
    $providerIds = [];
    foreach ($notifications as &$notif) {
        $notif['is_read'] = (bool)$notif['is_read'];
        if ($notif['action_data']) {
            $notif['action_data'] = json_decode($notif['action_data'], true);
        }

        // Attach clinic avatar (or default) for appointment notifications
        $type = (string)($notif['type'] ?? '');
        if ($type === 'appointment') {
            $logo = trim((string)($notif['clinic_logo'] ?? ''));
            $notif['clinic_avatar'] = ($logo !== '') ? $logo : $defaultAvatar;
        }

        // Pre-collect service provider IDs for avatar lookup
        if (in_array($type, ['trainer', 'sitter', 'breeder'], true)) {
            $action = is_array($notif['action_data'] ?? null) ? $notif['action_data'] : [];
            $providerId = 0;
            if ($type === 'trainer') {
                $providerId = (int)($action['trainer_id'] ?? ($notif['entity_id'] ?? 0));
            } elseif ($type === 'sitter') {
                $providerId = (int)($action['sitter_id'] ?? ($notif['entity_id'] ?? 0));
            } elseif ($type === 'breeder') {
                $providerId = (int)($action['breeder_id'] ?? ($notif['entity_id'] ?? 0));
            }
            if ($providerId > 0) {
                $providerIds[$providerId] = true;
            }
        }
    }

    // Resolve provider avatars in one query (reflects latest uploaded photo)
    $avatarByUserId = [];
    if (!empty($providerIds)) {
        $ids = array_keys($providerIds);
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmtAv = $pdo->prepare("SELECT id, avatar FROM users WHERE id IN ($in)");
        $stmtAv->execute($ids);
        $rows = $stmtAv->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $id = (int)($r['id'] ?? 0);
            $av = trim((string)($r['avatar'] ?? ''));
            $avatarByUserId[$id] = ($av !== '') ? $av : $defaultAvatar;
        }
    }

    // Attach provider avatar URL (or default) for relevant notifications
    foreach ($notifications as &$notif) {
        $type = (string)($notif['type'] ?? '');
        if (!in_array($type, ['trainer', 'sitter', 'breeder'], true)) {
            continue;
        }

        // Upgrade legacy decline messages so the reason stays on one line
        $title = (string)($notif['title'] ?? '');
        if (stripos($title, 'declined') !== false) {
            $notif['message'] = $normalizeReasonInline($notif['message'] ?? '');
        }

        $action = is_array($notif['action_data'] ?? null) ? $notif['action_data'] : [];
        $providerId = 0;
        if ($type === 'trainer') {
            $providerId = (int)($action['trainer_id'] ?? ($notif['entity_id'] ?? 0));
        } elseif ($type === 'sitter') {
            $providerId = (int)($action['sitter_id'] ?? ($notif['entity_id'] ?? 0));
        } elseif ($type === 'breeder') {
            $providerId = (int)($action['breeder_id'] ?? ($notif['entity_id'] ?? 0));
        }

        $notif['provider_avatar'] = $providerId > 0
            ? ($avatarByUserId[$providerId] ?? $defaultAvatar)
            : $defaultAvatar;
    }
    
    // Count unread
    $unread_count = count(array_filter($notifications, fn($n) => !$n['is_read']));
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
    
} catch (Exception $e) {
    error_log('Get notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch notifications'
    ]);
}
