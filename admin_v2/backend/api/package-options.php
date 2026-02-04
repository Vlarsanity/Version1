<?php
/**
 * 패키지 룸 옵션 API
 * 패키지의 룸 옵션 정보를 조회
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 기존 backend/conn.php 사용
$conn_file = __DIR__ . '/../../../backend/conn.php';
if (!file_exists($conn_file)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection file not found'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $conn_file;

// GET 또는 POST 데이터 받기
$packageId = $_GET['packageId'] ?? $_POST['packageId'] ?? $_GET['package_id'] ?? $_POST['package_id'] ?? null;

if (!$packageId) {
    echo json_encode([
        'success' => false,
        'message' => 'Package ID가 필요합니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 패키지 룸 옵션 조회
    // 먼저 어떤 테이블에 룸 옵션이 있는지 확인
    // 일반적으로 package_rooms, rooms, 또는 package_room_options 같은 테이블을 사용할 수 있음
    
    // package_rooms 테이블 확인
    $tableCheck = $conn->query("SHOW TABLES LIKE 'package_rooms'");
    if ($tableCheck->num_rows > 0) {
        $sql = "SELECT roomId, roomType, roomPrice, capacity, description 
                FROM package_rooms 
                WHERE packageId = ? AND isAvailable = 1
                ORDER BY roomPrice ASC";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $packageId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $roomOptions = [];
            while ($row = $result->fetch_assoc()) {
                $roomOptions[] = [
                    'roomId' => $row['roomId'],
                    'roomType' => $row['roomType'] ?? '',
                    'roomPrice' => floatval($row['roomPrice'] ?? 0),
                    'capacity' => intval($row['capacity'] ?? 1),
                    'description' => $row['description'] ?? ''
                ];
            }
            $stmt->close();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'roomOptions' => $roomOptions
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    
    // rooms 테이블 확인 (packageId로 연결)
    $tableCheck2 = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($tableCheck2->num_rows > 0) {
        // rooms 테이블에 packageId 컬럼이 있는지 확인
        $columnCheck = $conn->query("SHOW COLUMNS FROM rooms LIKE 'packageId'");
        if ($columnCheck->num_rows > 0) {
            $sql = "SELECT roomId, roomType, price as roomPrice, capacity, description 
                    FROM rooms 
                    WHERE packageId = ? AND isAvailable = 1
                    ORDER BY price ASC";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $packageId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $roomOptions = [];
                while ($row = $result->fetch_assoc()) {
                    $roomOptions[] = [
                        'roomId' => $row['roomId'],
                        'roomType' => $row['roomType'] ?? '',
                        'roomPrice' => floatval($row['roomPrice'] ?? 0),
                        'capacity' => intval($row['capacity'] ?? 1),
                        'description' => $row['description'] ?? ''
                    ];
                }
                $stmt->close();
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'roomOptions' => $roomOptions
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }
    
    // 룸 옵션 테이블이 없으면 빈 배열 반환
    echo json_encode([
        'success' => true,
        'data' => [
            'roomOptions' => []
        ],
        'message' => 'No room options table found'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Package room options API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '룸 옵션 조회 중 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    error_log("Package room options API fatal error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '룸 옵션 조회 중 치명적 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>

