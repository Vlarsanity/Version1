<?php
/**
 * Logout API
 * 세션을 종료하고 로그아웃 처리
 */

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

// GET 요청은 무시 (POST만 처리)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // GET 요청이 오면 단순히 성공 응답 (아무것도 안함)
    echo json_encode([
        'success' => false,
        'message' => 'Use POST method to logout'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 세션 변수 모두 제거
    $_SESSION = array();

    // 세션 쿠키 삭제
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // 세션 파괴
    session_destroy();

    // 성공 응답
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Logout failed: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
