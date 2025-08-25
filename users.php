<?php
require_once 'db.php';
requireLogin();
if (!isManager()) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

global $pdo;

$msg = $_GET['msg'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token';
    } else {
        if (!empty($_POST['reset_id'])) {
            $id = (int)$_POST['reset_id'];
            $newpass = bin2hex(random_bytes(4));
            $hash = password_hash($newpass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hash, $id]);
            $success = "Password reset for user #$id: new password = $newpass";
        } elseif (!empty($_POST['delete_id'])) {
            $id = (int)$_POST['delete_id'];
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $success = "Deleted user #$id";
        }
    }
}

$users = $pdo->query('SELECT id, username, role, created_at FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white shadow p-6 rounded">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <?php if ($msg): ?>
        <div class="p-2 bg-blue-100 text-blue-800 rounded mb-4"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="p-2 bg-red-100 text-red-800 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="p-2 bg-green-100 text-green-800 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Created</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $u['id']; ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($u['username']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($u['role']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($u['created_at']); ?></td>
                <td class="px-4 py-2">
                    <form method="post" style="display:inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="reset_id" value="<?php echo $u['id']; ?>">
                        <button class="bg-yellow-400 px-3 py-1 rounded" type="submit">Reset Password</button>
                    </form>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete user?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="delete_id" value="<?php echo $u['id']; ?>">
                        <button class="bg-red-500 text-white px-3 py-1 rounded" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <a href="create_user.php" class="bg-blue-500 text-white px-4 py-2 rounded">Create user</a>
        <a href="reconcile_rooms.php" class="ml-2 bg-gray-700 text-white px-4 py-2 rounded">Reconcile rooms</a>
    </div>
</div>
</body>
</html>
