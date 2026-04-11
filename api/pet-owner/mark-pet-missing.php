<?php
/**
 * Mark Pet Missing API
 * Converts a pet from the My Pets page into a Lost & Found report.
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = db();
    $userId = $_SESSION['user_id'];

    $petId = $_POST['pet_id'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $datetime = $_POST['datetime'] ?? ''; // backward compatibility
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $circumstances = trim($_POST['circumstances'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $reward = $_POST['reward'] ?? null;

    if (empty($petId) || empty($location)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Required fields: pet_id, location'
        ]);
        exit;
    }

    $petStmt = $db->prepare("\n        SELECT id, user_id, name, species, breed, color, photo_url\n        FROM pets\n        WHERE id = ? AND user_id = ?\n    ");
    $petStmt->execute([(int)$petId, (int)$userId]);
    $pet = $petStmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pet not found or unauthorized'
        ]);
        exit;
    }

    // Prefer date/time split fields; fallback to datetime-local if present.
    if (empty($date) && !empty($datetime)) {
        $dateTimeParts = explode('T', $datetime);
        $date = $dateTimeParts[0] ?? '';
        $time = $dateTimeParts[1] ?? '';
    }

    if (empty($date)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Last seen date is required'
        ]);
        exit;
    }

    if (empty($time)) {
        $time = date('H:i');
    }

    $userStmt = $db->prepare("SELECT email, phone FROM users WHERE id = ?");
    $userStmt->execute([(int)$userId]);
    $userContact = $userStmt->fetch(PDO::FETCH_ASSOC) ?: ['email' => '', 'phone' => ''];

    // Handle up to 3 uploaded photos (optional)
    $photoPaths = [];
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . '/../../uploads/lost-found/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;
        $fileCount = min(count($_FILES['photos']['name']), 3);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpName = $_FILES['photos']['tmp_name'][$i];
            $fileName = $_FILES['photos']['name'][$i];
            $fileSize = $_FILES['photos']['size'][$i];
            $fileType = $_FILES['photos']['type'][$i];

            if (!in_array($fileType, $allowedTypes) || $fileSize > $maxSize) {
                continue;
            }

            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('pet_missing_') . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $photoPaths[] = '/PETVET/uploads/lost-found/' . $newFileName;
            }
        }
    }

    if (empty($photoPaths) && !empty($pet['photo_url'])) {
        $photoPaths = [$pet['photo_url']];
    }

    $description = [
        'species' => $pet['species'] ?? '',
        'name' => $pet['name'] ?? null,
        'breed' => $pet['breed'] ?? '',
        'color' => $pet['color'] ?? '',
        'notes' => $circumstances,
        'features' => $features,
        'reward' => ($reward !== null && $reward !== '') ? (float)$reward : null,
        'time' => $time,
        'photos' => $photoPaths,
        'contact' => [
            'phone' => $userContact['phone'] ?? '',
            'phone2' => '',
            'email' => $userContact['email'] ?? ''
        ],
        'latitude' => $latitude !== null && $latitude !== '' ? (float)$latitude : null,
        'longitude' => $longitude !== null && $longitude !== '' ? (float)$longitude : null,
        'user_id' => (int)$userId,
        'pet_id' => (int)$petId,
        'submitted_at' => date('Y-m-d H:i:s')
    ];

    $stmt = $db->prepare("\n        INSERT INTO LostFoundReport (type, location, date_reported, description)\n        VALUES (:type, :location, :date_reported, :description)\n    ");

    $stmt->execute([
        ':type' => 'lost',
        ':location' => $location,
        ':date_reported' => $date,
        ':description' => json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Pet successfully marked as missing',
        'report_id' => $db->lastInsertId()
    ]);
} catch (PDOException $e) {
    error_log('Database error in mark-pet-missing.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Error in mark-pet-missing.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
