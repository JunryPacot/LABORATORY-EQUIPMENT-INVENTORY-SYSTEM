<?php
// LABORATORY EQUIPMENT INVENTORY SYSTEM
// Database Connection Configuration (InfinityFree Hosting)

$host   = 'sql312.infinityfree.com';      // MySQL Hostname from InfinityFree
$dbname = 'if0_42826009_lab_inventory';   // Database Name from InfinityFree
$user   = 'if0_42826009';                 //  MySQL Username from InfinityFree
$pass   = 'qg09vaHlH9H05m';         //  vPanel/InfinityFree Password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>
