<?php
require_once 'db.php';
require_once 'auth.php';
requireLogin();

// Query to get all tasks for the current user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => getCurrentUserId()]);
$tasks = $stmt->fetchAll();

$pageTitle = "My Tasks";
require 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">My Tasks</h1>
    <a href="add_task.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Add Task</a>
</div>

<!-- Tasks table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Task Name</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Time Spent (hrs)</th>
                <th>Attachment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td>
                        <?php
                            $badgeClass = match($row['priority']) {
                                'high'   => 'bg-danger',
                                'medium' => 'bg-warning text-dark',
                                'low'    => 'bg-success',
                                default  => 'bg-secondary'
                            };
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($row['priority']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_spent']); ?></td>
                    <td>
                        <?php if ($row['attachment']): ?>
                            <a href="uploads/<?php echo htmlspecialchars($row['attachment']); ?>" target="_blank"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-paperclip me-1"></i>View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this task?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No tasks found. <a href="add_task.php">Add one!</a></p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require 'footer.php'; ?>
