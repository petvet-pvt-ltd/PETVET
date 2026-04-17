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
    $datetime = $_POST['datetime'] ?? '';
    $circumstances = trim($_POST['circumstances'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $reward = $_POST['reward'] ?? null;

    if (empty($petId) || empty($location) || empty($datetime)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Required fields: pet_id, location, datetime'
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

    $dateTimeParts = explode('T', $datetime);
    $date = $dateTimeParts[0] ?? '';
    $time = $dateTimeParts[1] ?? '';

    if (empty($date)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid datetime format'
        ]);
        exit;
    }

    $userStmt = $db->prepare("SELECT email, phone FROM users WHERE id = ?");
    $userStmt->execute([(int)$userId]);
    $userContact = $userStmt->fetch(PDO::FETCH_ASSOC) ?: ['email' => '', 'phone' => ''];

    $reportData = [
        'species' => $pet['species'] ?? '',
        'name' => $pet['name'] ?? null,
        'breed' => $pet['breed'] ?? '',
        'color' => $pet['color'] ?? '',
        'age' => null,
        'notes' => $circumstances,
        'reward' => ($reward !== null && $reward !== '') ? (float)$reward : null,
        'urgency' => 'medium',
        'time' => $time,
        'photos' => !empty($pet['photo_url']) ? [$pet['photo_url']] : [],
        'contact' => [
            'phone' => $userContact['phone'] ?? '',
            'phone2' => '',
            'email' => $userContact['email'] ?? ''
        ],
        'latitude' => null,
        'longitude' => null,
        'user_id' => (int)$userId,
        'pet_id' => (int)$petId,
        'submitted_at' => date('Y-m-d H:i:s')
    ];

    // Use model to insert (saves to individual columns)
    require_once __DIR__ . '/../../models/PetOwner/LostFoundModel.php';
    $lostFoundModel = new LostFoundModel($db);
    $reportId = $lostFoundModel->insertReport('lost', $location, $date, $reportData);

    echo json_encode([
        'success' => true,
        'message' => 'Pet successfully marked as missing',
        'report_id' => $reportId
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
