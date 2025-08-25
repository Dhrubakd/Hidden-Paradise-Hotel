<?php
require_once 'db.php';
requireLogin();
if (!isManager()) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

// db.php exposes $pdo in the global scope
global $pdo;

// Reconcile rooms.status with reservation/payment state
// For each room, if there is any active reservation (current date between check_in and check_out exclusive)
// and not fully paid, mark room.status = 'Booked', else mark 'Available'.

$now = date('Y-m-d');

// Mark rooms with overlapping unpaid reservations as Booked
$updateBooked = $pdo->prepare(
    "UPDATE rooms r
     SET r.status = 'Booked'
     WHERE EXISTS (
         SELECT 1 FROM reservations res
         LEFT JOIN payments p ON p.reservation_id = res.id
         WHERE res.room_id = r.id
           AND NOT (res.check_out <= :now OR res.check_in >= :now)
           AND (p.id IS NULL)
     )"
);
$updateBooked->execute(['now' => $now]);

// Mark rooms without active unpaid reservations as Available
$updateAvail = $pdo->prepare(
    "UPDATE rooms r
     SET r.status = 'Available'
     WHERE NOT EXISTS (
         SELECT 1 FROM reservations res
         LEFT JOIN payments p ON p.reservation_id = res.id
         WHERE res.room_id = r.id
           AND NOT (res.check_out <= :now OR res.check_in >= :now)
           AND (p.id IS NULL)
     )"
);
$updateAvail->execute(['now' => $now]);

$updated = $updateBooked->rowCount() + $updateAvail->rowCount();

header('Location: users.php?msg=' . urlencode("Reconciled rooms. SQL affected: $updated"));
exit;
