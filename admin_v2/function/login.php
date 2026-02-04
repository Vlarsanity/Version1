<?php
session_start();

// ====================================================
// DEVELOPMENT SETTINGS
// ====================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON header immediately
header('Content-Type: application/json; charset=utf-8');

// ====================================================
// DATABASE CONNECTION
// ====================================================
$servername = "47.128.242.86";
$username   = "root";
$password   = "";
$dbname     = "smarttravel";
$port       = 3306;

// Log file
$logFolder = __DIR__ . '/logs';
if (!file_exists($logFolder)) mkdir($logFolder, 0777, true);
$logFile = $logFolder . '/db_errors.log';

function log_db_error($message) {
    global $logFile;
    $time = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$time] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Connect
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    log_db_error("Database connection failed: ({$conn->connect_errno}) {$conn->connect_error}");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error'   => $conn->connect_error,
        'errno'   => $conn->connect_errno
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

if (!$conn->set_charset("utf8")) {
    log_db_error("Error setting charset utf8: {$conn->error}");
}

// ====================================================
// HELPER FUNCTIONS
// ====================================================
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function log_activity($accountId, $action, $details = '') {
    $logFolder = __DIR__ . '/logs';
    if (!file_exists($logFolder)) mkdir($logFolder, 0777, true);
    $logFile = $logFolder . '/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $msg = "[$timestamp] Account: $accountId | $action | $details" . PHP_EOL;
    file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
}

// ====================================================
// REQUEST VALIDATION
// ====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$loginId  = sanitize_input($input['loginId'] ?? '');
$password = $input['password'] ?? '';

if (empty($loginId) || empty($password)) {
    send_json_response(['success' => false, 'message' => 'All fields are required'], 400);
}

// ====================================================
// FETCH ACCOUNT
// ====================================================
$stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ? OR emailAddress = ? LIMIT 1");
$stmt->bind_param("ss", $loginId, $loginId);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

if (!$account) {
    log_activity(null, "LOGIN FAILED", "Account not found: $loginId");
    send_json_response(['success' => false, 'message' => 'Account not found'], 404);
}

// ====================================================
// STATUS & EMAIL VERIFICATION
// ====================================================
if ($account['accountStatus'] !== 'active') {
    log_activity($account['accountId'], "LOGIN FAILED", "Inactive account");
    send_json_response(['success' => false, 'message' => 'Your account is inactive'], 403);
}

if ($account['emailVerified'] == 0) {
    log_activity($account['accountId'], "LOGIN FAILED", "Email not verified");
    send_json_response(['success' => false, 'message' => 'Please verify your email first'], 403);
}

if (!empty($account['lockedUntil']) && strtotime($account['lockedUntil']) > time()) {
    $remaining = ceil((strtotime($account['lockedUntil']) - time()) / 60);
    log_activity($account['accountId'], "LOGIN FAILED", "Account locked");
    send_json_response(['success' => false, 'message' => "Account locked. Try again in $remaining minutes"], 423);
}

// ====================================================
// PASSWORD CHECK
// ====================================================
if (!password_verify($password, $account['password'])) {
    $attempts = $account['loginAttempts'] + 1;
    $updateStmt = $conn->prepare("UPDATE accounts SET loginAttempts = ? WHERE accountId = ?");
    $updateStmt->bind_param("ii", $attempts, $account['accountId']);
    $updateStmt->execute();

    if ($attempts >= 5) {
        $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $lockStmt = $conn->prepare("UPDATE accounts SET lockedUntil = ? WHERE accountId = ?");
        $lockStmt->bind_param("si", $lockUntil, $account['accountId']);
        $lockStmt->execute();
        log_activity($account['accountId'], "ACCOUNT LOCKED", "Too many failed attempts");
        send_json_response(['success' => false, 'message' => 'Too many failed attempts. Account locked for 15 minutes'], 423);
    }

    log_activity($account['accountId'], "LOGIN FAILED", "Incorrect password. Attempts=$attempts");
    send_json_response(['success' => false, 'message' => "Incorrect password. Attempts: $attempts/5"], 401);
}

// ====================================================
// RESET ATTEMPTS ON SUCCESS
// ====================================================
$resetStmt = $conn->prepare("UPDATE accounts SET loginAttempts = 0, lockedUntil = NULL, lastLoginAt = NOW() WHERE accountId = ?");
$resetStmt->bind_param("i", $account['accountId']);
$resetStmt->execute();

log_activity($account['accountId'], "LOGIN SUCCESS");

// ====================================================
// CREATE SESSION
// ====================================================
$_SESSION['accountId']         = $account['accountId'];
$_SESSION['username']          = $account['username'];
$_SESSION['displayName']       = $account['displayName'];
$_SESSION['emailAddress']      = $account['emailAddress'];
$_SESSION['accountType']       = $account['accountType'];
$_SESSION['profileImage']      = $account['profileImage'];
$_SESSION['preferredLanguage'] = $account['preferredLanguage'];

// ====================================================
// SUCCESS RESPONSE
// ====================================================
send_json_response(['success' => true, 'message' => 'Login successful']);
