<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../conn.php';

function table_exists($conn, $table) {
    $safe = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '{$safe}'");
    return $res && $res->num_rows > 0;
}

//    
if (isset($_SESSION['user_id']) || isset($_SESSION['accountId'])) {
    $userId = $_SESSION['user_id'] ?? $_SESSION['accountId'];

    // DB  /   (guide-mypage    )
    $profile = [
        'accountId' => (int)$userId,
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'accountType' => $_SESSION['account_type'] ?? ($_SESSION['accountRole'] ?? ''),
        // B2B ()
        'affiliateCode' => '',
        'clientType' => '',
        'companyId' => null,
        'isB2B' => false
    ];
    try {
        $hasClient = table_exists($conn, 'client');
        $hasGuides = table_exists($conn, 'guides');
        $joins = '';
        $select = "a.username, a.emailAddress, a.accountType, COALESCE(NULLIF(TRIM(a.affiliateCode), ''), '') AS affiliateCode";
        if ($hasClient) $select .= ", c.fName, c.lName, COALESCE(c.clientType,'') AS clientType, c.companyId AS companyId";
        if ($hasGuides) $select .= ", g.guideName, g.guideCode";
        if ($hasClient) $joins .= " LEFT JOIN client c ON a.accountId = c.accountId";
        if ($hasGuides) $joins .= " LEFT JOIN guides g ON a.accountId = g.accountId";

        $stmtP = $conn->prepare("SELECT {$select} FROM accounts a {$joins} WHERE a.accountId = ? LIMIT 1");
        if ($stmtP) {
            $aid = (int)$userId;
            $stmtP->bind_param("i", $aid);
            $stmtP->execute();
            $row = $stmtP->get_result()->fetch_assoc();
            $stmtP->close();
            if ($row) {
                $profile['username'] = $row['username'] ?? $profile['username'];
                $profile['email'] = $row['emailAddress'] ?? $profile['email'];
                $profile['accountType'] = $row['accountType'] ?? $profile['accountType'];
                $profile['firstName'] = $row['fName'] ?? '';
                $profile['lastName'] = $row['lName'] ?? '';
                $profile['guideName'] = $row['guideName'] ?? '';
                $profile['guideCode'] = $row['guideCode'] ?? '';
                $profile['affiliateCode'] = (string)($row['affiliateCode'] ?? '');
                $profile['clientType'] = strtolower(trim((string)($row['clientType'] ?? '')));
                $profile['companyId'] = isset($row['companyId']) ? (int)$row['companyId'] : null;

                $display = trim((string)($profile['guideName'] ?? ''));
                if ($display === '') {
                    $display = trim(($profile['firstName'] ?? '') . ' ' . ($profile['lastName'] ?? ''));
                }
                if ($display === '') $display = (string)($profile['username'] ?? '');
                $profile['displayName'] = $display;

                // B2B/B2C 판별: accounts.accountType 기반
                // - accountType IN ('agent', 'admin') → B2B
                // - accountType IN ('guest', 'guide', 'cs', '') → B2C
                $profile['isB2B'] = in_array(strtolower($profile['accountType'] ?? ''), ['agent', 'admin'], true);
            }
        }
    } catch (Throwable $e) {
        // ignore profile enrichment failures
    }
    
    //    (  )
    $stmt = $conn->prepare("SELECT * FROM user_sessions WHERE accountid = ? AND last_activity > DATE_SUB(NOW(), INTERVAL 4 HOUR)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        //  
        $updateStmt = $conn->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE accountid = ?");
        $updateStmt->bind_param("i", $userId);
        $updateStmt->execute();
        
        send_json_response([
            'success' => true,
            'isLoggedIn' => true,
            'user' => [
                'id' => $userId,
                'username' => $profile['username'] ?? ($_SESSION['username'] ?? ''),
                'email' => $profile['email'] ?? ($_SESSION['email'] ?? ''),
                'account_type' => $profile['accountType'] ?? ($_SESSION['account_type'] ?? $_SESSION['accountRole'] ?? ''),
                'accountType' => $profile['accountType'] ?? ($_SESSION['account_type'] ?? $_SESSION['accountRole'] ?? ''),
                'isB2B' => (bool)($profile['isB2B'] ?? false),
                'firstName' => $profile['firstName'] ?? '',
                'lastName' => $profile['lastName'] ?? '',
                'displayName' => $profile['displayName'] ?? ($profile['username'] ?? ''),
                'guideName' => $profile['guideName'] ?? '',
                'guideCode' => $profile['guideCode'] ?? ''
            ]
        ]);
    } else {
        //    PHP    ()
        send_json_response([
            'success' => true,
            'isLoggedIn' => true,
            'user' => [
                'id' => $userId,
                'username' => $profile['username'] ?? ($_SESSION['username'] ?? ''),
                'email' => $profile['email'] ?? ($_SESSION['email'] ?? ''),
                'account_type' => $profile['accountType'] ?? ($_SESSION['account_type'] ?? $_SESSION['accountRole'] ?? ''),
                'accountType' => $profile['accountType'] ?? ($_SESSION['account_type'] ?? $_SESSION['accountRole'] ?? ''),
                'isB2B' => (bool)($profile['isB2B'] ?? false),
                'firstName' => $profile['firstName'] ?? '',
                'lastName' => $profile['lastName'] ?? '',
                'displayName' => $profile['displayName'] ?? ($profile['username'] ?? ''),
                'guideName' => $profile['guideName'] ?? '',
                'guideCode' => $profile['guideCode'] ?? ''
            ]
        ]);
    }
} else {
    send_json_response([
        'success' => false,
        'isLoggedIn' => false,
        'message' => ' .'
    ]);
}
?>









