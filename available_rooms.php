<?php
// Returns JSON list of rooms available for the given date range
include 'db.php';
requireLogin();

// accept GET or POST
$check_in = $_REQUEST['check_in'] ?? null;
$check_out = $_REQUEST['check_out'] ?? null;

header('Content-Type: application/json; charset=utf-8');

if (!$check_in || !$check_out) {
    echo json_encode(['error' => 'Missing dates']);
    exit;
}

try {
    // Compute availability purely from reservations/payments and room records.
    // A room is available if it exists in `rooms` and it has NO unpaid reservation overlapping the requested range.
    $stmt = $pdo->prepare("SELECT r.id, r.room_number, r.type, r.price, r.status
        FROM rooms r
        WHERE NOT EXISTS (
            SELECT 1 FROM reservations res
            LEFT JOIN payments p ON p.reservation_id = res.id AND p.paid = 1
            WHERE res.room_id = r.id AND p.id IS NULL AND NOT (res.check_out_date <= ? OR res.check_in_date >= ?)
        )");
    $stmt->execute([$check_in, $check_out]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['rooms' => $rooms]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
