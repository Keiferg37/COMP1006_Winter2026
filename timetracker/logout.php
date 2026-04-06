<?php
// Load auth so session starts
require "includes/auth.php";

// Clear all session variables
$_SESSION = [];

// Unset all session variables in memory
session_unset();

// Destroy the session on the server
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;