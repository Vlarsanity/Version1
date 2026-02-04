<?php
header('Content-Type: application/json');
session_start();

// ==========================
// Logging Setup
// ==========================
$logDir  = dirname(__DIR__, 3) . '/logs';
$logFile = $logDir . '/account_bulk.log';

if (!file_exists($logDir)) mkdir($logDir, 0777, true);

function write_log($msg)
{
    global $logFile;
    $date = date('Y-m-d H:i:s');
    error_log("[$date] $msg\n", 3, $logFile);
}

include_once("../../../admin_v2/config/conn.php");

// ==========================
// Read JSON Input
// ==========================
$inputJSON = file_get_contents('php://input');
write_log("Received JSON: " . $inputJSON);

$input = json_decode($inputJSON, true);
if ($input === null) {
    write_log("JSON decode failed: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$accounts = $input['accounts'] ?? [];
if (!is_array($accounts) || empty($accounts)) {
    write_log("No accounts provided");
    echo json_encode(['success' => false, 'message' => 'No accounts provided']);
    exit;
}

// ==========================
// Insert Accounts
// ==========================
$errors = [];

foreach ($accounts as $acct) {
    $accountType   = $acct['accountType'] ?? '';
    $travelAgency  = $acct['travelAgency'] ?? '';
    $firstName     = $acct['firstName'] ?? '';
    $middleName    = $acct['middleName'] ?? '';
    $lastName      = $acct['lastName'] ?? '';
    $username      = $acct['username'] ?? '';
    $email         = $acct['email'] ?? '';
    $password      = $acct['password'] ?? '';

    // Validation
    if (!$accountType || !$firstName || !$lastName || !$username || !$email || !$password) {
        $errors[] = "Missing data for username: $username";
        write_log("Missing data for username: $username or email: $email");
        continue;
    }

    // Optional: further validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format for username: $username ($email)";
        write_log("Invalid email format for username: $username ($email)");
        continue;
    }

    $displayName = trim("$firstName $middleName $lastName");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into accounts table
    $stmt = $conn->prepare("INSERT INTO accounts 
        (username, firstName, lastName, displayName, emailAddress, password, accountType, accountStatus, defaultPasswordStat, createdAt, emailVerified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 'yes', NOW(), 1)");

    $stmt->bind_param("sssssss", $username, $firstName, $lastName, $displayName, $email, $hashedPassword, $accountType);

    if (!$stmt->execute()) {
        $errors[] = "Failed inserting $username: " . $stmt->error;
        write_log("Failed inserting $username: " . $stmt->error);
        continue;
    }


    $accountId = $stmt->insert_id;

    // =========================
    // Agent / Employee / Guide
    // =========================
    if ($accountType === 'agent') {
        $stmt2 = $conn->prepare("
        INSERT INTO agent (accountId, fName, lName, mName, agencyName, agentType, agentRole)
        VALUES (?, ?, ?, ?, ?, 'Retailer', 'Head Agent')
    ");
        $stmt2->bind_param("issss", $accountId, $firstName, $lastName, $middleName, $travelAgency);

        if (!$stmt2->execute()) {
            $errors[] = "Failed inserting agent for $username: " . $stmt2->error;
            write_log("Failed inserting agent for $username: " . $stmt2->error);
        }
    } elseif ($accountType === 'employee') {

        // Get last employeeId from table
        $result = $conn->query("SELECT employeeId FROM employee ORDER BY id DESC LIMIT 1");
        $lastId = $result->fetch_assoc()['employeeId'] ?? null;

        if ($lastId) {
            $num = (int)substr($lastId, 3); // remove 'EMP' prefix
            $num++;
        } else {
            $num = 1; // first employee
        }

        $employeeId = 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $stmt2 = $conn->prepare("
        INSERT INTO employee 
        (employeeId, accountId, fName, lName, mName, position, branch) 
        VALUES (?, ?, ?, ?, ?, '', 'Manila')
    ");
        $stmt2->bind_param("sisss", $employeeId, $accountId, $firstName, $lastName, $middleName);

        if (!$stmt2->execute()) {
            $errors[] = "Failed inserting employee for $username: " . $stmt2->error;
            write_log("Failed inserting employee for $username: " . $stmt2->error);
        }
    } elseif ($accountType === 'guide') {

        // Get last guideCode from table
        $result = $conn->query("SELECT guideCode FROM guides ORDER BY guideId DESC LIMIT 1");
        $lastCode = $result->fetch_assoc()['guideCode'] ?? null;

        if ($lastCode) {
            $num = (int)substr($lastCode, 5); // remove 'GUIDE' prefix
            $num++;
        } else {
            $num = 1; // first guide
        }

        $guideCode = 'GUIDE' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $stmt2 = $conn->prepare("
        INSERT INTO guides 
        (accountId, guideName, guideCode, profileImage, phoneNumber, email, introduction, specialties, languages, certifications, rating, totalReviews, totalTours, experienceYears, status, createdAt, updatedAt) 
        VALUES (?, ?, ?, '', '', '', '', '', '', '', 0, 0, 0, 0, 'active', NOW(), NOW())
        ");

        $stmt2->bind_param("iss", $accountId, $displayName, $guideCode);

        if (!$stmt2->execute()) {
            $errors[] = "Failed inserting guide for $username: " . $stmt2->error;
            write_log("Failed inserting guide for $username: " . $stmt2->error);
        }
    }
}

$conn->close();

// ==========================
// Response
// ==========================
if (empty($errors)) {
    echo json_encode(['success' => true, 'message' => 'All accounts added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => implode("; ", $errors)]);
}
