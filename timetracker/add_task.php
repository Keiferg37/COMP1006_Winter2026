<?php
<<<<<<< HEAD
// Make sure user is logged in
require "includes/auth.php";
=======
require_once 'db.php';
require_once 'auth.php';
requireLogin();
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35

// Connect to database
require "includes/connect.php";

// Array for validation errors
$errors = [];

<<<<<<< HEAD
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize form values
    $taskName  = trim(filter_input(INPUT_POST, 'task_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $category  = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    $priority  = $_POST['priority'] ?? '';
    $dueDate   = $_POST['due_date'] ?? '';
    $timeSpent = filter_input(INPUT_POST, 'time_spent', FILTER_VALIDATE_FLOAT);

    // -----------------------------
    // Server-side Validation
    // -----------------------------

    if ($taskName === '') {
=======
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect and trim input
    $task_name  = trim($_POST['task_name'] ?? '');
    $category   = trim($_POST['category'] ?? '');
    $priority   = $_POST['priority'] ?? '';
    $due_date   = $_POST['due_date'] ?? '';
    $time_spent = $_POST['time_spent'] ?? '';

    // --- Server-side validation ---
    if (empty($task_name)) {
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
        $errors[] = "Task name is required.";
    } elseif (strlen($task_name) > 255) {
        $errors[] = "Task name must be under 255 characters.";
    }

<<<<<<< HEAD
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
=======
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
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
    }

    // Verify reCAPTCHA
    if (empty($_POST['g-recaptcha-response'])) {
        $errors[] = "Please complete the reCAPTCHA.";
    } else {
        $secret = "6LeCfG8sAAAAAGkFqfwxrmU0VoN18VtvtEhDDDFi";
        $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . urlencode($_POST['g-recaptcha-response']);
        $response = file_get_contents($recaptchaUrl);
        $responseData = json_decode($response);
        if (!$responseData->success) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }
    }

    // Handle file upload (optional)
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
<<<<<<< HEAD

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'text/plain'];
        $fileType = $_FILES['attachment']['type'];
        $fileSize = $_FILES['attachment']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Attachment must be an image (JPEG, PNG, GIF, WebP), PDF, or TXT file.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "Attachment must be under 5MB.";
        } else {
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $newFilename = "task_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
=======
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
            $errors[] = "Attachment must be an image (JPEG, PNG, GIF, WebP), PDF, DOC, DOCX, or TXT file.";
        } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Attachment must be under 5MB.";
        } else {
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $newFilename = "task_" . getCurrentUserId() . "_" . time() . "." . $ext;
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
            $destination = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $destination)) {
                $attachment = $newFilename;
            } else {
                $errors[] = "Failed to upload file. Please try again.";
            }
        }
    }

<<<<<<< HEAD
    // If no errors, insert into database
    if (empty($errors)) {

        $sql = "INSERT INTO tasks (user_id, task_name, category, priority, due_date, time_spent, attachment)
                VALUES (:user_id, :task_name, :category, :priority, :due_date, :time_spent, :attachment)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->bindParam(':time_spent', $timeSpent);
        $stmt->bindParam(':attachment', $attachment);

        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

// Include header after processing (so redirects work)
require "includes/header.php";
?>

<main class="container mt-4">
    <h1 class="mb-4">Add New Task</h1>

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

    <!-- enctype="multipart/form-data" required for file uploads -->
    <form method="post" enctype="multipart/form-data" class="mt-3">

        <label for="task_name" class="form-label">Task Name</label>
        <input
            type="text"
            id="task_name"
            name="task_name"
            class="form-control mb-3"
            value="<?= htmlspecialchars($taskName ?? ''); ?>"
            required
        >

        <label for="category" class="form-label">Category</label>
        <input
            type="text"
            id="category"
            name="category"
            class="form-control mb-3"
            value="<?= htmlspecialchars($category ?? ''); ?>"
            required
        >

        <label for="priority" class="form-label">Priority</label>
        <select name="priority" id="priority" class="form-control mb-3" required>
            <option value="">-- Select Priority --</option>
            <option value="high"   <?= (isset($priority) && $priority === 'high')   ? 'selected' : ''; ?>>High</option>
            <option value="medium" <?= (isset($priority) && $priority === 'medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="low"    <?= (isset($priority) && $priority === 'low')    ? 'selected' : ''; ?>>Low</option>
        </select>

        <label for="due_date" class="form-label">Due Date</label>
        <input
            type="date"
            id="due_date"
            name="due_date"
            class="form-control mb-3"
            value="<?= htmlspecialchars($dueDate ?? ''); ?>"
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
            value="<?= htmlspecialchars($timeSpent ?? ''); ?>"
            required
        >

        <label for="attachment" class="form-label">Attachment (optional)</label>
        <input
            type="file"
            id="attachment"
            name="attachment"
            class="form-control mb-3"
            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt"
        >
=======
    // Insert task if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task_name, category, priority, due_date, time_spent, attachment)
                               VALUES (:user_id, :task_name, :category, :priority, :due_date, :time_spent, :attachment)");
        $stmt->execute([
            ':user_id'    => getCurrentUserId(),
            ':task_name'  => $task_name,
            ':category'   => $category,
            ':priority'   => $priority,
            ':due_date'   => $due_date,
            ':time_spent' => $time_spent,
            ':attachment' => $attachment
        ]);

        header("Location: index.php");
        exit();
    }
}

$pageTitle = "Add Task";
$extraHead = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
require 'header.php';
?>

<h1 class="mb-4">Add New Task</h1>

<!-- Display errors -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="add_task.php" enctype="multipart/form-data" id="addTaskForm" novalidate>

    <div class="mb-3">
        <label for="task_name" class="form-label">Task Name</label>
        <input type="text" name="task_name" id="task_name" class="form-control" required maxlength="255"
               value="<?php echo htmlspecialchars($task_name ?? ''); ?>">
        <div class="invalid-feedback">Task name is required.</div>
    </div>

    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" name="category" id="category" class="form-control" required
               value="<?php echo htmlspecialchars($category ?? ''); ?>">
        <div class="invalid-feedback">Category is required.</div>
    </div>

    <div class="mb-3">
        <label for="priority" class="form-label">Priority</label>
        <select name="priority" id="priority" class="form-control" required>
            <option value="">-- Select Priority --</option>
            <option value="high"   <?php echo (isset($priority) && $priority === 'high')   ? 'selected' : ''; ?>>High</option>
            <option value="medium" <?php echo (isset($priority) && $priority === 'medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="low"    <?php echo (isset($priority) && $priority === 'low')    ? 'selected' : ''; ?>>Low</option>
        </select>
        <div class="invalid-feedback">Please select a priority.</div>
    </div>

    <div class="mb-3">
        <label for="due_date" class="form-label">Due Date</label>
        <input type="date" name="due_date" id="due_date" class="form-control" required
               value="<?php echo htmlspecialchars($due_date ?? ''); ?>">
        <div class="invalid-feedback">Due date is required.</div>
    </div>

    <div class="mb-3">
        <label for="time_spent" class="form-label">Time Spent (hours)</label>
        <input type="number" name="time_spent" id="time_spent" class="form-control" step="0.01" min="0" required
               value="<?php echo htmlspecialchars($time_spent ?? ''); ?>">
        <div class="invalid-feedback">Time spent must be a valid number.</div>
    </div>

    <div class="mb-3">
        <label for="attachment" class="form-label">Attachment (optional)</label>
        <input type="file" name="attachment" id="attachment" class="form-control"
               accept="image/*,.pdf,.doc,.docx,.txt">
        <div class="form-text">Max 5MB. Images, PDF, DOC, DOCX, or TXT files.</div>
    </div>
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35

    <!-- Google reCAPTCHA -->
    <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="6LeCfG8sAAAAACNSqIrBjMYM31KyTqM4nbgynC-6"></div>
    </div>

<<<<<<< HEAD
        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>
</main>

<!-- reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php require "includes/footer.php"; ?>
=======
    <a href="index.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">Add Task</button>
</form>

<!-- Client-side validation -->
<script>
document.getElementById('addTaskForm').addEventListener('submit', function(e) {
    this.classList.add('was-validated');
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
});
</script>

<?php require 'footer.php'; ?>
>>>>>>> 0d61451589dab17b9194f1eabc58dc821899ec35
