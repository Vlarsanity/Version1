<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    echo json_encode(['success' => false, 'message' => 'Not logged in', 'needsProfile' => false]);
    exit;
}

// Check if user is an agent
if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'agent') {
    echo json_encode(['success' => true, 'needsProfile' => false]);
    exit;
}

// Get agent ID from session
$agentId = $_SESSION['agentId'] ?? null;
if (!$agentId) {
    echo json_encode(['success' => false, 'message' => 'Agent ID not found', 'needsProfile' => false]);
    exit;
}

// Include database connection
require_once('../../config/conn.php');

// Check if agent profile is complete
$sql = "SELECT fName, lName, personInChargeEmail, contactNo FROM agent WHERE agentId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error', 'needsProfile' => false]);
    $conn->close();
    exit;
}

$stmt->bind_param("i", $agentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Agent not found', 'needsProfile' => false]);
    $stmt->close();
    $conn->close();
    exit;
}

$agent = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Check if required fields are filled
$needsProfile = empty($agent['fName']) || empty($agent['lName']) ||
                empty($agent['personInChargeEmail']) || empty($agent['contactNo']);

echo json_encode([
    'success' => true,
    'needsProfile' => $needsProfile,
    'profileData' => [
        'fName' => $agent['fName'] ?? '',
        'lName' => $agent['lName'] ?? '',
        'personInChargeEmail' => $agent['personInChargeEmail'] ?? '',
        'contactNo' => $agent['contactNo'] ?? ''
    ]
]);
?>
