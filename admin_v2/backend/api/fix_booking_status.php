<?php
/**
 * DB에 저장된 한글 상태 값을 영어로 변환하는 스크립트
 */

require_once __DIR__ . '/../../../backend/conn.php';

try {
    // bookingStatus 한글 → 영어 변환
    $statusMap = [
        '예약 확정' => 'confirmed',
        '여행 완료' => 'completed',
        '예약 취소' => 'cancelled',
        '환불 완료' => 'refunded',
        '선금 확인 전' => 'pending_deposit',
        '잔금 확인 전' => 'pending_balance'
    ];
    
    foreach ($statusMap as $korean => $english) {
        $sql = "UPDATE bookings SET bookingStatus = ? WHERE bookingStatus = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $english, $korean);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        if ($affected > 0) {
            echo "Updated $affected rows: '$korean' → '$english'\n";
        }
    }
    
    // paymentStatus 한글 → 영어 변환
    $paymentStatusMap = [
        '선금 확인 전' => 'pending',
        '잔금 확인 전' => 'partial',
        '선금 확인' => 'partial',
        '전액 확인' => 'paid',
        '결제 완료' => 'paid'
    ];
    
    foreach ($paymentStatusMap as $korean => $english) {
        $sql = "UPDATE bookings SET paymentStatus = ? WHERE paymentStatus = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $english, $korean);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        if ($affected > 0) {
            echo "Updated $affected rows: paymentStatus '$korean' → '$english'\n";
        }
    }
    
    echo "Status conversion completed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

