<?php

require_once __DIR__ . '/env.php';
$host     = 'localhost';
$username = 'root';
$password = $_ENV['DB_PASSWORD'];
$database = 'pinterest';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("<h3 style='color:red;font-family:Arial'>Database Connection Failed: " . mysqli_connect_error() . "<br><small>Check config/database.php</small></h3>");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>
