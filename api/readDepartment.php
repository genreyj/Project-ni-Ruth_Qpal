<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // Create PDO connection
    $conn = getDBConnection();

    // Query departments
    $stmt = $conn->query("SELECT department_id, department_name, department_code, is_active FROM departments ORDER BY department_id ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($departments);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
