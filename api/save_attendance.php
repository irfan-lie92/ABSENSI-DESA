<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['staff_id']) || !isset($input['date']) || !isset($input['signature'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    // Check if attendance already exists
    $stmt = $pdo->prepare("SELECT id FROM attendance WHERE staff_id = ? AND date = ?");
    $stmt->execute([$input['staff_id'], $input['date']]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Attendance already recorded for this date']);
        exit();
    }
    
    // Save new attendance record
    $stmt = $pdo->prepare("
        INSERT INTO attendance (staff_id, date, signature, created_by, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $input['staff_id'],
        $input['date'],
        $input['signature'],
        $_SESSION['user_id']
    ]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>