<?php
if (!isset($pageTitle)) $pageTitle = 'Hidden Paradise Hotel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow h-16">
        <div class="max-w-6xl mx-auto px-6 h-full flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">HP</div>
                <div>
                    <div class="text-lg font-semibold">Hidden Paradise Hotel</div>
                    <div class="text-sm text-gray-500">Management Dashboard</div>
                </div>
            </a>

            <nav class="space-x-4 text-sm">
                <a class="text-indigo-600 hover:underline" href="checkin.php">Check-In</a>
                <a class="text-indigo-600 hover:underline" href="checkout.php">Check-Out</a>
                <?php if (function_exists('isManager') && isManager()) { ?>
                    <a class="text-indigo-600 hover:underline" href="add_room.php">Add Room</a>
                    <a class="text-indigo-600 hover:underline" href="edit_room.php">Edit Rooms</a>
                    <a class="text-indigo-600 hover:underline" href="reports.php">Reports</a>
                <?php } ?>
                <span class="text-gray-400">|</span>
                <span class="text-sm text-gray-600">Role: <span class="font-medium text-indigo-600"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Guest'); ?></span></span>
                <a class="text-red-500 hover:underline ml-3" href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <!-- main gets top/bottom padding to offset fixed header/footer -->
    <main class="max-w-6xl mx-auto p-6 pt-20 pb-20">
