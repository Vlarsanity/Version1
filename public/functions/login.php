<?php
ob_start();
session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../configs/conn.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    log_error('Database connection failed', $e);
    send_response(['success' => false, 'message' => 'Database connection error.'], 500);
}

// ==================== HELPERS ====================
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function send_response($data, $status_code = 200) {
    if (ob_get_length()) ob_clean();
    http_response_code($status_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    ob_end_flush();
    exit();
}

function log_activity(mysqli $conn = null, int $accountId = null, string $action, string $details = '', string $level = 'INFO'): bool {
    $dbSuccess = false;
    $fileSuccess = false;

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    if ($conn instanceof mysqli && $accountId !== null && !empty($action)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO activity_logs (
                    accountId, action, description, ipAddress, userAgent, createdAt, level
                ) VALUES (?, ?, ?, ?, ?, NOW(), ?)
            ");
            if ($stmt) {
                $stmt->bind_param("isssss", $accountId, $action, $details, $ipAddress, $userAgent, $level);
                $dbSuccess = $stmt->execute();
                if (!$dbSuccess) error_log("Activity log DB execute failed: " . $stmt->error);
                $stmt->close();
            } else {
                error_log("Activity log DB prepare failed: " . $conn->error);
            }
        } catch (Throwable $e) {
            error_log('Activity log DB error: ' . $e->getMessage());
        }
    }

    try {
        $logFolder = __DIR__ . '/../../logs';
        if (!file_exists($logFolder)) mkdir($logFolder, 0755, true);

        $logFile = $logFolder . '/login.log';
        $timestamp = date('Y-m-d H:i:s');

        $message = sprintf(
            "[%s] [%s] Date: %s | AccountID: %s | IP: %s | Action: %s | Details: %s | User-Agent: %s\n",
            $timestamp, $level, date('Y-m-d'), $accountId ?? 'NULL', $ipAddress, $action, $details, $userAgent
        );

        file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
        $fileSuccess = true;
    } catch (Throwable $e) {
        error_log('Activity log file error: ' . $e->getMessage());
    }

    return $dbSuccess || $fileSuccess;
}

function log_error(string $message, Throwable $exception = null): bool {
    $success = false;
    try {
        $logFolder = __DIR__ . '/../../logs';
        if (!file_exists($logFolder)) mkdir($logFolder, 0755, true);

        $logFile = $logFolder . '/login_errors.log';
        $timestamp = date('Y-m-d H:i:s');

        $errorMessage = sprintf("[%s] Date: %s | ERROR: %s\n", $timestamp, date('Y-m-d'), $message);
        if ($exception) {
            $errorMessage .= "Exception: " . $exception->getMessage() . "\n";
            $errorMessage .= "Stack trace: " . $exception->getTraceAsString() . "\n";
        }
        $errorMessage .= str_repeat('-', 80) . "\n";
        file_put_contents($logFile, $errorMessage, FILE_APPEND | LOCK_EX);
        $success = true;
    } catch (Throwable $e) {
        error_log('Failed to write login error: ' . $e->getMessage());
    }
    return $success;
}

function deny_access(array $account, string $reason): void {
    log_activity(null, $account['accountId'], 'ACCESS_DENIED', $reason, 'WARNING');
    send_response(['success' => false, 'message' => 'Access denied.'], 403);
}

function check_existing_session($conn, $accountId, $currentIp) {
    try {
        $stmt = $conn->prepare("
            SELECT session_id, ip_address, login_time, last_activity, device_type
            FROM user_sessions
            WHERE accountid = ? AND ip_address = ? AND last_activity > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY last_activity DESC LIMIT 1
        ");
        $stmt->bind_param("is", $accountId, $currentIp);
        $stmt->execute();
        $res = $stmt->get_result();
        $session = $res->fetch_assoc();
        $stmt->close();
        return $session;
    } catch (Exception $e) {
        log_error('Failed to check existing session', $e);
        return null;
    }
}

function cleanup_old_sessions($conn, $accountId, $keepSessionId = null) {
    try {
        $stmt = $conn->prepare("
            DELETE FROM user_sessions
            WHERE accountid = ? AND session_id != ? AND last_activity < DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $keepSessionId = $keepSessionId ?? '';
        $stmt->bind_param("is", $accountId, $keepSessionId);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        if ($affectedRows > 0) {
            log_activity(null, $accountId, "Removed {$affectedRows} old session(s)", 'SESSION_CLEANUP', 'INFO');
        }

        return $affectedRows;
    } catch (Exception $e) {
        log_error('Failed to cleanup old sessions', $e);
        return 0;
    }
}

function detect_device_type($userAgent) {
    $ua = strtolower($userAgent);
    if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false) return 'mobile';
    if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) return 'tablet';
    return 'desktop';
}

function detect_browser($userAgent) {
    if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
    if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
    if (strpos($userAgent, 'Safari') !== false) return 'Safari';
    if (strpos($userAgent, 'Edge') !== false) return 'Edge';
    if (strpos($userAgent, 'Opera') !== false) return 'Opera';
    if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) return 'Internet Explorer';
    return 'Unknown';
}

// ==================== REQUEST VALIDATION ====================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_activity(null, null, 'INVALID_METHOD', $_SERVER['REQUEST_METHOD'], 'WARNING');
    send_response(['success' => false, 'message' => 'Only POST allowed.'], 405);
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) $input = $_POST;

$loginId = sanitize_input($input['loginId'] ?? '');
$password = $input['password'] ?? '';

if (empty($loginId) || empty($password)) {
    log_activity(null, null, 'LOGIN_FAILED', 'Missing fields', 'WARNING');
    send_response(['success' => false, 'message' => 'Email/username and password required'], 400);
}

// ==================== FETCH ACCOUNT ====================
try {
    $stmt = $conn->prepare("
        SELECT accountId, username, emailAddress, password, displayName, accountType, accountStatus, emailVerified,
               loginAttempts, lockedUntil, profileImage, preferredLanguage, createdAt, lastLoginAt
        FROM accounts
        WHERE username = ? OR emailAddress = ?
        LIMIT 1
    ");
    $stmt->bind_param("ss", $loginId, $loginId);
    $stmt->execute();
    $result  = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    log_error('Database query failed', $e);
    send_response(['success' => false, 'message' => 'Database error.'], 500);
}

if (!$account) {
    log_activity(null, null, 'LOGIN_FAILED', "Account not found: $loginId", 'WARNING');
    send_response(['success' => false, 'message' => 'Invalid email/username or password.'], 401);
}

// ==================== ACCOUNT STATUS CHECKS ====================
if ($account['accountStatus'] !== 'active') {
    log_activity($conn, $account['accountId'], 'LOGIN_FAILED', 'Account inactive', 'WARNING');
    send_response(['success' => false, 'message' => 'Account inactive.'], 403);
}
if ((int)$account['emailVerified'] !== 1) {
    log_activity($conn, $account['accountId'], 'LOGIN_FAILED', 'Email not verified', 'WARNING');
    send_response(['success' => false, 'message' => 'Please verify email before login.'], 403);
}
if (!empty($account['lockedUntil']) && strtotime($account['lockedUntil']) > time()) {
    $remaining = ceil((strtotime($account['lockedUntil']) - time()) / 60);
    log_activity($conn, $account['accountId'], 'LOGIN_FAILED', 'Account locked', 'WARNING');
    send_response(['success' => false, 'message' => "Account locked. Try again in {$remaining} min."], 423);
}


// ==================== CHECK EXISTING SESSION ====================
$currentIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$existingSession = check_existing_session($conn, $account['accountId'], $currentIp);
if ($existingSession) {
    $lastActivity = strtotime($existingSession['last_activity']);
    $minutesAgo = floor((time() - $lastActivity)/60);
    log_activity($conn, $account['accountId'], 'EXISTING_SESSION_DETECTED', "Login attempt blocked. Last activity {$minutesAgo} min ago", 'INFO');
    send_response([
        'success' => false,
        'message' => "Already logged in from this location ({$minutesAgo} min ago).",
        'existing_session' => true
    ], 409);
}


// ==================== PASSWORD VERIFICATION ====================
if (!password_verify($password, $account['password'])) {
    $attempts = $account['loginAttempts'] + 1;

    try {
        $stmt = $conn->prepare("UPDATE accounts SET loginAttempts=? WHERE accountId=?");
        $stmt->bind_param("ii", $attempts, $account['accountId']);
        $stmt->execute();
        $stmt->close();

        if ($attempts >= 5) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $stmt = $conn->prepare("UPDATE accounts SET lockedUntil=? WHERE accountId=?");
            $stmt->bind_param("si", $lockUntil, $account['accountId']);
            $stmt->execute();
            $stmt->close();

            log_activity($conn, $account['accountId'], 'ACCOUNT_LOCKED', "Too many failed attempts ({$attempts})", 'WARNING');
            send_response(['success'=>false,'message'=>'Too many failed attempts. Account locked 15 min.'], 423);
        }
    } catch (Exception $e) {
        log_error('Failed to update login attempts', $e);
    }

    log_activity($conn, $account['accountId'], 'LOGIN_FAILED', "Incorrect password ({$attempts}/5)", 'WARNING');
    send_response(['success'=>false,'message'=>"Incorrect password. Attempt {$attempts} of 5."], 401);
}



// ==================== SUCCESSFUL LOGIN ====================
try {
    $stmt = $conn->prepare("UPDATE accounts SET loginAttempts=0, lockedUntil=NULL, lastLoginAt=NOW() WHERE accountId=?");
    $stmt->bind_param("i", $account['accountId']);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    log_error('Failed to reset login security flags', $e);
}



// ==================== SESSION INIT ====================
session_regenerate_id(true);
$sessionId = session_id();

/**
 * Normalize and validate account type
 */
$accountType = strtolower(trim($account['accountType'] ?? ''));

if ($accountType === '') {
    send_response([
        'success' => false,
        'message' => 'Invalid account type.'
    ], 403);
    exit;
}

/**
 * Base session variables (shared by all account types)
 */
$_SESSION['logged_in']         = true;
$_SESSION['accountId']         = (int)$account['accountId'];
$_SESSION['username']          = $account['username'];
$_SESSION['displayName']       = $account['displayName'];
$_SESSION['emailAddress']      = $account['emailAddress'];
$_SESSION['accountType']       = $accountType;
$_SESSION['accountStatus']     = $account['accountStatus'];
$_SESSION['profileImage']      = $account['profileImage'] ?? null;
$_SESSION['preferredLanguage'] = $account['preferredLanguage'] ?? 'en';
$_SESSION['login_time']        = time();
$_SESSION['last_activity']     = time();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Account-type specific session data
 */
switch ($accountType) {

    case 'admin':
        $_SESSION['roleLevel']   = 100;
        $_SESSION['accessScope'] = 'SYSTEM';
        $_SESSION['canManage']   = true;
        break;

    case 'agent':
        $_SESSION['roleLevel']   = 60;
        $_SESSION['accessScope'] = 'SALES';
        $_SESSION['canBook']     = true;
        break;

    case 'employee':
        $_SESSION['roleLevel']   = 40;
        $_SESSION['accessScope'] = 'OPERATIONS';
        $_SESSION['canProcess']  = true;
        break;

    case 'tourguide':
        $_SESSION['roleLevel']   = 30;
        $_SESSION['accessScope'] = 'FIELD';
        $_SESSION['canGuide']    = true;
        break;

    case 'guest':
        $_SESSION['roleLevel']   = 10;
        $_SESSION['accessScope'] = 'LIMITED';
        $_SESSION['canBook']     = false;
        break;

    default:
        session_unset();
        session_destroy();

        send_response([
            'success' => false,
            'message' => 'Unauthorized account type.'
        ], 403);
        exit;
}



// ==================== ACTIVITY LOG ====================
log_activity($conn, $account['accountId'], 'LOGIN_SUCCESS', "Logged in as {$account['accountType']} from {$deviceType} ({$browser})", 'INFO');



// ==================== RESPONSE ====================
$redirectMap = [
    'admin'     => '../admin/dashboard.php',
    'agent'     => '/agent/dashboard.php',
    'employee'  => '/employee/dashboard.php',
    'tourguide' => '/tourguide/dashboard.php',
    'guest'     => '/guest/home.php'
];

send_response([
    'success' => true,
    'message' => 'Login successful! Redirecting...',
    'redirect' => $redirectMap[$account['accountType']] ?? '/dashboard.php',
    'data' => [
        'accountId' => $account['accountId'],
        'username' => $account['username'],
        'displayName' => $account['displayName'],
        'accountType' => $account['accountType']
    ]
], 200);


