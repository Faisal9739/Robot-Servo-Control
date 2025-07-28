<?php
include 'db_connect.php';

// Check if the form data contains the ID
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM poses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Check if any row was actually deleted
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No pose found with that ID']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request - ID not provided']);
}

$conn->close();
?>