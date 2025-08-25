<?php
include 'db.php';
requireLogin();

// Get active reservations (exclude reservations that already have a paid payment)
$stmt = $pdo->query("SELECT r.id, g.name, ro.room_number, r.check_in_date, r.check_out_date 
                     FROM reservations r 
                     JOIN guests g ON r.guest_id = g.id 
                     JOIN rooms ro ON r.room_id = ro.id
                     LEFT JOIN payments p ON p.reservation_id = r.id AND p.paid = 1
                     WHERE p.id IS NULL");
$reservations = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        header('Location: checkout.php?error=' . urlencode('Invalid CSRF token'));
        exit;
    }

    $res_id = (int)($_POST['res_id'] ?? 0);

    if ($res_id <= 0) {
        header('Location: checkout.php?error=' . urlencode('Invalid reservation'));
        exit;
    }

    // Prevent duplicate payment: check if a paid payment already exists for this reservation
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE reservation_id = ? AND paid = 1");
    $stmt->execute([$res_id]);
    $already = (int)$stmt->fetchColumn();
    if ($already > 0) {
        header('Location: checkout.php?error=' . urlencode('This reservation has already been checked out.'));
        exit;
    }

    // Fetch reservation and services and perform payment + room update inside a transaction
    try {
        $pdo->beginTransaction();

        // Get reservation details
        $stmt = $pdo->prepare("SELECT ro.price, DATEDIFF(r.check_out_date, r.check_in_date) as days, r.room_id 
                               FROM reservations r JOIN rooms ro ON r.room_id = ro.id WHERE r.id = ? FOR UPDATE");
        $stmt->execute([$res_id]);
        $res = $stmt->fetch();
        if (!$res) {
            $pdo->rollBack();
            header('Location: checkout.php?error=' . urlencode('Reservation not found'));
            exit;
        }

        // Get services total
        $stmt = $pdo->prepare("SELECT SUM(s.price) as serv_total 
                               FROM reservation_services rs JOIN services s ON rs.service_id = s.id WHERE rs.reservation_id = ?");
        $stmt->execute([$res_id]);
        $serv_total = $stmt->fetch()['serv_total'] ?? 0;

        $room_total = $res['price'] * max(1, (int)$res['days']);
        $total = $room_total + $serv_total;

        // Insert payment
        $stmt = $pdo->prepare("INSERT INTO payments (reservation_id, total_amount, paid) VALUES (?, ?, 1)");
        $stmt->execute([$res_id, $total]);

        // Update room status: if there are no other unpaid reservations for the room, mark Available,
        // otherwise keep/set Booked. This keeps `rooms.status` in sync after checkout.
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations res LEFT JOIN payments p ON p.reservation_id = res.id AND p.paid = 1 WHERE res.room_id = ? AND res.id != ? AND p.id IS NULL");
        $stmt->execute([$res['room_id'], $res_id]);
        $unpaidCount = (int)$stmt->fetchColumn();
        if ($unpaidCount === 0) {
            $stmt = $pdo->prepare("UPDATE rooms SET status = 'Available' WHERE id = ?");
            $stmt->execute([$res['room_id']]);
        } else {
            // There are other unpaid reservations for this room, keep it booked.
            $stmt = $pdo->prepare("UPDATE rooms SET status = 'Booked' WHERE id = ?");
            $stmt->execute([$res['room_id']]);
        }

        $pdo->commit();

        // Post-Redirect-Get: avoid duplicate submissions on refresh
        header('Location: checkout.php?success=' . urlencode('Checkout successful. Total: $' . number_format($total, 2)));
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header('Location: checkout.php?error=' . urlencode('Checkout failed: ' . $e->getMessage()));
        exit;
    }
}

// Read any messages from query params (after redirect)
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<?php $pageTitle = 'Check-Out'; include 'header.php'; ?>

    <div class="bg-white p-6 rounded-md shadow-sm max-w-6xl">
    <h1 class="text-2xl font-semibold mb-4">Check-Out</h1>
    <?php if (!empty($error)) echo "<div class='mb-4 text-red-600'>".htmlspecialchars($error)."</div>"; ?>
    <?php if (!empty($success)) echo "<div class='mb-4 text-green-600'>".htmlspecialchars($success)."</div>"; ?>

        <form method="POST">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700">Select Reservation</label>
                <select class="mt-1 block w-full rounded-md border-gray-300" name="res_id" required>
                    <?php if (count($reservations) === 0): ?>
                        <option value="">No reservations available for checkout</option>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): ?>
                            <option value="<?php echo $res['id']; ?>"><?php echo htmlspecialchars($res['name'] . ' - Room ' . $res['room_number']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mt-4">
                <button class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700" <?php echo count($reservations) === 0 ? 'disabled' : ''; ?>>Checkout</button>
                <a class="ml-4 text-sm text-gray-600 hover:underline" href="index.php">Back</a>
            </div>
        </form>
    </div>

<?php include 'footer.php'; ?>