<?php
// ============================================
// IMMEDIATE LOGGING (runs even if script fails)
// ============================================
$immediateLogFile = dirname(__DIR__, 3) . '/logs/logout_errors.log';
$immediateLogDir  = dirname($immediateLogFile);

if (!file_exists($immediateLogDir)) {
    mkdir($immediateLogDir, 0777, true);
}

error_log("[" . date('Y-m-d H:i:s') . "] LOGOUT-PROCESS.PHP STARTED\n", 3, $immediateLogFile);


// ============================================
// CORS HEADERS
// ============================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


// ============================================
// LOGGING FUNCTION
// ============================================
$logDir  = dirname(__DIR__, 3) . '/logs';
$logFile = $logDir . '/logout_errors.log';

if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

function write_log($message) {
    global $logFile;
    error_log("[" . date('Y-m-d H:i:s') . "] $message\n", 3, $logFile);
}


// ============================================
// REQUEST VALIDATION
// ============================================
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
write_log("REQUEST METHOD: $requestMethod");

if ($requestMethod !== 'POST') {
    write_log("REJECTED: Non-POST request detected");

    http_response_code(405);
    header("Allow: POST");

    echo json_encode([
        'success' => false,
        'message' => "POST method required. Received: $requestMethod"
    ]);
    exit;
}


// ============================================
// START SESSION
// ============================================
session_start();
header("Content-Type: application/json");

include_once("../../../admin_v2/config/conn.php");


// ============================================
// VALIDATE ACTIVE SESSION
// ============================================
if (!isset($_SESSION['accountId'])) {
    write_log("LOGOUT ERROR: No active session found.");

    echo json_encode([
        'success' => false,
        'message' => 'No active session'
    ]);
    exit;
}

$accountId        = $_SESSION['accountId'];  // FIXED — corrected from ACCOUNTID
$currentSessionId = session_id();

write_log("LOGOUT ATTEMPT: accountId=$accountId | sessionId=$currentSessionId");


// ============================================
// DELETE user_session RECORD
// ============================================
$stmt = $conn->prepare(
    "DELETE FROM user_sessions WHERE session_id = ? AND accountid = ?"
);

$stmt->bind_param("si", $currentSessionId, $accountId);

if ($stmt->execute()) {
    write_log("USER_SESSION RECORD REMOVED for session_id=$currentSessionId");
} else {
    write_log("DATABASE ERROR deleting user_session: " . $stmt->error);
}


// ============================================
// DESTROY SESSION
// ============================================
$_SESSION = [];
session_unset();
session_destroy();

write_log("SESSION DESTROYED SUCCESSFULLY");


// ============================================
// RESPONSE
// ============================================
echo json_encode([
    'success' => true,
    'message' => 'Logout successful'
]);

exit;
