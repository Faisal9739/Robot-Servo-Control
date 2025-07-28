<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robot Servo Control</title>
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
            max-width: 1200px;
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
        
        .sliders-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .slider-group {
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .slider-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: 500;
            color: #495057;
        }
        
        .slider-value {
            background: #0d6efd;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            min-width: 40px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .slider-input {
            width: 100%;
            height: 30px;
            margin-bottom: 10px;
            -webkit-appearance: none;
            background: linear-gradient(to right, #0d6efd, #dc3545);
            border-radius: 15px;
            outline: none;
        }
        
        .slider-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 25px;
            height: 25px;
            background: white;
            border-radius: 50%;
            border: 2px solid #0d6efd;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .buttons-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            min-width: 120px;
        }
        
        .btn-reset {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-save {
            background: #198754;
            color: white;
        }
        
        .btn-run {
            background: #0d6efd;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .poses-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.9rem;
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
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: opacity 0.2s;
        }
        
        .load-btn {
            background-color: #0d6efd;
            color: white;
            margin-right: 5px;
        }
        
        .remove-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .action-btn:hover {
            opacity: 0.9;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .sliders-container {
                grid-template-columns: 1fr;
            }
            
            .buttons-container {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Robot Servo Control</h1>
        
        <div class="card">
            <h2>Servo Control Panel</h2>
            <div class="sliders-container" id="servoSliders">
                <!-- Sliders will be generated by JavaScript -->
            </div>
            
            <div class="buttons-container">
                <button class="btn btn-reset" id="resetBtn">Reset</button>
                <button class="btn btn-save" id="saveBtn">Save Pose</button>
                <button class="btn btn-run" id="runBtn">Run</button>
            </div>
        </div>
        
        <div class="card">
            <h2>Saved Poses</h2>
            <div class="poses-container">
                <table id="posesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servo 1</th>
                            <th>Servo 2</th>
                            <th>Servo 3</th>
                            <th>Servo 4</th>
                            <th>Servo 5</th>
                            <th>Servo 6</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="posesList">
                        <!-- Poses will be loaded by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Robot Servo Control System</p>
    </footer>
    
    <script>
        // Servo data and state
        const servos = [
            { id: 1, name: 'Servo 1', value: 90 },
            { id: 2, name: 'Servo 2', value: 90 },
            { id: 3, name: 'Servo 3', value: 90 },
            { id: 4, name: 'Servo 4', value: 90 },
            { id: 5, name: 'Servo 5', value: 90 },
            { id: 6, name: 'Servo 6', value: 90 }
        ];
        
        // DOM Elements
        const slidersContainer = document.getElementById('servoSliders');
        const posesList = document.getElementById('posesList');
        const resetBtn = document.getElementById('resetBtn');
        const saveBtn = document.getElementById('saveBtn');
        const runBtn = document.getElementById('runBtn');
        
        // Initialize sliders
        function initSliders() {
            slidersContainer.innerHTML = '';
            
            servos.forEach(servo => {
                const sliderGroup = document.createElement('div');
                sliderGroup.className = 'slider-group';
                
                sliderGroup.innerHTML = `
                    <div class="slider-header">
                        <span>${servo.name}</span>
                        <span class="slider-value" id="value${servo.id}">${servo.value}°</span>
                    </div>
                    <input 
                        type="range" 
                        min="0" 
                        max="180" 
                        value="${servo.value}" 
                        class="slider-input" 
                        id="slider${servo.id}"
                        data-servo="${servo.id}"
                    >
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.8rem;">0°</span>
                        <span style="font-size: 0.8rem;">90°</span>
                        <span style="font-size: 0.8rem;">180°</span>
                    </div>
                `;
                
                slidersContainer.appendChild(sliderGroup);
                
                // Add event listener
                const slider = document.getElementById(`slider${servo.id}`);
                const valueDisplay = document.getElementById(`value${servo.id}`);
                
                slider.addEventListener('input', function() {
                    const value = this.value;
                    valueDisplay.textContent = `${value}°`;
                    servos[servo.id - 1].value = parseInt(value);
                });
            });
        }
        
        // Load poses from the database
        function loadPoses() {
            fetch('get_poses.php')
                .then(response => response.json())
                .then(poses => {
                    renderPoses(poses);
                })
                .catch(error => console.error('Error loading poses:', error));
        }
        
        // Render poses in the table
        function renderPoses(poses) {
            posesList.innerHTML = '';
            
            poses.forEach(pose => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${pose.id}</td>
                    <td>${pose.servo1}°</td>
                    <td>${pose.servo2}°</td>
                    <td>${pose.servo3}°</td>
                    <td>${pose.servo4}°</td>
                    <td>${pose.servo5}°</td>
                    <td>${pose.servo6}°</td>
                    <td>
                        <button class="action-btn load-btn" data-id="${pose.id}">Load</button>
                        <button class="action-btn remove-btn" data-id="${pose.id}">Remove</button>
                    </td>
                `;
                
                posesList.appendChild(row);
            });
            
            // Add event listeners to action buttons
            document.querySelectorAll('.load-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const poseId = this.getAttribute('data-id');
                    loadPose(poseId);
                });
            });
            
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const poseId = this.getAttribute('data-id');
                    removePose(poseId);
                });
            });
        }
        
        // Load pose into sliders
        function loadPose(poseId) {
            fetch(`load_pose.php?id=${poseId}`)
                .then(response => response.json())
                .then(pose => {
                    if (pose) {
                        // Update sliders
                        for (let i = 1; i <= 6; i++) {
                            const servoValue = pose[`servo${i}`];
                            const slider = document.getElementById(`slider${i}`);
                            const valueDisplay = document.getElementById(`value${i}`);
                            
                            if (slider && valueDisplay) {
                                slider.value = servoValue;
                                valueDisplay.textContent = `${servoValue}°`;
                                servos[i - 1].value = servoValue;
                            }
                        }
                        
                        showNotification(`Pose #${poseId} loaded`);
                    }
                })
                .catch(error => console.error('Error loading pose:', error));
        }
        
        // Remove pose - FIXED VERSION
        function removePose(poseId) {
            // Create form data to send the pose ID
            const formData = new FormData();
            formData.append('id', poseId);
            
            fetch('delete_pose.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Pose #${poseId} removed`);
                    loadPoses(); // Reload the poses
                } else {
                    showNotification(`Error: ${data.error}`);
                }
            })
            .catch(error => {
                console.error('Error removing pose:', error);
                showNotification('Error removing pose');
            });
        }
        
        // Reset all sliders to 90
        function resetSliders() {
            servos.forEach(servo => {
                const slider = document.getElementById(`slider${servo.id}`);
                const valueDisplay = document.getElementById(`value${servo.id}`);
                
                if (slider && valueDisplay) {
                    slider.value = 90;
                    valueDisplay.textContent = '90°';
                    servo.value = 90;
                }
            });
            
            showNotification('All servos reset to 90°');
        }
        
        // Save current pose
        function savePose() {
            const servoValues = servos.map(s => s.value);
            
            // Create form data
            const formData = new FormData();
            for (let i = 0; i < servoValues.length; i++) {
                formData.append(`servo${i+1}`, servoValues[i]);
            }
            
            fetch('save_pose.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Pose saved (ID: ${data.id})`);
                    loadPoses(); // Reload poses
                }
            })
            .catch(error => console.error('Error saving pose:', error));
        }
        
        // Run pose - redirect to get_run_pose.php
        function runPose() {
            // Save the current pose as the last pose to run
            const servoValues = servos.map(s => s.value);
            const formData = new FormData();
            for (let i = 0; i < servoValues.length; i++) {
                formData.append(`servo${i+1}`, servoValues[i]);
            }
            
            fetch('save_pose.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to get_run_pose.php
                    window.location.href = `get_run_pose.php?pose_id=${data.id}`;
                }
            })
            .catch(error => console.error('Error saving pose:', error));
        }
        
        // Show notification
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                background-color: #212529;
                color: white;
                border-radius: 4px;
                z-index: 1000;
                font-size: 0.9rem;
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Initialize the application
        function initApp() {
            initSliders();
            loadPoses(); // Load poses from database
            
            // Add event listeners
            resetBtn.addEventListener('click', resetSliders);
            saveBtn.addEventListener('click', savePose);
            runBtn.addEventListener('click', runPose);
        }
        
        // Start the app when DOM is loaded
        document.addEventListener('DOMContentLoaded', initApp);
    </script>
</body>
</html>