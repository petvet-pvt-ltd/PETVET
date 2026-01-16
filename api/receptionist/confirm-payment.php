<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('receptionist', '/PETVET/index.php');

if (empty($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic not set']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['appointment_id']) || !isset($input['invoice_data'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required data']);
    exit;
}

$appointmentId = (int)$input['appointment_id'];
$invoiceData = $input['invoice_data'];
$clinicId = (int)$_SESSION['clinic_id'];
$receptionistId = (int)($_SESSION['user_id'] ?? 0);

if ($appointmentId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid appointment ID']);
    exit;
}

try {
    $pdo = db();
    
    // Disable strict mode warnings for this operation
    $pdo->exec("SET SESSION sql_mode = ''");
    
    // Verify appointment belongs to this clinic and is completed
    $stmt = $pdo->prepare("
        SELECT id, status 
        FROM appointments 
        WHERE id = ? AND clinic_id = ?
    ");
    $stmt->execute([$appointmentId, $clinicId]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appt) {
        echo json_encode(['success' => false, 'error' => 'Appointment not found or does not belong to this clinic']);
        exit;
    }
    
    if ($appt['status'] !== 'completed') {
        echo json_encode(['success' => false, 'error' => 'Only completed appointments can be marked as paid. Current status: ' . $appt['status']]);
        exit;
    }
    
    // Check if payment already exists for this appointment
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE appointment_id = ?");
    $stmt->execute([$appointmentId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Payment already recorded for this appointment']);
        exit;
    }
    
    // Start transaction
    try {
        $pdo->beginTransaction();
    } catch (PDOException $e) {
        // If transaction already active, rollback and start fresh
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $pdo->beginTransaction();
    }
    
    try {
        // Update appointment status to 'paid'
        $stmt = $pdo->prepare("
            UPDATE appointments 
            SET status = 'paid',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND clinic_id = ? AND status = 'completed'
        ");
        $result = $stmt->execute([$appointmentId, $clinicId]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to update appointment status. Appointment may have already been processed.");
        }
        
        // Check if payments table exists, if not create it
        $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
        $tableExists = $stmt->rowCount() > 0;
        
        if (!$tableExists) {
            // Create payments table
            $pdo->exec("
                CREATE TABLE payments (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    appointment_id INT NOT NULL,
                    invoice_number VARCHAR(50) NOT NULL,
                    payment_date DATE NOT NULL,
                    payment_method VARCHAR(20) NOT NULL,
                    client_phone VARCHAR(20),
                    invoice_note TEXT,
                    gross_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                    discount_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                    card_fee DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                    items_data JSON,
                    receptionist_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
                    FOREIGN KEY (receptionist_id) REFERENCES users(id) ON DELETE SET NULL,
                    INDEX idx_appointment (appointment_id),
                    INDEX idx_invoice (invoice_number),
                    INDEX idx_date (payment_date)
                )
            ");
        }
        
        // Prepare and validate all data
        $invoiceNo = isset($invoiceData['invoiceNo']) ? substr(trim((string)$invoiceData['invoiceNo']), 0, 50) : 'INV-' . $appointmentId;
        
        $paymentDate = date('Y-m-d');
        if (isset($invoiceData['invoiceDate']) && !empty($invoiceData['invoiceDate'])) {
            $dateStr = trim($invoiceData['invoiceDate']);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                $paymentDate = $dateStr;
            }
        }
        
        $paymentMethod = isset($invoiceData['paymentMethod']) ? substr(strtoupper(trim((string)$invoiceData['paymentMethod'])), 0, 20) : 'CASH';
        if (!in_array($paymentMethod, ['CASH', 'CARD'])) {
            $paymentMethod = 'CASH';
        }
        
        $clientPhone = null;
        if (isset($invoiceData['clientPhone']) && !empty($invoiceData['clientPhone'])) {
            $clientPhone = substr(trim((string)$invoiceData['clientPhone']), 0, 20);
        }
        
        $invoiceNote = null;
        if (isset($invoiceData['note']) && !empty($invoiceData['note'])) {
            $invoiceNote = trim((string)$invoiceData['note']);
        }
        
        $grossAmount = 0.0;
        if (isset($invoiceData['gross'])) {
            $grossAmount = round((float)$invoiceData['gross'], 2);
        }
        
        $discountAmount = 0.0;
        if (isset($invoiceData['discount'])) {
            $discountAmount = round((float)$invoiceData['discount'], 2);
        }
        
        $cardFee = 0.0;
        if (isset($invoiceData['cardFee'])) {
            $cardFee = round((float)$invoiceData['cardFee'], 2);
        }
        
        $totalAmount = 0.0;
        if (isset($invoiceData['total'])) {
            $totalAmount = round((float)$invoiceData['total'], 2);
        }
        
        // Prepare items data (clean it to avoid issues)
        $itemsToStore = [];
        if (isset($invoiceData['items']) && is_array($invoiceData['items'])) {
            foreach ($invoiceData['items'] as $item) {
                $itemsToStore[] = [
                    'description' => isset($item['description']) ? substr(trim((string)$item['description']), 0, 255) : '',
                    'qty' => isset($item['qty']) ? max(1, (int)$item['qty']) : 1,
                    'unitPrice' => isset($item['unitPrice']) ? round((float)$item['unitPrice'], 2) : 0.0
                ];
            }
        }
        
        $itemsJson = json_encode($itemsToStore, JSON_UNESCAPED_UNICODE);
        
        // Insert payment record
        $stmt = $pdo->prepare("
            INSERT INTO payments (
                appointment_id, 
                invoice_number, 
                payment_date, 
                payment_method,
                client_phone,
                invoice_note,
                gross_amount,
                discount_amount,
                card_fee,
                total_amount,
                items_data,
                receptionist_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $appointmentId,
            $invoiceNo,
            $paymentDate,
            $paymentMethod,
            $clientPhone,
            $invoiceNote,
            $grossAmount,
            $discountAmount,
            $cardFee,
            $totalAmount,
            $itemsJson,
            $receptionistId > 0 ? $receptionistId : null
        ]);
        
        $paymentId = $pdo->lastInsertId();
        
        // Commit transaction
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment confirmed successfully',
            'invoice_number' => $invoiceNo,
            'payment_id' => $paymentId
        ]);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("confirm-payment error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("confirm-payment error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error confirming payment: ' . $e->getMessage()
    ]);
}
