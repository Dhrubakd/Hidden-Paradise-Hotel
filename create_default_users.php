<?php
/**
 * Create or update default users (admin, staff) with hashed passwords.
 * Safe to run locally from XAMPP Shell (CLI) or via browser on localhost.
 */
include __DIR__ . '/db.php';

// Allow CLI or localhost only
if (php_sapi_name() !== 'cli') {
    $addr = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($addr !== '127.0.0.1' && $addr !== '::1') {
        http_response_code(403);
        echo "Forbidden\n";
        exit;
    }
}

$defaults = [
    ['username' => 'admin', 'password' => 'password', 'role' => 'manager'],
    ['username' => 'staff', 'password' => 'staffpass', 'role' => 'staff'],
];

foreach ($defaults as $u) {
    $username = $u['username'];
    $plain = $u['password'];
    $role = $u['role'];

    $hash = password_hash($plain, PASSWORD_DEFAULT);

    // Check if users table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() === 0) {
            echo "Users table does not exist. Run migrations/create_users.sql first.\n";
            exit(1);
        }
    } catch (Exception $e) {
        echo "DB error: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Insert or update
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $stmt = $pdo->prepare('UPDATE users SET password = ?, role = ? WHERE id = ?');
        $stmt->execute([$hash, $role, $row['id']]);
        echo "Updated user: $username\n";
    } else {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->execute([$username, $hash, $role]);
        echo "Created user: $username\n";
    }
}

echo "Done.\n";
