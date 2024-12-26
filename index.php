<?php
include('db.php');

// Handle sorting
$sort_by = $_GET['sort_by'] ?? 'created_at'; // Default sort by creation date
$order = $sort_by === 'timer_duration' ? 'ASC' : 'DESC'; // Duration ascending, date descending

$stmt = $conn->prepare("SELECT * FROM tasks ORDER BY $sort_by $order");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding tasks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = htmlspecialchars(trim($_POST['task_name']));
    $hours = intval($_POST['hours'] ?? 0);
    $minutes = intval($_POST['minutes'] ?? 0);
    $seconds = intval($_POST['seconds'] ?? 0);

    $timer_duration = ($hours * 3600) + ($minutes * 60) + $seconds;

    if (!empty($task_name) && $timer_duration > 0) {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, timer_duration) VALUES (?, ?)");
        $stmt->execute([$task_name, $timer_duration]);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Taskly</title>
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

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"> -->
    <style>
        body {
            background-color: #f8f9fa;
            color: #000;
        }
        .container {
            max-width: 700px;
            margin-top: 50px;
        }
        .app-title {
            color: #007bff;
            font-weight: bold;
            text-align: center;
        }
        .task-list-item {
            color: #000;
            cursor: pointer;
        }
        .task-duration {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="app-title">Taskly</h1>
        <p class="text-muted text-center">A simple task and timer management app</p>

        <!-- Sorting Dropdown -->
        <form method="GET" class="mb-3 text-center">
            <label for="sort_by" class="form-label">Sort Tasks By:</label>
            <select name="sort_by" id="sort_by" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                <option value="created_at" <?= $sort_by === 'created_at' ? 'selected' : '' ?>>Creation Date</option>
                <option value="timer_duration" <?= $sort_by === 'timer_duration' ? 'selected' : '' ?>>Timer Duration</option>
            </select>
        </form>

        <!-- Add Task Form -->
        <form method="POST" class="border rounded p-3 mb-4 bg-white">
            <div class="mb-3">
                <label for="taskName" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="taskName" name="task_name" placeholder="Enter Task Name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Set Timer</label>
                <div class="row">
                    <div class="col">
                        <input type="number" class="form-control" name="hours" placeholder="Hours" min="0" value="0">
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" name="minutes" placeholder="Minutes" min="0" value="0">
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" name="seconds" placeholder="Seconds" min="0" value="0">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Task</button>
        </form>

        <!-- Task List -->
        <h2 class="h5 mb-3">Your Tasks</h2>
        <ul class="list-group">
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <form action="view_task.php" method="POST" class="d-flex align-items-center">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <button type="submit" class="btn btn-link task-list-item p-0"><?= htmlspecialchars($task['task_name']) ?></button>
                    </form>
                    <span class="task-duration">
                        <?= gmdate("H:i:s", $task['timer_duration']) ?> <!-- Display timer in H:M:S -->
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
