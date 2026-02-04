<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST method required']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if user is an agent
if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'agent') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Agent account required']);
    exit;
}

// Get agent ID from session
$agentId = $_SESSION['agentId'] ?? null;
if (!$agentId) {
    echo json_encode(['success' => false, 'message' => 'Agent ID not found in session']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$agencyName = isset($input['agencyName']) ? trim($input['agencyName']) : '';
$fName = isset($input['fName']) ? trim($input['fName']) : '';
$mName = isset($input['mName']) ? trim($input['mName']) : null;
$lName = isset($input['lName']) ? trim($input['lName']) : '';
$personInChargeEmail = isset($input['personInChargeEmail']) ? trim($input['personInChargeEmail']) : '';
$contactNo = isset($input['contactNo']) ? trim($input['contactNo']) : '';

// Check required fields
if (empty($agencyName) || empty($fName) || empty($lName) || empty($personInChargeEmail) || empty($contactNo)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate email format
if (!filter_var($personInChargeEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Include database connection
require_once('../../config/conn.php');

// Check if agent exists
$checkSql = "SELECT agentId, fName FROM agent WHERE agentId = ?";
$checkStmt = $conn->prepare($checkSql);

if (!$checkStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$checkStmt->bind_param("i", $agentId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Agent not found']);
    $checkStmt->close();
    $conn->close();
    exit;
}

$agent = $checkResult->fetch_assoc();
$checkStmt->close();

// Update agent profile
$updateSql = "UPDATE agent
              SET agencyName = ?,
                  fName = ?,
                  mName = ?,
                  lName = ?,
                  personInChargeEmail = ?,
                  contactNo = ?
              WHERE agentId = ?";

$updateStmt = $conn->prepare($updateSql);

if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit;
}

$updateStmt->bind_param(
    "ssssssi",
    $agencyName,
    $fName,
    $mName,
    $lName,
    $personInChargeEmail,
    $contactNo,
    $agentId
);

if ($updateStmt->execute()) {
    // Update display name in accounts table
    $displayName = $fName . ' ' . $lName;
    $updateAccountSql = "UPDATE accounts SET displayName = ? WHERE accountId = (SELECT accountId FROM agent WHERE agentId = ?)";
    $updateAccountStmt = $conn->prepare($updateAccountSql);

    if ($updateAccountStmt) {
        $updateAccountStmt->bind_param("si", $displayName, $agentId);
        $updateAccountStmt->execute();
        $updateAccountStmt->close();
    }

    // Update session display name
    $_SESSION['displayName'] = $displayName;

    echo json_encode([
        'success' => true,
        'message' => 'Profile completed successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update profile: ' . $updateStmt->error
    ]);
}

$updateStmt->close();
$conn->close();
?>
