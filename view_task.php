<?php
include('db.php');

// Fetch task details securely via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die("Task not found.");
    }
} else {
    // die("Invalid request.");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($task['task_name']) ?> - Taskly</title>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A small web app for reading and getting commonly used Git commands">
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.css" rel="stylesheet">
    <link href="assets/boxicons/css/boxicons.css" rel="stylesheet">
    <link href="assets/boxicons/css/boxicons.min.css" rel="stylesheet">

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>
    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
 <style>
        body {
            background-color: #f8f9fa;
            color: #000;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            text-align: center;
        }
        .app-title {
            color: #007bff;
            font-weight: bold;
        }
        .timer {
            font-size: 5rem;
            font-weight: bold;
            color: #28a745;
            margin-top: 20px;
        }
        .task-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #6c757d;
        }
        .progress-bar {
            height: 10px;
            background-color: #007bff;
            transition: width 1s;
        }
    </style>
    <script>
        let timer = <?= $task['timer_duration'] ?>; // Initial timer duration in seconds
        const totalDuration = timer;
        let interval;

        function formatTime(seconds) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${hrs}:${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
        }

        function startTimer() {
            if (!interval) {
                interval = setInterval(() => {
                    if (timer > 0) {
                        timer--;
                        document.getElementById('timer').textContent = formatTime(timer);
                        updateProgressBar();
                    } else {
                        clearInterval(interval);
                        new Audio('assets/alert.wav').play(); // Play alert sound
                        alert('Time is up!');
                    }
                }, 1000);
            }
        }

        function pauseTimer() {
            clearInterval(interval);
            interval = null;
        }

        function resetTimer() {
            timer = totalDuration;
            document.getElementById('timer').textContent = formatTime(timer);
            clearInterval(interval);
            interval = null;
            updateProgressBar();
        }

        function updateProgressBar() {
            const progress = ((totalDuration - timer) / totalDuration) * 100;
            document.getElementById('progress-bar').style.width = `${progress}%`;
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('timer').textContent = formatTime(timer);
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="app-title">Taskly</h1>
        <button class="btn btn-secondary mb-4" onclick="toggleDarkMode()">Toggle Dark Mode</button>
        
        <div id="timer" class="timer"></div>
        <div class="task-title"><?= htmlspecialchars($task['task_name']) ?></div>
        <div class="progress mt-3">
            <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
        </div>

        <div class="d-flex justify-content-around mt-4">
            <button class="btn btn-success" onclick="startTimer()">
                <i class="fa fa-play"></i> Start
            </button>
            <button class="btn btn-warning" onclick="pauseTimer()">
                <i class="fa fa-pause"></i> Pause
            </button>
            <button class="btn btn-danger" onclick="resetTimer()">
                <i class="fa fa-recycle"></i> Reset
            </button>
        </div>

        <form method="POST" action="delete_task.php" class="mt-4">
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <button type="submit" class="btn btn-danger">
                <i class="fa fa-trash"></i> Delete Task
            </button>
        </form>

        <a href="index.php" class="btn btn-secondary mt-4">
            <i class="fa fa-arrow-left"></i> Back to Tasks
        </a>
    </div>
</body>
</html>
