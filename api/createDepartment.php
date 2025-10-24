<?php
header('Content-Type: application/json');
require_once '../config/db.php'; // adjust path if needed

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['name']) || empty($data['code'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: name and code.'
    ]);
    exit;
}

$name = trim($data['name']);
$code = trim($data['code']);

try {
    $pdo = getDBConnection();

    // Check for duplicate name or code
    $check = $pdo->prepare("SELECT * FROM departments WHERE department_name = ? OR department_code = ?");
    $check->execute([$name, $code]);

    if ($check->rowCount() > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Department name or code already exists.'
        ]);
        exit;
    }

    // Insert new department
    $stmt = $pdo->prepare("
        INSERT INTO departments (department_name, department_code, is_active)
        VALUES (?, ?, 1)
    ");
    $stmt->execute([$name, $code]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Department added successfully.',
        'data' => [
            'department_id' => $pdo->lastInsertId(),
            'department_name' => $name,
            'department_code' => $code
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
