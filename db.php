<?php
$host = 'localhost'; // Your DB host
$dbname = 'taskly'; // Your database name
$username = 'root'; // Your username
$password = ''; // Your password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
