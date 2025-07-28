<?php
include 'db_connect.php';

// Get the pose ID from URL parameter
$pose_id = isset($_GET['pose_id']) ? intval($_GET['pose_id']) : 0;

// Get the pose from the database
$stmt = $conn->prepare("SELECT * FROM poses WHERE id = ?");
$stmt->bind_param("i", $pose_id);
$stmt->execute();
$result = $stmt->get_result();
$pose = $result->fetch_assoc();

if (!$pose) {
    die("Pose not found");
}

// Set status to 1 for this pose
$updateStmt = $conn->prepare("UPDATE poses SET status = 1 WHERE id = ?");
$updateStmt->bind_param("i", $pose['id']);
$updateStmt->execute();
$updateStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Pose</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #212529;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }
        
        h1 {
            color: #495057;
            margin-bottom: 25px;
            font-size: 1.8rem;
            text-align: center;
        }
        
        h2 {
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 1.4rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }
        
        .status-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .status-active {
            background-color: #198754;
        }
        
        .status-inactive {
            background-color: #dc3545;
        }
        
        .status-text {
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            background: #0d6efd;
            color: white;
            display: block;
            margin: 0 auto;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Running Pose #<?php echo $pose['id']; ?></h1>
        
        <div class="card">
            <h2>Servo Values</h2>
            <table>
                <thead>
                    <tr>
                        <th>Servo</th>
                        <th>Angle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <tr>
                        <td>Servo <?php echo $i; ?></td>
                        <td><?php echo $pose['servo'.$i]; ?>°</td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            
            <h2>Pose Status</h2>
            <div class="status-container">
                <div class="status-indicator status-active"></div>
                <div class="status-text">Active (1)</div>
            </div>
            
            <button id="updateStatusBtn" class="btn">Complete Execution</button>
        </div>
    </div>
    
    <script>
        document.getElementById('updateStatusBtn').addEventListener('click', function() {
            // Call update_status.php to set status to 0
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pose_id: <?php echo $pose['id']; ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pose execution completed! Status updated to inactive.');
                    window.location.href = 'index.php';
                }
            });
        });
    </script>
</body>
</html>