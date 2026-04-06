<?php
<<<<<<< HEAD
// Make sure user is logged in
require "includes/auth.php";

// Connect to database
require "includes/connect.php";

// Make sure we received an ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
=======
require_once 'db.php';
require_once 'auth.php';
requireLogin();

// Check if an id was provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Fetch the task to get attachment filename - only if owned by current user
    $stmt = $pdo->prepare("SELECT attachment FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $id, ':user_id' => getCurrentUserId()]);
    $task = $stmt->fetch();

    if ($task) {
        // Delete the attachment file if it exists
        if ($task['attachment'] && file_exists("uploads/" . $task['attachment'])) {
            unlink("uploads/" . $task['attachment']);
        }

        // Delete the task from the database
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => getCurrentUserId()]);
    }
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
}

$taskId = (int)$_GET['id'];

// Fetch the task to get attachment filename - only if owned by current user
$sql = "SELECT attachment FROM tasks WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $taskId);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$task = $stmt->fetch();

if ($task) {
    // Delete the attachment file if it exists
    if ($task['attachment'] && file_exists("uploads/" . $task['attachment'])) {
        unlink("uploads/" . $task['attachment']);
    }

    // Delete the task from the database
    $sql = "DELETE FROM tasks WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $taskId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}

// Redirect back to task list
header("Location: index.php");
<<<<<<< HEAD
exit;
=======
exit();
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
