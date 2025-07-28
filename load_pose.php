<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM poses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pose = $result->fetch_assoc();
        echo json_encode($pose);
    } else {
        echo json_encode(null);
    }
    
    $stmt->close();
} else {
    echo json_encode(null);
}

$conn->close();
?>