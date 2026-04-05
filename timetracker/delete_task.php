<?php
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
}

// Redirect back to index
header("Location: index.php");
exit();
