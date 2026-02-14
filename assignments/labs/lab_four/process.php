<?php
require "includes/connect.php";   // use the same connection file everywhere

// TODO: connect to the database
// (connection is handled inside connect.php)

// TODO: Grab form data (no validation or sanitization for this lab)
$FIRST_NAME = $_POST["first_name"];
$LAST_NAME  = $_POST["last_name"];
$EMAIL      = $_POST["email"];

/*
  1. Write an INSERT statement with named placeholders
  2. Prepare the statement
  3. Execute the statement with an array of values
*/

$STMT = $DBH->prepare("
    INSERT INTO subscribers (first_name, last_name, email)
    VALUES (:first_name, :last_name, :email)
");

$STMT->execute([
    ":first_name" => $FIRST_NAME,
    ":last_name"  => $LAST_NAME,
    ":email"      => $EMAIL
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <main class="container mt-4">
        <h2>Thank You for Subscribing</h2>

        <!-- TODO: Display a confirmation message -->
        <p>
            Thanks, <?= htmlspecialchars($FIRST_NAME) ?>!
            You have been added to our mailing list.
        </p>

        <p class="mt-3">
            <a href="subscribers.php">View Subscribers</a>
        </p>
    </main>
</body>
</html>
