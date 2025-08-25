<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

    // Try database-backed auth if users table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        $exists = $stmt->rowCount() > 0;
    } catch (Exception $e) {
        $exists = false;
    }

    if ($exists) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid credentials';
        }
    } else {
        $error = "Authentication is not configured. Create the users table and insert users (see README).";
    }
    }
}
?>
<?php $pageTitle = 'Login'; include 'header.php'; ?>

    <div class="flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-4 text-gray-800">Hidden Paradise Hotel</h1>
            <?php if (isset($error)) echo "<div class='mb-4 text-red-600'>".htmlspecialchars($error)."</div>"; ?>
            <form method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="text" name="username" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="password" name="password" required>
                </div>
                <div>
                    <button class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">Login</button>
                </div>
            </form>
        </div>
    </div>

<?php include 'footer.php'; ?>