<?php
// Database connection settings
$host = "sql303.infinityfree.com";
$user = "if0_41185034";
$password = "fDg08WjtcZoy7";
$database = "if0_41185034_timetracker";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>