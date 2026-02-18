<?php
require 'db.php';

// Check if an id was provided in the URL
if (isset($_GET['id'])) {

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete the task from the database
    $sql = "DELETE FROM tasks WHERE id = '$id'";

    mysqli_query($conn, $sql);
}

// Redirect back to index
header("Location: index.php");
exit();
?>