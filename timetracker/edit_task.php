<?php
require 'db.php';

$errors = [];

// Get the task id from the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch the existing task data
$result = mysqli_query($conn, "SELECT * FROM tasks WHERE id = '$id'");
$task = mysqli_fetch_assoc($result);

// If task not found redirect home
if (!$task) {
    header("Location: index.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Server-side validation
    if (empty($_POST['task_name'])) {
        $errors[] = "Task name is required.";
    }
    if (empty($_POST['category'])) {
        $errors[] = "Category is required.";
    }
    if (empty($_POST['priority'])) {
        $errors[] = "Priority is required.";
    }
    if (empty($_POST['due_date'])) {
        $errors[] = "Due date is required.";
    }
    if ($_POST['time_spent'] === "" || !is_numeric($_POST['time_spent'])) {
        $errors[] = "Time spent must be a number.";
    }

    // If no errors update the task
    if (empty($errors)) {
        $task_name  = mysqli_real_escape_string($conn, $_POST['task_name']);
        $category   = mysqli_real_escape_string($conn, $_POST['category']);
        $priority   = mysqli_real_escape_string($conn, $_POST['priority']);
        $due_date   = mysqli_real_escape_string($conn, $_POST['due_date']);
        $time_spent = mysqli_real_escape_string($conn, $_POST['time_spent']);

        $sql = "UPDATE tasks SET
                task_name  = '$task_name',
                category   = '$category',
                priority   = '$priority',
                due_date   = '$due_date',
                time_spent = '$time_spent'
                WHERE id   = '$id'";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Edit Task</h1>

    <!-- Display errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="mb-0"><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_task.php?id=<?php echo $id; ?>">

        <div class="mb-3">
            <label>Task Name</label>
            <input type="text" name="task_name" class="form-control" required
                   value="<?php echo htmlspecialchars($task['task_name']); ?>">
        </div>

        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required
                   value="<?php echo htmlspecialchars($task['category']); ?>">
        </div>

        <div class="mb-3">
            <label>Priority</label>
            <select name="priority" class="form-control" required>
                <option value="">-- Select Priority --</option>
                <option value="high"   <?php echo $task['priority'] == 'high'   ? 'selected' : ''; ?>>High</option>
                <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="low"    <?php echo $task['priority'] == 'low'    ? 'selected' : ''; ?>>Low</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control" required
                   value="<?php echo $task['due_date']; ?>">
        </div>

        <div class="mb-3">
            <label>Time Spent (hours)</label>
            <input type="number" name="time_spent" class="form-control" step="0.01" min="0" required
                   value="<?php echo $task['time_spent']; ?>">
        </div>

        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Task</button>

    </form>
</div>

</body>
</html>