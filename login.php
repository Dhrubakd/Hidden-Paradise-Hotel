<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded auth (demo only; use database and password_verify in production)
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['user_role'] = 'manager';
        header('Location: index.php');
        exit;
    } elseif ($username === 'staff' && $password === 'staffpass') {
        $_SESSION['user_role'] = 'staff';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<?php $pageTitle = 'Login'; include 'header.php'; ?>

    <div class="flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-4 text-gray-800">Hidden Paradise Hotel</h1>
            <?php if (isset($error)) echo "<div class='mb-4 text-red-600'>".htmlspecialchars($error)."</div>"; ?>
            <form method="POST" class="space-y-4">
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