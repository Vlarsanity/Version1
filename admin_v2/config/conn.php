<?php
// ==========================
// Database Connection
// ==========================
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'smarttravel';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    write_log("DB connection failed: " . $conn->connect_error);
    echo json_encode(['success'=>false,'message'=>'Database connection failed']);
    exit;
}


?>