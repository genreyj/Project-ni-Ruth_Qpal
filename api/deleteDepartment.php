<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // Get the department_id from the request (supports JSON or query param)
    $data = json_decode(file_get_contents("php://input"), true);
    $department_id = $data['department_id'] ?? $_GET['department_id'] ?? null;

    if (!$department_id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing department_id"]);
        exit;
    }

    $conn = getDBConnection();

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM departments WHERE department_id = :department_id");
    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Department deleted successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Department not found or already deleted."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
