<?php
// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$database = "timetracker";
$port = 3307;

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>