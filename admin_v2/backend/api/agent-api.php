<?php
/**
 * Agent Admin API
 * 모든 Agent 관련 API 엔드포인트를 처리합니다.
 */

// 출력 버퍼링 시작 (에러 캡처를 위해)
ob_start();

// 개발 환경에서 에러 표시 (디버깅용)
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);

// 기존 backend/conn.php 사용
$conn_file = __DIR__ . '/../../../backend/conn.php';
if (!file_exists($conn_file)) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection file not found: ' . $conn_file . ' | Resolved: ' . realpath($conn_file) . ' | __DIR__: ' . __DIR__
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    require_once $conn_file;
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load database connection: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 데이터베이스 연결 확인
if (!isset($conn) || !$conn) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection not established'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// JSON 응답 헬퍼 함수 (conn.php에 이미 정의되어 있을 수 있으므로 확인)
if (!function_exists('send_json_response')) {
    function send_json_response($data, $status_code = 200) {
        if (ob_get_level() > 0) {
            ob_clean(); // 출력 버퍼 지우기
        }
        http_response_code($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (ob_get_level() > 0) {
            ob_end_flush(); // 출력 버퍼 플러시
        }
        exit;
    }
}

// 에러 응답 함수
if (!function_exists('send_error_response')) {
    function send_error_response($message, $status_code = 400) {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
        exit;
    }
}

// 성공 응답 함수
if (!function_exists('send_success_response')) {
    function send_success_response($data = [], $message = 'Success') {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
        exit;
    }
}

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 로그인 확인 및 agent accountType 검증
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    send_error_response('Authentication required. Please log in.', 401);
}

if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'agent') {
    send_error_response('Access denied. Agent account required.', 403);
}

if (!isset($_SESSION['agentId']) || empty($_SESSION['agentId'])) {
    send_error_response('Agent ID not found in session.', 403);
}

// 세션에서 agent 정보 가져오기
$sessionAgentId = $_SESSION['agentId'];
$sessionAccountId = $_SESSION['accountId'];

// 요청 메서드 확인
$method = $_SERVER['REQUEST_METHOD'];

// JSON 입력 받기 (먼저 JSON body를 읽어서 action도 포함)
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// GET 파라미터 병합
if (!empty($_GET)) {
    $input = array_merge($input, $_GET);
}

// POST 데이터와 병합
if ($method === 'POST' && !empty($_POST)) {
    $input = array_merge($input, $_POST);
}

// 멀티파트로 전달된 JSON 페이로드 처리
if (isset($input['data']) && is_string($input['data'])) {
    $decodedPayload = json_decode($input['data'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPayload)) {
        $input = array_merge($input, $decodedPayload);
    }
    unset($input['data']);
}

// action 파라미터 확인 (GET, POST, JSON body 모두에서 확인)
$action = $_GET['action'] ?? $_POST['action'] ?? ($input['action'] ?? '');

try {
    switch ($action) {
        // ========== 사용자 정보 ==========
        case 'getUserInfo':
            getUserInfo();
            break;

        case 'changePassword':
            changePassword($conn, $input);
            break;

        // ========== Overview 관련 ==========
        case 'getOverview':
            getOverview($conn, $sessionAgentId);
            break;
            
        case 'getTodayItineraries':
            getTodayItineraries($conn, $sessionAgentId);
            break;
            
        // ========== 예약 관련 ==========
        case 'getReservations':
            getReservations($conn, $input, $sessionAgentId);
            break;
            
        case 'getReservationDetail':
            getReservationDetail($conn, $input);
            break;
            
        case 'createReservation':
            createReservation($conn, $input, $sessionAgentId);
            break;
            
        case 'updateReservation':
            updateReservation($conn, $input);
            break;
            
        case 'updateReservationStatus':
            updateReservationStatus($conn, $input);
            break;
            
        case 'confirmDeposit':
            confirmDeposit($conn, $input);
            break;
            
        case 'confirmBalance':
            confirmBalance($conn, $input);
            break;
            
        case 'removeDepositProofFile':
            removeDepositProofFile($conn, $input);
            break;

        // ========== 3단계 결제 시스템 ==========
        case 'uploadDownPayment':
        case 'uploadDown': // Alias for backward compatibility
            uploadDownPayment($conn, $input);
            break;

        case 'uploadAdvancePayment':
        case 'uploadAdvance': // Alias for backward compatibility
            uploadAdvancePayment($conn, $input);
            break;

        case 'uploadBalancePayment':
        case 'uploadBalance': // Alias for backward compatibility
            uploadBalancePayment($conn, $input);
            break;

        case 'confirmDownPayment':
            confirmDownPayment($conn, $input);
            break;

        case 'rejectDownPayment':
            rejectDownPayment($conn, $input);
            break;

        case 'confirmAdvancePayment':
            confirmAdvancePayment($conn, $input);
            break;

        case 'rejectAdvancePayment':
            rejectAdvancePayment($conn, $input);
            break;

        case 'confirmBalancePayment':
            confirmBalancePayment($conn, $input);
            break;

        case 'rejectBalancePayment':
            rejectBalancePayment($conn, $input);
            break;

        case 'removeDownPaymentFile':
            removeDownPaymentFile($conn, $input);
            break;

        case 'removeAdvancePaymentFile':
            removeAdvancePaymentFile($conn, $input);
            break;

        case 'removeBalanceFile':
            removeBalanceFile($conn, $input);
            break;

        // ========== 여행자 정보 관련 ==========
        case 'updateTravelers':
            updateTravelers($conn, $input);
            break;

        case 'deletePassportImage':
            deletePassportImage($conn, $input);
            break;

        case 'uploadPassportImage':
            uploadPassportImage($conn, $input);
            break;

        // ========== 고객 관련 ==========
        case 'getCustomers':
            getCustomers($conn, $input, $sessionAgentId);
            break;
            
        case 'getCustomerDetail':
            getCustomerDetail($conn, $input);
            break;
            
        case 'createCustomer':
            createCustomer($conn, $input, $sessionAgentId);
            break;
            
        case 'updateCustomer':
            updateCustomer($conn, $input);
            break;
            
        case 'resetPassword':
            resetPassword($conn, $input);
            break;
            
        case 'downloadCustomers':
            downloadCustomers($conn, $input);
            break;
            
        case 'downloadCustomerSample':
            downloadCustomerSample();
            break;
            
        case 'downloadReservations':
            downloadReservations($conn, $input);
            break;
            
        case 'batchUploadCustomers':
            batchUploadCustomers($conn, $sessionAgentId);
            break;
            
        // ========== 문의 관련 ==========
        case 'getInquiries':
            getInquiries($conn, $input);
            break;
            
        case 'getInquiryDetail':
            getInquiryDetail($conn, $input);
            break;
            
        case 'createInquiry':
        case 'registerInquiry':
            createInquiry($conn, $input);
            break;
            
        case 'updateInquiry':
            updateInquiry($conn, $input);
            break;
            
        // ========== 항공편 정보 관련 ==========
        case 'getFlightInfo':
            getFlightInfo($conn, $input);
            break;
            
        default:
            send_error_response('Invalid action: ' . $action, 400);
    }
} catch (Exception $e) {
    ob_clean(); // 출력 버퍼 지우기
    error_log('API Error: ' . $e->getMessage());
    error_log('API Trace: ' . $e->getTraceAsString());
    send_error_response('Server error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine(), 500);
} catch (Error $e) {
    ob_clean(); // 출력 버퍼 지우기
    error_log('API Fatal Error: ' . $e->getMessage());
    error_log('API Trace: ' . $e->getTraceAsString());
    send_error_response('Fatal error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine(), 500);
} catch (Throwable $e) {
    ob_clean(); // 출력 버퍼 지우기
    error_log('API Throwable Error: ' . $e->getMessage());
    error_log('API Trace: ' . $e->getTraceAsString());
    send_error_response('Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine(), 500);
}

// ========== Overview 함수들 ==========

function getOverview($conn, $agentId) {
    try {
        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }

        // agentId 컬럼 존재 확인
        $hasAgentId = in_array('agentid', $bookingsColumns);

        // Agent 필터링 조건 추가
        $agentFilter = $hasAgentId ? "AND agentId = ?" : "";

        // Payment Status 기준으로 카운트
        $bookingStatusSql = "
            SELECT
                SUM(CASE WHEN bookingStatus = 'waiting_down_payment' THEN 1 ELSE 0 END) as waiting_down_payment,
                SUM(CASE WHEN bookingStatus = 'waiting_advance_payment' THEN 1 ELSE 0 END) as waiting_advance_payment,
                SUM(CASE WHEN bookingStatus = 'waiting_balance' THEN 1 ELSE 0 END) as waiting_balance
            FROM bookings
            WHERE 1=1 {$agentFilter}
        ";

        if ($hasAgentId) {
            $stmt = $conn->prepare($bookingStatusSql);
            $stmt->bind_param("i", $agentId);
            $stmt->execute();
            $bookingResult = $stmt->get_result();
            if (!$bookingResult) {
                throw new Exception('Failed to query booking status: ' . $conn->error);
            }
            $bookingStatus = $bookingResult->fetch_assoc();
            $stmt->close();
        } else {
            $bookingResult = $conn->query($bookingStatusSql);
            if (!$bookingResult) {
                throw new Exception('Failed to query booking status: ' . $conn->error);
            }
            $bookingStatus = $bookingResult->fetch_assoc();
        }

        send_success_response([
            'paymentStatus' => [
                'waitingDownPayment' => (int)($bookingStatus['waiting_down_payment'] ?? 0),
                'waitingAdvancePayment' => (int)($bookingStatus['waiting_advance_payment'] ?? 0),
                'waitingBalance' => (int)($bookingStatus['waiting_balance'] ?? 0)
            ]
        ]);
    } catch (Exception $e) {
        send_error_response('Failed to get overview: ' . $e->getMessage());
    }
}

function getTodayItineraries($conn, $agentId) {
    try {
        $today = date('Y-m-d');

        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }

        // agentId 컬럼 존재 확인
        $hasAgentId = in_array('agentid', $bookingsColumns);
        
        // packages 테이블 컬럼 확인
        $packagesColumns = [];
        $packageColumnResult = $conn->query("SHOW COLUMNS FROM packages");
        if ($packageColumnResult) {
            while ($col = $packageColumnResult->fetch_assoc()) {
                $packagesColumns[] = strtolower($col['Field']);
            }
        }
        
        // guides 테이블 존재 확인
        $guidesTableCheck = $conn->query("SHOW TABLES LIKE 'guides'");
        $hasGuidesTable = ($guidesTableCheck && $guidesTableCheck->num_rows > 0);
        
        // guides 테이블 컬럼 확인
        $guidesColumns = [];
        if ($hasGuidesTable) {
            $guideColumnResult = $conn->query("SHOW COLUMNS FROM guides");
            if ($guideColumnResult) {
                while ($col = $guideColumnResult->fetch_assoc()) {
                    $guidesColumns[] = strtolower($col['Field']);
                }
            }
        }
        
        $hasGuideId = in_array('guideid', $bookingsColumns);
        $hasGuideName = $hasGuidesTable && (in_array('guidename', $guidesColumns) || in_array('name', $guidesColumns));
        $hasDurationDays = in_array('duration_days', $packagesColumns) || in_array('durationdays', $packagesColumns);
        $hasDuration = in_array('duration', $packagesColumns);
        
        // SELECT 절 구성
        $selectFields = [
            'b.bookingId',
            'b.packageId',
            'p.packageName',
            'b.departureDate',
            'b.departureTime'
        ];
        
        // returnDate 계산
        if ($hasDurationDays) {
            $selectFields[] = "DATE_ADD(b.departureDate, INTERVAL (p.duration_days - 1) DAY) as returnDate";
        } elseif (in_array('durationdays', $packagesColumns)) {
            $selectFields[] = "DATE_ADD(b.departureDate, INTERVAL (p.durationDays - 1) DAY) as returnDate";
        } elseif ($hasDuration) {
            $selectFields[] = "DATE_ADD(b.departureDate, INTERVAL (p.duration - 1) DAY) as returnDate";
        } else {
            $selectFields[] = "b.departureDate as returnDate";
        }
        
        $selectFields[] = 'b.adults';
        $selectFields[] = 'b.children';
        $selectFields[] = 'b.infants';
        $selectFields[] = "CONCAT(c.fName, ' ', c.lName) as customerName";
        $selectFields[] = 'c.clientType';
        
        // guideName 추가
        if ($hasGuideName) {
            if (in_array('guidename', $guidesColumns)) {
                $selectFields[] = 'g.guideName';
            } elseif (in_array('name', $guidesColumns)) {
                $selectFields[] = 'g.name as guideName';
            }
        } else {
            $selectFields[] = "NULL as guideName";
        }
        
        $sql = "
            SELECT " . implode(', ', $selectFields) . "
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            LEFT JOIN client c ON b.accountId = c.accountId
        ";
        
        if ($hasGuideId && $hasGuidesTable) {
            $sql .= " LEFT JOIN guides g ON b.guideId = g.guideId";
        } else {
            $sql .= " LEFT JOIN (SELECT NULL as guideName) g ON 1=1";
        }

        // Agent 필터링 조건 추가
        $agentFilter = $hasAgentId ? "AND b.agentId = ?" : "";

        $sql .= "
            WHERE b.departureDate = ?
            {$agentFilter}
            AND b.bookingStatus = 'confirmed'
            ORDER BY b.departureTime ASC
            LIMIT 20
        ";

        $stmt = $conn->prepare($sql);
        if ($hasAgentId) {
            $stmt->bind_param("si", $today, $agentId);
        } else {
            $stmt->bind_param("s", $today);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $itineraries = [];
        while ($row = $result->fetch_assoc()) {
            // 고객 유형 결정 (clientType: Retailer/Wholeseller -> B2C/B2B)
            $customerType = 'B2C';
            if (!empty($row['clientType'])) {
                if ($row['clientType'] === 'Wholeseller') {
                    $customerType = 'B2B';
                } else {
                    $customerType = 'B2C';
                }
            }
            
            $itineraries[] = [
                'bookingId' => $row['bookingId'],
                'packageName' => $row['packageName'] ?? '',
                'travelPeriod' => ($row['departureDate'] ?? '') . ' ~ ' . ($row['returnDate'] ?? ''),
                'customerType' => $customerType,
                'numPeople' => (int)($row['adults'] ?? 0) + (int)($row['children'] ?? 0) + (int)($row['infants'] ?? 0),
                'guideName' => $row['guideName'] ?? '미배정'
            ];
        }
        
        send_success_response($itineraries);
    } catch (Exception $e) {
        send_error_response('Failed to get today itineraries: ' . $e->getMessage());
    }
}

// ========== 예약 관련 함수들 ==========

function getReservations($conn, $input, $agentId) {
    try {
        $page = isset($input['page']) ? (int)$input['page'] : 1;
        $limit = isset($input['limit']) ? (int)$input['limit'] : 20;
        $offset = ($page - 1) * $limit;

        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }

        // agentId 컬럼 존재 확인
        $hasAgentId = in_array('agentid', $bookingsColumns);

        $where = [];
        $params = [];
        $types = '';

        // Agent 필터링 조건 (최우선)
        if ($hasAgentId) {
            $where[] = "b.agentId = ?";
            $params[] = $agentId;
            $types .= 'i';
        }

        // 검색 조건
        if (!empty($input['search'])) {
            $where[] = "(p.packageName LIKE ? OR c.fName LIKE ? OR c.lName LIKE ? OR b.bookingId LIKE ?)";
            $searchTerm = '%' . $input['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }

        // 여행 시작일 필터
        if (!empty($input['travelStartDate'])) {
            $where[] = "b.departureDate >= ?";
            $params[] = $input['travelStartDate'];
            $types .= 's';
        }

        // 상태 필터
        if (!empty($input['status'])) {
            if ($input['status'] === 'pending_deposit') {
                $where[] = "b.paymentStatus = 'pending' AND b.bookingStatus = 'confirmed'";
            } elseif ($input['status'] === 'pending_balance') {
                $where[] = "b.paymentStatus = 'partial' AND b.bookingStatus = 'confirmed'";
            } else {
                $where[] = "b.bookingStatus = ?";
                $params[] = $input['status'];
                $types .= 's';
            }
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // 전체 개수 조회
        $countSql = "
            SELECT COUNT(*) as total
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            LEFT JOIN client c ON b.accountId = c.accountId
            $whereClause
        ";
        
        if (!empty($params)) {
            $countStmt = $conn->prepare($countSql);
            if ($types) {
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $totalResult = $countStmt->get_result();
        } else {
            $totalResult = $conn->query($countSql);
        }
        $total = $totalResult->fetch_assoc()['total'];
        
        // 목록 조회
        $sql = "
            SELECT 
                b.bookingId,
                b.packageId,
                p.packageName,
                b.departureDate,
                CONCAT(c.fName, ' ', c.lName) as reserverName,
                (b.adults + b.children + b.infants) as numPeople,
                b.bookingStatus,
                b.paymentStatus,
                b.createdAt
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            LEFT JOIN client c ON b.accountId = c.accountId
            $whereClause
            ORDER BY b.createdAt DESC
            LIMIT ? OFFSET ?
        ";
        
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $conn->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reservations = [];
        $rowNum = $total - $offset;
        while ($row = $result->fetch_assoc()) {
            // 상태 배지 결정
            $statusBadge = getBookingStatusBadge($row['bookingStatus'], $row['paymentStatus']);
            
            $reservations[] = [
                'rowNum' => $rowNum--,
                'bookingId' => $row['bookingId'],
                'packageName' => $row['packageName'],
                'departureDate' => $row['departureDate'],
                'reserverName' => $row['reserverName'] ?? 'N/A',
                'numPeople' => (int)$row['numPeople'],
                'status' => $statusBadge['status'],
                'statusLabel' => $statusBadge['label'],
                'statusClass' => $statusBadge['class']
            ];
        }
        
        send_success_response([
            'reservations' => $reservations,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($total / $limit)
            ]
        ]);
    } catch (Exception $e) {
        send_error_response('Failed to get reservations: ' . $e->getMessage());
    }
}

function getBookingStatusBadge($bookingStatus, $paymentStatus) {
    // DB에 한글로 저장된 경우 영어로 변환
    $bookingStatus = normalizeBookingStatus($bookingStatus);
    $paymentStatus = normalizePaymentStatus($paymentStatus);

    // New standardized booking statuses
    if ($bookingStatus === 'waiting_deposit') {
        return ['status' => 'waiting_deposit', 'label' => 'Waiting for Deposit', 'class' => 'badge-blue'];
    } elseif ($bookingStatus === 'waiting_balance') {
        return ['status' => 'waiting_balance', 'label' => 'Waiting for Balance', 'class' => 'badge-green'];
    } elseif ($bookingStatus === 'confirmed') {
        return ['status' => 'confirmed', 'label' => 'Reservation confirmed', 'class' => 'badge-orange'];
    } elseif ($bookingStatus === 'completed') {
        return ['status' => 'completed', 'label' => 'Trip completed', 'class' => 'badge-gray'];
    } elseif ($bookingStatus === 'cancelled') {
        return ['status' => 'cancelled', 'label' => 'Reservation cancellation', 'class' => 'badge-red'];
    } elseif ($bookingStatus === 'refund_completed') {
        return ['status' => 'refund_completed', 'label' => 'Refund completed', 'class' => 'badge-purple'];
    }
    // Legacy logic for backwards compatibility
    elseif ($paymentStatus === 'pending' && $bookingStatus === 'confirmed') {
        return ['status' => 'pending_deposit', 'label' => 'Checking before advance payment', 'class' => 'badge-blue'];
    } elseif ($paymentStatus === 'partial' && $bookingStatus === 'confirmed') {
        return ['status' => 'pending_balance', 'label' => 'Check remaining balance', 'class' => 'badge-green'];
    } else {
        return ['status' => $bookingStatus, 'label' => $bookingStatus, 'class' => 'badge-gray'];
    }
}

function normalizeBookingStatus($status) {
    if (empty($status)) return $status;
    
    // 한글 상태를 영어로 변환
    $statusMap = [
        '예약 확정' => 'confirmed',
        '여행 완료' => 'completed',
        '예약 취소' => 'cancelled',
        '환불 완료' => 'refunded',
        'refund_completed' => 'refunded',
        '선금 확인 전' => 'pending_deposit',
        '잔금 확인 전' => 'pending_balance'
    ];
    
    $statusLower = strtolower(trim($status));
    foreach ($statusMap as $korean => $english) {
        if (strtolower($korean) === $statusLower || $status === $korean) {
            return $english;
        }
    }
    
    return $status;
}

function normalizePaymentStatus($status) {
    if (empty($status)) return $status;
    
    // 한글 상태를 영어로 변환
    $statusMap = [
        '선금 확인 전' => 'pending',
        '잔금 확인 전' => 'partial',
        '선금 확인' => 'partial',
        '전액 확인' => 'paid',
        'paid' => 'paid',
        'partial' => 'partial',
        'pending' => 'pending'
    ];
    
    $statusLower = strtolower(trim($status));
    foreach ($statusMap as $korean => $english) {
        if (strtolower($korean) === $statusLower || $status === $korean) {
            return $english;
        }
    }
    
    return $status;
}

function getReservationDetail($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        
        if (empty($bookingId)) {
            send_error_response('Booking ID is required');
        }
        
        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }
        
        // guideId 컬럼이 없으면 추가
        $hasGuideId = in_array('guideid', $bookingsColumns);
        if (!$hasGuideId) {
            $alterSql = "ALTER TABLE bookings ADD COLUMN guideId INT NULL";
            try {
                $conn->query($alterSql);
                $hasGuideId = true;
                $bookingsColumns[] = 'guideid';
            } catch (Exception $e) {
                // 컬럼 추가 실패는 무시 (이미 존재하거나 다른 이유)
            }
        }
        
        // guides 테이블 존재 및 컬럼 확인
        $guideColumns = [];
        $guidesTableExists = false;
        $tableCheck = $conn->query("SHOW TABLES LIKE 'guides'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $guidesTableExists = true;
            $guideColumnResult = $conn->query("SHOW COLUMNS FROM guides");
            if ($guideColumnResult) {
                while ($col = $guideColumnResult->fetch_assoc()) {
                    $guideColumns[] = strtolower($col['Field']);
                }
            }
        }
        
        // 가이드 전화번호 컬럼명 확인
        $guidePhoneCol = 'NULL';
        if ($guidesTableExists && $hasGuideId) {
            if (in_array('guidephone', $guideColumns)) {
                $guidePhoneCol = 'g.guidePhone';
            } elseif (in_array('phonenumber', $guideColumns)) {
                $guidePhoneCol = 'g.phoneNumber';
            } elseif (in_array('phone', $guideColumns)) {
                $guidePhoneCol = 'g.phone';
            }
        }
        
        // 가이드 이메일 컬럼명 확인
        $guideEmailCol = 'NULL';
        if ($guidesTableExists && $hasGuideId) {
            if (in_array('guideemail', $guideColumns)) {
                $guideEmailCol = 'g.guideEmail';
            } elseif (in_array('email', $guideColumns)) {
                $guideEmailCol = 'g.email';
            }
        }
        
        // guideName 컬럼도 확인
        $guideNameCol = 'NULL';
        if ($guidesTableExists && $hasGuideId && in_array('guidename', $guideColumns)) {
            $guideNameCol = 'g.guideName';
        }
        
        // JOIN 조건 구성
        $guideJoin = '';
        if ($guidesTableExists && $hasGuideId) {
            $guideJoin = "LEFT JOIN guides g ON b.guideId = g.guideId";
        }
        
        $sql = "
            SELECT
                b.*,
                p.packageName,
                p.packageType,
                p.duration_days,
                p.meeting_time,
                p.meeting_location,
                c.fName,
                c.lName,
                c.emailAddress,
                c.contactNo,
                $guideNameCol as guideName,
                $guidePhoneCol as guidePhone,
                $guideEmailCol as guideEmail
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            LEFT JOIN client c ON b.accountId = c.accountId
            $guideJoin
            WHERE b.bookingId = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            send_error_response('Reservation not found', 404);
        }
        
        $booking = $result->fetch_assoc();
        
        // selectedOptions 파싱
        $selectedOptions = [];
        if (!empty($booking['selectedOptions'])) {
            $selectedOptions = json_decode($booking['selectedOptions'], true);
        }
        
        // 여행자 정보 조회 (booking_travelers 테이블, www/user와 동일하게 transactNo 사용)
        // www/user에서는 transactNo 컬럼에 bookingId 값을 저장하므로, 조회 시에도 bookingId 사용
        $searchValue = $booking['bookingId']; // www/user와 동일하게 bookingId 사용
        
        // booking_travelers 테이블 컬럼 확인
        $travelerColumns = [];
        $travelerColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers");
        if ($travelerColumnCheck) {
            while ($col = $travelerColumnCheck->fetch_assoc()) {
                $travelerColumns[] = strtolower($col['Field']);
            }
        }
        
        // www/user와 동일하게 transactNo 컬럼 사용 (값은 bookingId)
        $travelerBookingIdColumn = 'transactNo';
        if (!in_array('transactno', $travelerColumns)) {
            if (in_array('bookingid', $travelerColumns)) {
                $travelerBookingIdColumn = 'bookingId';
            }
        }
        
        // ORDER BY 절 구성 (www/user와 동일한 순서)
        $orderBy = [];
        // 1. travelerType 순서 (adult -> child -> infant)
        $orderBy[] = 'CASE travelerType WHEN \'adult\' THEN 1 WHEN \'child\' THEN 2 WHEN \'infant\' THEN 3 END';
        // 2. isMainTraveler DESC
        if (in_array('ismaintraveler', $travelerColumns)) {
            $orderBy[] = 'isMainTraveler DESC';
        }
        // 3. bookingTravelerId ASC
        if (in_array('bookingtravelerid', $travelerColumns)) {
            $orderBy[] = 'bookingTravelerId ASC';
        } elseif (in_array('travelerid', $travelerColumns)) {
            $orderBy[] = 'travelerId ASC';
        }
        $orderByClause = !empty($orderBy) ? 'ORDER BY ' . implode(', ', $orderBy) : '';
        
        $travelersSql = "
            SELECT *
            FROM booking_travelers
            WHERE $travelerBookingIdColumn = ?
            $orderByClause
        ";
        $travelersStmt = $conn->prepare($travelersSql);
        $travelersStmt->bind_param("s", $searchValue);
        $travelersStmt->execute();
        $travelersResult = $travelersStmt->get_result();
        
        // 디버깅: SQL 쿼리 및 결과 확인
        error_log("Traveler SQL: " . $travelersSql);
        error_log("Search Value: " . $searchValue);
        error_log("Traveler Count: " . $travelersResult->num_rows);
        
        $travelers = [];
        while ($traveler = $travelersResult->fetch_assoc()) {
            // 디버깅: 첫 번째 여행자 데이터 확인
            if (count($travelers) === 0) {
                error_log("First traveler raw data: " . json_encode($traveler));
            }
            
            // 생년월일로부터 나이 계산 함수
            $calculateAge = function($birthDate) {
                if (!$birthDate) return null;
                try {
                    $birth = new DateTime($birthDate);
                    $today = new DateTime();
                    return $today->diff($birth)->y;
                } catch (Exception $e) {
                    return null;
                }
            };
            
            // www/user와 동일한 컬럼명으로 매핑
            $birthDate = $traveler['birthDate'] ?? $traveler['dateOfBirth'] ?? $traveler['birthdate'] ?? null;
            $age = $traveler['age'] ?? null;
            // age가 없으면 생년월일로부터 계산
            if (empty($age) && $birthDate) {
                $age = $calculateAge($birthDate);
            }
            
            // travelerType 정규화 (대소문자 구분 없이)
            $travelerTypeRaw = $traveler['travelerType'] ?? $traveler['type'] ?? 'adult';
            $travelerType = strtolower($travelerTypeRaw);
            if (!in_array($travelerType, ['adult', 'child', 'infant'])) {
                $travelerType = 'adult'; // 기본값
            }
            
            // title 정규화
            $titleRaw = $traveler['title'] ?? '';
            $title = strtoupper(trim($titleRaw));
            if (!in_array($title, ['MR', 'MRS', 'MS'])) {
                $title = 'MR'; // 기본값
            }
            
            // gender 정규화
            $genderRaw = $traveler['gender'] ?? '';
            $gender = strtolower(trim($genderRaw));
            if (!in_array($gender, ['male', 'female', 'm', 'f', '남성', '여성'])) {
                $gender = 'male'; // 기본값
            } elseif ($gender === 'm' || $gender === '남성') {
                $gender = 'male';
            } elseif ($gender === 'f' || $gender === '여성') {
                $gender = 'female';
            }
            
            // visaRequired 정규화
            $visaRequired = 0;
            if (isset($traveler['visaStatus']) && $traveler['visaStatus'] !== 'not_required' && $traveler['visaStatus'] !== 'Not applied') {
                $visaRequired = 1;
            } elseif (isset($traveler['visaRequired'])) {
                $visaRequired = (int)$traveler['visaRequired'];
            }
            
            $travelers[] = [
                'travelerType' => $travelerType,
                'title' => $title,
                'firstName' => $traveler['firstName'] ?? $traveler['fName'] ?? '',
                'lastName' => $traveler['lastName'] ?? $traveler['lName'] ?? '',
                'gender' => $gender,
                'age' => $age,
                'dateOfBirth' => $birthDate,
                'nationality' => $traveler['nationality'] ?? '',
                'passportNumber' => $traveler['passportNumber'] ?? $traveler['passportNo'] ?? '',
                'passportIssueDate' => $traveler['passportIssueDate'] ?? $traveler['passportIssuedDate'] ?? null,
                'passportExpiryDate' => $traveler['passportExpiry'] ?? $traveler['passportExpiryDate'] ?? $traveler['passportExp'] ?? null,
                'passportImage' => $traveler['passportImage'] ?? '',
                'visaRequired' => $visaRequired,
                'visaStatus' => $traveler['visaStatus'] ?? 'not_required',
                'isMainTraveler' => isset($traveler['isMainTraveler']) ? (int)$traveler['isMainTraveler'] : 0,
                'specialRequests' => $traveler['specialRequests'] ?? '',
                'accountId' => $traveler['accountId'] ?? null
            ];
        }

        // 중복 여행자 제거 (accountId 또는 firstName+lastName으로 중복 체크)
        $uniqueTravelers = [];
        $seen = [];
        foreach ($travelers as $t) {
            $accountId = $t['accountId'] ?? null;
            $firstName = strtolower(trim($t['firstName'] ?? ''));
            $lastName = strtolower(trim($t['lastName'] ?? ''));

            if ($accountId) {
                $key = 'id:' . $accountId;
            } elseif ($firstName && $lastName) {
                $key = 'name:' . $firstName . '|' . $lastName;
            } else {
                // 이름이 비어있는 경우는 그냥 추가
                $uniqueTravelers[] = $t;
                continue;
            }

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueTravelers[] = $t;
            }
        }

        // 중복 제거 전후 로그
        if (count($travelers) !== count($uniqueTravelers)) {
            error_log("Travelers deduplicated: " . count($travelers) . " -> " . count($uniqueTravelers));
        }

        $travelers = $uniqueTravelers;

        // 결제 상태 정보 추가
        $booking['depositConfirmed'] = isset($booking['depositConfirmed']) ? (bool)$booking['depositConfirmed'] : false;
        $booking['balanceConfirmed'] = isset($booking['balanceConfirmed']) ? (bool)$booking['balanceConfirmed'] : false;
        $booking['depositStatus'] = $booking['depositStatus'] ?? null;
        $booking['balanceStatus'] = $booking['balanceStatus'] ?? null;
        $booking['depositConfirmedAmount'] = $booking['depositConfirmedAmount'] ?? $booking['depositAmount'] ?? 0;
        $booking['balanceConfirmedAmount'] = $booking['balanceConfirmedAmount'] ?? ($booking['totalAmount'] - ($booking['depositAmount'] ?? 0));
        $booking['depositDueDate'] = $booking['depositDueDate'] ?? null;
        $booking['balanceDueDate'] = $booking['balanceDueDate'] ?? null;

        // 고객 이름 (client 테이블의 fName + lName)
        $booking['customerFirstName'] = $booking['fName'] ?? '';
        $booking['customerLastName'] = $booking['lName'] ?? '';
        $booking['customerEmail'] = $booking['emailAddress'] ?? '';
        $booking['customerPhone'] = $booking['contactNo'] ?? '';

        // 패키지 미팅 정보 (packages 테이블)
        $booking['meetingTime'] = $booking['meeting_time'] ?? '';
        $booking['meetingPlace'] = $booking['meeting_location'] ?? '';

        // 패키지 항공편 정보 조회 (package_flights 테이블)
        if (!empty($booking['packageId'])) {
            // package_flights 테이블 존재 확인
            $flightTableCheck = $conn->query("SHOW TABLES LIKE 'package_flights'");
            if ($flightTableCheck && $flightTableCheck->num_rows > 0) {
                // 출발편 조회
                $outFlightSql = "SELECT flight_number, departure_time, arrival_time, departure_point, destination
                                FROM package_flights WHERE package_id = ? AND flight_type = 'departure' LIMIT 1";
                $outFlightStmt = $conn->prepare($outFlightSql);
                $outFlightStmt->bind_param("i", $booking['packageId']);
                $outFlightStmt->execute();
                $outFlightResult = $outFlightStmt->get_result();
                $outFlight = $outFlightResult->fetch_assoc();
                $outFlightStmt->close();

                if ($outFlight) {
                    $booking['outboundFlight'] = [
                        'flightNumber' => $outFlight['flight_number'],
                        'departureDateTime' => $outFlight['departure_time'],
                        'arrivalDateTime' => $outFlight['arrival_time'],
                        'departureAirport' => $outFlight['departure_point'],
                        'arrivalAirport' => $outFlight['destination']
                    ];
                }

                // 귀국편 조회
                $inFlightSql = "SELECT flight_number, departure_time, arrival_time, departure_point, destination
                               FROM package_flights WHERE package_id = ? AND flight_type = 'return' LIMIT 1";
                $inFlightStmt = $conn->prepare($inFlightSql);
                $inFlightStmt->bind_param("i", $booking['packageId']);
                $inFlightStmt->execute();
                $inFlightResult = $inFlightStmt->get_result();
                $inFlight = $inFlightResult->fetch_assoc();
                $inFlightStmt->close();

                if ($inFlight) {
                    $booking['inboundFlight'] = [
                        'flightNumber' => $inFlight['flight_number'],
                        'departureDateTime' => $inFlight['departure_time'],
                        'arrivalDateTime' => $inFlight['arrival_time'],
                        'departureAirport' => $inFlight['departure_point'],
                        'arrivalAirport' => $inFlight['destination']
                    ];
                }
            }
        }

        send_success_response([
            'booking' => $booking,
            'selectedOptions' => $selectedOptions,
            'travelers' => $travelers
        ]);
    } catch (Exception $e) {
        send_error_response('Failed to get reservation detail: ' . $e->getMessage());
    }
}

function createReservation($conn, $input, $agentId = null) {
    try {
        $files = $_FILES ?? [];
        $downPaymentProofPath = null;
        $downPaymentProofAbsolutePath = null;
        $shouldDeleteDownPaymentProof = false;
        
        // 필수 필드 검증
        $requiredFields = ['packageId', 'departureDate', 'customerInfo', 'travelers'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // 트랜잭션 시작
        $conn->begin_transaction();
        
        try {
            // 고객 정보 처리 (기존 고객 또는 신규 고객)
            $accountId = null;
            if (!empty($input['customerInfo']['accountId'])) {
                $accountId = $input['customerInfo']['accountId'];
            } else {
                // 신규 고객 생성
                $accountId = createNewCustomer($conn, $input['customerInfo']);
            }
            
            // 예약 번호 생성
            $bookingId = generateBookingId($conn);
            
            // 선금 파일 업로드 처리
            if (isset($files['downPaymentProof']) && $files['downPaymentProof']['error'] === UPLOAD_ERR_OK) {
                $depositDir = __DIR__ . '/../../../uploads/payments/down_payment/';
                if (!is_dir($depositDir)) {
                    mkdir($depositDir, 0755, true);
                }
                $extension = strtolower(pathinfo($files['downPaymentProof']['name'], PATHINFO_EXTENSION));
                $extension = preg_replace('/[^a-z0-9]/', '', $extension);
                $extension = $extension ? '.' . $extension : '';
                $fileName = 'down_payment_' . $bookingId . '_' . time() . '_' . uniqid() . $extension;
                $uploadPath = $depositDir . $fileName;
                if (!move_uploaded_file($files['downPaymentProof']['tmp_name'], $uploadPath)) {
                    throw new Exception('Failed to upload down payment proof file');
                }
                $downPaymentProofPath = 'uploads/payments/down_payment/' . $fileName;
                $downPaymentProofAbsolutePath = $uploadPath;
                $shouldDeleteDownPaymentProof = true;
            }
            
            // 예약 정보 저장
            $packageId = $input['packageId'];
            $departureDate = $input['departureDate'];
            $departureTime = $input['departureTime'] ?? '12:20:00';
            $adults = isset($input['adults']) ? (int)$input['adults'] : 0;
            $children = isset($input['children']) ? (int)$input['children'] : 0;
            $infants = isset($input['infants']) ? (int)$input['infants'] : 0;
            
            // 가격 계산: 날짜별 가격(package_available_dates) 우선, 없으면 base_price 폴백
            // 1. 먼저 선택된 날짜의 가격 조회
            $datePriceSql = "SELECT price FROM package_available_dates WHERE package_id = ? AND available_date = ? LIMIT 1";
            $datePriceStmt = $conn->prepare($datePriceSql);
            $datePriceStmt->bind_param("is", $packageId, $departureDate);
            $datePriceStmt->execute();
            $datePriceResult = $datePriceStmt->get_result();
            $datePrice = $datePriceResult->fetch_assoc();
            $datePriceStmt->close();

            // 2. 패키지 기본 정보 조회 (폴백용 가격 및 아동/유아 가격)
            $packageSql = "SELECT base_price, packagePrice, childPrice, infantPrice FROM packages WHERE packageId = ?";
            $packageStmt = $conn->prepare($packageSql);
            $packageStmt->bind_param("i", $packageId);
            $packageStmt->execute();
            $packageResult = $packageStmt->get_result();
            $package = $packageResult->fetch_assoc();
            $packageStmt->close();

            // 3. 성인 가격: 날짜별 가격 > base_price > packagePrice 순으로 우선
            $adultPrice = $datePrice['price'] ?? $package['base_price'] ?? $package['packagePrice'] ?? 0;
            $childPrice = $package['childPrice'] ?? ($adultPrice * 0.8);
            $infantPrice = $package['infantPrice'] ?? ($adultPrice * 0.1);
            
            $baseAmount = ($adultPrice * $adults) + ($childPrice * $children) + ($infantPrice * $infants);
            
            // 룸 옵션 가격 계산
            $roomAmount = 0;
            if (!empty($input['selectedRooms'])) {
                foreach ($input['selectedRooms'] as $room) {
                    $roomPrice = $room['roomPrice'] ?? $room['price'] ?? 0;
                    $roomCount = $room['count'] ?? 1;
                    $roomAmount += $roomPrice * $roomCount;
                }
            }
            
            // 추가 옵션 가격 계산
            $optionsAmount = 0;
            if (!empty($input['selectedOptions'])) {
                foreach ($input['selectedOptions'] as $option) {
                    $optionsAmount += ($option['price'] ?? 0) * ($option['quantity'] ?? 0);
                }
            }
            
            $totalAmount = $baseAmount + $roomAmount + $optionsAmount;
            
            // selectedOptions JSON 생성
            $selectedOptions = [
                'selectedRooms' => $input['selectedRooms'] ?? [],
                'selectedOptions' => $input['selectedOptions'] ?? [],
                'customerInfo' => $input['customerInfo'] ?? [],
                'seatRequest' => $input['seatRequest'] ?? '',
                'otherRequest' => $input['otherRequest'] ?? '',
                'memo' => $input['memo'] ?? ''
            ];
            
            // 3단계 결제 금액 계산
            $downPaymentAmount = 5000.00; // 선금 고정
            $advancePaymentAmount = 10000.00; // 중도금 고정
            $balanceAmount = max(0, $totalAmount - $downPaymentAmount - $advancePaymentAmount); // 잔금

            // 결제 기한 계산
            $createdAt = new DateTime();
            $departureDateObj = new DateTime($departureDate);

            // Step 1: Down Payment - 예약일 + 3일
            $downPaymentDueDateObj = (clone $createdAt)->modify('+3 days');
            $downPaymentDueDate = $downPaymentDueDateObj->format('Y-m-d');

            // 출발일과 예약일의 차이 계산 (최우선 조건)
            $daysDifference = $createdAt->diff($departureDateObj)->days;

            // Step 2, Step 3 계산
            if ($daysDifference <= 30) {
                // 출발일이 예약일 기준 30일 이내면 Step 2, Step 3 모두 Step 1과 동일
                $advancePaymentDueDate = $downPaymentDueDate;
                $balanceDueDate = $downPaymentDueDate;
            } else {
                // 출발일이 30일 이상 차이나면
                // Step 3 = 출발일 - 30일
                $balanceDueDateObj = (clone $departureDateObj)->modify('-30 days');
                $balanceDueDate = $balanceDueDateObj->format('Y-m-d');

                // Step 2 = Down Payment Proof 업로드 전에는 null
                $advancePaymentDueDate = null;
            }

            // 초기 상태 결정
            $initialStatus = 'waiting_down_payment';
            $downPaymentUploadedAt = null;

            if ($downPaymentProofPath) {
                // 선금 파일이 업로드되면 관리자 확인 대기 상태로
                $initialStatus = 'checking_down_payment';
                $downPaymentUploadedAt = $createdAt->format('Y-m-d H:i:s');

                // 30일 초과인 경우에만 Step 2 계산 (30일 이내는 이미 위에서 설정됨)
                if ($daysDifference > 30) {
                    // Step 2: Advance Payment - Down Payment Proof 업로드일 + 30일
                    $advancePaymentDueDateObj = (clone $createdAt)->modify('+30 days');
                    $advancePaymentDueDate = $advancePaymentDueDateObj->format('Y-m-d');

                    // Step 2가 Step 3보다 이후면 Step 3와 동일하게
                    if ($advancePaymentDueDate > $balanceDueDate) {
                        $advancePaymentDueDate = $balanceDueDate;
                    }
                }
            }
            
            $packageNameSql = "SELECT packageName FROM packages WHERE packageId = ?";
            $packageNameStmt = $conn->prepare($packageNameSql);
            $packageNameStmt->bind_param("i", $packageId);
            $packageNameStmt->execute();
            $packageNameResult = $packageNameStmt->get_result();
            $packageNameRow = $packageNameResult->fetch_assoc();
            $packageName = $packageNameRow['packageName'] ?? '';
            
            $selectedOptionsJson = json_encode($selectedOptions, JSON_UNESCAPED_UNICODE);
            $specialRequests = ($input['seatRequest'] ?? '') . ($input['otherRequest'] ?? '');
            $contactEmail = $input['customerInfo']['email'] ?? '';
            $contactPhone = $input['customerInfo']['phone'] ?? '';

            // roomOption JSON 생성 (선택된 룸 옵션 저장)
            $roomOptionJson = json_encode($input['selectedRooms'] ?? [], JSON_UNESCAPED_UNICODE);

            // INSERT 쿼리 구성 (3단계 결제 시스템)
            $insertSql = "
                INSERT INTO bookings (
                    bookingId, accountId, packageId, packageName, packagePrice,
                    departureDate, departureTime, adults, children, infants,
                    totalAmount, bookingStatus, paymentStatus, selectedOptions, roomOption,
                    specialRequests, contactEmail, contactPhone,
                    downPaymentAmount, downPaymentDueDate, downPaymentFile, downPaymentUploadedAt,
                    advancePaymentAmount, advancePaymentDueDate,
                    balanceAmount, balanceDueDate,
                    agentId, createdAt
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ";

            $insertStmt = $conn->prepare($insertSql);

            // 타입 문자열 설명:
            // s: bookingId, i: accountId, i: packageId, s: packageName, d: packagePrice,
            // s: departureDate, s: departureTime, i: adults, i: children, i: infants,
            // d: totalAmount, s: bookingStatus, s: selectedOptionsJson, s: roomOptionJson,
            // s: specialRequests, s: contactEmail, s: contactPhone,
            // d: downPaymentAmount, s: downPaymentDueDate, s: downPaymentFile, s: downPaymentUploadedAt,
            // d: advancePaymentAmount, s: advancePaymentDueDate,
            // d: balanceAmount, s: balanceDueDate, i: agentId
            $insertStmt->bind_param(
                "siisdssiiidssssssdsssdsdsi",
                $bookingId, $accountId, $packageId, $packageName, $adultPrice,
                $departureDate, $departureTime, $adults, $children, $infants,
                $totalAmount, $initialStatus, $selectedOptionsJson, $roomOptionJson,
                $specialRequests, $contactEmail, $contactPhone,
                $downPaymentAmount, $downPaymentDueDate, $downPaymentProofPath, $downPaymentUploadedAt,
                $advancePaymentAmount, $advancePaymentDueDate,
                $balanceAmount, $balanceDueDate,
                $agentId
            );
            $insertStmt->execute();

            // transactNo 설정 (bookingId와 동일한 값)
            $bookingsColumns = [];
            $bookingsColumnCheck = $conn->query("SHOW COLUMNS FROM bookings");
            if ($bookingsColumnCheck) {
                while ($col = $bookingsColumnCheck->fetch_assoc()) {
                    $bookingsColumns[] = strtolower($col['Field']);
                }
            }

            $hasTransactNo = in_array('transactno', $bookingsColumns);
            if ($hasTransactNo) {
                $updateSql = "UPDATE bookings SET transactNo = ? WHERE bookingId = ? AND (transactNo IS NULL OR transactNo = '')";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ss", $bookingId, $bookingId);
                $updateStmt->execute();
            }
            
            // www/user와 동일하게 transactNo에 bookingId 값을 저장
            // booking_travelers 테이블의 transactNo 컬럼에는 항상 bookingId 값이 저장됨
            $transactNoForTravelers = $bookingId; // www/user와 동일하게 bookingId 사용
            
            // 여행자 정보 저장 (booking_travelers 테이블 컬럼명 확인)
            $travelerColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers LIKE 'transactNo'");
            $useTransactNo = ($travelerColumnCheck->num_rows > 0);
            $bookingIdColumn = $useTransactNo ? 'transactNo' : 'bookingId';
            
            $firstNameColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers LIKE 'firstName'");
            $useFirstName = ($firstNameColumnCheck->num_rows > 0);
            $firstNameColumn = $useFirstName ? 'firstName' : 'fName';
            $lastNameColumn = $useFirstName ? 'lastName' : 'lName';

            // 중복 제거: accountId 또는 이름(firstName + lastName)으로
            $uniqueTravelers = [];
            $seen = [];
            foreach ($input['travelers'] as $traveler) {
                $accountId = $traveler['accountId'] ?? null;
                $firstName = strtolower(trim($traveler['firstName'] ?? ''));
                $lastName = strtolower(trim($traveler['lastName'] ?? ''));

                if ($accountId) {
                    $key = 'id:' . $accountId;
                } elseif ($firstName && $lastName) {
                    $key = 'name:' . $firstName . '|' . $lastName;
                } else {
                    // 이름이 비어있는 경우는 그냥 추가
                    $uniqueTravelers[] = $traveler;
                    continue;
                }

                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $uniqueTravelers[] = $traveler;
                }
            }

            error_log("Original travelers: " . count($input['travelers']) . ", Unique travelers: " . count($uniqueTravelers));

            foreach ($uniqueTravelers as $index => $traveler) {
                // booking_travelers 테이블 컬럼 확인
                $travelerColumns = [];
                $travelerColumnResult = $conn->query("SHOW COLUMNS FROM booking_travelers");
                while ($col = $travelerColumnResult->fetch_assoc()) {
                    $travelerColumns[] = strtolower($col['Field']);
                }
                
                $travelerFields = [];
                $travelerValues = [];
                $travelerTypes = '';
                
                // 기본 필드 (www/user와 동일하게 transactNo 컬럼에 bookingId 값 저장)
                $travelerFields[] = $bookingIdColumn;
                $travelerValues[] = $transactNoForTravelers; // www/user와 동일하게 bookingId 값 사용
                $travelerTypes .= 's';
                
                // travelerIndex 컬럼이 있으면 추가
                if (in_array('travelerindex', $travelerColumns)) {
                $travelerFields[] = 'travelerIndex';
                $travelerValues[] = $index;
                $travelerTypes .= 'i';
                }
                
                // isMainTraveler 컬럼이 있으면 추가
                if (in_array('ismaintraveler', $travelerColumns)) {
                $isMain = ($index === 0 || ($traveler['isMainTraveler'] ?? false)) ? 1 : 0;
                    $travelerFields[] = 'isMainTraveler';
                $travelerValues[] = $isMain;
                $travelerTypes .= 'i';
                }
                
                // travelerType 컬럼이 있으면 추가
                if (in_array('travelertype', $travelerColumns)) {
                $travelerType = $traveler['type'] ?? 'adult';
                    $travelerFields[] = 'travelerType';
                $travelerValues[] = $travelerType;
                $travelerTypes .= 's';
                }
                
                // title
                if (in_array('title', $travelerColumns)) {
                    $travelerFields[] = 'title';
                    $travelerValues[] = $traveler['title'] ?? '';
                    $travelerTypes .= 's';
                }
                
                // firstName / fName
                if (in_array('firstname', $travelerColumns)) {
                    $travelerFields[] = 'firstName';
                    $travelerValues[] = $traveler['firstName'] ?? '';
                    $travelerTypes .= 's';
                } elseif (in_array('fname', $travelerColumns)) {
                    $travelerFields[] = 'fName';
                    $travelerValues[] = $traveler['firstName'] ?? '';
                    $travelerTypes .= 's';
                }
                
                // lastName / lName
                if (in_array('lastname', $travelerColumns)) {
                    $travelerFields[] = 'lastName';
                    $travelerValues[] = $traveler['lastName'] ?? '';
                    $travelerTypes .= 's';
                } elseif (in_array('lname', $travelerColumns)) {
                    $travelerFields[] = 'lName';
                    $travelerValues[] = $traveler['lastName'] ?? '';
                    $travelerTypes .= 's';
                }
                
                // gender
                if (in_array('gender', $travelerColumns)) {
                    $travelerFields[] = 'gender';
                    $travelerValues[] = $traveler['gender'] ?? '';
                    $travelerTypes .= 's';
                }
                
                // birthDate (여러 가능한 컬럼명 확인, YYYY-MM-DD 형식으로 변환)
                $birthDate = null;
                if (!empty($traveler['birthDate'])) {
                    // YYYYMMDD 형식을 YYYY-MM-DD로 변환
                    $birthDateStr = $traveler['birthDate'];
                    if (strlen($birthDateStr) === 8 && is_numeric($birthDateStr)) {
                        // YYYYMMDD 형식인 경우
                        $birthDate = substr($birthDateStr, 0, 4) . '-' . substr($birthDateStr, 4, 2) . '-' . substr($birthDateStr, 6, 2);
                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDateStr)) {
                        // 이미 YYYY-MM-DD 형식인 경우
                        $birthDate = $birthDateStr;
                    } else {
                        // 그 외 (날짜 파싱 시도)
                        try {
                            $date = new DateTime($birthDateStr);
                            $birthDate = $date->format('Y-m-d');
                        } catch (Exception $e) {
                            $birthDate = null;
                        }
                    }
                    
                    if ($birthDate) {
                        if (in_array('birthdate', $travelerColumns)) {
                    $travelerFields[] = 'birthDate';
                            $travelerValues[] = $birthDate;
                            $travelerTypes .= 's';
                        } elseif (in_array('dateofbirth', $travelerColumns)) {
                            $travelerFields[] = 'dateOfBirth';
                            $travelerValues[] = $birthDate;
                    $travelerTypes .= 's';
                }
                    }
                }
                
                // age (생년월일로부터 자동 계산)
                if (in_array('age', $travelerColumns)) {
                    $age = null;
                    if (isset($traveler['age']) && $traveler['age'] > 0) {
                        $age = (int)$traveler['age'];
                    } elseif ($birthDate) {
                        // 생년월일로부터 나이 계산
                        try {
                            $birth = new DateTime($birthDate);
                            $today = new DateTime();
                            $age = $today->diff($birth)->y;
                        } catch (Exception $e) {
                            $age = null;
                        }
                    }
                    if ($age !== null) {
                    $travelerFields[] = 'age';
                        $travelerValues[] = $age;
                    $travelerTypes .= 'i';
                    }
                }
                
                // contact
                if (in_array('contact', $travelerColumns) && !empty($traveler['contact'])) {
                    $travelerFields[] = 'contact';
                    $travelerValues[] = $traveler['contact'];
                    $travelerTypes .= 's';
                }
                
                // email
                if (in_array('email', $travelerColumns) && !empty($traveler['email'])) {
                    $travelerFields[] = 'email';
                    $travelerValues[] = $traveler['email'];
                    $travelerTypes .= 's';
                }
                
                // nationality
                if (in_array('nationality', $travelerColumns) && !empty($traveler['nationality'])) {
                    $travelerFields[] = 'nationality';
                    $travelerValues[] = $traveler['nationality'];
                    $travelerTypes .= 's';
                }
                
                // passportNumber
                if (in_array('passportnumber', $travelerColumns) && !empty($traveler['passportNumber'])) {
                    $travelerFields[] = 'passportNumber';
                    $travelerValues[] = $traveler['passportNumber'];
                    $travelerTypes .= 's';
                }
                
                // passportIssueDate (있는 경우, YYYY-MM-DD 형식으로 변환)
                $passportIssueDate = null;
                if (!empty($traveler['passportIssueDate']) || !empty($traveler['passportIssue'])) {
                    $passportIssueDateStr = $traveler['passportIssueDate'] ?? $traveler['passportIssue'] ?? null;
                    if ($passportIssueDateStr) {
                        if (strlen($passportIssueDateStr) === 8 && is_numeric($passportIssueDateStr)) {
                            $passportIssueDate = substr($passportIssueDateStr, 0, 4) . '-' . substr($passportIssueDateStr, 4, 2) . '-' . substr($passportIssueDateStr, 6, 2);
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $passportIssueDateStr)) {
                            $passportIssueDate = $passportIssueDateStr;
                        } else {
                            try {
                                $date = new DateTime($passportIssueDateStr);
                                $passportIssueDate = $date->format('Y-m-d');
                            } catch (Exception $e) {
                                $passportIssueDate = null;
                            }
                        }
                    }
                    
                    if ($passportIssueDate) {
                        if (in_array('passportissuedate', $travelerColumns)) {
                            $travelerFields[] = 'passportIssueDate';
                            $travelerValues[] = $passportIssueDate;
                            $travelerTypes .= 's';
                        } elseif (in_array('passportissueddate', $travelerColumns)) {
                            $travelerFields[] = 'passportIssuedDate';
                            $travelerValues[] = $passportIssueDate;
                            $travelerTypes .= 's';
                        }
                    }
                }
                
                // passportExpiry (여러 가능한 컬럼명 확인, YYYY-MM-DD 형식으로 변환)
                $passportExpiry = null;
                if (!empty($traveler['passportExpiry'])) {
                    $passportExpiryStr = $traveler['passportExpiry'];
                    if (strlen($passportExpiryStr) === 8 && is_numeric($passportExpiryStr)) {
                        // YYYYMMDD 형식인 경우
                        $passportExpiry = substr($passportExpiryStr, 0, 4) . '-' . substr($passportExpiryStr, 4, 2) . '-' . substr($passportExpiryStr, 6, 2);
                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $passportExpiryStr)) {
                        // 이미 YYYY-MM-DD 형식인 경우
                        $passportExpiry = $passportExpiryStr;
                    } else {
                        try {
                            $date = new DateTime($passportExpiryStr);
                            $passportExpiry = $date->format('Y-m-d');
                        } catch (Exception $e) {
                            $passportExpiry = null;
                        }
                    }
                    
                    if ($passportExpiry) {
                        if (in_array('passportexpiry', $travelerColumns)) {
                    $travelerFields[] = 'passportExpiry';
                            $travelerValues[] = $passportExpiry;
                            $travelerTypes .= 's';
                        } elseif (in_array('passportexpirydate', $travelerColumns)) {
                            $travelerFields[] = 'passportExpiryDate';
                            $travelerValues[] = $passportExpiry;
                    $travelerTypes .= 's';
                }
                    }
                }
                
                // passportImage (있는 경우) - Save as file, not base64
                if (!empty($traveler['passportImage']) && in_array('passportimage', $travelerColumns)) {
                    $passportImageValue = $traveler['passportImage'];

                    // Check if it's base64 data and save to file
                    if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $passportImageValue, $imgMatches)) {
                        $imgExt = $imgMatches[1];
                        $imgData = base64_decode($imgMatches[2]);

                        if ($imgData !== false) {
                            $travelerPassportDir = __DIR__ . '/../../../uploads/passport_photos/';
                            if (!is_dir($travelerPassportDir)) {
                                mkdir($travelerPassportDir, 0755, true);
                            }

                            $travelerPassportFileName = 'passport_traveler_' . $bookingId . '_' . ($i + 1) . '_' . time() . '.' . $imgExt;
                            $travelerPassportFilePath = $travelerPassportDir . $travelerPassportFileName;

                            if (file_put_contents($travelerPassportFilePath, $imgData)) {
                                $passportImageValue = '/uploads/passport_photos/' . $travelerPassportFileName;
                            }
                        }
                    }

                    $travelerFields[] = 'passportImage';
                    $travelerValues[] = $passportImageValue;
                    $travelerTypes .= 's';
                }
                
                // visaStatus (visaRequired 대신 visaStatus 사용하는 경우)
                if (in_array('visastatus', $travelerColumns)) {
                    $visaStatus = 'not_required';
                    if (!empty($traveler['visaRequired']) && $traveler['visaRequired']) {
                        $visaStatus = 'applied';
                    }
                    $travelerFields[] = 'visaStatus';
                    $travelerValues[] = $visaStatus;
                    $travelerTypes .= 's';
                } elseif (in_array('visarequired', $travelerColumns)) {
                    // visaRequired (boolean)를 사용하는 경우
                    $travelerFields[] = 'visaRequired';
                    $travelerValues[] = isset($traveler['visaRequired']) ? (int)$traveler['visaRequired'] : 0;
                    $travelerTypes .= 'i';
                }
                
                // specialRequests 또는 remarks
                if (!empty($traveler['remarks']) || !empty($traveler['specialRequests'])) {
                    $remarks = $traveler['remarks'] ?? $traveler['specialRequests'] ?? '';
                    if (in_array('specialrequests', $travelerColumns)) {
                        $travelerFields[] = 'specialRequests';
                        $travelerValues[] = $remarks;
                        $travelerTypes .= 's';
                    } elseif (in_array('remarks', $travelerColumns)) {
                    $travelerFields[] = 'remarks';
                        $travelerValues[] = $remarks;
                    $travelerTypes .= 's';
                    }
                }
                
                $travelerPlaceholders = str_repeat('?,', count($travelerFields) - 1) . '?';
                $travelerSql = "INSERT INTO booking_travelers (" . implode(', ', $travelerFields) . ") VALUES ($travelerPlaceholders)";
                
                $travelerStmt = $conn->prepare($travelerSql);
                if (!$travelerStmt) {
                    throw new Exception('Failed to prepare traveler SQL: ' . $conn->error);
                }
                
                $travelerStmt->bind_param($travelerTypes, ...$travelerValues);
                if (!$travelerStmt->execute()) {
                    throw new Exception('Failed to execute traveler insert: ' . $travelerStmt->error . ' | SQL: ' . $travelerSql);
                }
                $travelerStmt->close();

                // Save passport photo to client table if traveler has accountId and passportImage
                if (!empty($traveler['accountId']) && !empty($traveler['passportImage'])) {
                    $travelerAccountId = $traveler['accountId'];
                    $passportImageData = $traveler['passportImage'];

                    // Check if it's a base64 data URL (newly uploaded image)
                    if (preg_match('/^data:image\//', $passportImageData)) {
                        // Save the base64 image to file
                        $passportDir = __DIR__ . '/../../../uploads/passport_photos/';
                        if (!is_dir($passportDir)) {
                            mkdir($passportDir, 0755, true);
                        }

                        // Extract image data and extension
                        if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $passportImageData, $matches)) {
                            $imageExt = $matches[1];
                            $imageData = base64_decode($matches[2]);

                            if ($imageData !== false) {
                                $passportFileName = 'passport_' . $travelerAccountId . '_' . time() . '.' . $imageExt;
                                $passportFilePath = $passportDir . $passportFileName;

                                if (file_put_contents($passportFilePath, $imageData)) {
                                    $passportImagePath = '/uploads/passport_photos/' . $passportFileName;

                                    // Update client table with the new profileImage
                                    $updateClientSql = "UPDATE client SET profileImage = ? WHERE accountId = ?";
                                    $updateClientStmt = $conn->prepare($updateClientSql);
                                    if ($updateClientStmt) {
                                        $updateClientStmt->bind_param("si", $passportImagePath, $travelerAccountId);
                                        $updateClientStmt->execute();
                                        $updateClientStmt->close();
                                        error_log("Updated client profileImage for accountId: $travelerAccountId with path: $passportImagePath");
                                    }
                                }
                            }
                        }
                    } elseif (!preg_match('/^(https?:\/\/)/', $passportImageData)) {
                        // It's already a file path (not a URL), update client table
                        $updateClientSql = "UPDATE client SET profileImage = ? WHERE accountId = ?";
                        $updateClientStmt = $conn->prepare($updateClientSql);
                        if ($updateClientStmt) {
                            $updateClientStmt->bind_param("si", $passportImageData, $travelerAccountId);
                            $updateClientStmt->execute();
                            $updateClientStmt->close();
                            error_log("Updated client profileImage for accountId: $travelerAccountId with existing path: $passportImageData");
                        }
                    }
                }
            }

            $conn->commit();
            $shouldDeleteDownPaymentProof = false;

            send_success_response([
                'bookingId' => $bookingId,
                'bookingStatus' => $initialStatus,
                'downPaymentAmount' => $downPaymentAmount,
                'advancePaymentAmount' => $advancePaymentAmount,
                'balanceAmount' => $balanceAmount
            ], 'Reservation created successfully');

        } catch (Exception $e) {
            if ($shouldDeleteDownPaymentProof && $downPaymentProofAbsolutePath && file_exists($downPaymentProofAbsolutePath)) {
                unlink($downPaymentProofAbsolutePath);
            }
            $conn->rollback();
            throw $e;
        }

    } catch (Exception $e) {
        if ($shouldDeleteDownPaymentProof && $downPaymentProofAbsolutePath && file_exists($downPaymentProofAbsolutePath)) {
            unlink($downPaymentProofAbsolutePath);
        }
        send_error_response('Failed to create reservation: ' . $e->getMessage());
    }
}

function updateReservation($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        
        if (empty($bookingId)) {
            send_error_response('Booking ID is required');
        }
        
        // 업데이트 가능한 필드들
        $updates = [];
        $params = [];
        $types = '';
        
        $allowedFields = [
            'departureDate' => 's',
            'departureTime' => 's',
            'selectedOptions' => 's',
            'specialRequests' => 's',
            'contactEmail' => 's',
            'contactPhone' => 's'
        ];
        
        foreach ($allowedFields as $field => $type) {
            if (isset($input[$field])) {
                $updates[] = "$field = ?";
                $params[] = $input[$field];
                $types .= $type;
            }
        }
        
        if (empty($updates)) {
            send_error_response('No fields to update');
        }
        
        $params[] = $bookingId;
        $types .= 's';
        
        $sql = "UPDATE bookings SET " . implode(', ', $updates) . " WHERE bookingId = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        send_success_response([], 'Reservation updated successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to update reservation: ' . $e->getMessage());
    }
}

function updateReservationStatus($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $status = $input['status'] ?? '';
        
        if (empty($bookingId) || empty($status)) {
            send_error_response('Booking ID and status are required');
        }
        
        // 한글 상태를 영어로 변환
        $status = normalizeBookingStatus($status);
        
        $sql = "UPDATE bookings SET bookingStatus = ? WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $status, $bookingId);
        $stmt->execute();
        
        send_success_response([], 'Reservation status updated successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to update reservation status: ' . $e->getMessage());
    }
}

function confirmDeposit($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $amount = isset($input['amount']) ? floatval($input['amount']) : 0;
        $dueDate = $input['dueDate'] ?? null;
        
        if (empty($bookingId)) {
            send_error_response('Booking ID is required');
        }
        
        if ($amount <= 0) {
            send_error_response('Amount must be greater than 0');
        }
        
        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }
        
        // 업데이트할 필드들 구성
        $updateFields = [];
        $updateValues = [];
        $updateTypes = '';
        
        // depositConfirmedAmount 또는 depositAmount 컬럼 확인
        if (in_array('depositconfirmedamount', $bookingsColumns)) {
            $updateFields[] = 'depositConfirmedAmount = ?';
            $updateValues[] = $amount;
            $updateTypes .= 'd';
        } elseif (in_array('depositamount', $bookingsColumns)) {
            $updateFields[] = 'depositAmount = ?';
            $updateValues[] = $amount;
            $updateTypes .= 'd';
        }
        
        // depositConfirmed 컬럼 확인
        if (in_array('depositconfirmed', $bookingsColumns)) {
            $updateFields[] = 'depositConfirmed = 1';
        }
        
        // depositStatus 컬럼 확인
        if (in_array('depositstatus', $bookingsColumns)) {
            $updateFields[] = 'depositStatus = ?';
            $updateValues[] = 'confirmed';
            $updateTypes .= 's';
        }
        
        // depositDueDate 컬럼 확인
        if ($dueDate && in_array('depositduedate', $bookingsColumns)) {
            $updateFields[] = 'depositDueDate = ?';
            $updateValues[] = $dueDate;
            $updateTypes .= 's';
        }
        
        if (empty($updateFields)) {
            send_error_response('No deposit-related columns found in bookings table');
        }
        
        $updateValues[] = $bookingId;
        $updateTypes .= 's';
        
        $sql = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($updateTypes, ...$updateValues);
        $stmt->execute();
        
        send_success_response([], 'Deposit confirmed successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to confirm deposit: ' . $e->getMessage());
    }
}

function removeDepositProofFile($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        
        if (empty($bookingId)) {
            send_error_response('Booking ID is required');
        }
        
        // bookings 테이블에서 현재 파일 경로 가져오기
        $stmt = $conn->prepare("SELECT depositProofFile FROM bookings WHERE bookingId = ?");
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            send_error_response('Booking not found');
        }
        
        $booking = $result->fetch_assoc();
        $filePath = $booking['depositProofFile'] ?? '';
        
        // 파일 삭제
        if (!empty($filePath)) {
            // 경로 정규화
            $filePathClean = str_replace('/smart-travel2/', '/', $filePath);
            $filePathClean = str_replace('smart-travel2/', '', $filePathClean);
            $filePathClean = preg_replace('#/uploads/uploads/#', '/uploads/', $filePathClean);
            $filePathClean = ltrim($filePathClean, '/');
            
            // 여러 경로 확인하여 파일 삭제
            $filePath1 = __DIR__ . '/../../../' . $filePathClean;
            $filePath2 = __DIR__ . '/../../../../' . $filePathClean;
            
            if (file_exists($filePath1)) {
                unlink($filePath1);
            } elseif (file_exists($filePath2)) {
                unlink($filePath2);
            }
        }
        
        // DB에서 파일 경로 제거
        $updateStmt = $conn->prepare("UPDATE bookings SET depositProofFile = NULL WHERE bookingId = ?");
        $updateStmt->bind_param("s", $bookingId);
        $updateStmt->execute();
        
        send_success_response([], 'Deposit proof file removed successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to remove deposit proof file: ' . $e->getMessage());
    }
}

function confirmBalance($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $amount = isset($input['amount']) ? floatval($input['amount']) : 0;
        $dueDate = $input['dueDate'] ?? null;
        
        if (empty($bookingId)) {
            send_error_response('Booking ID is required');
        }
        
        if ($amount <= 0) {
            send_error_response('Amount must be greater than 0');
        }
        
        // bookings 테이블 컬럼 확인
        $bookingsColumns = [];
        $bookingColumnResult = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingColumnResult) {
            while ($col = $bookingColumnResult->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }
        
        // 업데이트할 필드들 구성
        $updateFields = [];
        $updateValues = [];
        $updateTypes = '';
        
        // balanceConfirmedAmount 컬럼 확인
        if (in_array('balanceconfirmedamount', $bookingsColumns)) {
            $updateFields[] = 'balanceConfirmedAmount = ?';
            $updateValues[] = $amount;
            $updateTypes .= 'd';
        }
        
        // balanceConfirmed 컬럼 확인
        if (in_array('balanceconfirmed', $bookingsColumns)) {
            $updateFields[] = 'balanceConfirmed = 1';
        }
        
        // balanceStatus 컬럼 확인
        if (in_array('balancestatus', $bookingsColumns)) {
            $updateFields[] = 'balanceStatus = ?';
            $updateValues[] = 'confirmed';
            $updateTypes .= 's';
        }
        
        // balanceDueDate 컬럼 확인
        if ($dueDate && in_array('balanceduedate', $bookingsColumns)) {
            $updateFields[] = 'balanceDueDate = ?';
            $updateValues[] = $dueDate;
            $updateTypes .= 's';
        }
        
        if (empty($updateFields)) {
            send_error_response('No balance-related columns found in bookings table');
        }
        
        $updateValues[] = $bookingId;
        $updateTypes .= 's';
        
        $sql = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($updateTypes, ...$updateValues);
        $stmt->execute();
        
        send_success_response([], 'Balance confirmed successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to confirm balance: ' . $e->getMessage());
    }
}

// ========== 고객 관련 함수들 ==========

function getCustomers($conn, $input, $agentId) {
    try {
        // 테이블 존재 확인
        $tableCheck = $conn->query("SHOW TABLES LIKE 'client'");
        if ($tableCheck->num_rows === 0) {
            throw new Exception('client table does not exist');
        }

        $page = isset($input['page']) ? (int)$input['page'] : 1;
        $limit = isset($input['limit']) ? (int)$input['limit'] : 20;
        $offset = ($page - 1) * $limit;

        // client 및 accounts 테이블 컬럼 확인
        $clientColumns = [];
        $clientColumnsCheck = $conn->query("SHOW COLUMNS FROM client");
        if ($clientColumnsCheck) {
            while ($col = $clientColumnsCheck->fetch_assoc()) {
                $clientColumns[] = strtolower($col['Field']);
            }
        }

        $accountsColumns = [];
        $accountsColumnsCheck = $conn->query("SHOW COLUMNS FROM accounts");
        if ($accountsColumnsCheck) {
            while ($col = $accountsColumnsCheck->fetch_assoc()) {
                $accountsColumns[] = strtolower($col['Field']);
            }
        }

        // agentId 컬럼 위치 확인
        $hasClientAgentId = in_array('agentid', $clientColumns);
        $hasAccountsAgentId = in_array('agentid', $accountsColumns);

        $where = [];
        $params = [];
        $types = '';

        // Agent 필터링 조건 (최우선)
        if ($hasClientAgentId) {
            $where[] = "c.agentId = ?";
            $params[] = $agentId;
            $types .= 'i';
        } elseif ($hasAccountsAgentId) {
            $where[] = "a.agentId = ?";
            $params[] = $agentId;
            $types .= 'i';
        }

        if (!empty($input['search'])) {
            $where[] = "(c.fName LIKE ? OR c.lName LIKE ? OR a.emailAddress LIKE ? OR c.contactNo LIKE ?)";
            $searchTerm = '%' . $input['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // COUNT 쿼리 - JOIN을 포함하여 정확한 카운트
        $countSql = "SELECT COUNT(*) as total FROM client c LEFT JOIN accounts a ON c.accountId = a.accountId $whereClause";
        
        if (!empty($params)) {
            $countStmt = $conn->prepare($countSql);
            if (!$countStmt) {
                throw new Exception('Failed to prepare count query: ' . $conn->error . ' | SQL: ' . $countSql);
            }
            $countStmt->bind_param($types, ...$params);
            if (!$countStmt->execute()) {
                throw new Exception('Failed to execute count query: ' . $countStmt->error);
            }
            $totalResult = $countStmt->get_result();
        } else {
            $totalResult = $conn->query($countSql);
            if (!$totalResult) {
                throw new Exception('Failed to execute count query: ' . $conn->error . ' | SQL: ' . $countSql);
            }
        }
        $totalRow = $totalResult->fetch_assoc();
        $total = $totalRow ? (int)$totalRow['total'] : 0;
        
        // 컬럼 존재 여부 확인 (옵셔널 컬럼들)
        $columnsCheck = $conn->query("SHOW COLUMNS FROM client");
        $clientColumns = [];
        while ($col = $columnsCheck->fetch_assoc()) {
            $clientColumns[] = strtolower($col['Field']);
        }
        
        // 옵셔널 컬럼들을 선택적으로 포함
        $genderCol = in_array('gender', $clientColumns) ? 'c.gender' : "NULL as gender";
        $dobCol = in_array('dateofbirth', $clientColumns) ? 'c.dateOfBirth' : "NULL as dateOfBirth";
        $nationalityCol = in_array('nationality', $clientColumns) ? 'c.nationality' : "NULL as nationality";
        $passportNumCol = in_array('passportnumber', $clientColumns) ? 'c.passportNumber' : "NULL as passportNumber";
        $passportExpCol = in_array('passportexpiry', $clientColumns) ? 'c.passportExpiry' : "NULL as passportExpiry";
        $passportIssueDateCol = in_array('passportissuedate', $clientColumns) ? 'c.passportIssueDate' : "NULL as passportIssueDate";
        $profileImageCol = in_array('profileimage', $clientColumns) ? 'c.profileImage' : "NULL as profileImage";
        $emailCol = in_array('emailaddress', $clientColumns) ? 'COALESCE(a.emailAddress, c.emailAddress)' : 'a.emailAddress';
        $hasCreatedAt = in_array('createdat', $clientColumns);
        $createdAtCol = $hasCreatedAt ? 'COALESCE(a.createdAt, c.createdAt)' : 'a.createdAt';

        $sql = "
            SELECT
                c.accountId,
                c.fName,
                c.lName,
                $genderCol,
                $dobCol,
                c.contactNo,
                $emailCol as emailAddress,
                $nationalityCol,
                $passportNumCol,
                $passportExpCol,
                $passportIssueDateCol,
                $profileImageCol,
                $createdAtCol as createdAt
            FROM client c
            LEFT JOIN accounts a ON c.accountId = a.accountId
            $whereClause
            ORDER BY $createdAtCol DESC
            LIMIT ? OFFSET ?
        ";
        
        // LIMIT과 OFFSET 파라미터 추가
        $limitParams = [$limit, $offset];
        $limitTypes = 'ii';
        
        // 기존 파라미터와 병합
        if (!empty($params)) {
            $allParams = array_merge($params, $limitParams);
            $allTypes = $types . $limitTypes;
        } else {
            $allParams = $limitParams;
            $allTypes = $limitTypes;
        }
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare query: ' . $conn->error);
        }
        
        $stmt->bind_param($allTypes, ...$allParams);
        $stmt->execute();
        
        if ($stmt->error) {
            throw new Exception('Failed to execute query: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        $customers = [];
        $rowNum = $total - $offset;
        while ($row = $result->fetch_assoc()) {
            $fName = $row['fName'] ?? '';
            $lName = $row['lName'] ?? '';
            $customerName = trim($fName . ' ' . $lName);
            
            // profileImage 경로 처리
            $profileImage = $row['profileImage'] ?? '';
            if (!empty($profileImage) && !preg_match('/^(https?:\/\/|data:)/', $profileImage)) {
                // 상대 경로를 절대 경로로 변환
                $profileImage = str_replace('/smart-travel2/', '/', $profileImage);
                $profileImage = str_replace('smart-travel2/', '', $profileImage);
                $profileImage = preg_replace('#/uploads/uploads/#', '/uploads/', $profileImage);
                if (strpos($profileImage, '/') !== 0) {
                    $profileImage = '/' . $profileImage;
                }
            }

            $customers[] = [
                'rowNum' => $rowNum--,
                'accountId' => $row['accountId'],
                'customerName' => $customerName,
                'email' => $row['emailAddress'] ?? '',
                'phone' => $row['contactNo'] ?? '',
                'fName' => $fName,
                'lName' => $lName,
                'gender' => $row['gender'] ?? '',
                'dateOfBirth' => $row['dateOfBirth'] ?? null,
                'contactNo' => $row['contactNo'] ?? '',
                'emailAddress' => $row['emailAddress'] ?? '',
                'nationality' => $row['nationality'] ?? '',
                'passportNumber' => $row['passportNumber'] ?? '',
                'passportExpiry' => $row['passportExpiry'] ?? null,
                'passportIssueDate' => $row['passportIssueDate'] ?? null,
                'profileImage' => $profileImage,
                'createdAt' => $row['createdAt'] ?? ''
            ];
        }
        
        send_success_response([
            'customers' => $customers,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($total / $limit)
            ]
        ]);
    } catch (Exception $e) {
        error_log('getCustomers error: ' . $e->getMessage());
        error_log('getCustomers trace: ' . $e->getTraceAsString());
        send_error_response('Failed to get customers: ' . $e->getMessage() . ' | SQL Error: ' . ($conn->error ?? 'N/A'));
    }
}

function getCustomerDetail($conn, $input) {
    try {
        $accountId = $input['accountId'] ?? '';
        
        if (empty($accountId)) {
            send_error_response('Account ID is required');
        }
        
        // client 테이블 컬럼 확인
        $clientColumnCheck = $conn->query("SHOW COLUMNS FROM client");
        $clientColumns = [];
        while ($col = $clientColumnCheck->fetch_assoc()) {
            $clientColumns[] = strtolower($col['Field']);
        }
        
        // passportIssueDate 컬럼이 없으면 생성
        if (!in_array('passportissuedate', $clientColumns)) {
            try {
                $conn->query("ALTER TABLE client ADD COLUMN passportIssueDate DATE NULL");
                $clientColumns[] = 'passportissuedate';
                error_log("Created passportIssueDate column in client table");
            } catch (Exception $e) {
                error_log("Failed to create passportIssueDate column: " . $e->getMessage());
            }
        }
        
        // 고객 정보 조회 (더 많은 정보 포함)
        $sql = "
            SELECT
                c.*,
                a.emailAddress as accountEmail,
                a.username,
                a.accountType,
                a.createdAt as accountCreatedAt,
                comp.companyName,
                br.branchName,
                ag.fName as agentFName,
                ag.lName as agentLName
            FROM client c
            LEFT JOIN accounts a ON c.accountId = a.accountId
            LEFT JOIN company comp ON c.companyId = comp.companyId
            LEFT JOIN branch br ON comp.branchId = br.branchId
            LEFT JOIN agent ag ON c.agentId = ag.agentId
            WHERE c.accountId = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // 고객이 없어도 빈 객체 반환 (에러 처리하지 않음)
        $customer = [];
        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            
            // passportIssueDate 필드명 정규화 (여러 가능한 필드명 확인)
            if (empty($customer['passportIssueDate']) && isset($customer['passportIssuedDate'])) {
                $customer['passportIssueDate'] = $customer['passportIssuedDate'];
            }
            if (empty($customer['passportIssueDate']) && isset($customer['passportIssue'])) {
                $customer['passportIssueDate'] = $customer['passportIssue'];
            }
        }
        
        // 디버깅: profileImage 확인
        error_log("getCustomerDetail - accountId: $accountId, profileImage: " . ($customer['profileImage'] ?? 'NULL'));
        
        // profileImage 경로를 전체 URL로 변환 (있는 경우)
        if (!empty($customer['profileImage'])) {
            // 상대 경로인 경우 전체 URL로 변환
            if (!preg_match('/^(https?:\/\/|data:)/', $customer['profileImage'])) {
                // smart-travel2 제거 및 경로 정규화
                $profileImage = $customer['profileImage'];
                
                // smart-travel2 제거
                $profileImage = str_replace('/smart-travel2/', '/', $profileImage);
                $profileImage = str_replace('smart-travel2/', '', $profileImage);
                
                // uploads/uploads 중복 제거
                $profileImage = preg_replace('#/uploads/uploads/#', '/uploads/', $profileImage);
                
                // /로 시작하지 않으면 추가
                if (strpos($profileImage, '/') !== 0) {
                    $profileImage = '/' . $profileImage;
                }
                
                $customer['profileImage'] = $profileImage;
            }
        }
        
        // 예약 내역 조회 (더 자세한 정보)
        $bookingsSql = "
            SELECT 
                b.bookingId,
                b.packageId,
                p.packageName,
                b.departureDate,
                b.bookingStatus,
                b.totalAmount,
                (b.adults + b.children + b.infants) as numPeople,
                b.createdAt as bookingDate
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            WHERE b.accountId = ?
            ORDER BY b.createdAt DESC
            LIMIT 20
        ";
        $bookingsStmt = $conn->prepare($bookingsSql);
        $bookingsStmt->bind_param("i", $accountId);
        $bookingsStmt->execute();
        $bookingsResult = $bookingsStmt->get_result();
        
        $bookings = [];
        while ($booking = $bookingsResult->fetch_assoc()) {
            $bookings[] = $booking;
        }
        
        // 문의 내역 조회
        // 컬럼명 확인 (category 또는 inquiryType, subject 또는 inquiryTitle)
        $columnCheck = $conn->query("SHOW COLUMNS FROM inquiries");
        $inquiryColumns = [];
        while ($col = $columnCheck->fetch_assoc()) {
            $inquiryColumns[] = strtolower($col['Field']);
        }
        
        $categoryColumn = in_array('category', $inquiryColumns) ? 'category' : 
                         (in_array('inquirytype', $inquiryColumns) ? 'inquiryType' : 'category');
        $titleColumn = in_array('subject', $inquiryColumns) ? 'subject' : 
                      (in_array('inquirytitle', $inquiryColumns) ? 'inquiryTitle' : 'subject');
        
        $inquiriesSql = "
            SELECT 
                i.inquiryId,
                i.$categoryColumn as inquiryType,
                i.$titleColumn as inquiryTitle,
                i.status,
                i.createdAt,
                CASE 
                    WHEN EXISTS (SELECT 1 FROM inquiry_replies ir WHERE ir.inquiryId = i.inquiryId) THEN '답변완료'
                    ELSE '미답변'
                END as replyStatus
            FROM inquiries i
            WHERE i.accountId = ?
            ORDER BY i.createdAt DESC
            LIMIT 20
        ";
        $inquiriesStmt = $conn->prepare($inquiriesSql);
        $inquiriesStmt->bind_param("i", $accountId);
        $inquiriesStmt->execute();
        $inquiriesResult = $inquiriesStmt->get_result();
        
        $inquiries = [];
        while ($inquiry = $inquiriesResult->fetch_assoc()) {
            $inquiries[] = $inquiry;
        }
        
        send_success_response([
            'customer' => $customer,
            'bookings' => $bookings,
            'inquiries' => $inquiries
        ]);
    } catch (Exception $e) {
        send_error_response('Failed to get customer detail: ' . $e->getMessage());
    }
}

function createCustomerRecord($conn, $input, $files = null) {
    $files = $files ?? $_FILES;
    try {
        // 필수 필드 검증
        $requiredFields = ['firstName', 'lastName', 'email', 'phone', 'agentId'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                send_error_response("Field '$field' is required");
            }
        }
        
        // 이메일 중복 확인
        $emailCheckSql = "SELECT accountId FROM accounts WHERE emailAddress = ?";
        $emailCheckStmt = $conn->prepare($emailCheckSql);
        $emailCheckStmt->bind_param("s", $input['email']);
        $emailCheckStmt->execute();
        $emailResult = $emailCheckStmt->get_result();
        
        if ($emailResult->num_rows > 0) {
            throw new Exception('Email already exists');
        }
        
        // 여권 사진 업로드 처리
        $passportPhotoPath = null;
        error_log("createCustomer - Checking for passportPhoto file upload");
        error_log("createCustomer - _FILES: " . print_r($files, true));
        
        if (isset($files['passportPhoto']) && $files['passportPhoto']['error'] === UPLOAD_ERR_OK) {
            error_log("createCustomer - passportPhoto file found, error code: " . $files['passportPhoto']['error']);
            // 실제 저장 경로: /var/www/html/uploads/passports/
            // __DIR__ = /var/www/html/admin_v2/backend/api/
            // ../../../ = /var/www/html/
            $uploadDir = __DIR__ . '/../../../uploads/passports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                error_log("createCustomer - Created upload directory: " . $uploadDir);
            }
            error_log("createCustomer - Upload directory resolved: " . realpath($uploadDir));
            
            $fileExtension = pathinfo($files['passportPhoto']['name'], PATHINFO_EXTENSION);
            $fileName = 'passport_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            error_log("createCustomer - Attempting to move file to: " . $uploadPath);
            if (move_uploaded_file($files['passportPhoto']['tmp_name'], $uploadPath)) {
                // 웹에서 접근 가능한 경로: /uploads/passports/
                $passportPhotoPath = 'uploads/passports/' . $fileName;
                error_log("createCustomer - File uploaded successfully: " . $passportPhotoPath);
            } else {
                error_log("createCustomer - Failed to move uploaded file");
            }
        } else {
            if (isset($files['passportPhoto'])) {
                error_log("createCustomer - passportPhoto file error code: " . $files['passportPhoto']['error']);
            } else {
                error_log("createCustomer - passportPhoto not found in _FILES");
            }
        }
        
        $conn->begin_transaction();
        
        try {
            // accounts 테이블 컬럼 확인
            $accountColumns = [];
            $accountColumnResult = $conn->query("SHOW COLUMNS FROM accounts");
            while ($row = $accountColumnResult->fetch_assoc()) {
                $accountColumns[] = $row['Field'];
            }
            
            // accounts 테이블에 먼저 생성 (password 또는 passwordHash 컬럼 확인)
            $columnCheck = $conn->query("SHOW COLUMNS FROM accounts LIKE 'password'");
            $passwordColumn = ($columnCheck->num_rows > 0) ? 'password' : 'passwordHash';

            // username 필드가 있으면 이메일을 username으로 사용하거나 firstName + lastName 조합 사용
            $accountFields = ['emailAddress', $passwordColumn, 'accountType'];
            $accountValues = [$input['email'], password_hash($input['password'] ?? 'temp123', PASSWORD_DEFAULT), 'guest'];
            $accountTypes = 'sss';

            if (in_array('username', $accountColumns)) {
                // username이 필수이면 이메일을 username으로 사용 (또는 firstName + lastName 조합)
                $username = $input['email']; // 이메일을 username으로 사용
                // 또는: $username = strtolower($input['firstName'] . $input['lastName']);
                $accountFields[] = 'username';
                $accountValues[] = $username;
                $accountTypes .= 's';
            }

            // agentId 추가 (agent가 생성한 고객인 경우)
            if (isset($input['agentId']) && in_array('agentId', $accountColumns)) {
                $accountFields[] = 'agentId';
                $accountValues[] = $input['agentId'];
                $accountTypes .= 'i';
            }
            
            // createdAt은 DEFAULT CURRENT_TIMESTAMP가 설정되어 있으면 자동으로 값이 들어가므로 명시적으로 추가하지 않음
            // 만약 명시적으로 추가해야 한다면 아래 주석을 해제
            /*
            if (in_array('createdAt', $accountColumns)) {
                $accountFields[] = 'createdAt';
                $accountValues[] = date('Y-m-d H:i:s');
                $accountTypes .= 's';
            }
            */
            
            $accountPlaceholders = str_repeat('?,', count($accountFields) - 1) . '?';
            $accountSql = "INSERT INTO accounts (" . implode(', ', $accountFields) . ") VALUES ($accountPlaceholders)";
            
            $accountStmt = $conn->prepare($accountSql);
            $accountStmt->bind_param($accountTypes, ...$accountValues);
            $accountStmt->execute();
            $accountId = $conn->insert_id;
            
            // client 테이블 컬럼 확인 (countryCode, memo 등이 있는지 확인)
            $clientColumns = [];
            $columnResult = $conn->query("SHOW COLUMNS FROM client");
            while ($row = $columnResult->fetch_assoc()) {
                $clientColumns[] = strtolower($row['Field']); // 대소문자 구분 없이 저장
            }
            
            // clientId 생성 (필수 필드 - DB 스키마에 UNIQUE NOT NULL로 정의됨)
            $clientId = null;
            if (in_array('clientid', $clientColumns)) {
                // clientId 형식: CLI + 6자리 accountId (앞에 0 패딩)
                $clientId = 'CLI' . str_pad($accountId, 6, '0', STR_PAD_LEFT);
            }
            
            // client 테이블에 생성
            $clientFields = [];
            $clientValues = [];
            $clientTypes = '';
            
            // 필수 필드: clientId (컬럼이 존재하면 항상 추가)
            if (in_array('clientid', $clientColumns)) {
                if (empty($clientId)) {
                    throw new Exception('Failed to generate clientId');
                }
                $clientFields[] = 'clientId';
                $clientValues[] = $clientId;
                $clientTypes .= 's';
            }
            
            // 필수 필드: accountId
            $clientFields[] = 'accountId';
            $clientValues[] = $accountId;
            $clientTypes .= 'i';
            
            // 필수 필드: fName (여행자 이름이 있으면 우선 사용, 없으면 기본 이름 사용)
            $clientFields[] = 'fName';
            $clientValues[] = !empty($input['travelerFirstName']) ? $input['travelerFirstName'] : $input['firstName'];
            $clientTypes .= 's';
            
            // 필수 필드: lName (여행자 성이 있으면 우선 사용, 없으면 기본 성 사용)
            $clientFields[] = 'lName';
            $clientValues[] = !empty($input['travelerLastName']) ? $input['travelerLastName'] : $input['lastName'];
            $clientTypes .= 's';
            
            // 필수 필드: contactNo
            $clientFields[] = 'contactNo';
            $clientValues[] = $input['phone'];
            $clientTypes .= 's';
            
            // companyId 처리 (NULL 가능이지만 기본값 설정)
            if (in_array('companyid', $clientColumns)) {
                $clientFields[] = 'companyId';
                $clientValues[] = $input['companyId'] ?? 1; // 기본값 1 또는 NULL
                $clientTypes .= 'i';
            }
            
            // emailAddress 컬럼이 있으면 추가
            if (in_array('emailaddress', $clientColumns)) {
                $clientFields[] = 'emailAddress';
                $clientValues[] = $input['email'];
                $clientTypes .= 's';
            }
            
            // countryCode 컬럼이 있으면 추가 (기본값 처리)
            if (in_array('countrycode', $clientColumns)) {
                $clientFields[] = 'countryCode';
                $clientValues[] = $input['countryCode'] ?? '+82';
                $clientTypes .= 's';
            }
            
            // clientType 컬럼이 있으면 추가 (기본값 'Retailer')
            if (in_array('clienttype', $clientColumns)) {
                $clientFields[] = 'clientType';
                $clientValues[] = 'Retailer';
                $clientTypes .= 's';
            }
            
            // clientRole 컬럼이 있으면 추가 (기본값 'Sub-Agent')
            if (in_array('clientrole', $clientColumns)) {
                $clientFields[] = 'clientRole';
                $clientValues[] = 'Sub-Agent';
                $clientTypes .= 's';
            }

            // agentId 추가 (agent가 생성한 고객인 경우) - client 테이블에도
            if (isset($input['agentId']) && in_array('agentid', $clientColumns)) {
                $clientFields[] = 'agentId';
                $clientValues[] = $input['agentId'];
                $clientTypes .= 'i';
            }

            // dateOfBirth 컬럼이 있고 여행자 생년월일이 있으면 추가
            if (in_array('dateofbirth', $clientColumns) && !empty($input['travelerBirth'])) {
                $clientFields[] = 'dateOfBirth';
                $clientValues[] = $input['travelerBirth'];
                $clientTypes .= 's';
            }
            
            // gender 컬럼이 있고 여행자 성별이 있으면 추가
            if (in_array('gender', $clientColumns) && !empty($input['travelerGender'])) {
                $clientFields[] = 'gender';
                $clientValues[] = $input['travelerGender'];
                $clientTypes .= 's';
            }
            
            // nationality 컬럼이 있고 여행자 출신국가가 있으면 추가
            if (in_array('nationality', $clientColumns) && !empty($input['travelerNationality'])) {
                $clientFields[] = 'nationality';
                $clientValues[] = $input['travelerNationality'];
                $clientTypes .= 's';
            }
            
            // passportNumber 컬럼이 있고 여행자 여권번호가 있으면 추가
            if (in_array('passportnumber', $clientColumns) && !empty($input['travelerPassportNo'])) {
                $clientFields[] = 'passportNumber';
                $clientValues[] = $input['travelerPassportNo'];
                $clientTypes .= 's';
            }
            
            // passportIssueDate 컬럼이 없으면 생성
            if (!in_array('passportissuedate', $clientColumns)) {
                try {
                    $conn->query("ALTER TABLE client ADD COLUMN passportIssueDate DATE NULL");
                    $clientColumns[] = 'passportissuedate';
                    error_log("Created passportIssueDate column in client table");
                } catch (Exception $e) {
                    error_log("Failed to create passportIssueDate column: " . $e->getMessage());
                }
            }
            // passportIssueDate 컬럼이 있고 여행자 여권 발행일이 있으면 추가
            if (in_array('passportissuedate', $clientColumns) && isset($input['travelerPassportIssue']) && $input['travelerPassportIssue'] !== '') {
                $issueDate = $input['travelerPassportIssue'];
                // 날짜 형식 변환 (YYYYMMDD -> YYYY-MM-DD 또는 이미 YYYY-MM-DD 형식인 경우 그대로 사용)
                if (strlen($issueDate) === 8 && is_numeric($issueDate)) {
                    // YYYYMMDD 형식인 경우
                    $issueDate = substr($issueDate, 0, 4) . '-' . substr($issueDate, 4, 2) . '-' . substr($issueDate, 6, 2);
                } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $issueDate)) {
                    // 유효한 날짜 형식이 아니면 로그만 남기고 저장하지 않음
                    error_log("Invalid passportIssueDate format: " . $issueDate);
                    $issueDate = null;
                }
                
                if ($issueDate) {
                    $clientFields[] = 'passportIssueDate';
                    $clientValues[] = $issueDate;
                    $clientTypes .= 's';
                    error_log("Adding passportIssueDate to client: " . $issueDate);
                }
            }
            
            // passportExpiry 컬럼이 있고 여행자 여권 만료일이 있으면 추가
            if (in_array('passportexpiry', $clientColumns) && !empty($input['travelerPassportExpire'])) {
                $clientFields[] = 'passportExpiry';
                $clientValues[] = $input['travelerPassportExpire'];
                $clientTypes .= 's';
            }
            
            // title 컬럼이 있고 여행자 호칭이 있으면 추가
            if (!in_array('title', $clientColumns) && !empty($input['travelerTitle'])) {
                // 컬럼이 없으면 생성
                try {
                    $conn->query("ALTER TABLE client ADD COLUMN title VARCHAR(10) NULL");
                    $clientColumns[] = 'title';
                    error_log("Created title column in client table");
                } catch (Exception $e) {
                    error_log("Failed to create title column: " . $e->getMessage());
                }
            }
            if (in_array('title', $clientColumns) && !empty($input['travelerTitle'])) {
                $clientFields[] = 'title';
                $clientValues[] = $input['travelerTitle'];
                $clientTypes .= 's';
            }
            
            // memo 컬럼이 있고 메모가 있으면 추가
            if (!in_array('memo', $clientColumns) && !empty($input['memo'])) {
                // 컬럼이 없으면 생성
                try {
                    $conn->query("ALTER TABLE client ADD COLUMN memo TEXT NULL");
                    $clientColumns[] = 'memo';
                    error_log("Created memo column in client table");
                } catch (Exception $e) {
                    error_log("Failed to create memo column: " . $e->getMessage());
                }
            }
            if (in_array('memo', $clientColumns) && isset($input['memo'])) {
                $clientFields[] = 'memo';
                $clientValues[] = $input['memo'] ?? '';
                $clientTypes .= 's';
            }
            
            // profileImage 컬럼이 없으면 생성
            if (!in_array('profileimage', $clientColumns)) {
                // 컬럼이 없으면 생성 (사진이 있든 없든 생성)
                try {
                    $conn->query("ALTER TABLE client ADD COLUMN profileImage VARCHAR(255) NULL");
                    $clientColumns[] = 'profileimage';
                    error_log("Created profileImage column in client table");
                } catch (Exception $e) {
                    error_log("Failed to create profileImage column: " . $e->getMessage());
                }
            }
            // profileImage 컬럼이 있고 여권 사진이 있으면 추가
            if (in_array('profileimage', $clientColumns) && $passportPhotoPath) {
                $clientFields[] = 'profileImage';
                $clientValues[] = $passportPhotoPath;
                $clientTypes .= 's';
                
                error_log("Creating customer with profileImage: " . $passportPhotoPath);
            } elseif ($passportPhotoPath) {
                // profileImage 컬럼이 없는데 사진이 업로드된 경우 로그
                error_log("WARNING: profileImage column not found but passport photo was uploaded: " . $passportPhotoPath);
            } else {
                // profileImage 컬럼이 있는데 사진이 없는 경우
                error_log("INFO: profileImage column exists but no passport photo uploaded. passportPhotoPath: " . ($passportPhotoPath ?? 'NULL'));
            }
            
            // createdAt 컬럼이 있으면 추가 (DEFAULT CURRENT_TIMESTAMP가 있으면 자동으로 값이 들어가므로 명시적으로 추가하지 않음)
            // 만약 명시적으로 추가해야 한다면 아래 주석을 해제
            /*
            if (in_array('createdAt', $clientColumns)) {
                $clientFields[] = 'createdAt';
                $clientValues[] = date('Y-m-d H:i:s');
                $clientTypes .= 's';
            }
            */
            
            $placeholders = str_repeat('?,', count($clientFields) - 1) . '?';
            $clientSql = "INSERT INTO client (" . implode(', ', $clientFields) . ") VALUES ($placeholders)";
            
            $clientStmt = $conn->prepare($clientSql);
            if (!$clientStmt) {
                throw new Exception('Failed to prepare client SQL: ' . $conn->error . ' | SQL: ' . $clientSql);
            }
            
            $clientStmt->bind_param($clientTypes, ...$clientValues);
            if (!$clientStmt->execute()) {
                throw new Exception('Failed to execute client insert: ' . $clientStmt->error . ' | SQL: ' . $clientSql . ' | Values: ' . json_encode($clientValues));
            }
            $customerId = $conn->insert_id;
            
            // 메모가 있으면 별도 테이블에 저장하거나 notes 필드에 저장 (DB 구조에 따라)
            // 현재는 client 테이블에 직접 저장하지 않고, 필요시 별도 테이블 사용 가능
            
            $conn->commit();
            
            return [
                'accountId' => $accountId,
                'customerId' => $customerId
            ];
            
        } catch (Exception $e) {
            $conn->rollback();
            // 업로드된 파일이 있으면 삭제
            // 기존 파일 삭제 (여러 경로 확인)
            if ($passportPhotoPath) {
                // __DIR__ = /var/www/html/admin_v2/backend/api/
                // ../../../ = /var/www/html/
                $oldFilePath1 = __DIR__ . '/../../../' . $passportPhotoPath;
                $oldFilePath2 = __DIR__ . '/../../../' . ltrim($passportPhotoPath, '/');
                if (file_exists($oldFilePath1)) {
                    unlink($oldFilePath1);
                } elseif (file_exists($oldFilePath2)) {
                    unlink($oldFilePath2);
                }
            }
            throw $e;
        }
        
    } catch (Exception $e) {
        throw $e;
    }
}

function createCustomer($conn, $input, $agentId) {
    try {
        // agentId를 input에 추가
        $input['agentId'] = $agentId;
        $result = createCustomerRecord($conn, $input, $_FILES);
        send_success_response($result, 'Customer created successfully');
    } catch (Exception $e) {
        send_error_response('Failed to create customer: ' . $e->getMessage());
    }
}

function updateCustomer($conn, $input) {
    try {
        $accountId = $input['accountId'] ?? '';
        
        if (empty($accountId)) {
            send_error_response('Account ID is required');
        }
        
        $conn->begin_transaction();
        
        // accounts 테이블 업데이트
        $accountUpdates = [];
        $accountParams = [];
        $accountTypes = '';
        
        // 이메일 업데이트
        if (isset($input['email'])) {
            $accountUpdates[] = "emailAddress = ?";
            $accountParams[] = $input['email'];
            $accountTypes .= 's';
        }
        
        // 비밀번호 업데이트 (resetPassword 액션일 때)
        if (isset($input['resetPassword']) && $input['resetPassword'] === true) {
            $columnCheck = $conn->query("SHOW COLUMNS FROM accounts LIKE 'password'");
            $passwordColumn = ($columnCheck->num_rows > 0) ? 'password' : 'passwordHash';
            $defaultPassword = password_hash('123456', PASSWORD_DEFAULT); // 기본 비밀번호
            $accountUpdates[] = "$passwordColumn = ?";
            $accountParams[] = $defaultPassword;
            $accountTypes .= 's';
        }
        
        if (!empty($accountUpdates)) {
            $accountParams[] = $accountId;
            $accountTypes .= 'i';
            $accountSql = "UPDATE accounts SET " . implode(', ', $accountUpdates) . " WHERE accountId = ?";
            $accountStmt = $conn->prepare($accountSql);
            $accountStmt->bind_param($accountTypes, ...$accountParams);
            $accountStmt->execute();
        }
        
        // client 테이블 업데이트
        $clientColumns = [];
        $columnResult = $conn->query("SHOW COLUMNS FROM client");
        while ($row = $columnResult->fetch_assoc()) {
            $clientColumns[] = strtolower($row['Field']);
        }
        
        $clientUpdates = [];
        $clientParams = [];
        $clientTypes = '';
        
        // 이름
        if (isset($input['firstName'])) {
            $clientUpdates[] = "fName = ?";
            $clientParams[] = $input['firstName'];
            $clientTypes .= 's';
        }
        
        if (isset($input['lastName'])) {
            $clientUpdates[] = "lName = ?";
            $clientParams[] = $input['lastName'];
            $clientTypes .= 's';
        }
        
        // 연락처
        if (isset($input['phone'])) {
            $clientUpdates[] = "contactNo = ?";
            $clientParams[] = $input['phone'];
            $clientTypes .= 's';
        }
        
        // 국가 코드
        if (isset($input['countryCode']) && in_array('countrycode', $clientColumns)) {
            $clientUpdates[] = "countryCode = ?";
            $clientParams[] = $input['countryCode'];
            $clientTypes .= 's';
        }
        
        // 메모 - 컬럼이 없으면 생성
        if (isset($input['memo']) && !in_array('memo', $clientColumns)) {
            try {
                $conn->query("ALTER TABLE client ADD COLUMN memo TEXT NULL");
                $clientColumns[] = 'memo';
                error_log("Created memo column in client table");
            } catch (Exception $e) {
                error_log("Failed to create memo column: " . $e->getMessage());
            }
        }
        if (isset($input['memo']) && in_array('memo', $clientColumns)) {
            $clientUpdates[] = "memo = ?";
            $clientParams[] = $input['memo'];
            $clientTypes .= 's';
        }
        
        // 여행자 정보 - title 컬럼이 없으면 생성
        if (isset($input['title']) && !in_array('title', $clientColumns)) {
            try {
                $conn->query("ALTER TABLE client ADD COLUMN title VARCHAR(10) NULL");
                $clientColumns[] = 'title';
                error_log("Created title column in client table");
            } catch (Exception $e) {
                error_log("Failed to create title column: " . $e->getMessage());
            }
        }
        if (isset($input['title']) && in_array('title', $clientColumns)) {
            $clientUpdates[] = "title = ?";
            $clientParams[] = $input['title'];
            $clientTypes .= 's';
        }
        
        if (isset($input['travelerGender']) && in_array('gender', $clientColumns)) {
            $clientUpdates[] = "gender = ?";
            $clientParams[] = $input['travelerGender'];
            $clientTypes .= 's';
        }
        
        if (isset($input['travelerBirth']) && in_array('dateofbirth', $clientColumns)) {
            // YYYYMMDD 형식을 YYYY-MM-DD로 변환
            $birthDate = $input['travelerBirth'];
            if (strlen($birthDate) === 8 && is_numeric($birthDate)) {
                $birthDate = substr($birthDate, 0, 4) . '-' . substr($birthDate, 4, 2) . '-' . substr($birthDate, 6, 2);
            }
            $clientUpdates[] = "dateOfBirth = ?";
            $clientParams[] = $birthDate;
            $clientTypes .= 's';
        }
        
        if (isset($input['travelerNationality']) && in_array('nationality', $clientColumns)) {
            $clientUpdates[] = "nationality = ?";
            $clientParams[] = $input['travelerNationality'];
            $clientTypes .= 's';
        }
        
        if (isset($input['travelerPassportNo']) && in_array('passportnumber', $clientColumns)) {
            $clientUpdates[] = "passportNumber = ?";
            $clientParams[] = $input['travelerPassportNo'];
            $clientTypes .= 's';
        }
        
        // passportIssueDate 컬럼이 없으면 생성
        if (isset($input['travelerPassportIssue']) && !in_array('passportissuedate', $clientColumns)) {
            try {
                $conn->query("ALTER TABLE client ADD COLUMN passportIssueDate DATE NULL");
                $clientColumns[] = 'passportissuedate';
                error_log("Created passportIssueDate column in client table");
            } catch (Exception $e) {
                error_log("Failed to create passportIssueDate column: " . $e->getMessage());
            }
        }
        if (isset($input['travelerPassportIssue']) && in_array('passportissuedate', $clientColumns)) {
            $issueDate = $input['travelerPassportIssue'];
            if (strlen($issueDate) === 8 && is_numeric($issueDate)) {
                $issueDate = substr($issueDate, 0, 4) . '-' . substr($issueDate, 4, 2) . '-' . substr($issueDate, 6, 2);
            }
            $clientUpdates[] = "passportIssueDate = ?";
            $clientParams[] = $issueDate;
            $clientTypes .= 's';
        }
        
        if (isset($input['travelerPassportExpire']) && in_array('passportexpiry', $clientColumns)) {
            $expiryDate = $input['travelerPassportExpire'];
            if (strlen($expiryDate) === 8 && is_numeric($expiryDate)) {
                $expiryDate = substr($expiryDate, 0, 4) . '-' . substr($expiryDate, 4, 2) . '-' . substr($expiryDate, 6, 2);
            }
            $clientUpdates[] = "passportExpiry = ?";
            $clientParams[] = $expiryDate;
            $clientTypes .= 's';
        }
        
        // 여권 사진 업로드 처리
        $passportPhotoPath = null;
        if (isset($_FILES['passportPhoto']) && $_FILES['passportPhoto']['error'] === UPLOAD_ERR_OK) {
            // 실제 저장 경로: /var/www/html/uploads/passports/
            // __DIR__ = /var/www/html/admin_v2/backend/api/
            // ../../../ = /var/www/html/
            $uploadDir = __DIR__ . '/../../../uploads/passports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['passportPhoto']['name'], PATHINFO_EXTENSION);
            $fileName = 'passport_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['passportPhoto']['tmp_name'], $uploadPath)) {
                // 웹에서 접근 가능한 경로
                $passportPhotoPath = 'uploads/passports/' . $fileName;
                
                // profileImage 컬럼이 없으면 생성
                if (!in_array('profileimage', $clientColumns)) {
                    try {
                        $conn->query("ALTER TABLE client ADD COLUMN profileImage VARCHAR(255) NULL");
                        $clientColumns[] = 'profileimage';
                        error_log("Created profileImage column in client table");
                    } catch (Exception $e) {
                        error_log("Failed to create profileImage column: " . $e->getMessage());
                    }
                }
                
                // 기존 여권 사진 삭제
                $oldPhotoSql = "SELECT profileImage FROM client WHERE accountId = ?";
                $oldPhotoStmt = $conn->prepare($oldPhotoSql);
                $oldPhotoStmt->bind_param("i", $accountId);
                $oldPhotoStmt->execute();
                $oldPhotoResult = $oldPhotoStmt->get_result();
                if ($oldPhotoRow = $oldPhotoResult->fetch_assoc()) {
                    // 기존 파일 삭제 (여러 경로 확인)
                    $oldImagePath = $oldPhotoRow['profileImage'];
                    if (!empty($oldImagePath)) {
                        // smart-travel2 제거 및 경로 정규화
                        $oldImagePathClean = str_replace('/smart-travel2/', '/', $oldImagePath);
                        $oldImagePathClean = str_replace('smart-travel2/', '', $oldImagePathClean);
                        $oldImagePathClean = preg_replace('#/uploads/uploads/#', '/uploads/', $oldImagePathClean);
                        $oldImagePathClean = ltrim($oldImagePathClean, '/');
                        
                        $oldFilePath1 = __DIR__ . '/../../../' . $oldImagePathClean;
                        $oldFilePath2 = __DIR__ . '/../../../../' . $oldImagePathClean;
                        
                        if (file_exists($oldFilePath1)) {
                            unlink($oldFilePath1);
                        } elseif (file_exists($oldFilePath2)) {
                            unlink($oldFilePath2);
                        }
                    }
                }
                
                if (in_array('profileimage', $clientColumns)) {
                    $clientUpdates[] = "profileImage = ?";
                    $clientParams[] = $passportPhotoPath;
                    $clientTypes .= 's';
                }
            }
        }
        
        if (!empty($clientUpdates)) {
            $clientParams[] = $accountId;
            $clientTypes .= 'i';
            $clientSql = "UPDATE client SET " . implode(', ', $clientUpdates) . " WHERE accountId = ?";
            $clientStmt = $conn->prepare($clientSql);
            if (!$clientStmt) {
                throw new Exception('Failed to prepare client SQL: ' . $conn->error);
            }
            $clientStmt->bind_param($clientTypes, ...$clientParams);
            if (!$clientStmt->execute()) {
                throw new Exception('Failed to execute client update: ' . $clientStmt->error);
            }
        }
        
        $conn->commit();
        
        send_success_response([], 'Customer updated successfully');
        
    } catch (Exception $e) {
        $conn->rollback();
        // 기존 파일 삭제 (여러 경로 확인)
        if (isset($passportPhotoPath) && $passportPhotoPath) {
            $oldFilePath1 = __DIR__ . '/../../../../' . $passportPhotoPath;
            $oldFilePath2 = __DIR__ . '/../../../../' . ltrim($passportPhotoPath, '/');
            if (file_exists($oldFilePath1)) {
                unlink($oldFilePath1);
            } elseif (file_exists($oldFilePath2)) {
                unlink($oldFilePath2);
            }
        }
        send_error_response('Failed to update customer: ' . $e->getMessage());
    }
}

function resetPassword($conn, $input) {
    try {
        $accountId = $input['accountId'] ?? '';
        
        if (empty($accountId)) {
            send_error_response('Account ID is required');
        }
        
        // 비밀번호 컬럼 확인
        $columnCheck = $conn->query("SHOW COLUMNS FROM accounts LIKE 'password'");
        $passwordColumn = ($columnCheck->num_rows > 0) ? 'password' : 'passwordHash';
        
        // 기본 비밀번호 생성 (123456)
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
        
        $sql = "UPDATE accounts SET $passwordColumn = ? WHERE accountId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $defaultPassword, $accountId);
        $stmt->execute();
        
        send_success_response([], 'Password reset successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to reset password: ' . $e->getMessage());
    }
}

// ========== 문의 관련 함수들 ==========

function getInquiries($conn, $input) {
    try {
        $page = isset($input['page']) ? (int)$input['page'] : 1;
        $limit = isset($input['limit']) ? (int)$input['limit'] : 20;
        $offset = ($page - 1) * $limit;
        
        $where = [];
        $params = [];
        $types = '';
        
        if (!empty($input['status'])) {
            $where[] = "i.status = ?";
            $params[] = $input['status'];
            $types .= 's';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $countSql = "SELECT COUNT(*) as total FROM inquiries i $whereClause";
        
        if (!empty($params)) {
            $countStmt = $conn->prepare($countSql);
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $totalResult = $countStmt->get_result();
        } else {
            $totalResult = $conn->query($countSql);
        }
        $total = $totalResult->fetch_assoc()['total'];
        
        // 컬럼명 확인 (inquiryTitle 또는 subject)
        $columnCheck = $conn->query("SHOW COLUMNS FROM inquiries LIKE 'subject'");
        $useSubjectColumn = ($columnCheck->num_rows > 0);
        
        if ($useSubjectColumn) {
            $sql = "
                SELECT 
                    i.inquiryId,
                    i.subject as inquiryTitle,
                    i.status,
                    i.createdAt,
                    c.fName,
                    c.lName
                FROM inquiries i
                LEFT JOIN client c ON i.accountId = c.accountId
                $whereClause
                ORDER BY i.createdAt DESC
                LIMIT ? OFFSET ?
            ";
        } else {
            $sql = "
                SELECT 
                    i.inquiryId,
                    i.inquiryTitle,
                    i.status,
                    i.createdAt,
                    c.fName,
                    c.lName
                FROM inquiries i
                LEFT JOIN client c ON i.accountId = c.accountId
                $whereClause
                ORDER BY i.createdAt DESC
                LIMIT ? OFFSET ?
            ";
        }
        
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $inquiries = [];
        $rowNum = $total - $offset;
        while ($row = $result->fetch_assoc()) {
            $inquiries[] = [
                'rowNum' => $rowNum--,
                'inquiryId' => $row['inquiryId'],
                'inquiryTitle' => $row['inquiryTitle'],
                'customerName' => trim(($row['fName'] ?? '') . ' ' . ($row['lName'] ?? '')),
                'status' => $row['status'],
                'statusLabel' => $row['status'] === 'pending' ? '미답변' : ($row['status'] === 'completed' ? '답변 완료' : '처리중'),
                'createdAt' => $row['createdAt']
            ];
        }
        
        send_success_response([
            'inquiries' => $inquiries,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($total / $limit)
            ]
        ]);
    } catch (Exception $e) {
        send_error_response('Failed to get inquiries: ' . $e->getMessage());
    }
}

function getInquiryDetail($conn, $input) {
    try {
        $inquiryId = $input['inquiryId'] ?? '';
        
        if (empty($inquiryId)) {
            send_error_response('Inquiry ID is required');
        }
        
        // inquiries 테이블 컬럼 확인
        $inquiryColumns = [];
        $columnResult = $conn->query("SHOW COLUMNS FROM inquiries");
        if ($columnResult) {
            while ($col = $columnResult->fetch_assoc()) {
                $inquiryColumns[] = strtolower($col['Field']);
            }
        }
        
        $useSubjectColumn = in_array('subject', $inquiryColumns);
        $titleField = $useSubjectColumn ? 'subject' : 'inquiryTitle';
        $contentField = $useSubjectColumn ? 'content' : 'inquiryContent';
        
        // SELECT 절 동적 구성
        $selectFields = ['i.*'];
        
        // 별칭 추가 (일관성을 위해)
        if ($useSubjectColumn) {
            $selectFields[] = 'i.subject as inquiryTitle';
            $selectFields[] = 'i.content as inquiryContent';
        }
        
        // replyContent 컬럼이 있는지 확인
        $hasReplyContent = in_array('replycontent', $inquiryColumns);
        if ($hasReplyContent) {
            $selectFields[] = 'i.replyContent';
        }
        
            $sql = "
                SELECT 
                " . implode(', ', $selectFields) . ",
                    c.fName,
                    c.lName,
                    c.emailAddress,
                    c.contactNo
                FROM inquiries i
                LEFT JOIN client c ON i.accountId = c.accountId
                WHERE i.inquiryId = ?
            ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $inquiryId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            send_error_response('Inquiry not found', 404);
        }
        
        $inquiry = $result->fetch_assoc();
        
        // inquiryTitle, inquiryContent 필드가 없으면 subject, content에서 매핑
        if (!isset($inquiry['inquiryTitle']) && isset($inquiry['subject'])) {
            $inquiry['inquiryTitle'] = $inquiry['subject'];
        }
        if (!isset($inquiry['inquiryContent']) && isset($inquiry['content'])) {
            $inquiry['inquiryContent'] = $inquiry['content'];
        }
        
        // replyContent가 없으면 inquiry_replies 테이블에서 가져오기
        if (!$hasReplyContent || empty($inquiry['replyContent'])) {
            $replyCheck = $conn->query("SHOW TABLES LIKE 'inquiry_replies'");
            if ($replyCheck && $replyCheck->num_rows > 0) {
                // inquiry_replies 테이블 컬럼 확인
                $replyColumns = [];
                $replyColumnResult = $conn->query("SHOW COLUMNS FROM inquiry_replies");
                if ($replyColumnResult) {
                    while ($col = $replyColumnResult->fetch_assoc()) {
                        $replyColumns[] = strtolower($col['Field']);
                    }
                }
                
                $replyContentField = in_array('replycontent', $replyColumns) ? 'replyContent' : (in_array('content', $replyColumns) ? 'content' : 'message');
                $replyIdField = in_array('replyid', $replyColumns) ? 'replyId' : 'id';
                
                $replySql = "
                    SELECT $replyContentField as replyContent, createdAt as repliedAt
                    FROM inquiry_replies
                    WHERE inquiryId = ?
                    ORDER BY createdAt DESC
                    LIMIT 1
                ";
                $replyStmt = $conn->prepare($replySql);
                $replyStmt->bind_param("i", $inquiryId);
                $replyStmt->execute();
                $replyResult = $replyStmt->get_result();
                
                if ($replyResult && $replyResult->num_rows > 0) {
                    $reply = $replyResult->fetch_assoc();
                    $inquiry['replyContent'] = $reply['replyContent'] ?? '';
                    $inquiry['repliedAt'] = $reply['repliedAt'] ?? null;
                }
            }
        }
        
        // 첨부파일 조회 (inquiry_attachments 테이블이 있는 경우)
        $attachmentCheck = $conn->query("SHOW TABLES LIKE 'inquiry_attachments'");
        if ($attachmentCheck && $attachmentCheck->num_rows > 0) {
            $attachmentSql = "
                SELECT fileName, filePath, fileSize, fileType
                FROM inquiry_attachments
                WHERE inquiryId = ?
            ";
            $attachmentStmt = $conn->prepare($attachmentSql);
            $attachmentStmt->bind_param("i", $inquiryId);
            $attachmentStmt->execute();
            $attachmentResult = $attachmentStmt->get_result();
            
            $attachments = [];
            while ($attachment = $attachmentResult->fetch_assoc()) {
                $attachments[] = $attachment;
            }
            $inquiry['attachments'] = $attachments;
        }
        
        send_success_response(['inquiry' => $inquiry]);
    } catch (Exception $e) {
        send_error_response('Failed to get inquiry detail: ' . $e->getMessage());
    }
}

function createInquiry($conn, $input) {
    try {
        $requiredFields = ['inquiryTitle', 'inquiryContent'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                send_error_response("Field '$field' is required");
            }
        }
        
        // inquiries 테이블 컬럼명 확인 (inquiryTitle/inquiryContent 또는 subject/content)
        $tableCheck = $conn->query("SHOW TABLES LIKE 'inquiries'");
        if ($tableCheck->num_rows === 0) {
            send_error_response('Inquiries table does not exist');
        }
        
        // 모든 컬럼 확인
        $inquiryColumns = [];
        $columnResult = $conn->query("SHOW COLUMNS FROM inquiries");
        if ($columnResult) {
            while ($col = $columnResult->fetch_assoc()) {
                $inquiryColumns[] = strtolower($col['Field']);
            }
        }
        
        $useSubjectColumn = in_array('subject', $inquiryColumns);
        $useCategoryColumn = in_array('category', $inquiryColumns);
        $useInquiryTypeColumn = in_array('inquirytype', $inquiryColumns);
        $useInquiryNoColumn = in_array('inquiryno', $inquiryColumns);
        
        // 필드명 결정
        $titleField = $useSubjectColumn ? 'subject' : 'inquiryTitle';
        $contentField = $useSubjectColumn ? 'content' : 'inquiryContent';
        
        // INSERT 쿼리 구성
        $fields = [];
        $placeholders = [];
        $types = '';
        $params = [];
        
        // accountId 설정 (개발용: 로그인 없이도 동작)
        $accountId = $input['accountId'] ?? null;
        if (empty($accountId)) {
            // 세션 시작 확인
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            // 세션에서 accountId 가져오기
            $accountId = $_SESSION['accountId'] ?? null;
        }
        
        // accountId가 NULL을 허용하는지 확인
        $accountIdNullable = false;
        $accountIdColumnResult = $conn->query("SHOW COLUMNS FROM inquiries WHERE Field = 'accountId'");
        if ($accountIdColumnResult && $accountIdColumnResult->num_rows > 0) {
            $accountIdColumn = $accountIdColumnResult->fetch_assoc();
            $accountIdNullable = ($accountIdColumn['Null'] === 'YES');
        }
        
        // accountId 검증 및 설정 (개발용: 없으면 임의로 생성 또는 기존 계정 사용)
        if (empty($accountId) && !$accountIdNullable) {
            // 1. agent 계정 찾기
            $agentAccountResult = $conn->query("SELECT accountId FROM accounts WHERE accountType = 'agent' LIMIT 1");
            if ($agentAccountResult && $agentAccountResult->num_rows > 0) {
                $agentAccount = $agentAccountResult->fetch_assoc();
                $accountId = $agentAccount['accountId'];
        } else {
                // 2. admin 계정 찾기
                $adminAccountResult = $conn->query("SELECT accountId FROM accounts WHERE accountType = 'admin' LIMIT 1");
                if ($adminAccountResult && $adminAccountResult->num_rows > 0) {
                    $adminAccount = $adminAccountResult->fetch_assoc();
                    $accountId = $adminAccount['accountId'];
                } else {
                    // 3. 아무 계정이나 찾기
                    $anyAccountResult = $conn->query("SELECT accountId FROM accounts LIMIT 1");
                    if ($anyAccountResult && $anyAccountResult->num_rows > 0) {
                        $anyAccount = $anyAccountResult->fetch_assoc();
                        $accountId = $anyAccount['accountId'];
                    } else {
                        // 4. 임시 계정 생성 (개발용)
                        $tempEmail = 'temp_agent_' . time() . '@dev.com';
                        $tempPassword = password_hash('temp123', PASSWORD_DEFAULT);
                        
                        // accounts 테이블 컬럼 확인
                        $accountColumns = [];
                        $accountColumnResult = $conn->query("SHOW COLUMNS FROM accounts");
                        if ($accountColumnResult) {
                            while ($col = $accountColumnResult->fetch_assoc()) {
                                $accountColumns[] = strtolower($col['Field']);
                            }
                        }
                        
                        $accountFields = ['emailAddress'];
                        $accountValues = [$tempEmail];
                        $accountTypes = 's';
                        
                        // password 또는 passwordHash 컬럼 확인
                        if (in_array('password', $accountColumns)) {
                            $accountFields[] = 'password';
                            $accountValues[] = $tempPassword;
                            $accountTypes .= 's';
                        } elseif (in_array('passwordhash', $accountColumns)) {
                            $accountFields[] = 'passwordHash';
                            $accountValues[] = $tempPassword;
                            $accountTypes .= 's';
        }
        
                        // accountType 컬럼 확인
                        if (in_array('accounttype', $accountColumns)) {
                            $accountFields[] = 'accountType';
                            $accountValues[] = 'agent';
                            $accountTypes .= 's';
                        }
                        
                        // username 컬럼 확인
                        if (in_array('username', $accountColumns)) {
                            $accountFields[] = 'username';
                            $accountValues[] = $tempEmail;
                            $accountTypes .= 's';
                        }
                        
                        $accountPlaceholders = str_repeat('?,', count($accountFields) - 1) . '?';
                        $accountSql = "INSERT INTO accounts (" . implode(', ', $accountFields) . ") VALUES ($accountPlaceholders)";
        
                        $accountStmt = $conn->prepare($accountSql);
                        if ($accountStmt) {
                            $accountStmt->bind_param($accountTypes, ...$accountValues);
                            if ($accountStmt->execute()) {
                                $accountId = $conn->insert_id;
                            }
                        }
                        
                        // 계정 생성 실패 시 에러 반환
        if (empty($accountId)) {
                            send_error_response('Failed to create temporary account. Please specify accountId or create an account first.');
                        }
                    }
                }
            }
        }
        
        // accountId가 NULL이 아닌 경우 유효성 검증
        if ($accountId !== null) {
            $accountCheckStmt = $conn->prepare("SELECT accountId FROM accounts WHERE accountId = ?");
            $accountCheckStmt->bind_param("i", $accountId);
            $accountCheckStmt->execute();
            $accountCheckResult = $accountCheckStmt->get_result();
            
            if ($accountCheckResult->num_rows === 0) {
                send_error_response('Invalid accountId: account does not exist');
        }
        }
        
        // accountId 필드 추가
        if ($accountId !== null) {
            $fields[] = 'accountId';
            $placeholders[] = '?';
            $types .= 'i';
            $params[] = (int)$accountId;
        } elseif ($accountIdNullable) {
            // NULL을 허용하는 경우 NULL 사용
            $fields[] = 'accountId';
            $placeholders[] = 'NULL';
        }
        
        // 제목과 내용 필드 추가
        $fields[] = $titleField;
        $fields[] = $contentField;
        $placeholders[] = '?';
        $placeholders[] = '?';
        $types .= 'ss';
        $params[] = $input['inquiryTitle'] ?? $input['subject'] ?? '';
        $params[] = $input['inquiryContent'] ?? $input['content'] ?? '';
        
        // inquiryNo 생성 (필수인 경우)
        if ($useInquiryNoColumn) {
            $inquiryNo = $input['inquiryNo'] ?? null;
            if (empty($inquiryNo)) {
                // inquiryNo 자동 생성 (예: INQ20251106001 형식)
                $datePrefix = date('Ymd');
                $lastInquiryResult = $conn->query("SELECT inquiryNo FROM inquiries WHERE inquiryNo LIKE 'INQ{$datePrefix}%' ORDER BY inquiryNo DESC LIMIT 1");
                $lastNumber = 0;
                if ($lastInquiryResult && $lastInquiryResult->num_rows > 0) {
                    $lastInquiry = $lastInquiryResult->fetch_assoc();
                    $lastNo = $lastInquiry['inquiryNo'];
                    $lastNumber = (int)substr($lastNo, -3);
                }
                $nextNumber = $lastNumber + 1;
                $inquiryNo = 'INQ' . $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
            $fields[] = 'inquiryNo';
            $placeholders[] = '?';
            $types .= 's';
            $params[] = $inquiryNo;
        }
        
        // 타입 필드가 있으면 추가 (category 또는 inquiryType)
        if ($useCategoryColumn) {
            $fields[] = 'category';
            $placeholders[] = '?';
            $types .= 's';
            $params[] = $input['category'] ?? $input['inquiryType'] ?? 'general';
        } elseif ($useInquiryTypeColumn) {
            $fields[] = 'inquiryType';
            $placeholders[] = '?';
            $types .= 's';
            $params[] = $input['inquiryType'] ?? $input['category'] ?? 'general';
        }
        
        // status 필드 추가
        if (in_array('status', $inquiryColumns)) {
            // status 컬럼의 ENUM 값 확인
            $statusEnumValues = [];
            $statusColumnResult = $conn->query("SHOW COLUMNS FROM inquiries WHERE Field = 'status'");
            if ($statusColumnResult && $statusColumnResult->num_rows > 0) {
                $statusColumn = $statusColumnResult->fetch_assoc();
                $type = $statusColumn['Type'];
                // ENUM('value1','value2',...) 형식에서 값 추출
                if (preg_match("/^enum\s*\((.+)\)$/i", $type, $matches)) {
                    $enumValues = explode(',', $matches[1]);
                    foreach ($enumValues as $val) {
                        $statusEnumValues[] = trim($val, "'\"");
                    }
                }
            }
            
            // 기본값 결정 (pending이나 open 중 사용 가능한 값 선택)
            $defaultStatus = 'pending';
            if (!empty($statusEnumValues)) {
                if (in_array('pending', $statusEnumValues)) {
                    $defaultStatus = 'pending';
                } elseif (in_array('open', $statusEnumValues)) {
                    $defaultStatus = 'open';
                } elseif (in_array('in_progress', $statusEnumValues)) {
                    $defaultStatus = 'in_progress';
                } else {
                    // 첫 번째 ENUM 값 사용
                    $defaultStatus = $statusEnumValues[0];
                }
            }
            
            $fields[] = 'status';
            $placeholders[] = '?';
            $types .= 's';
            $params[] = $input['status'] ?? $defaultStatus;
        }
        
        // createdAt 필드 추가
        if (in_array('createdat', $inquiryColumns)) {
            $fields[] = 'createdAt';
            $placeholders[] = 'NOW()';
        }
        
        $sql = "INSERT INTO inquiries (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare SQL: ' . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute: ' . $stmt->error);
        }
        
        $inquiryId = $conn->insert_id;
        
        // 첨부파일 업로드 처리
        if (!empty($_FILES)) {
            $attachmentTableCheck = $conn->query("SHOW TABLES LIKE 'inquiry_attachments'");
            if ($attachmentTableCheck && $attachmentTableCheck->num_rows > 0) {
                // 업로드 디렉토리 설정
                // 실제 저장 경로: /var/www/html/uploads/inquiries/
                // __DIR__ = /var/www/html/admin_v2/backend/api/
                // ../../../ = /var/www/html/
                $uploadDir = __DIR__ . '/../../../uploads/inquiries/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // inquiry_attachments 테이블 컬럼 확인
                $attachmentColumns = [];
                $attachmentColumnResult = $conn->query("SHOW COLUMNS FROM inquiry_attachments");
                if ($attachmentColumnResult) {
                    while ($col = $attachmentColumnResult->fetch_assoc()) {
                        $attachmentColumns[] = strtolower($col['Field']);
                    }
                }
                
                // $_FILES에서 파일 처리
                foreach ($_FILES as $key => $file) {
                    // file_0, file_1 등의 형식으로 전송된 파일만 처리
                    if (strpos($key, 'file_') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $fileName = 'inquiry_' . $inquiryId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $filePath = 'uploads/inquiries/' . $fileName;
                            $fileSize = $file['size'];
                            $fileType = $file['type'];
                            $originalFileName = $file['name'];
                            
                            // inquiry_attachments 테이블에 저장
                            $attachmentFields = [];
                            $attachmentPlaceholders = [];
                            $attachmentTypes = '';
                            $attachmentParams = [];
                            
                            // inquiryId 필드
                            if (in_array('inquiryid', $attachmentColumns)) {
                                $attachmentFields[] = 'inquiryId';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $inquiryId;
                            }
                            
                            // fileName 필드
                            if (in_array('filename', $attachmentColumns)) {
                                $attachmentFields[] = 'fileName';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $originalFileName;
                            } elseif (in_array('name', $attachmentColumns)) {
                                $attachmentFields[] = 'name';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $originalFileName;
                            }
                            
                            // filePath 필드
                            if (in_array('filepath', $attachmentColumns)) {
                                $attachmentFields[] = 'filePath';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $filePath;
                            } elseif (in_array('path', $attachmentColumns)) {
                                $attachmentFields[] = 'path';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $filePath;
                            }
                            
                            // fileSize 필드
                            if (in_array('filesize', $attachmentColumns)) {
                                $attachmentFields[] = 'fileSize';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $fileSize;
                            } elseif (in_array('size', $attachmentColumns)) {
                                $attachmentFields[] = 'size';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $fileSize;
                            }
                            
                            // fileType 필드
                            if (in_array('filetype', $attachmentColumns)) {
                                $attachmentFields[] = 'fileType';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $fileType;
                            } elseif (in_array('type', $attachmentColumns)) {
                                $attachmentFields[] = 'type';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $fileType;
                            }
                            
                            // uploadedBy 필드 (accountId 사용)
                            if (in_array('uploadedby', $attachmentColumns)) {
                                // uploadedBy 컬럼이 NULL을 허용하는지 확인
                                $uploadedByNullable = false;
                                $uploadedByColumnResult = $conn->query("SHOW COLUMNS FROM inquiry_attachments WHERE Field = 'uploadedBy'");
                                if ($uploadedByColumnResult && $uploadedByColumnResult->num_rows > 0) {
                                    $uploadedByColumn = $uploadedByColumnResult->fetch_assoc();
                                    $uploadedByNullable = ($uploadedByColumn['Null'] === 'YES');
                                }
                                
                                // accountId 사용 (이미 createInquiry 함수에서 설정됨)
                                $uploadedByValue = $accountId ?? null;
                                
                                // NULL을 허용하지 않으면 기본값 설정
                                if ($uploadedByValue === null && !$uploadedByNullable) {
                                    // 기존 계정 찾기 (accountId 설정 로직과 동일)
                                    $agentAccountResult = $conn->query("SELECT accountId FROM accounts WHERE accountType = 'agent' LIMIT 1");
                                    if ($agentAccountResult && $agentAccountResult->num_rows > 0) {
                                        $agentAccount = $agentAccountResult->fetch_assoc();
                                        $uploadedByValue = $agentAccount['accountId'];
                                    } else {
                                        $anyAccountResult = $conn->query("SELECT accountId FROM accounts LIMIT 1");
                                        if ($anyAccountResult && $anyAccountResult->num_rows > 0) {
                                            $anyAccount = $anyAccountResult->fetch_assoc();
                                            $uploadedByValue = $anyAccount['accountId'];
                                        } else {
                                            $uploadedByValue = 0; // 최후의 수단
                                        }
                                    }
                                }
                                
                                // uploadedBy 필드 추가 (NULL 허용이거나 값이 있는 경우)
                                if ($uploadedByValue !== null || $uploadedByNullable) {
                                    $attachmentFields[] = 'uploadedBy';
                                    if ($uploadedByValue !== null) {
                                        $attachmentPlaceholders[] = '?';
                                        $attachmentTypes .= 'i';
                                        $attachmentParams[] = (int)$uploadedByValue;
                                    } else {
                                        $attachmentPlaceholders[] = 'NULL';
                                    }
                                }
                            }
                            
                            // createdAt 필드
                            if (in_array('createdat', $attachmentColumns)) {
                                $attachmentFields[] = 'createdAt';
                                $attachmentPlaceholders[] = 'NOW()';
                            }
                            
                            if (!empty($attachmentFields)) {
                                $attachmentSql = "INSERT INTO inquiry_attachments (" . implode(', ', $attachmentFields) . ") VALUES (" . implode(', ', $attachmentPlaceholders) . ")";
                                
                                // NOW()가 포함된 경우와 아닌 경우 분리 처리
                                $finalPlaceholders = [];
                                $finalParams = [];
                                $finalTypes = '';
                                
                                foreach ($attachmentPlaceholders as $idx => $placeholder) {
                                    if ($placeholder === 'NOW()') {
                                        $finalPlaceholders[] = 'NOW()';
                                    } else {
                                        $finalPlaceholders[] = '?';
                                        $finalParams[] = $attachmentParams[$idx];
                                        $finalTypes .= $attachmentTypes[$idx];
                                    }
                                }
                                
                                $attachmentSql = "INSERT INTO inquiry_attachments (" . implode(', ', $attachmentFields) . ") VALUES (" . implode(', ', $finalPlaceholders) . ")";
                                
                                $attachmentStmt = $conn->prepare($attachmentSql);
                                if ($attachmentStmt) {
                                    if (!empty($finalParams)) {
                                        $attachmentStmt->bind_param($finalTypes, ...$finalParams);
                                    }
                                    $attachmentStmt->execute();
                                }
                            }
                        }
                    }
                }
            }
        }
        
        send_success_response(['inquiryId' => $inquiryId], 'Inquiry created successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to create inquiry: ' . $e->getMessage());
    }
}

function updateInquiry($conn, $input) {
    try {
        $inquiryId = $input['inquiryId'] ?? '';
        
        if (empty($inquiryId)) {
            send_error_response('Inquiry ID is required');
        }
        
        $updates = [];
        $params = [];
        $types = '';
        
        // 컬럼명 확인
        $columnCheck = $conn->query("SHOW COLUMNS FROM inquiries LIKE 'subject'");
        $useSubjectColumn = ($columnCheck->num_rows > 0);
        
        if (isset($input['inquiryTitle'])) {
            $fieldName = $useSubjectColumn ? 'subject' : 'inquiryTitle';
            $updates[] = "$fieldName = ?";
            $params[] = $input['inquiryTitle'];
            $types .= 's';
        }
        
        if (isset($input['inquiryContent'])) {
            $fieldName = $useSubjectColumn ? 'content' : 'inquiryContent';
            $updates[] = "$fieldName = ?";
            $params[] = $input['inquiryContent'];
            $types .= 's';
        }
        
        if (isset($input['responseContent']) || isset($input['replyContent'])) {
            $replyContent = $input['responseContent'] ?? $input['replyContent'] ?? '';
            $updates[] = "replyContent = ?";
            $params[] = $replyContent;
            $types .= 's';
        }
        
        if (isset($input['status'])) {
            $updates[] = "status = ?";
            $params[] = $input['status'];
            $types .= 's';
        }
        
        if (empty($updates)) {
            send_error_response('No fields to update');
        }
        
        $params[] = $inquiryId;
        $types .= 'i';
        
        $sql = "UPDATE inquiries SET " . implode(', ', $updates) . " WHERE inquiryId = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        // 첨부파일 업로드 처리 (새로 추가된 파일들)
        if (!empty($_FILES)) {
            $attachmentTableCheck = $conn->query("SHOW TABLES LIKE 'inquiry_attachments'");
            if ($attachmentTableCheck && $attachmentTableCheck->num_rows > 0) {
                // 업로드 디렉토리 설정
                // 실제 저장 경로: /var/www/html/uploads/inquiries/
                // __DIR__ = /var/www/html/admin_v2/backend/api/
                // ../../../ = /var/www/html/
                $uploadDir = __DIR__ . '/../../../uploads/inquiries/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // inquiry_attachments 테이블 컬럼 확인
                $attachmentColumns = [];
                $attachmentColumnResult = $conn->query("SHOW COLUMNS FROM inquiry_attachments");
                if ($attachmentColumnResult) {
                    while ($col = $attachmentColumnResult->fetch_assoc()) {
                        $attachmentColumns[] = strtolower($col['Field']);
                    }
                }
                
                // accountId 가져오기 (uploadedBy용)
                $accountId = $input['accountId'] ?? null;
                if (empty($accountId)) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $accountId = $_SESSION['accountId'] ?? null;
                }
                
                // $_FILES에서 파일 처리
                foreach ($_FILES as $key => $file) {
                    // file_0, file_1 등의 형식으로 전송된 파일만 처리
                    if (strpos($key, 'file_') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $fileName = 'inquiry_' . $inquiryId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $filePath = 'uploads/inquiries/' . $fileName;
                            $fileSize = $file['size'];
                            $fileType = $file['type'];
                            $originalFileName = $file['name'];
                            
                            // inquiry_attachments 테이블에 저장
                            $attachmentFields = [];
                            $attachmentPlaceholders = [];
                            $attachmentTypes = '';
                            $attachmentParams = [];
                            
                            // inquiryId 필드
                            if (in_array('inquiryid', $attachmentColumns)) {
                                $attachmentFields[] = 'inquiryId';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $inquiryId;
                            }
                            
                            // fileName 필드
                            if (in_array('filename', $attachmentColumns)) {
                                $attachmentFields[] = 'fileName';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $originalFileName;
                            } elseif (in_array('name', $attachmentColumns)) {
                                $attachmentFields[] = 'name';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $originalFileName;
                            }
                            
                            // filePath 필드
                            if (in_array('filepath', $attachmentColumns)) {
                                $attachmentFields[] = 'filePath';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $filePath;
                            } elseif (in_array('path', $attachmentColumns)) {
                                $attachmentFields[] = 'path';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $filePath;
                            }
                            
                            // fileSize 필드
                            if (in_array('filesize', $attachmentColumns)) {
                                $attachmentFields[] = 'fileSize';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $fileSize;
                            } elseif (in_array('size', $attachmentColumns)) {
                                $attachmentFields[] = 'size';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 'i';
                                $attachmentParams[] = $fileSize;
                            }
                            
                            // fileType 필드
                            if (in_array('filetype', $attachmentColumns)) {
                                $attachmentFields[] = 'fileType';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $fileType;
                            } elseif (in_array('type', $attachmentColumns)) {
                                $attachmentFields[] = 'type';
                                $attachmentPlaceholders[] = '?';
                                $attachmentTypes .= 's';
                                $attachmentParams[] = $fileType;
                            }
                            
                            // uploadedBy 필드 (accountId 사용)
                            if (in_array('uploadedby', $attachmentColumns)) {
                                $uploadedByNullable = false;
                                $uploadedByColumnResult = $conn->query("SHOW COLUMNS FROM inquiry_attachments WHERE Field = 'uploadedBy'");
                                if ($uploadedByColumnResult && $uploadedByColumnResult->num_rows > 0) {
                                    $uploadedByColumn = $uploadedByColumnResult->fetch_assoc();
                                    $uploadedByNullable = ($uploadedByColumn['Null'] === 'YES');
                                }
                                
                                $uploadedByValue = $accountId ?? null;
                                
                                if ($uploadedByValue === null && !$uploadedByNullable) {
                                    $agentAccountResult = $conn->query("SELECT accountId FROM accounts WHERE accountType = 'agent' LIMIT 1");
                                    if ($agentAccountResult && $agentAccountResult->num_rows > 0) {
                                        $agentAccount = $agentAccountResult->fetch_assoc();
                                        $uploadedByValue = $agentAccount['accountId'];
                                    } else {
                                        $anyAccountResult = $conn->query("SELECT accountId FROM accounts LIMIT 1");
                                        if ($anyAccountResult && $anyAccountResult->num_rows > 0) {
                                            $anyAccount = $anyAccountResult->fetch_assoc();
                                            $uploadedByValue = $anyAccount['accountId'];
                                        } else {
                                            $uploadedByValue = 0;
                                        }
                                    }
                                }
                                
                                if ($uploadedByValue !== null || $uploadedByNullable) {
                                    $attachmentFields[] = 'uploadedBy';
                                    if ($uploadedByValue !== null) {
                                        $attachmentPlaceholders[] = '?';
                                        $attachmentTypes .= 'i';
                                        $attachmentParams[] = (int)$uploadedByValue;
                                    } else {
                                        $attachmentPlaceholders[] = 'NULL';
                                    }
                                }
                            }
                            
                            // createdAt 필드
                            if (in_array('createdat', $attachmentColumns)) {
                                $attachmentFields[] = 'createdAt';
                                $attachmentPlaceholders[] = 'NOW()';
                            }
                            
                            if (!empty($attachmentFields)) {
                                // NOW()와 NULL 처리
                                $finalPlaceholders = [];
                                $finalParams = [];
                                $finalTypes = '';
                                
                                foreach ($attachmentPlaceholders as $idx => $placeholder) {
                                    if ($placeholder === 'NOW()' || $placeholder === 'NULL') {
                                        $finalPlaceholders[] = $placeholder;
                                    } else {
                                        $finalPlaceholders[] = '?';
                                        $finalParams[] = $attachmentParams[$idx];
                                        $finalTypes .= $attachmentTypes[$idx];
                                    }
                                }
                                
                                $attachmentSql = "INSERT INTO inquiry_attachments (" . implode(', ', $attachmentFields) . ") VALUES (" . implode(', ', $finalPlaceholders) . ")";
                                
                                $attachmentStmt = $conn->prepare($attachmentSql);
                                if ($attachmentStmt) {
                                    if (!empty($finalParams)) {
                                        $attachmentStmt->bind_param($finalTypes, ...$finalParams);
                                    }
                                    $attachmentStmt->execute();
                                }
                            }
                        }
                    }
                }
            }
        }
        
        send_success_response([], 'Inquiry updated successfully');
        
    } catch (Exception $e) {
        send_error_response('Failed to update inquiry: ' . $e->getMessage());
    }
}

// ========== 헬퍼 함수들 ==========

function generateBookingId($conn) {
    $prefix = 'BK';
    $date = date('Ymd');

    // 오늘 날짜로 시작하는 예약 번호 중 가장 큰 번호 확인
    $sql = "SELECT MAX(bookingId) as maxId FROM bookings WHERE bookingId LIKE ?";
    $likePattern = $prefix . $date . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $likePattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['maxId']) {
        // 기존 최대 번호에서 시퀀스 추출 후 +1
        $lastSequence = (int)substr($row['maxId'], -3);
        $sequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
    } else {
        // 오늘 첫 예약
        $sequence = '001';
    }

    return $prefix . $date . $sequence;
}

function createNewCustomer($conn, $customerInfo) {
    // 이메일 중복 확인
    $emailCheckSql = "SELECT accountId FROM accounts WHERE emailAddress = ?";
    $emailCheckStmt = $conn->prepare($emailCheckSql);
    $emailCheckStmt->bind_param("s", $customerInfo['email']);
    $emailCheckStmt->execute();
    $emailResult = $emailCheckStmt->get_result();
    
    if ($emailResult->num_rows > 0) {
        $row = $emailResult->fetch_assoc();
        return $row['accountId'];
    }
    
    // accounts 테이블에 생성 (password 또는 passwordHash 컬럼 확인)
    $columnCheck = $conn->query("SHOW COLUMNS FROM accounts LIKE 'password'");
    $passwordColumn = ($columnCheck->num_rows > 0) ? 'password' : 'passwordHash';
    
    $accountSql = "INSERT INTO accounts (emailAddress, $passwordColumn, accountType, createdAt) VALUES (?, ?, 'guest', NOW())";
    $accountStmt = $conn->prepare($accountSql);
    $passwordHash = password_hash($customerInfo['password'] ?? 'temp123', PASSWORD_DEFAULT);
    $accountStmt->bind_param("ss", $customerInfo['email'], $passwordHash);
    $accountStmt->execute();
    $accountId = $conn->insert_id;
    
    // client 테이블에 생성
    $clientSql = "
        INSERT INTO client (
            accountId, fName, lName, emailAddress, contactNo
        ) VALUES (?, ?, ?, ?, ?)
    ";
    $clientStmt = $conn->prepare($clientSql);
    $firstName = $customerInfo['firstName'] ?? '';
    $lastName = $customerInfo['lastName'] ?? '';
    $email = $customerInfo['email'] ?? '';
    $phone = $customerInfo['phone'] ?? '';
    $clientStmt->bind_param("issss", $accountId, $firstName, $lastName, $email, $phone);
    $clientStmt->execute();
    
    return $accountId;
}

function downloadCustomers($conn, $input) {
    try {
        // 출력 버퍼 정리 (CSV 다운로드를 위해)
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        // 검색 조건
        $search = $input['search'] ?? '';
        
        // 컬럼 존재 여부 확인 (옵셔널 컬럼들)
        $columnsCheck = $conn->query("SHOW COLUMNS FROM client");
        $clientColumns = [];
        while ($col = $columnsCheck->fetch_assoc()) {
            $clientColumns[] = strtolower($col['Field']);
        }
        
        // 옵셔널 컬럼들을 선택적으로 포함
        $emailCol = in_array('emailaddress', $clientColumns) ? 'COALESCE(a.emailAddress, c.emailAddress)' : 'a.emailAddress';
        $hasCreatedAt = in_array('createdat', $clientColumns);
        $createdAtCol = $hasCreatedAt ? 'COALESCE(a.createdAt, c.createdAt)' : 'a.createdAt';
        
        // 쿼리 구성
        $sql = "
            SELECT 
                c.accountId,
                c.fName,
                c.lName,
                $emailCol as emailAddress,
                c.contactNo,
                $createdAtCol as createdAt
            FROM client c
            LEFT JOIN accounts a ON c.accountId = a.accountId
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $sql .= " AND (c.fName LIKE ? OR c.lName LIKE ? OR a.emailAddress LIKE ? OR c.contactNo LIKE ?)";
            $searchPattern = '%' . $search . '%';
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= 'ssss';
        }
        
        $sql .= " ORDER BY $createdAtCol DESC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        // CSV 헤더 설정
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="customers_' . date('YmdHis') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM 추가 (Excel에서 한글 깨짐 방지)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // 헤더 작성 (escape 파라미터 추가로 deprecated 경고 방지)
        fputcsv($output, ['No', 'Customer Name', 'Email', 'Contact', 'Registration Date'], ',', '"', '');
        
        // 데이터 작성
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $fName = $row['fName'] ?? '';
            $lName = $row['lName'] ?? '';
            $customerName = trim($fName . ' ' . $lName);
            $email = $row['emailAddress'] ?? '';
            $contact = $row['contactNo'] ?? '';
            $regDate = $row['createdAt'] ?? '';
            
            // 연락처를 문자열로 강제 변환 (Excel에서 과학적 표기법 방지)
            // 앞에 작은따옴표를 추가하여 텍스트로 인식되도록 함
            if ($contact) {
                // 숫자로만 구성된 경우 작은따옴표 추가
                if (preg_match('/^\d+$/', $contact)) {
                    $contact = "'" . $contact;
                }
            }
            
            // 날짜 포맷팅 (Excel이 인식하는 형식: YYYY-MM-DD HH:MM:SS)
            if ($regDate) {
                try {
                    $dateObj = new DateTime($regDate);
                    $regDate = $dateObj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    // 날짜 파싱 실패 시 원본 값 유지
                }
            }
            
            // escape 파라미터 추가로 deprecated 경고 방지
            fputcsv($output, [
                $no++,
                $customerName,
                $email,
                $contact,
                $regDate
            ], ',', '"', '');
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        error_log('downloadCustomers error: ' . $e->getMessage());
        error_log('downloadCustomers trace: ' . $e->getTraceAsString());
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Failed to download customers: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function downloadCustomerSample() {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="customer_sample.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    $headers = [
        'Customer First Name',
        'Customer Last Name',
        'Email',
        'Country Code',
        'Contact',
        'Password',
        'Note',
        'Traveler Title',
        'Traveler First Name',
        'Traveler Last Name',
        'Traveler Gender',
        'Traveler Age',
        'Traveler Birth (YYYY-MM-DD)',
        'Traveler Nationality',
        'Traveler Passport Number',
        'Traveler Passport Issue Date (YYYY-MM-DD)',
        'Traveler Passport Expiry Date (YYYY-MM-DD)'
    ];
    fputcsv($output, $headers, ',', '"', '');
    
    $sampleRows = [
        [
            'Juan',
            'Dela Cruz',
            'juan@example.com',
            '+63',
            '9171234567',
            'Temp123!',
            'VIP customer',
            'MR',
            'Juan',
            'Dela Cruz',
            'male',
            '35',
            '1990-05-12',
            'Philippines',
            'PP1234567',
            '2020-01-01',
            '2030-01-01'
        ],
        [
            'Maria',
            'Santos',
            'maria@example.com',
            '+82',
            '1012345678',
            'Temp123!',
            'Preferred traveler',
            'MS',
            'Maria',
            'Santos',
            'female',
            '29',
            '1996-08-23',
            'South Korea',
            'KP7654321',
            '2021-03-15',
            '2031-03-14'
        ]
    ];
    
    foreach ($sampleRows as $row) {
        fputcsv($output, $row, ',', '"', '');
    }
    
    fclose($output);
    exit;
}

function downloadReservations($conn, $input) {
    try {
        // 출력 버퍼 정리 (CSV 다운로드를 위해)
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        $where = [];
        $params = [];
        $types = '';
        
        // 검색 조건
        if (!empty($input['search'])) {
            $where[] = "(p.packageName LIKE ? OR c.fName LIKE ? OR c.lName LIKE ? OR b.bookingId LIKE ?)";
            $searchTerm = '%' . $input['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        // 여행 시작일 필터
        if (!empty($input['travelStartDate'])) {
            $where[] = "b.departureDate >= ?";
            $params[] = $input['travelStartDate'];
            $types .= 's';
        }
        
        // 상태 필터
        if (!empty($input['status'])) {
            if ($input['status'] === 'pending_deposit') {
                $where[] = "b.paymentStatus = 'pending' AND b.bookingStatus = 'confirmed'";
            } elseif ($input['status'] === 'pending_balance') {
                $where[] = "b.paymentStatus = 'partial' AND b.bookingStatus = 'confirmed'";
            } else {
                $where[] = "b.bookingStatus = ?";
                $params[] = $input['status'];
                $types .= 's';
            }
        }
        
        // 검색 타입 필터
        if (!empty($input['searchType']) && !empty($input['search'])) {
            $searchTerm = '%' . $input['search'] . '%';
            // 기존 검색 조건 제거
            $where = array_filter($where, function($w) {
                return strpos($w, 'LIKE') === false;
            });
            $params = array_filter($params, function($p) use ($searchTerm) {
                return $p !== $searchTerm;
            });
            $types = preg_replace('/s{4}/', '', $types);
            
            // searchType 값 매핑 (HTML의 value와 API의 기대값 매핑)
            $searchType = $input['searchType'] ?? '';
            if ($searchType === 'product' || $searchType === 'packageName') {
                $where[] = "p.packageName LIKE ?";
                $params[] = $searchTerm;
                $types .= 's';
            } elseif ($searchType === 'customer' || $searchType === 'customerName') {
                $where[] = "(c.fName LIKE ? OR c.lName LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ss';
            } elseif ($searchType === 'bookingId' || $searchType === 'bookingNumber') {
                $where[] = "b.bookingId LIKE ?";
                $params[] = $searchTerm;
                $types .= 's';
            }
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // 쿼리 구성
        $sql = "
            SELECT 
                b.bookingId,
                p.packageName,
                b.departureDate,
                CONCAT(c.fName, ' ', c.lName) as reserverName,
                (b.adults + b.children + b.infants) as numPeople,
                b.bookingStatus,
                b.paymentStatus,
                b.createdAt
            FROM bookings b
            LEFT JOIN packages p ON b.packageId = p.packageId
            LEFT JOIN client c ON b.accountId = c.accountId
            $whereClause
            ORDER BY b.createdAt DESC
        ";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        // CSV 헤더 설정
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reservations_' . date('YmdHis') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM 추가 (Excel에서 한글 깨짐 방지)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // 헤더 작성
        fputcsv($output, ['No', 'Booking Number', 'Package Name', 'Travel Start Date', 'Reserver Name', 'Number of People', 'Status', 'Registration Date'], ',', '"', '');
        
        // 데이터 작성
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $bookingId = $row['bookingId'] ?? '';
            $packageName = $row['packageName'] ?? 'N/A';
            $departureDate = $row['departureDate'] ?? '';
            $reserverName = $row['reserverName'] ?? 'N/A';
            $numPeople = (int)($row['numPeople'] ?? 0);
            $bookingStatus = $row['bookingStatus'] ?? '';
            $paymentStatus = $row['paymentStatus'] ?? '';
            $createdAt = $row['createdAt'] ?? '';
            
            // 상태 배지 결정
            $statusBadge = getBookingStatusBadge($bookingStatus, $paymentStatus);
            $statusLabel = $statusBadge['label'] ?? $bookingStatus;
            
            // 날짜 포맷팅 (Excel이 인식하는 형식: YYYY-MM-DD)
            if ($departureDate) {
                try {
                    $dateObj = new DateTime($departureDate);
                    $departureDate = $dateObj->format('Y-m-d');
                } catch (Exception $e) {
                    // 날짜 파싱 실패 시 원본 값 유지
                }
            }
            
            // 등록일 포맷팅
            if ($createdAt) {
                try {
                    $dateObj = new DateTime($createdAt);
                    $createdAt = $dateObj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    // 날짜 파싱 실패 시 원본 값 유지
                }
            }
            
            // escape 파라미터 추가로 deprecated 경고 방지
            fputcsv($output, [
                $no++,
                $bookingId,
                $packageName,
                $departureDate,
                $reserverName,
                $numPeople,
                $statusLabel,
                $createdAt
            ], ',', '"', '');
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        error_log('downloadReservations error: ' . $e->getMessage());
        error_log('downloadReservations trace: ' . $e->getTraceAsString());
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Failed to download reservations: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function normalizeCsvHeaderKey($header) {
    return strtolower(preg_replace('/[^a-z0-9]+/', '_', trim($header)));
}

function getCsvValueByHeader($row, $headerIndex, $aliases) {
    foreach ((array)$aliases as $alias) {
        $normalized = normalizeCsvHeaderKey($alias);
        if (isset($headerIndex[$normalized])) {
            return trim($row[$headerIndex[$normalized]] ?? '');
        }
    }
    return '';
}

function normalizeCsvDateValue($value) {
    if (!$value) return '';
    $value = trim($value);
    
    // Already in YYYY-MM-DD format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }
    
    // YYYYMMDD format (no separators)
    if (preg_match('/^\d{8}$/', $value)) {
        return substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2);
    }
    
    // YYYY/MM/DD or YYYY.MM.DD format
    if (preg_match('/^\d{4}[\/\.]\d{1,2}[\/\.]\d{1,2}$/', $value)) {
        $parts = preg_split('/[\/\.]/', $value);
        return sprintf('%04d-%02d-%02d', $parts[0], $parts[1], $parts[2]);
    }
    
    // MM/DD/YYYY or MM-DD-YYYY format (NEW!)
    if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $value)) {
        $parts = preg_split('/[\/\-]/', $value);
        return sprintf('%04d-%02d-%02d', $parts[2], $parts[0], $parts[1]);
    }
    
    // DD/MM/YYYY or DD-MM-YYYY format (if you need this)
    // Uncomment if your dates might be in this format
    /*
    if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $value)) {
        $parts = preg_split('/[\/\-]/', $value);
        // Assuming DD/MM/YYYY
        return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
    }
    */
    
    return '';
}

function splitCountryCodeFromContact($countryCode, $contact) {
    $countryCode = trim($countryCode ?? '');
    $contact = trim($contact ?? '');
    
    if (!$countryCode && preg_match('/^\s*(\+\d{1,4})\s*(.*)$/', $contact, $matches)) {
        $countryCode = $matches[1];
        $contact = trim($matches[2]);
    }
    
    $contact = preg_replace('/\s+/', '', $contact);
    
    return [$countryCode ?: '+63', $contact];
}

function batchUploadCustomers($conn, $agentId) {
    try {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            send_error_response('파일 업로드에 실패했습니다.');
        }
        
        $file = $_FILES['file'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 파일 형식 검증
        if (!in_array($fileExt, ['csv', 'xlsx', 'xls'])) {
            send_error_response('CSV 또는 Excel 파일만 업로드 가능합니다.');
        }
        
        $rows = [];
        $headerIndex = [];
        if ($fileExt === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            if ($handle === false) {
                send_error_response('파일을 읽을 수 없습니다.');
            }
            
            $firstLine = fgets($handle);
            if (substr($firstLine, 0, 3) === chr(0xEF).chr(0xBB).chr(0xBF)) {
                $firstLine = substr($firstLine, 3);
            }
            $headers = str_getcsv($firstLine);
            foreach ($headers as $idx => $header) {
                $headerIndex[normalizeCsvHeaderKey($header)] = $idx;
            }
            
            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row, fn($value) => trim((string)$value) !== ''))) {
                    continue;
                }
                $rows[] = $row;
            }
            fclose($handle);
        } else {
            send_error_response('Excel 파일 처리는 현재 지원하지 않습니다. CSV 파일을 사용해주세요.');
        }
        
        if (empty($rows)) {
            send_error_response('업로드된 파일에 유효한 데이터가 없습니다.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($rows as $index => $row) {
            $customerFirstName = getCsvValueByHeader($row, $headerIndex, ['Customer First Name', 'First Name']);
            $customerLastName = getCsvValueByHeader($row, $headerIndex, ['Customer Last Name', 'Last Name']);
            $email = getCsvValueByHeader($row, $headerIndex, ['Email']);
            $countryCode = getCsvValueByHeader($row, $headerIndex, ['Country Code']);
            $contact = getCsvValueByHeader($row, $headerIndex, ['Contact', 'Phone']);
            $password = getCsvValueByHeader($row, $headerIndex, ['Password']);
            $note = getCsvValueByHeader($row, $headerIndex, ['Note', 'Memo']);
            $travelerTitle = getCsvValueByHeader($row, $headerIndex, ['Traveler Title']);
            $travelerFirstName = getCsvValueByHeader($row, $headerIndex, ['Traveler First Name']);
            $travelerLastName = getCsvValueByHeader($row, $headerIndex, ['Traveler Last Name']);
            $travelerGender = getCsvValueByHeader($row, $headerIndex, ['Traveler Gender']);
            $travelerNationality = getCsvValueByHeader($row, $headerIndex, ['Traveler Nationality']);
            $travelerPassportNo = getCsvValueByHeader($row, $headerIndex, ['Traveler Passport Number']);
            $travelerBirth = normalizeCsvDateValue(getCsvValueByHeader($row, $headerIndex, ['Traveler Birth', 'Traveler Birth (YYYY-MM-DD)']));
            $passportIssue = normalizeCsvDateValue(getCsvValueByHeader($row, $headerIndex, ['Traveler Passport Issue Date', 'Traveler Passport Issue Date (YYYY-MM-DD)']));
            $passportExpiry = normalizeCsvDateValue(getCsvValueByHeader($row, $headerIndex, ['Traveler Passport Expiry Date', 'Traveler Passport Expiry Date (YYYY-MM-DD)']));
            
            [$resolvedCountryCode, $resolvedContact] = splitCountryCodeFromContact($countryCode, $contact);
            
            $firstName = $customerFirstName ?: $travelerFirstName;
            $lastName = $customerLastName ?: $travelerLastName;
            
            if (empty($firstName) || empty($email) || empty($resolvedContact)) {
                $errors[] = "Row " . ($index + 2) . ": 필수 필드(이름, 이메일, 연락처)가 누락되었습니다.";
                $errorCount++;
                continue;
            }
            
            $customerInput = [
                'firstName' => $firstName,
                'lastName' => $lastName ?: '',
                'email' => $email,
                'phone' => $resolvedContact,
                'password' => $password ?: 'temp123',
                'countryCode' => $resolvedCountryCode,
                'memo' => $note,
                'travelerTitle' => strtoupper($travelerTitle ?: 'MR'),
                'travelerFirstName' => $travelerFirstName ?: $firstName,
                'travelerLastName' => $travelerLastName ?: $lastName,
                'travelerGender' => strtolower($travelerGender ?: 'male'),
                'travelerNationality' => $travelerNationality,
                'travelerPassportNo' => $travelerPassportNo,
                'travelerPassportIssue' => $passportIssue,
                'travelerPassportExpire' => $passportExpiry,
                'travelerBirth' => $travelerBirth
            ];

            // Add agentId if provided
            if ($agentId !== null) {
                $customerInput['agentId'] = $agentId;
            }
            
            try {
                createCustomerRecord($conn, $customerInput, []);
                $successCount++;
            } catch (Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                $errorCount++;
            }
        }
        
        send_success_response([
            'successCount' => $successCount,
            'errorCount' => $errorCount,
            'errors' => $errors
        ], "총 {$successCount}건이 등록되었습니다. " . ($errorCount > 0 ? "{$errorCount}건의 오류가 발생했습니다." : ""));
        
    } catch (Exception $e) {
        send_error_response('일괄 등록 중 오류가 발생했습니다: ' . $e->getMessage());
    }
}

/**
 * 항공편 정보 조회
 */
function getFlightInfo($conn, $input) {
    $flightId = $input['flightId'] ?? null;
    
    if (!$flightId) {
        send_error_response('항공편 ID가 필요합니다.', 400);
        return;
    }
    
    try {
        $flightId = intval($flightId);
        
        // flight 테이블에서 항공편 상세 정보 조회
        $query = "SELECT 
                    f.flightId,
                    f.packageId,
                    f.origin,
                    f.destination,
                    f.flightName,
                    f.flightCode,
                    f.flightDepartureDate,
                    f.flightDepartureTime,
                    f.flightArrivalDate,
                    f.flightArrivalTime,
                    f.returnOrigin,
                    f.returnDestination,
                    f.returnFlightName,
                    f.returnFlightCode,
                    f.returnDepartureDate,
                    f.returnDepartureTime,
                    f.returnArrivalDate,
                    f.returnArrivalTime,
                    f.flightPrice,
                    f.landPrice,
                    f.availSeats,
                    f.is_active
                  FROM flight f
                  WHERE f.flightId = ? AND f.is_active = 1";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $flightId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            send_error_response('항공편 정보를 찾을 수 없습니다.', 404);
            return;
        }
        
        $flight = $result->fetch_assoc();
        
        // 예약된 좌석 수 계산
        // bookings 테이블에 flightId 컬럼이 있는지 확인
        $bookingsColumns = [];
        $bookingsColumnCheck = $conn->query("SHOW COLUMNS FROM bookings");
        if ($bookingsColumnCheck) {
            while ($col = $bookingsColumnCheck->fetch_assoc()) {
                $bookingsColumns[] = strtolower($col['Field']);
            }
        }
        
        $hasFlightId = in_array('flightid', $bookingsColumns);
        $hasBookingStatus = in_array('bookingstatus', $bookingsColumns);
        
        if ($hasFlightId && $hasBookingStatus) {
            // bookings 테이블 사용
        $bookedQuery = "SELECT COUNT(*) as booked_seats 
                           FROM bookings 
                           WHERE flightId = ? AND bookingStatus IN ('confirmed', 'pending')";
        } else {
            // flightId가 없으면 0 반환
            $flight['bookedSeats'] = 0;
            $flight['remainingSeats'] = intval($flight['availSeats']);
            send_success_response($flight, '항공편 정보를 성공적으로 조회했습니다.');
            return;
        }
        
        $bookedStmt = $conn->prepare($bookedQuery);
        $bookedStmt->bind_param('i', $flightId);
        $bookedStmt->execute();
        $bookedResult = $bookedStmt->get_result();
        $bookedData = $bookedResult->fetch_assoc();
        
        $flight['bookedSeats'] = intval($bookedData['booked_seats'] ?? 0);
        $flight['remainingSeats'] = intval($flight['availSeats']) - $flight['bookedSeats'];
        
        send_success_response($flight, '항공편 정보를 성공적으로 조회했습니다.');
        
    } catch (Exception $e) {
        error_log('getFlightInfo Error: ' . $e->getMessage());
        error_log('getFlightInfo Trace: ' . $e->getTraceAsString());
        send_error_response('항공편 정보 조회 중 오류가 발생했습니다: ' . $e->getMessage());
    }
}

// ============================================================================
// 3-Tier Payment System Functions
// ============================================================================

/**
 * 선금 파일 업로드 (예약 후 또는 재업로드)
 */
function uploadDownPayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $files = $_FILES ?? [];

        // Support multiple file field names for compatibility
        $fileKey = null;
        $possibleKeys = ['downProof', 'downPaymentProof', 'paymentFile', 'depositProof', 'file'];
        foreach ($possibleKeys as $key) {
            if (isset($files[$key]) && $files[$key]['error'] === UPLOAD_ERR_OK) {
                $fileKey = $key;
                break;
            }
        }

        if ($fileKey === null) {
            throw new Exception('Down payment proof file is required');
        }

        // 예약 확인
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // 파일 업로드
        $uploadDir = __DIR__ . '/../../../uploads/deposits/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($files[$fileKey]['name'], PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension);
        $extension = $extension ? '.' . $extension : '';
        $fileName = 'down_payment_' . $bookingId . '_' . time() . '_' . uniqid() . $extension;
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($files[$fileKey]['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload file');
        }

        $filePath = 'uploads/deposits/' . $fileName;
        $uploadedAt = date('Y-m-d H:i:s');
        $uploadedAtObj = new DateTime($uploadedAt);

        // 기존 파일 삭제
        if (!empty($booking['downPaymentFile'])) {
            $oldFile = __DIR__ . '/../../../' . $booking['downPaymentFile'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // 기존 값들 가져오기
        $downPaymentDueDate = $booking['downPaymentDueDate'];
        $balanceDueDate = $booking['balanceDueDate'];
        $departureDate = $booking['departureDate'];
        $createdAt = new DateTime($booking['createdAt']);
        $departureDateObj = new DateTime($departureDate);

        // 출발일과 예약일의 차이 계산 (최우선 조건)
        $daysDifference = $createdAt->diff($departureDateObj)->days;

        if ($daysDifference <= 30) {
            // 출발일이 예약일 기준 30일 이내면 Step 2 = Step 1
            $advancePaymentDueDate = $downPaymentDueDate;
        } else {
            // Step 2: Advance Payment - Down Payment Proof 업로드일 + 30일
            $advancePaymentDueDateObj = (clone $uploadedAtObj)->modify('+30 days');
            $advancePaymentDueDate = $advancePaymentDueDateObj->format('Y-m-d');

            // Step 2가 Step 3보다 이후면 Step 3와 동일하게
            if ($advancePaymentDueDate > $balanceDueDate) {
                $advancePaymentDueDate = $balanceDueDate;
            }
        }

        // DB 업데이트 (balanceDueDate는 변경하지 않음 - 예약 생성 시 이미 설정됨)
        // 재업로드 시 rejection 정보 초기화
        $sql = "UPDATE bookings SET
                downPaymentFile = ?,
                downPaymentUploadedAt = NOW(),
                advancePaymentDueDate = ?,
                downPaymentRejectedAt = NULL,
                downPaymentRejectionReason = NULL,
                bookingStatus = 'checking_down_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $filePath, $advancePaymentDueDate, $bookingId);
        $stmt->execute();

        send_success_response(['filePath' => $filePath], 'Down payment proof uploaded successfully');

    } catch (Exception $e) {
        send_error_response('Failed to upload down payment proof: ' . $e->getMessage());
    }
}

/**
 * 중도금 파일 업로드
 */
function uploadAdvancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $files = $_FILES ?? [];

        // Support multiple file field names for compatibility
        $fileKey = null;
        $possibleKeys = ['advanceProof', 'advancePaymentProof', 'paymentFile', 'depositProof', 'file'];
        foreach ($possibleKeys as $key) {
            if (isset($files[$key]) && $files[$key]['error'] === UPLOAD_ERR_OK) {
                $fileKey = $key;
                break;
            }
        }

        if ($fileKey === null) {
            throw new Exception('Advance payment proof file is required');
        }

        // 예약 확인 및 상태 검증
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'waiting_advance_payment') {
            throw new Exception('Cannot upload advance payment at this stage. Current status: ' . $booking['bookingStatus']);
        }

        // 파일 업로드
        $uploadDir = __DIR__ . '/../../../uploads/deposits/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($files[$fileKey]['name'], PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension);
        $extension = $extension ? '.' . $extension : '';
        $fileName = 'advance_payment_' . $bookingId . '_' . time() . '_' . uniqid() . $extension;
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($files[$fileKey]['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload file');
        }

        $filePath = 'uploads/deposits/' . $fileName;
        $uploadedAt = date('Y-m-d H:i:s');

        // 기존 파일 삭제
        if (!empty($booking['advancePaymentFile'])) {
            $oldFile = __DIR__ . '/../../../' . $booking['advancePaymentFile'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // DB 업데이트 - 재업로드 시 rejection 정보 초기화
        $sql = "UPDATE bookings SET
                advancePaymentFile = ?,
                advancePaymentUploadedAt = NOW(),
                advancePaymentRejectedAt = NULL,
                advancePaymentRejectionReason = NULL,
                bookingStatus = 'checking_advance_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $filePath, $bookingId);
        $stmt->execute();

        send_success_response(['filePath' => $filePath], 'Advance payment proof uploaded successfully');

    } catch (Exception $e) {
        send_error_response('Failed to upload advance payment proof: ' . $e->getMessage());
    }
}

/**
 * 잔금 파일 업로드
 */
function uploadBalancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $files = $_FILES ?? [];

        // Support multiple file field names for compatibility
        $fileKey = null;
        $possibleKeys = ['balanceProof', 'balancePaymentProof', 'paymentFile', 'depositProof', 'file'];
        foreach ($possibleKeys as $key) {
            if (isset($files[$key]) && $files[$key]['error'] === UPLOAD_ERR_OK) {
                $fileKey = $key;
                break;
            }
        }

        if ($fileKey === null) {
            throw new Exception('Balance payment proof file is required');
        }

        // 예약 확인 및 상태 검증
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'waiting_balance') {
            throw new Exception('Cannot upload balance payment at this stage. Current status: ' . $booking['bookingStatus']);
        }

        // 파일 업로드
        $uploadDir = __DIR__ . '/../../../uploads/deposits/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($files[$fileKey]['name'], PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension);
        $extension = $extension ? '.' . $extension : '';
        $fileName = 'balance_' . $bookingId . '_' . time() . '_' . uniqid() . $extension;
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($files[$fileKey]['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload file');
        }

        $filePath = 'uploads/deposits/' . $fileName;
        $uploadedAt = date('Y-m-d H:i:s');

        // 기존 파일 삭제
        if (!empty($booking['balanceFile'])) {
            $oldFile = __DIR__ . '/../../../' . $booking['balanceFile'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // DB 업데이트 - 재업로드 시 rejection 정보 초기화
        $sql = "UPDATE bookings SET
                balanceFile = ?,
                balanceUploadedAt = NOW(),
                balanceRejectedAt = NULL,
                balanceRejectionReason = NULL,
                bookingStatus = 'checking_balance'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $filePath, $bookingId);
        $stmt->execute();

        send_success_response(['filePath' => $filePath], 'Balance payment proof uploaded successfully');

    } catch (Exception $e) {
        send_error_response('Failed to upload balance payment proof: ' . $e->getMessage());
    }
}

/**
 * 선금 증빙 파일 삭제
 */
function removeDownPaymentFile($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        // 예약 확인
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // 파일 삭제
        if (!empty($booking['downPaymentFile'])) {
            $filePath = __DIR__ . '/../../../' . $booking['downPaymentFile'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // DB 업데이트 - 파일 정보 삭제 및 상태를 waiting으로 변경
        $sql = "UPDATE bookings SET
                downPaymentFile = NULL,
                downPaymentUploadedAt = NULL,
                bookingStatus = 'waiting_down_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();

        send_success_response(null, 'Down payment file removed successfully');

    } catch (Exception $e) {
        send_error_response('Failed to remove down payment file: ' . $e->getMessage());
    }
}

/**
 * 중도금 증빙 파일 삭제
 */
function removeAdvancePaymentFile($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        // 예약 확인
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // 파일 삭제
        if (!empty($booking['advancePaymentFile'])) {
            $filePath = __DIR__ . '/../../../' . $booking['advancePaymentFile'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // DB 업데이트 - 파일 정보 삭제 및 상태를 waiting으로 변경
        $sql = "UPDATE bookings SET
                advancePaymentFile = NULL,
                advancePaymentUploadedAt = NULL,
                bookingStatus = 'waiting_advance_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();

        send_success_response(null, 'Advance payment file removed successfully');

    } catch (Exception $e) {
        send_error_response('Failed to remove advance payment file: ' . $e->getMessage());
    }
}

/**
 * 잔금 증빙 파일 삭제
 */
function removeBalanceFile($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        // 예약 확인
        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // 파일 삭제
        if (!empty($booking['balanceFile'])) {
            $filePath = __DIR__ . '/../../../' . $booking['balanceFile'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // DB 업데이트 - 파일 정보 삭제 및 상태를 waiting으로 변경
        $sql = "UPDATE bookings SET
                balanceFile = NULL,
                balanceUploadedAt = NULL,
                bookingStatus = 'waiting_balance'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();

        send_success_response(null, 'Balance file removed successfully');

    } catch (Exception $e) {
        send_error_response('Failed to remove balance file: ' . $e->getMessage());
    }
}

/**
 * 여행자 정보 업데이트
 */
function updateTravelers($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $travelers = $input['travelers'] ?? [];
        $mainTravelerId = $input['mainTravelerId'] ?? null;

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        if (empty($travelers)) {
            throw new Exception('No traveler data provided');
        }

        // booking_travelers 테이블 컬럼 확인
        $travelerColumns = [];
        $travelerColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers");
        if ($travelerColumnCheck) {
            while ($col = $travelerColumnCheck->fetch_assoc()) {
                $travelerColumns[] = strtolower($col['Field']);
            }
        }

        // transactNo 또는 bookingId 컬럼 확인
        $useTransactNo = in_array('transactno', $travelerColumns);
        $bookingIdColumn = $useTransactNo ? 'transactNo' : 'bookingId';

        // firstName/fName 컬럼 확인
        $useFirstName = in_array('firstname', $travelerColumns);
        $firstNameColumn = $useFirstName ? 'firstName' : 'fName';
        $lastNameColumn = $useFirstName ? 'lastName' : 'lName';

        // bookingTravelerId 컬럼 확인
        $hasTravelerId = in_array('bookingtravelerid', $travelerColumns);

        $conn->begin_transaction();

        // Reset all isMainTraveler first if mainTravelerId is provided
        if ($mainTravelerId && in_array('ismaintraveler', $travelerColumns)) {
            $resetStmt = $conn->prepare("UPDATE booking_travelers SET isMainTraveler = 0 WHERE $bookingIdColumn = ?");
            if ($resetStmt) {
                $resetStmt->bind_param('s', $bookingId);
                $resetStmt->execute();
                $resetStmt->close();
            }
        }

        foreach ($travelers as $traveler) {
            $travelerId = $traveler['id'] ?? '';
            $originalFirstName = $traveler['originalFirstName'] ?? '';
            $originalLastName = $traveler['originalLastName'] ?? '';
            $travelerType = $traveler['travelerType'] ?? 'adult';

            // 업데이트할 필드들
            $updateFields = [];
            $updateValues = [];
            $types = '';

            // 각 필드 매핑
            $fieldMappings = [
                'title' => 'title',
                'firstName' => $firstNameColumn,
                'lastName' => $lastNameColumn,
                'gender' => 'gender',
                'travelerType' => 'travelerType',
                'dateOfBirth' => 'birthDate',
                'nationality' => 'nationality',
                'passportNumber' => 'passportNumber',
                'passportIssueDate' => 'passportIssueDate',
                'passportExpiryDate' => in_array('passportexpiry', $travelerColumns) ? 'passportExpiry' : 'passportExpiryDate',
                'visaRequired' => in_array('visastatus', $travelerColumns) ? 'visaStatus' : 'visaRequired'
            ];

            foreach ($fieldMappings as $inputField => $dbField) {
                if (isset($traveler[$inputField])) {
                    // DB 컬럼이 실제로 존재하는지 확인
                    if (in_array(strtolower($dbField), $travelerColumns)) {
                        $updateFields[] = "$dbField = ?";

                        // visaRequired/visaStatus 특별 처리
                        if ($inputField === 'visaRequired') {
                            if (in_array('visastatus', $travelerColumns)) {
                                $updateValues[] = $traveler[$inputField] == 1 ? 'applied' : 'not_required';
                                $types .= 's';
                            } else {
                                $updateValues[] = (int)$traveler[$inputField];
                                $types .= 'i';
                            }
                        } elseif ($inputField === 'dateOfBirth' || $inputField === 'passportIssueDate' || $inputField === 'passportExpiryDate') {
                            // YYYYMMDD 형식을 YYYY-MM-DD로 변환
                            $dateValue = $traveler[$inputField];
                            if (strlen($dateValue) === 8 && is_numeric($dateValue)) {
                                $dateValue = substr($dateValue, 0, 4) . '-' . substr($dateValue, 4, 2) . '-' . substr($dateValue, 6, 2);
                            }
                            $updateValues[] = $dateValue;
                            $types .= 's';
                        } else {
                            $updateValues[] = $traveler[$inputField];
                            $types .= 's';
                        }
                    }
                }
            }

            // Set main traveler if this is the selected one
            if ($mainTravelerId && $travelerId == $mainTravelerId && in_array('ismaintraveler', $travelerColumns)) {
                $updateFields[] = "isMainTraveler = 1";
            }

            if (empty($updateFields)) {
                continue;
            }

            // WHERE 조건: travelerId가 있으면 ID로, 없으면 이름으로
            if ($hasTravelerId && !empty($travelerId)) {
                $updateValues[] = $travelerId;
                $types .= 'i';
                $sql = "UPDATE booking_travelers SET " . implode(', ', $updateFields) .
                       " WHERE bookingTravelerId = ?";
            } else {
                $updateValues[] = $bookingId;
                $types .= 's';
                $updateValues[] = $originalFirstName;
                $types .= 's';
                $updateValues[] = $originalLastName;
                $types .= 's';
                $sql = "UPDATE booking_travelers SET " . implode(', ', $updateFields) .
                       " WHERE $bookingIdColumn = ? AND $firstNameColumn = ? AND $lastNameColumn = ?";
            }

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare traveler update: ' . $conn->error);
            }

            $stmt->bind_param($types, ...$updateValues);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update traveler: ' . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();
        send_success_response(null, 'Traveler information updated successfully');

    } catch (Exception $e) {
        if ($conn) {
            $conn->rollback();
        }
        error_log('updateTravelers error: ' . $e->getMessage());
        send_error_response('Failed to update traveler information: ' . $e->getMessage());
    }
}

/**
 * 여권 이미지 삭제
 */
function deletePassportImage($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $travelerIndex = $input['travelerIndex'] ?? 0;
        $firstName = $input['firstName'] ?? '';
        $lastName = $input['lastName'] ?? '';

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        // booking_travelers 테이블 컬럼 확인
        $travelerColumns = [];
        $travelerColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers");
        if ($travelerColumnCheck) {
            while ($col = $travelerColumnCheck->fetch_assoc()) {
                $travelerColumns[] = strtolower($col['Field']);
            }
        }

        $useTransactNo = in_array('transactno', $travelerColumns);
        $bookingIdColumn = $useTransactNo ? 'transactNo' : 'bookingId';

        $useFirstName = in_array('firstname', $travelerColumns);
        $firstNameColumn = $useFirstName ? 'firstName' : 'fName';
        $lastNameColumn = $useFirstName ? 'lastName' : 'lName';

        // 현재 여권 이미지 경로 조회
        $sql = "SELECT passportImage FROM booking_travelers
                WHERE $bookingIdColumn = ? AND $firstNameColumn = ? AND $lastNameColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $bookingId, $firstName, $lastName);
        $stmt->execute();
        $result = $stmt->get_result();
        $traveler = $result->fetch_assoc();
        $stmt->close();

        // 기존 파일 삭제
        if (!empty($traveler['passportImage'])) {
            $imagePath = $traveler['passportImage'];
            // 상대 경로 처리
            if (!str_starts_with($imagePath, '/') && !str_starts_with($imagePath, 'http')) {
                $fullPath = __DIR__ . '/../../../' . $imagePath;
            } else {
                $fullPath = __DIR__ . '/../../..' . $imagePath;
            }
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        // DB 업데이트 - passportImage NULL로 설정
        $sql = "UPDATE booking_travelers SET passportImage = NULL
                WHERE $bookingIdColumn = ? AND $firstNameColumn = ? AND $lastNameColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $bookingId, $firstName, $lastName);
        $stmt->execute();
        $stmt->close();

        send_success_response(null, 'Passport image deleted successfully');

    } catch (Exception $e) {
        error_log('deletePassportImage error: ' . $e->getMessage());
        send_error_response('Failed to delete passport image: ' . $e->getMessage());
    }
}

/**
 * 여권 이미지 업로드
 */
function uploadPassportImage($conn, $input) {
    try {
        // FormData로 전송된 경우 $_POST와 $_FILES 사용
        $bookingId = $_POST['bookingId'] ?? $input['bookingId'] ?? '';
        $travelerIndex = $_POST['travelerIndex'] ?? $input['travelerIndex'] ?? 0;
        $firstName = $_POST['firstName'] ?? $input['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? $input['lastName'] ?? '';

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        if (!isset($_FILES['passportImage']) || $_FILES['passportImage']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Passport image file is required');
        }

        $file = $_FILES['passportImage'];

        // 파일 크기 체크 (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('File size must be less than 5MB');
        }

        // 파일 타입 체크
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and PDF are allowed.');
        }

        // 업로드 디렉토리 설정
        $uploadDir = __DIR__ . '/../../../uploads/passports/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 파일명 생성
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'passport_' . $bookingId . '_' . $travelerIndex . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // 파일 이동
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception('Failed to upload file');
        }

        // DB 경로 (상대 경로)
        $dbPath = '/uploads/passports/' . $fileName;

        // booking_travelers 테이블 컬럼 확인
        $travelerColumns = [];
        $travelerColumnCheck = $conn->query("SHOW COLUMNS FROM booking_travelers");
        if ($travelerColumnCheck) {
            while ($col = $travelerColumnCheck->fetch_assoc()) {
                $travelerColumns[] = strtolower($col['Field']);
            }
        }

        $useTransactNo = in_array('transactno', $travelerColumns);
        $bookingIdColumn = $useTransactNo ? 'transactNo' : 'bookingId';

        $useFirstName = in_array('firstname', $travelerColumns);
        $firstNameColumn = $useFirstName ? 'firstName' : 'fName';
        $lastNameColumn = $useFirstName ? 'lastName' : 'lName';

        // 기존 이미지 삭제
        $sql = "SELECT passportImage FROM booking_travelers
                WHERE $bookingIdColumn = ? AND $firstNameColumn = ? AND $lastNameColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $bookingId, $firstName, $lastName);
        $stmt->execute();
        $result = $stmt->get_result();
        $traveler = $result->fetch_assoc();
        $stmt->close();

        if (!empty($traveler['passportImage'])) {
            $oldPath = __DIR__ . '/../../..' . $traveler['passportImage'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // DB 업데이트
        $sql = "UPDATE booking_travelers SET passportImage = ?
                WHERE $bookingIdColumn = ? AND $firstNameColumn = ? AND $lastNameColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $dbPath, $bookingId, $firstName, $lastName);
        $stmt->execute();
        $stmt->close();

        send_success_response(['passportImage' => $dbPath], 'Passport image uploaded successfully');

    } catch (Exception $e) {
        error_log('uploadPassportImage error: ' . $e->getMessage());
        send_error_response('Failed to upload passport image: ' . $e->getMessage());
    }
}

/**
 * 선금 승인 (관리자)
 */
function confirmDownPayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $adminId = $input['adminId'] ?? 0; // 실제 세션에서 가져와야 함

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_down_payment') {
            throw new Exception('Cannot confirm down payment at this stage');
        }

        $confirmedAt = date('Y-m-d H:i:s');
        // advancePaymentDueDate는 uploadDownPayment에서 이미 설정됨 (업로드일 + 30일)

        // DB 업데이트
        $sql = "UPDATE bookings SET
                downPaymentConfirmedAt = ?,
                downPaymentConfirmedBy = ?,
                bookingStatus = 'waiting_advance_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $confirmedAt, $adminId, $bookingId);
        $stmt->execute();

        send_success_response([], 'Down payment confirmed successfully');

    } catch (Exception $e) {
        send_error_response('Failed to confirm down payment: ' . $e->getMessage());
    }
}

/**
 * 선금 거부 (관리자)
 */
function rejectDownPayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $reason = $input['reason'] ?? '';

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_down_payment') {
            throw new Exception('Cannot reject down payment at this stage');
        }

        $rejectedAt = date('Y-m-d H:i:s');

        // DB 업데이트 - 파일은 유지하고 상태만 변경
        $sql = "UPDATE bookings SET
                downPaymentRejectedAt = ?,
                downPaymentRejectionReason = ?,
                bookingStatus = 'waiting_down_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $rejectedAt, $reason, $bookingId);
        $stmt->execute();

        send_success_response([], 'Down payment rejected');

    } catch (Exception $e) {
        send_error_response('Failed to reject down payment: ' . $e->getMessage());
    }
}

/**
 * 중도금 승인 (관리자)
 */
function confirmAdvancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $adminId = $input['adminId'] ?? 0;

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_advance_payment') {
            throw new Exception('Cannot confirm advance payment at this stage');
        }

        $confirmedAt = date('Y-m-d H:i:s');

        // DB 업데이트
        $sql = "UPDATE bookings SET
                advancePaymentConfirmedAt = ?,
                advancePaymentConfirmedBy = ?,
                bookingStatus = 'waiting_balance'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $confirmedAt, $adminId, $bookingId);
        $stmt->execute();

        send_success_response([], 'Advance payment confirmed successfully');

    } catch (Exception $e) {
        send_error_response('Failed to confirm advance payment: ' . $e->getMessage());
    }
}

/**
 * 중도금 거부 (관리자)
 */
function rejectAdvancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $reason = $input['reason'] ?? '';

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_advance_payment') {
            throw new Exception('Cannot reject advance payment at this stage');
        }

        $rejectedAt = date('Y-m-d H:i:s');

        // DB 업데이트
        $sql = "UPDATE bookings SET
                advancePaymentRejectedAt = ?,
                advancePaymentRejectionReason = ?,
                bookingStatus = 'waiting_advance_payment'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $rejectedAt, $reason, $bookingId);
        $stmt->execute();

        send_success_response([], 'Advance payment rejected');

    } catch (Exception $e) {
        send_error_response('Failed to reject advance payment: ' . $e->getMessage());
    }
}

/**
 * 잔금 승인 (관리자)
 */
function confirmBalancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $adminId = $input['adminId'] ?? 0;

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_balance') {
            throw new Exception('Cannot confirm balance payment at this stage');
        }

        $confirmedAt = date('Y-m-d H:i:s');

        // DB 업데이트 - 모든 결제 완료, 예약 확정
        $sql = "UPDATE bookings SET
                balanceConfirmedAt = ?,
                balanceConfirmedBy = ?,
                bookingStatus = 'confirmed',
                paymentStatus = 'paid'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $confirmedAt, $adminId, $bookingId);
        $stmt->execute();

        send_success_response([], 'Balance payment confirmed successfully. Booking is now confirmed.');

    } catch (Exception $e) {
        send_error_response('Failed to confirm balance payment: ' . $e->getMessage());
    }
}

/**
 * 잔금 거부 (관리자)
 */
function rejectBalancePayment($conn, $input) {
    try {
        $bookingId = $input['bookingId'] ?? '';
        $reason = $input['reason'] ?? '';

        if (empty($bookingId)) {
            throw new Exception('Booking ID is required');
        }

        $booking = getBookingById($conn, $bookingId);
        if (!$booking) {
            throw new Exception('Booking not found');
        }

        if ($booking['bookingStatus'] !== 'checking_balance') {
            throw new Exception('Cannot reject balance payment at this stage');
        }

        $rejectedAt = date('Y-m-d H:i:s');

        // DB 업데이트
        $sql = "UPDATE bookings SET
                balanceRejectedAt = ?,
                balanceRejectionReason = ?,
                bookingStatus = 'waiting_balance'
                WHERE bookingId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $rejectedAt, $reason, $bookingId);
        $stmt->execute();

        send_success_response([], 'Balance payment rejected');

    } catch (Exception $e) {
        send_error_response('Failed to reject balance payment: ' . $e->getMessage());
    }
}

/**
 * Helper: bookingId로 예약 정보 조회
 */
function getBookingById($conn, $bookingId) {
    $sql = "SELECT * FROM bookings WHERE bookingId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * 사용자 정보 조회 (세션 기반)
 */
function getUserInfo() {
    // 세션에서 사용자 정보 가져오기
    $userInfo = [
        'accountId' => $_SESSION['accountId'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'displayName' => $_SESSION['displayName'] ?? '',
        'accountType' => $_SESSION['accountType'] ?? '',
        'languagePreference' => $_SESSION['languagePreference'] ?? 'eng',
    ];

    // Agent 계정인 경우 추가 정보
    if ($_SESSION['accountType'] === 'agent') {
        $userInfo['agentId'] = $_SESSION['agentId'] ?? null;
        $userInfo['agentType'] = $_SESSION['agentType'] ?? '';
        $userInfo['agentRole'] = $_SESSION['agentRole'] ?? '';
        $userInfo['role'] = $_SESSION['agentRole'] ?? 'Agent'; // 표시용
    }

    // Employee 계정인 경우 추가 정보
    if ($_SESSION['accountType'] === 'employee') {
        $userInfo['employeeId'] = $_SESSION['employeeId'] ?? null;
        $userInfo['position'] = $_SESSION['position'] ?? '';
        $userInfo['branch'] = $_SESSION['branch'] ?? '';
        $userInfo['role'] = $_SESSION['position'] ?? 'Employee'; // 표시용
    }

    send_success_response($userInfo, 'User info retrieved successfully');
}

/**
 * 비밀번호 변경
 */
function changePassword($conn, $input) {
    // 디버깅 로그
    error_log("=== changePassword START ===");
    error_log("Input: " . print_r($input, true));
    error_log("Session accountId: " . ($_SESSION['accountId'] ?? 'NOT SET'));

    try {
        $currentPassword = $input['currentPassword'] ?? '';
        $newPassword = $input['newPassword'] ?? '';

        error_log("Has currentPassword: " . (empty($currentPassword) ? 'NO' : 'YES'));
        error_log("Has newPassword: " . (empty($newPassword) ? 'NO' : 'YES'));

        if (empty($currentPassword) || empty($newPassword)) {
            throw new Exception('Current password and new password are required');
        }

        if (strlen($newPassword) < 8) {
            throw new Exception('New password must be at least 8 characters long');
        }

        $accountId = $_SESSION['accountId'];

        // 현재 비밀번호 확인
        $sql = "SELECT password FROM accounts WHERE accountId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            error_log("User not found for accountId: " . $accountId);
            throw new Exception('User not found');
        }

        error_log("User found, verifying password...");

        // 현재 비밀번호 검증
        if (!password_verify($currentPassword, $user['password'])) {
            error_log("Password verification FAILED");
            throw new Exception('Current password is incorrect');
        }

        error_log("Password verified OK");

        // 새 비밀번호 해시
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        error_log("New password hashed");

        // 비밀번호 업데이트
        $updateSql = "UPDATE accounts SET password = ? WHERE accountId = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $hashedPassword, $accountId);
        $updateStmt->execute();

        $affectedRows = $updateStmt->affected_rows;
        error_log("UPDATE executed, affected_rows: " . $affectedRows);

        if ($affectedRows > 0) {
            error_log("Password changed successfully!");
            send_success_response([], 'Password changed successfully');
        } else {
            error_log("UPDATE failed - no rows affected");
            throw new Exception('Failed to update password - no rows affected');
        }

    } catch (Exception $e) {
        error_log("changePassword ERROR: " . $e->getMessage());
        send_error_response($e->getMessage(), 400);
    }
}

?>
