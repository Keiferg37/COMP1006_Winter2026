<?php
$DBH = new PDO(
    "mysql:host=localhost;dbname=YOUR_DB_NAME;charset=utf8mb4",
    "YOUR_DB_USER",
    "YOUR_DB_PASS",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);