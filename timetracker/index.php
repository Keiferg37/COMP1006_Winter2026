<?php
// Include database connection
require 'db.php';

// Query to get all tasks from the database
$result = mysqli_query($conn, "SELECT * FROM tasks ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Time Tracker</h1>

    <!-- Button to go to add task form -->
    <a href="add_task.php" class="btn btn-primary mb-3">+ Add Task</a>

    <!-- Tasks table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Task Name</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Time Spent (hrs)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo ucfirst($row['priority']); ?></td>
                    <td><?php echo $row['due_date']; ?></td>
                    <td><?php echo $row['time_spent']; ?></td>
                    <td>
                        <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No tasks found. Add one!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>