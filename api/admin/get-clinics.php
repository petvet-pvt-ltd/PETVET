<?php
require_once '../../config/connect.php';
header('Content-Type: application/json');

try {
    // Get filter parameters
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    
    // Use PDO for consistent querying
    $pdo = db();
    
    $query = "SELECT 
        c.id,
        c.clinic_name,
        c.clinic_email,
        c.clinic_phone,
        c.clinic_address,
        c.district,
        c.city,
        c.verification_status,
        c.is_active,
        c.created_at,
        c.clinic_logo,
        c.license_document,
        COUNT(DISTINCT cs.id) as staff_count,
        COUNT(DISTINCT a.id) as appointment_count
    FROM clinics c
    LEFT JOIN clinic_staff cs ON c.id = cs.clinic_id
    LEFT JOIN appointments a ON c.id = a.clinic_id
    WHERE 1=1";
    
    // Apply status filter
    if ($status === 'pending') {
        $query .= " AND c.verification_status = 'pending'";
    } elseif ($status === 'approved') {
        $query .= " AND c.verification_status = 'approved'";
    } elseif ($status === 'rejected') {
        $query .= " AND c.verification_status = 'rejected'";
    }
    
    $query .= " GROUP BY c.id ORDER BY 
        CASE c.verification_status
            WHEN 'pending' THEN 1
            WHEN 'approved' THEN 2
            WHEN 'rejected' THEN 3
        END,
        c.created_at DESC";
    
    $stmt = $pdo->query($query);
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // For each clinic, fetch associated documents from role_verification_documents
    $docStmt = $pdo->prepare("
        SELECT vd.id, vd.document_type, vd.document_name, vd.file_path
        FROM role_verification_documents vd
        JOIN user_roles ur ON vd.user_role_id = ur.id
        JOIN roles r ON ur.role_id = r.id
        JOIN clinic_manager_profiles cmp ON cmp.user_id = ur.user_id
        WHERE r.role_name = 'clinic_manager'
          AND cmp.clinic_id = ?
        ORDER BY vd.uploaded_at DESC
    ");
    
    foreach ($clinics as &$clinic) {
        $docStmt->execute([$clinic['id']]);
        $docsRows = $docStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $clinic['documents'] = [];
        foreach ($docsRows as $d) {
            $label = $d['document_type'] === 'license' 
                ? 'License Document (PDF)' 
                : (ucfirst($d['document_type']) . ' Document');
            $clinic['documents'][] = [
                'id' => $d['id'],
                'label' => $label,
                'name' => $d['document_name'],
                'url' => '/PETVET/api/download-file.php?doc_id=' . (int)$d['id']
            ];
        }
    }
    unset($clinic);
    
    // Get statistics
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN verification_status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
    FROM clinics";
    
    $statsStmt = $pdo->query($statsQuery);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'clinics' => $clinics,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
