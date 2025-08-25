<?php
include 'db.php';
requireLogin();
if (!isManager()) {
    header('Location: index.php');
    exit;
}

// Weekly Occupancy: % rooms booked last week
$week_start = date('Y-m-d', strtotime('-7 days'));
$stmt = $pdo->prepare("SELECT COUNT(*) as booked FROM reservations WHERE check_in_date >= ?");
$stmt->execute([$week_start]);
$booked_week = $stmt->fetch()['booked'];

$stmt = $pdo->query("SELECT COUNT(*) as total_rooms FROM rooms");
$total_rooms = $stmt->fetch()['total_rooms'];
$occupancy_week = $total_rooms > 0 ? round(($booked_week / $total_rooms) * 100, 2) : 0;

// Monthly Occupancy
$month_start = date('Y-m-d', strtotime('-30 days'));
$stmt = $pdo->prepare("SELECT COUNT(*) as booked FROM reservations WHERE check_in_date >= ?");
$stmt->execute([$month_start]);
$booked_month = $stmt->fetch()['booked'];
$occupancy_month = $total_rooms > 0 ? round(($booked_month / $total_rooms) * 100, 2) : 0;

// Weekly Sales
$stmt = $pdo->prepare("SELECT SUM(total_amount) as sales FROM payments p JOIN reservations r ON p.reservation_id = r.id WHERE r.check_out_date >= ?");
$stmt->execute([$week_start]);
$sales_week = $stmt->fetch()['sales'] ?? 0;

// Monthly Sales
$stmt = $pdo->prepare("SELECT SUM(total_amount) as sales FROM payments p JOIN reservations r ON p.reservation_id = r.id WHERE r.check_out_date >= ?");
$stmt->execute([$month_start]);
$sales_month = $stmt->fetch()['sales'] ?? 0;

$pageTitle = 'Reports';
include 'header.php';
?>

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Reports</h1>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-md shadow-sm">
                <h2 class="text-lg font-medium mb-2">Weekly (Last 7 Days)</h2>
                <p class="text-sm text-gray-600">Occupancy: <span class="font-semibold text-indigo-600"><?php echo $occupancy_week; ?>%</span></p>
                <p class="text-sm text-gray-600">Sales: <span class="font-semibold text-indigo-600">$<?php echo $sales_week; ?></span></p>
            </div>
            <div class="bg-white p-4 rounded-md shadow-sm">
                <h2 class="text-lg font-medium mb-2">Monthly (Last 30 Days)</h2>
                <p class="text-sm text-gray-600">Occupancy: <span class="font-semibold text-indigo-600"><?php echo $occupancy_month; ?>%</span></p>
                <p class="text-sm text-gray-600">Sales: <span class="font-semibold text-indigo-600">$<?php echo $sales_month; ?></span></p>
            </div>
        </section>

        <div class="mt-6">
            <a class="text-sm text-gray-600 hover:underline" href="index.php">Back</a>
        </div>
    </div>

<?php include 'footer.php'; ?>