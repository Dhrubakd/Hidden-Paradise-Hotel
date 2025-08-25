<?php
include 'db.php';
requireLogin();
if (!isManager()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, type, price) VALUES (?, ?, ?)");
    $stmt->execute([$room_number, $type, $price]);
    $success = 'Room added';
}
?>
<?php $pageTitle = 'Add Room'; include 'header.php'; ?>

    <div class="bg-white p-6 rounded-md shadow-sm max-w-6xl">
        <h1 class="text-2xl font-semibold mb-4">Add Room</h1>
        <?php if (isset($success)) echo "<div class='mb-4 text-green-600'>".htmlspecialchars($success)."</div>"; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Room Number</label>
                <input class="mt-1 block w-full rounded-md border-gray-300" type="text" name="room_number" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select class="mt-1 block w-full rounded-md border-gray-300" name="type" required>
                    <option>Single</option>
                    <option>Double</option>
                    <option>Suite</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Price</label>
                <input class="mt-1 block w-full rounded-md border-gray-300" type="number" step="0.01" name="price" required>
            </div>
            <div>
                <button class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Add</button>
                <a class="ml-4 text-sm text-gray-600 hover:underline" href="index.php">Back</a>
            </div>
        </form>
    </div>

<?php include 'footer.php'; ?>