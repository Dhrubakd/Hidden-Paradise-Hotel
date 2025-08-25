<?php
include 'db.php';
requireLogin();

// Dashboard stats
$stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms");
$total_rooms = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as booked FROM rooms WHERE status = 'Booked'");
$booked_rooms = $stmt->fetch()['booked'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as reservations FROM reservations");
$total_reservations = $stmt->fetch()['reservations'] ?? 0;

$occupancy = $total_rooms > 0 ? round(($booked_rooms / $total_rooms) * 100, 2) : 0;

$pageTitle = 'Dashboard';
include 'header.php';
?>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Rooms</div>
            <div class="mt-2 text-2xl font-bold"><?php echo $total_rooms; ?></div>
            <div class="text-sm text-gray-600 mt-1">Total rooms in inventory</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Booked</div>
            <div class="mt-2 text-2xl font-bold text-indigo-600"><?php echo $booked_rooms; ?></div>
            <div class="text-sm text-gray-600 mt-1">Currently booked rooms</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Occupancy</div>
            <div class="mt-2 text-2xl font-bold"><?php echo $occupancy; ?>%</div>
            <div class="text-sm text-gray-600 mt-1">Current occupancy</div>
        </div>
    </section>

    <section class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold mb-2">Recent Reservations</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <?php
                $stmt = $pdo->query("SELECT r.id, g.name, ro.room_number, r.check_in_date FROM reservations r JOIN guests g ON r.guest_id = g.id JOIN rooms ro ON r.room_id = ro.id ORDER BY r.id DESC LIMIT 5");
                $recent = $stmt->fetchAll();
                foreach ($recent as $r) {
                    echo '<li class="flex justify-between"><span>' . htmlspecialchars($r['name'] . ' - Room ' . $r['room_number']) . '</span><span class="text-gray-500">' . htmlspecialchars($r['check_in_date']) . '</span></li>';
                }
                ?>
            </ul>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold mb-2">Quick Actions</h3>
            <div class="space-y-2">
                <a class="block bg-indigo-600 text-white py-2 px-3 rounded-md hover:bg-indigo-700" href="checkin.php">New Check-In</a>
                <a class="block bg-indigo-100 text-indigo-700 py-2 px-3 rounded-md hover:bg-indigo-200" href="checkout.php">Process Check-Out</a>
                <?php if (isManager()) { ?>
                    <a class="block bg-green-100 text-green-700 py-2 px-3 rounded-md hover:bg-green-200" href="add_room.php">Add Room</a>
                    <a class="block bg-yellow-100 text-yellow-700 py-2 px-3 rounded-md hover:bg-yellow-200" href="edit_room.php">Manage Rooms</a>
                <?php } ?>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>