<?php
include 'db.php';
requireLogin();
if (!isManager()) {
    echo "Only manager can create users";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'staff';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            try {
                $stmt->execute([$username, $hash, $role]);
                $success = 'User created';
            } catch (Exception $e) {
                $error = 'Error creating user: ' . $e->getMessage();
            }
        }
    }
}

?>
<?php $pageTitle = 'Create User'; include 'header.php'; ?>

<div class="bg-white p-6 rounded-md shadow-sm max-w-md">
    <h1 class="text-xl font-semibold mb-4">Create User</h1>
    <?php if (!empty($error)) echo "<div class='mb-4 text-red-600'>".htmlspecialchars($error)."</div>"; ?>
    <?php if (!empty($success)) echo "<div class='mb-4 text-green-600'>".htmlspecialchars($success)."</div>"; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input name="username" class="mt-1 block w-full rounded-md border-gray-300" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="manager">manager</option>
                <option value="staff" selected>staff</option>
            </select>
        </div>
        <div>
            <button class="bg-indigo-600 text-white py-2 px-4 rounded-md">Create</button>
            <a class="ml-4 text-sm text-gray-600 hover:underline" href="index.php">Back</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
