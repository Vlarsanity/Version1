<?php
header('Content-Type: application/json');
session_start();

include_once("../../../admin_v2/config/conn.php");

// Get POST data
$accountType = $_POST['accountType'] ?? '';
$fullName    = $_POST['fullName'] ?? '';
$username    = $_POST['username'] ?? '';
$password    = $_POST['password'] ?? '';

if (!$accountType || !$fullName || !$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Split full name into first, middle, last
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0] ?? '';
$middleName = $nameParts[1] ?? '';
$lastName = $nameParts[2] ?? '';

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Optional: displayName
$displayName = $fullName;

// Insert into accounts table
$stmt = $conn->prepare("INSERT INTO accounts (username, firstName, lastName, displayName, password, accountType, accountStatus, defaultPasswordStat, createdAt) VALUES (?, ?, ?, ?, ?, ?, 'active', 'yes', NOW())");
$stmt->bind_param("sssss", $username, $firstName, $lastName, $displayName, $hashedPassword, $accountType);

if ($stmt->execute()) {
    $accountId = $stmt->insert_id;

    // Optionally insert into agent or employee tables if type
    if ($accountType === 'agent') {
        $stmt2 = $conn->prepare("INSERT INTO agent (accountId, fName, lName, mName, agentType, agentRole) VALUES (?, ?, ?, ?, 'Retailer', 'Head Agent')");
        $stmt2->bind_param("isss", $accountId, $firstName, $lastName, $middleName);
        $stmt2->execute();
    } elseif ($accountType === 'employee') {
        $stmt2 = $conn->prepare("INSERT INTO employee (accountId, fName, lName, mName, position, branch) VALUES (?, ?, ?, ?, '', 'Manila')");
        $stmt2->bind_param("isss", $accountId, $firstName, $lastName, $middleName);
        $stmt2->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Account added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding account: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
