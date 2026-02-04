<?php
/**
 * Create Account API
 * Automatically creates records in both accounts table and corresponding table (agent/employee/guide)
 */

// Error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json; charset=utf-8');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// POST method validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'POST method required'
    ]);
    exit;
}

// Database connection
$conn_file = __DIR__ . '/../../../backend/conn.php';
if (!file_exists($conn_file)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection file not found'
    ]);
    exit;
}

require_once $conn_file;

// Check database connection
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON input'
    ]);
    exit;
}

// Required fields validation
$requiredFields = ['accountType', 'firstName', 'lastName', 'username', 'email', 'password'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Field '{$field}' is required"
        ]);
        exit;
    }
}

try {
    $conn->begin_transaction();

    // Extract data
    $accountType = $input['accountType'];
    $firstName = $input['firstName'];
    $lastName = $input['lastName'];
    $middleName = $input['middleName'] ?? '';
    $username = $input['username'];
    $email = $input['email'];
    $password = $input['password'];
    $displayName = $input['displayName'] ?? trim("$firstName $middleName $lastName");

    // Check if username already exists
    $checkUsernameSql = "SELECT accountId FROM accounts WHERE username = ?";
    $stmtCheck = $conn->prepare($checkUsernameSql);
    $stmtCheck->bind_param("s", $username);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        throw new Exception('Username already exists');
    }
    $stmtCheck->close();

    // Check if email already exists
    $checkEmailSql = "SELECT accountId FROM accounts WHERE emailAddress = ?";
    $stmtCheck = $conn->prepare($checkEmailSql);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        throw new Exception('Email already exists');
    }
    $stmtCheck->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into accounts table
    $accountSql = "INSERT INTO accounts
        (username, firstName, lastName, displayName, emailAddress, password, accountType, accountStatus, emailVerified, createdAt)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 1, NOW())";

    $stmtAccount = $conn->prepare($accountSql);
    $stmtAccount->bind_param(
        "sssssss",
        $username,
        $firstName,
        $lastName,
        $displayName,
        $email,
        $hashedPassword,
        $accountType
    );

    if (!$stmtAccount->execute()) {
        throw new Exception('Failed to create account: ' . $stmtAccount->error);
    }

    $accountId = $conn->insert_id;
    $stmtAccount->close();

    // Insert into corresponding table based on accountType
    if ($accountType === 'agent') {
        $agentType = $input['agentType'] ?? 'Retailer';
        $agentRole = $input['agentRole'] ?? 'Head Agent';

        $agentSql = "INSERT INTO agent
            (accountId, fName, lName, mName, agentType, agentRole)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmtAgent = $conn->prepare($agentSql);
        $stmtAgent->bind_param(
            "isssss",
            $accountId,
            $firstName,
            $lastName,
            $middleName,
            $agentType,
            $agentRole
        );

        if (!$stmtAgent->execute()) {
            throw new Exception('Failed to create agent record: ' . $stmtAgent->error);
        }
        $stmtAgent->close();

    } elseif ($accountType === 'employee') {
        $position = $input['position'] ?? '';
        $branch = $input['branch'] ?? 'Manila';

        // Generate employeeId
        $result = $conn->query("SELECT employeeId FROM employee ORDER BY id DESC LIMIT 1");
        $lastId = $result->fetch_assoc()['employeeId'] ?? null;

        if ($lastId) {
            $num = (int)substr($lastId, 3); // remove 'EMP' prefix
            $num++;
        } else {
            $num = 1; // first employee
        }

        $employeeId = 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $employeeSql = "INSERT INTO employee
            (employeeId, accountId, fName, lName, mName, position, branch)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmtEmployee = $conn->prepare($employeeSql);
        $stmtEmployee->bind_param(
            "sissss",
            $employeeId,
            $accountId,
            $firstName,
            $lastName,
            $middleName,
            $position,
            $branch
        );

        if (!$stmtEmployee->execute()) {
            throw new Exception('Failed to create employee record: ' . $stmtEmployee->error);
        }
        $stmtEmployee->close();

    } elseif ($accountType === 'guide') {
        $phoneNumber = $input['phoneNumber'] ?? '';
        $languages = $input['languages'] ?? '';
        $experienceYears = $input['experienceYears'] ?? 0;

        // Generate guideCode
        $result = $conn->query("SELECT guideCode FROM guides ORDER BY guideId DESC LIMIT 1");
        $lastCode = $result->fetch_assoc()['guideCode'] ?? null;

        if ($lastCode) {
            $num = (int)substr($lastCode, 5); // remove 'GUIDE' prefix
            $num++;
        } else {
            $num = 1; // first guide
        }

        $guideCode = 'GUIDE' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $guideSql = "INSERT INTO guides
            (accountId, guideName, guideCode, phoneNumber, email, languages, experienceYears, status, createdAt, updatedAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())";

        $stmtGuide = $conn->prepare($guideSql);
        $stmtGuide->bind_param(
            "isssssi",
            $accountId,
            $displayName,
            $guideCode,
            $phoneNumber,
            $email,
            $languages,
            $experienceYears
        );

        if (!$stmtGuide->execute()) {
            throw new Exception('Failed to create guide record: ' . $stmtGuide->error);
        }
        $stmtGuide->close();
    }

    // Commit transaction
    $conn->commit();

    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "Account created successfully! Account Type: {$accountType}, Username: {$username}",
        'data' => [
            'accountId' => $accountId,
            'username' => $username,
            'email' => $email,
            'accountType' => $accountType
        ]
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
