<?php
include 'db_connect.php';

// Get the servo values from POST
$servo1 = $_POST['servo1'] ?? 90;
$servo2 = $_POST['servo2'] ?? 90;
$servo3 = $_POST['servo3'] ?? 90;
$servo4 = $_POST['servo4'] ?? 90;
$servo5 = $_POST['servo5'] ?? 90;
$servo6 = $_POST['servo6'] ?? 90;

// Insert the new pose
$stmt = $conn->prepare("INSERT INTO poses (servo1, servo2, servo3, servo4, servo5, servo6) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $servo1, $servo2, $servo3, $servo4, $servo5, $servo6);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode(['success' => true, 'id' => $id]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>