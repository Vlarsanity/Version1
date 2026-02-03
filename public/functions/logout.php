<?php

// ====================================================
// CRITICAL: NO OUTPUT BEFORE THIS POINT
// Remove any whitespace or BOM before <?php tag
// ====================================================

// Start output buffering to catch any accidental output
ob_start();


// Start session
session_start();


// Development settings (disable in production)
ini_set('display_errors', 0); // Changed to 0 to prevent HTML output
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log'); // Log to file instead


// ====================================================
// INCLUDES
// ====================================================
require_once '../../configs/conn.php';


// ====================================================
// GET DATABASE CONNECTION (Singleton Pattern)
// ====================================================
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
} catch (Exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    ob_end_flush();
    exit();
}

// ====================================================
// HELPER FUNCTIONS
// ====================================================

/** Safely get all headers with fallback */
function get_request_headers() {
    if (function_exists('getallheaders')) {
        return getallheaders();
    }
    
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $headers[$header] = $value;
        }
    }
    return $headers;
}


/** Activity logging (DB-backed) */
function log_activity(mysqli $conn, int $accountId, string $action, string $description = ''): bool
{
    try {
        $stmt = $conn->prepare("
            INSERT INTO activity_logs (
                accountId,
                action,
                description,
                ipAddress,
                userAgent,
                createdAt
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            error_log('Activity log prepare failed: ' . $conn->error);
            return false;
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $stmt->bind_param(
            "issss",
            $accountId,
            $action,
            $description,
            $ipAddress,
            $userAgent
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;

    } catch (Throwable $e) {
        error_log('Activity log error: ' . $e->getMessage());
        return false;
    }
}






/**
 * Send clean JSON response and exit
 */
function send_json_response($data, $httpCode = 200) {
    // Clear any output that might have been buffered
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Set headers
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // CORS headers (restrict in production)
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
    
    // Output JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    // Flush and end
    ob_end_flush();
    exit();
}

// ====================================================
// HANDLE PREFLIGHT
// ====================================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
    http_response_code(200);
    exit();
}

// ====================================================
// METHOD CHECK - Only allow POST
// ====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ], 405);
}


// ====================================================
// CSRF VALIDATION
// ====================================================
$headers = get_request_headers();

$csrfToken =
    $headers['X-CSRF-Token']
    ?? $headers['X-Csrf-Token']
    ?? $headers['X-csrf-token']
    ?? $_SERVER['HTTP_X_CSRF_TOKEN']
    ?? $_POST['csrf_token']
    ?? null;

if (
    empty($csrfToken) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $csrfToken)
) {
    send_json_response([
        'success' => false,
        'message' => 'Invalid or missing CSRF token.'
    ], 403);
}


// ====================================================
// CHECK IF USER IS LOGGED IN
// ====================================================
if (empty($_SESSION['accountId'])) {
    send_json_response([
        'success' => false,
        'message' => 'No active session found.',
        'redirect' => '../../public/login.php'
    ], 400);
}

// ====================================================
// LOGOUT MODE
// ====================================================
$logoutMode = 'ajax';
if (!empty($_POST['logout_mode']) && $_POST['logout_mode'] === 'fallback') {
    $logoutMode = 'fallback';
}


// ====================================================
// CAPTURE SESSION DATA BEFORE DESTROY
// ====================================================
$sessionId = session_id();
$accountId = $_SESSION['accountId'];
$username = $_SESSION['username'] ?? 'unknown';




// ====================================================
// LOGOUT PROCESS
// ====================================================
try {
    // Debug log
    error_log(sprintf(
        'LOGOUT: session_id=%s, accountId=%d, username=%s, mode=%s, IP=%s',
        $sessionId,
        $accountId,
        $username,
        $logoutMode,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));

    // Remove session from database
    if ($accountId && $sessionId) {
        $stmt = $conn->prepare("
            DELETE FROM user_sessions
            WHERE session_id = ? AND accountid = ?
            LIMIT 1
        ");
        
        if ($stmt) {
            $stmt->bind_param("si", $sessionId, $accountId);
            if (!$stmt->execute()) {
                error_log('Failed to delete session from database: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log('Failed to prepare session deletion: ' . $conn->error);
        }
    }

    // Log activity (pass $conn as parameter)
    log_activity(
        $conn,
        $accountId,
        'LOGOUT',
        sprintf('User logged out successfully (%s mode)', $logoutMode)
    );

    // Destroy PHP session
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    // Success response
    send_json_response([
        'success' => true,
        'message' => 'Logged out successfully.',
        'redirect' => '../../public/login.php',
        'logout_mode' => $logoutMode
    ], 200);

} catch (Exception $e) {
    error_log('LOGOUT ERROR: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Clear session even on error
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
    
    send_json_response([
        'success' => false,
        'message' => 'An error occurred during logout. Session cleared.',
        'redirect' => '../../public/login.php'
    ], 500);
}


