<?php
// Make sure user is logged in
require "includes/auth.php";

// Connect to database
require "includes/connect.php";

$errors = [];

// Make sure we received an ID in the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$taskId = (int)$_GET['id'];

// If form is submitted, UPDATE the task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize form values
    $taskName  = trim(filter_input(INPUT_POST, 'task_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $category  = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    $priority  = $_POST['priority'] ?? '';
    $dueDate   = $_POST['due_date'] ?? '';
    $timeSpent = filter_input(INPUT_POST, 'time_spent', FILTER_VALIDATE_FLOAT);

    // Server-side validation
    if ($taskName === '') {
        $errors[] = "Task name is required.";
    }

    if ($category === '') {
        $errors[] = "Category is required.";
    }

    if ($priority === '' || !in_array($priority, ['high', 'medium', 'low'])) {
        $errors[] = "Please select a valid priority.";
    }

    if ($dueDate === '') {
        $errors[] = "Due date is required.";
    }

    if ($timeSpent === false || $timeSpent < 0) {
        $errors[] = "Time spent must be a valid number.";
    }

    // Get current task data to preserve attachment
    $sql = "SELECT attachment FROM tasks WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $taskId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $currentTask = $stmt->fetch();

    if (!$currentTask) {
        header("Location: index.php");
        exit;
    }

    $attachment = $currentTask['attachment'];

    // Handle remove attachment checkbox
    if (isset($_POST['remove_attachment']) && $_POST['remove_attachment'] === '1') {
        if ($attachment && file_exists("uploads/" . $attachment)) {
            unlink("uploads/" . $attachment);
        }
        $attachment = null;
    }

    // Handle new file upload (replaces existing)
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'text/plain'];
        $fileType = $_FILES['attachment']['type'];
        $fileSize = $_FILES['attachment']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Attachment must be an image, PDF, or TXT file.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "Attachment must be under 5MB.";
        } else {
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $newFilename = "task_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
            $destination = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $destination)) {
                // Delete old file if it exists
                if ($currentTask['attachment'] && file_exists("uploads/" . $currentTask['attachment'])) {
                    unlink("uploads/" . $currentTask['attachment']);
                }
                $attachment = $newFilename;
            } else {
                $errors[] = "Failed to upload file.";
            }
        }
    }

    // Update if no errors
    if (empty($errors)) {

        $sql = "UPDATE tasks SET
                    task_name  = :task_name,
                    category   = :category,
                    priority   = :priority,
                    due_date   = :due_date,
                    time_spent = :time_spent,
                    attachment = :attachment
                WHERE id = :id AND user_id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->bindParam(':time_spent', $timeSpent);
        $stmt->bindParam(':attachment', $attachment);
        $stmt->bindParam(':id', $taskId);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

// --------------------------------------------------
// Load existing task data (to echo in the form)
// --------------------------------------------------
$sql = "SELECT * FROM tasks WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $taskId);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$task = $stmt->fetch();

if (!$task) {
    header("Location: index.php");
    exit;
}

// Include header after processing
require "includes/header.php";
?>

<main class="container mt-4">
    <h1 class="mb-4">Edit Task</h1>

    <!-- Display errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h3>Please fix the following:</h3>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="edit_task.php?id=<?= $taskId; ?>" enctype="multipart/form-data" class="mt-3">

        <label for="task_name" class="form-label">Task Name</label>
        <input
            type="text"
            id="task_name"
            name="task_name"
            class="form-control mb-3"
            value="<?= htmlspecialchars($task['task_name']); ?>"
            required
        >

        <label for="category" class="form-label">Category</label>
        <input
            type="text"
            id="category"
            name="category"
            class="form-control mb-3"
            value="<?= htmlspecialchars($task['category']); ?>"
            required
        >

        <label for="priority" class="form-label">Priority</label>
        <select name="priority" id="priority" class="form-control mb-3" required>
            <option value="">-- Select Priority --</option>
            <option value="high"   <?= $task['priority'] === 'high'   ? 'selected' : ''; ?>>High</option>
            <option value="medium" <?= $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
            <option value="low"    <?= $task['priority'] === 'low'    ? 'selected' : ''; ?>>Low</option>
        </select>

        <label for="due_date" class="form-label">Due Date</label>
        <input
            type="date"
            id="due_date"
            name="due_date"
            class="form-control mb-3"
            value="<?= htmlspecialchars($task['due_date']); ?>"
            required
        >

        <label for="time_spent" class="form-label">Time Spent (hours)</label>
        <input
            type="number"
            id="time_spent"
            name="time_spent"
            class="form-control mb-3"
            step="0.01"
            min="0"
            value="<?= htmlspecialchars($task['time_spent']); ?>"
            required
        >

        <!-- Attachment section -->
        <label for="attachment" class="form-label">Attachment</label>
        <?php if ($task['attachment']): ?>
            <div class="mb-2">
                <span class="text-muted">Current file: </span>
                <a href="uploads/<?= htmlspecialchars($task['attachment']); ?>" target="_blank">
                    <?= htmlspecialchars($task['attachment']); ?>
                </a>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="removeAttachment">
                    <label class="form-check-label" for="removeAttachment">Remove current attachment</label>
                </div>
            </div>
        <?php endif; ?>
        <input
            type="file"
            id="attachment"
            name="attachment"
            class="form-control mb-4"
            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt"
        >

        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</main>

<?php require "includes/footer.php"; ?>