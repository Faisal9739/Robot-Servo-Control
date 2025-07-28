<?php
include 'db_connect.php';

$sql = "SELECT id, servo1, servo2, servo3, servo4, servo5, servo6 FROM poses ORDER BY id DESC";
$result = $conn->query($sql);

$poses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $poses[] = $row;
    }
}

echo json_encode($poses);

$conn->close();
?>