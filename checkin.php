<?php
include 'db.php';
requireLogin();

// Get available rooms initially (status = 'Available'); JS will update based on dates
$stmt = $pdo->query("SELECT * FROM rooms WHERE status = 'Available'");
$rooms = $stmt->fetchAll();

// Get services
$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $selected_services = isset($_POST['services']) ? $_POST['services'] : [];

    // Validate dates
    if (new DateTime($check_out) <= new DateTime($check_in)) {
        $error = 'Check-out must be after check-in';
    } else {
        // Check availability: two date ranges (A = existing, B = requested) overlap
        // if NOT (B.end <= A.start OR B.start >= A.end)
    // Ignore reservations that already have a paid payment (checked-out)
    $stmt = $pdo->prepare("SELECT r.id, r.guest_id, r.check_in_date, r.check_out_date 
                   FROM reservations r 
                   LEFT JOIN payments p ON p.reservation_id = r.id AND p.paid = 1
                   WHERE r.room_id = ? AND p.id IS NULL AND NOT (r.check_out_date <= ? OR r.check_in_date >= ?) LIMIT 1");
    $stmt->execute([$room_id, $check_in, $check_out]);
        $conflict = $stmt->fetch();
        if ($conflict) {
            // get guest name for clearer message if available
            $guestName = 'Unknown guest';
            if (!empty($conflict['guest_id'])) {
                $g = $pdo->prepare('SELECT name FROM guests WHERE id = ?');
                $g->execute([$conflict['guest_id']]);
                $gname = $g->fetchColumn();
                if ($gname) $guestName = $gname;
            }
            $error = sprintf("Room not available for these dates. Existing booking: %s from %s to %s.", htmlspecialchars($guestName), $conflict['check_in_date'], $conflict['check_out_date']);
        } else {
            // Insert guest
            $stmt = $pdo->prepare("INSERT INTO guests (name, contact) VALUES (?, ?)");
            $stmt->execute([$name, $contact]);
            $guest_id = $pdo->lastInsertId();

            // Insert reservation
            $stmt = $pdo->prepare("INSERT INTO reservations (guest_id, room_id, check_in_date, check_out_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$guest_id, $room_id, $check_in, $check_out]);
            $res_id = $pdo->lastInsertId();

            // Insert services
            foreach ($selected_services as $serv_id) {
                $stmt = $pdo->prepare("INSERT INTO reservation_services (reservation_id, service_id) VALUES (?, ?)");
                $stmt->execute([$res_id, $serv_id]);
            }

            // Update room status
            $stmt = $pdo->prepare("UPDATE rooms SET status = 'Booked' WHERE id = ?");
            $stmt->execute([$room_id]);

            $success = 'Reservation successful';
        }
    }
}
?>
<?php $pageTitle = 'Check-In'; include 'header.php'; ?>
    <div class="bg-white p-6 rounded-md shadow-sm">
        <h1 class="text-2xl font-semibold mb-4">Check-In</h1>
        <?php if (isset($error)) echo "<div class='mb-4 text-red-600'>".htmlspecialchars($error)."</div>"; ?>
        <?php if (isset($success)) echo "<div class='mb-4 text-green-600'>".htmlspecialchars($success)."</div>"; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input class="mt-1 block w-full rounded-md border-gray-300" type="text" name="name" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Contact</label>
                <input class="mt-1 block w-full rounded-md border-gray-300" type="text" name="contact" required>
            </div>
            <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="room_id" class="mt-1 block w-full rounded-md border-gray-300" name="room_id" required>
                        <option value="">Select dates to load available rooms</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['room_number'] . ' - ' . $room['type'] . ' ($' . $room['price'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-In</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300" type="date" name="check_in" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-Out</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300" type="date" name="check_out" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Services</label>
                <div class="mt-2 space-y-2">
                <?php foreach ($services as $serv): ?>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="<?php echo $serv['id']; ?>" class="rounded border-gray-300">
                        <span class="ml-2 text-gray-700"><?php echo htmlspecialchars($serv['name'] . ' ($' . $serv['price'] . ')'); ?></span>
                    </label><br>
                <?php endforeach; ?>
                </div>
            </div>
            <div>
                <button class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Book</button>
                <a class="ml-4 text-sm text-gray-600 hover:underline" href="index.php">Back</a>
            </div>
        </form>
    </div>

<?php include 'footer.php'; ?>