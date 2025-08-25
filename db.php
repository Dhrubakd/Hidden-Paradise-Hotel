<?php
session_start(); // Start session for all pages

$host = 'localhost';
$db = 'hotel_db';
$user = 'root'; // Default XAMPP user
$pass = ''; // Default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check login (hardcoded for demo)
function isLoggedIn() {
    return isset($_SESSION['user_role']);
}

function isManager() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}