<?php
// Database connection settings
$host     = "sql303.infinityfree.com";
$user     = "if0_41185034";
$password = "fDg08WjtcZoy7";
$database = "if0_41185034_timetracker";

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}