<?php
include 'db.php';
requireLogin();
if (!isManager()) {
    header('Location: index.php');
    exit;
}

// Get rooms
$stmt = $pdo->query("SELECT * FROM rooms");
$rooms = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle delete action first (sent via JS-populated hidden field)
    if (!empty($_POST['delete_id'])) {
        $delId = (int)$_POST['delete_id'];
        // Check for any reservations referencing this room
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE room_id = ?");
        $stmt->execute([$delId]);
        $refCount = (int)$stmt->fetchColumn();
        if ($refCount > 0) {
            $error = 'Cannot delete room: there are ' . $refCount . ' reservation(s) referencing this room. Remove those first.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$delId]);
            $success = 'Room deleted';
        }
        // Refresh rooms
        $stmt = $pdo->query("SELECT * FROM rooms");
        $rooms = $stmt->fetchAll();
    }

    // Only run update logic when this is not a delete request and required fields are present
    if (empty($_POST['delete_id'])) {
        if (isset($_POST['id'], $_POST['room_number'], $_POST['type'], $_POST['price'], $_POST['status'])) {
            $id = (int)$_POST['id'];
            $room_number = $_POST['room_number'];
            $type = $_POST['type'];
            $price = $_POST['price'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("UPDATE rooms SET room_number = ?, type = ?, price = ?, status = ? WHERE id = ?");
            $stmt->execute([$room_number, $type, $price, $status, $id]);
            $success = 'Room updated';
            // Refresh rooms
            $stmt = $pdo->query("SELECT * FROM rooms");
            $rooms = $stmt->fetchAll();
        }
    }
}
?>
<?php $pageTitle = 'Edit Rooms'; include 'header.php'; ?>

    <div class="bg-white p-6 rounded-md shadow-sm overflow-x-auto">
        <h1 class="text-2xl font-semibold mb-4">Edit Rooms</h1>
        <?php if (isset($success)) echo "<div class='mb-4 text-green-600'>".htmlspecialchars($success)."</div>"; ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Number</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Type</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Price</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <form method="POST" class="w-full flex items-center space-x-2">
                        <td class="px-4 py-2"><?php echo $room['id']; ?><input type="hidden" name="id" value="<?php echo $room['id']; ?>"></td>
                        <td class="px-4 py-2"><input class="rounded-md border-gray-300" type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>"></td>
                        <td class="px-4 py-2">
                            <select class="rounded-md border-gray-300" name="type">
                                <option <?php if($room['type']=='Single') echo 'selected'; ?>>Single</option>
                                <option <?php if($room['type']=='Double') echo 'selected'; ?>>Double</option>
                                <option <?php if($room['type']=='Suite') echo 'selected'; ?>>Suite</option>
                            </select>
                        </td>
                        <td class="px-4 py-2"><input class="rounded-md border-gray-300" type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($room['price']); ?>"></td>
                        <td class="px-4 py-2">
                            <select class="rounded-md border-gray-300" name="status">
                                <option <?php if($room['status']=='Available') echo 'selected'; ?>>Available</option>
                                <option <?php if($room['status']=='Booked') echo 'selected'; ?>>Booked</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <button class="bg-indigo-600 text-white py-1 px-3 rounded-md">Update</button>
                            <button type="button" class="bg-red-600 text-white py-1 px-3 rounded-md ml-2 delete-room" data-id="<?php echo $room['id']; ?>">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <a class="text-sm text-gray-600 hover:underline" href="index.php">Back</a>
        </div>
    </div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.delete-room').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-id');
            if (!confirm('Delete room #' + id + '? This cannot be undone.')) return;
            // create a form and submit POST with delete_id
            var f = document.createElement('form');
            f.method = 'POST';
            f.style.display = 'none';
            var inp = document.createElement('input');
            inp.name = 'delete_id';
            inp.value = id;
            f.appendChild(inp);
            document.body.appendChild(f);
            f.submit();
        });
    });
});
</script>