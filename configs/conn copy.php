<?php
// Database credentials
$host     = "47.128.242.86";
$dbname   = "smarttravel";
$username = "root";
$password = "cloud1234";
$port     = 3306;

// Data Source Name
$dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

try {
    // PDO options for security + performance
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
    ];

    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    // Log error to file (optional)
    error_log("Database connection error: " . $e->getMessage());

    // Hide error details from users
    die("Database connection failed. Please try again later.");
}
?>
