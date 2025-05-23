<?php
// config.php - Database configuration
$host = 'localhost';
$db = 'nuttyloves_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>