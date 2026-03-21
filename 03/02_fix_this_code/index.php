<?php
// Turn on error reporting so syntax and runtime errors are visible during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";

$dbname = "week_two";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname";

try {

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database!";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}