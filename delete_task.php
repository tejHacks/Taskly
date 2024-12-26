<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);

    // Delete the task from the database
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);

    // Redirect back to the main page
    header("Location: index.php");
    exit;
} else {
    die("Invalid request.");
}
?>
