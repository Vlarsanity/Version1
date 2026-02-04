<?php
// ================================
// IMMEDIATE LOGGING
// ================================
$immediateLogFile = dirname(__DIR__, 3) . '/logs/login_errors.log';
$immediateLogDir  = dirname($immediateLogFile);
if (!file_exists($immediateLogDir)) mkdir($immediateLogDir, 0777, true);

error_log("[" . date('Y-m-d H:i:s') . "] LOGIN-PROCESS.PHP STARTED\n", 3, $immediateLogFile);

// ================================
// CORS HEADER
// ================================
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ================================
// ERROR LOGGING FUNCTION
// ================================
$logDir  = dirname(__DIR__, 3) . '/logs';
$logFile = $logDir . '/login_errors.log';
if (!file_exists($logDir)) mkdir($logDir, 0777, true);

function write_log($message) {
    global $logFile;
    error_log("[" . date('Y-m-d H:i:s') . "] $message\n", 3, $logFile);
}

// ================================
// POST VALIDATION
// ================================
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
write_log("REQUEST METHOD: $requestMethod");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    write_log("REJECTED: Non-POST request detected");
    http_response_code(405);
    header("Allow: POST");
    echo json_encode([
        'success' => false,
        'message' => "POST method required. Received: $requestMethod"
    ]);
    exit;
}

// ================================
// LOGIN PROCESS
// ================================
session_start();
header('Content-Type: application/json');

include_once("../../../admin_v2/config/conn.php");

$identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
$password   = isset($_POST['password']) ? $_POST['password'] : '';
$rememberMe = isset($_POST['rememberMe']) ? (bool)$_POST['rememberMe'] : false;

if ($identifier === '' || $password === '') {
    write_log("VALIDATION ERROR: Missing identifier or password.");
    echo json_encode(['success' => false, 'message' => 'Please enter both ID/Email and Password']);
    exit;
}

// ================================
// FETCH USER
// ================================
$sql = "SELECT a.accountId, a.username, a.displayName, a.profileImage, a.password, a.accountStatus, a.accountType,
               a.languagePreference, a.loginAttempts, a.lockedUntil, a.emailVerified,
               ag.agentId, ag.agentType, ag.agentRole,
               e.employeeId, e.position, e.branch
        FROM accounts a
        LEFT JOIN agent ag ON a.accountId = ag.accountId
        LEFT JOIN employee e ON a.accountId = e.accountId
        WHERE (a.username = ? OR a.emailAddress = ?)
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    write_log("SQL PREPARE ERROR: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    write_log("LOGIN FAILED: No user matched identifier = $identifier");
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$user = $result->fetch_assoc();

// ================================
// CHECK STATUS AND LOCK
// ================================
if ($user['accountStatus'] !== 'active') {
    write_log("ACCOUNT INACTIVE: User {$user['username']}");
    echo json_encode(['success' => false, 'message' => 'Account is inactive']);
    exit;
}

if (!empty($user['lockedUntil']) && strtotime($user['lockedUntil']) > time()) {
    $minutes = ceil((strtotime($user['lockedUntil']) - time()) / 60);
    write_log("ACCOUNT LOCKED: User {$user['username']} until {$user['lockedUntil']}");
    echo json_encode(['success' => false, 'message' => "Account locked. Try again in {$minutes} minutes"]);
    exit;
}

if (!password_verify($password, $user['password'])) {
    $attempts  = ($user['loginAttempts'] ?? 0) + 1;
    $lockUntil = $attempts >= 5 ? date('Y-m-d H:i:s', strtotime('+30 minutes')) : null;
    write_log("WRONG PASSWORD: User {$user['username']} | Attempts: $attempts");

    $updateSql  = "UPDATE accounts SET loginAttempts = ?, lockedUntil = ? WHERE accountId = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("isi", $attempts, $lockUntil, $user['accountId']);
    $updateStmt->execute();

    $remaining = 5 - $attempts;
    $message   = $attempts >= 5 ? 'Too many attempts. Account locked for 30 minutes' : "Invalid credentials. {$remaining} attempts remaining";

    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if (!$user['emailVerified']) {
    write_log("EMAIL NOT VERIFIED: User {$user['username']}");
    echo json_encode(['success' => false, 'message' => 'Please verify your email first']);
    exit;
}

// ================================
// LOGIN SUCCESS - CREATE SESSION
// ================================
write_log("LOGIN SUCCESS: User {$user['username']} (ID: {$user['accountId']})");

// Reset login attempts
$updateSql  = "UPDATE accounts 
               SET loginAttempts = 0, 
                   lockedUntil = NULL, 
                   lastLoginAt = NOW() 
               WHERE accountId = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("i", $user['accountId']);
$updateStmt->execute();


// ----------------------
// PHP Session
// ----------------------
$_SESSION['loggedIn']           = true;
$_SESSION['accountId']          = $user['accountId'];
$_SESSION['username']           = $user['username'];
$_SESSION['displayName']        = $user['displayName'];
$_SESSION['accountType']        = $user['accountType'];
$_SESSION['languagePreference'] = $user['languagePreference'];

// Store session_id for logout-process.php
$_SESSION['session_id'] = session_id();


// ----------------------
// USER TYPE SPECIFIC
// ----------------------
if ($user['accountType'] === 'agent') {
    $_SESSION['agentId']   = $user['agentId'];
    $_SESSION['agentType'] = $user['agentType'];
    $_SESSION['agentRole'] = $user['agentRole'];
}

if ($user['accountType'] === 'employee') {
    $_SESSION['employeeId'] = $user['employeeId'];
    $_SESSION['position']   = $user['position'];
    $_SESSION['branch']     = $user['branch'];
}


// ================================
// CREATE USER SESSION RECORD
// ================================
$sessionId = session_id();
$ip        = $_SERVER['REMOTE_ADDR']        ?? '';
$ua        = $_SERVER['HTTP_USER_AGENT']    ?? '';
$now       = date('Y-m-d H:i:s');

write_log("CREATING USER_SESSION RECORD: session_id=$sessionId | accountId={$user['accountId']} | ip=$ip");

$insertSql = "INSERT INTO user_sessions 
              (session_id, accountid, login_time, last_activity, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?)";

$insertSession = $conn->prepare($insertSql);
if (!$insertSession) {
    write_log("DB ERROR (prepare failed): " . $conn->error);
} else {
    $insertSession->bind_param("sissss",
        $sessionId,
        $user['accountId'],
        $now,
        $now,
        $ip,
        $ua
    );

    if ($insertSession->execute()) {
        write_log("USER_SESSION CREATED SUCCESSFULLY");
    } else {
        write_log("DB ERROR (execute failed): " . $insertSession->error);
    }
}





// ----------------------
// REDIRECT URL
// ----------------------
$redirectUrls = [
    'admin'    => 'super/overview.php',
    'agent'    => 'agent/overview.php',
    'employee' => '../../../../html/backend/Admin Section/admin-dashboard.php',
    'guide'    => 'guide/assigned-schedule-detail.html',
];

$redirect = $redirectUrls[$user['accountType']] ?? 'dashboard.php';

// ----------------------
// RETURN JSON
// ----------------------
echo json_encode([
    'success'  => true,
    'message'  => 'Login successful',
    'redirect' => $redirect,
]);

$stmt->close();
$conn->close();
?>
