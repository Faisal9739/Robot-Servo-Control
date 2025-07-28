<?php
include 'db_connect.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$pose_id = isset($data['pose_id']) ? intval($data['pose_id']) : 0;

if ($pose_id) {
    // Set status to 0 for the specified pose
    $stmt = $conn->prepare("UPDATE poses SET status = 0 WHERE id = ?");
    $stmt->bind_param("i", $pose_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid pose ID']);
}

$conn->close();
?>