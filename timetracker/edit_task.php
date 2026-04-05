<?php
require_once 'db.php';
require_once 'auth.php';
requireLogin();

$errors = [];

// Get the task id from the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch the existing task data - only if it belongs to the current user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $id, ':user_id' => getCurrentUserId()]);
$task = $stmt->fetch();

// If task not found or doesn't belong to user, redirect
if (!$task) {
    header("Location: index.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $task_name  = trim($_POST['task_name'] ?? '');
    $category   = trim($_POST['category'] ?? '');
    $priority   = $_POST['priority'] ?? '';
    $due_date   = $_POST['due_date'] ?? '';
    $time_spent = $_POST['time_spent'] ?? '';

    // --- Server-side validation ---
    if (empty($task_name)) {
        $errors[] = "Task name is required.";
    } elseif (strlen($task_name) > 255) {
        $errors[] = "Task name must be under 255 characters.";
    }

    if (empty($category)) {
        $errors[] = "Category is required.";
    }

    if (empty($priority) || !in_array($priority, ['high', 'medium', 'low'])) {
        $errors[] = "Please select a valid priority.";
    }

    if (empty($due_date)) {
        $errors[] = "Due date is required.";
    }

    if ($time_spent === "" || !is_numeric($time_spent) || $time_spent < 0) {
        $errors[] = "Time spent must be a valid positive number.";
    }

    // Handle file upload (optional - replaces existing attachment)
    $attachment = $task['attachment']; // keep existing by default
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $allowed = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];
        $fileType = $_FILES['attachment']['type'];
        $fileSize = $_FILES['attachment']['size'];

        if (!in_array($fileType, $allowed)) {
            $errors[] = "Attachment must be an image, PDF, DOC, DOCX, or TXT file.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "Attachment must be under 5MB.";
        } else {
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $newFilename = "task_" . getCurrentUserId() . "_" . time() . "." . $ext;
            $destination = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $destination)) {
                // Delete old attachment if exists
                if ($task['attachment'] && file_exists("uploads/" . $task['attachment'])) {
                    unlink("uploads/" . $task['attachment']);
                }
                $attachment = $newFilename;
            } else {
                $errors[] = "Failed to upload file.";
            }
        }
    }

    // Handle remove attachment checkbox
    if (isset($_POST['remove_attachment']) && $_POST['remove_attachment'] === '1') {
        if ($task['attachment'] && file_exists("uploads/" . $task['attachment'])) {
            unlink("uploads/" . $task['attachment']);
        }
        $attachment = null;
    }

    // Update if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE tasks SET
                               task_name = :task_name,
                               category = :category,
                               priority = :priority,
                               due_date = :due_date,
                               time_spent = :time_spent,
                               attachment = :attachment
                               WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':task_name'  => $task_name,
            ':category'   => $category,
            ':priority'   => $priority,
            ':due_date'   => $due_date,
            ':time_spent' => $time_spent,
            ':attachment' => $attachment,
            ':id'         => $id,
            ':user_id'    => getCurrentUserId()
        ]);

        header("Location: index.php");
        exit();
    }

    // Update task array with submitted values for form repopulation
    $task['task_name']  = $task_name;
    $task['category']   = $category;
    $task['priority']   = $priority;
    $task['due_date']   = $due_date;
    $task['time_spent'] = $time_spent;
}

$pageTitle = "Edit Task";
require 'header.php';
?>

<h1 class="mb-4">Edit Task</h1>

<!-- Display errors -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="edit_task.php?id=<?php echo $id; ?>" enctype="multipart/form-data" id="editTaskForm" novalidate>

    <div class="mb-3">
        <label for="task_name" class="form-label">Task Name</label>
        <input type="text" name="task_name" id="task_name" class="form-control" required maxlength="255"
               value="<?php echo htmlspecialchars($task['task_name']); ?>">
        <div class="invalid-feedback">Task name is required.</div>
    </div>

    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" name="category" id="category" class="form-control" required
               value="<?php echo htmlspecialchars($task['category']); ?>">
        <div class="invalid-feedback">Category is required.</div>
    </div>

    <div class="mb-3">
        <label for="priority" class="form-label">Priority</label>
        <select name="priority" id="priority" class="form-control" required>
            <option value="">-- Select Priority --</option>
            <option value="high"   <?php echo $task['priority'] === 'high'   ? 'selected' : ''; ?>>High</option>
            <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
            <option value="low"    <?php echo $task['priority'] === 'low'    ? 'selected' : ''; ?>>Low</option>
        </select>
        <div class="invalid-feedback">Please select a priority.</div>
    </div>

    <div class="mb-3">
        <label for="due_date" class="form-label">Due Date</label>
        <input type="date" name="due_date" id="due_date" class="form-control" required
               value="<?php echo htmlspecialchars($task['due_date']); ?>">
        <div class="invalid-feedback">Due date is required.</div>
    </div>

    <div class="mb-3">
        <label for="time_spent" class="form-label">Time Spent (hours)</label>
        <input type="number" name="time_spent" id="time_spent" class="form-control" step="0.01" min="0" required
               value="<?php echo htmlspecialchars($task['time_spent']); ?>">
        <div class="invalid-feedback">Time spent must be a valid number.</div>
    </div>

    <!-- Attachment -->
    <div class="mb-3">
        <label for="attachment" class="form-label">Attachment</label>
        <?php if ($task['attachment']): ?>
            <div class="mb-2">
                <span class="text-muted">Current file: </span>
                <a href="uploads/<?php echo htmlspecialchars($task['attachment']); ?>" target="_blank">
                    <?php echo htmlspecialchars($task['attachment']); ?>
                </a>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="removeAttachment">
                    <label class="form-check-label" for="removeAttachment">Remove current attachment</label>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" name="attachment" id="attachment" class="form-control"
               accept="image/*,.pdf,.doc,.docx,.txt">
        <div class="form-text">Max 5MB. Upload a new file to replace the current one.</div>
    </div>

    <a href="index.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">Update Task</button>
</form>

<!-- Client-side validation -->
<script>
document.getElementById('editTaskForm').addEventListener('submit', function(e) {
    this.classList.add('was-validated');
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
});
</script>

<?php require 'footer.php'; ?>
