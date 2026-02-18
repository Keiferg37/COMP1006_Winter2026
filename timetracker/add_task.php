<?php
require 'db.php';

$errors = [];

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
    // Verify reCAPTCHA
    if (empty($_POST['g-recaptcha-response'])) {
        $errors[] = "Please complete the reCAPTCHA.";
    } else {
        $secret = "6LeCfG8sAAAAAGkFqfwxrmU0VoN18VtvtEhDDDFi";
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=" . $_POST['g-recaptcha-response']);
        $responseData = json_decode($response);
        if (!$responseData->success) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $task_name  = mysqli_real_escape_string($conn, $_POST['task_name']);
        $category   = mysqli_real_escape_string($conn, $_POST['category']);
        $priority   = mysqli_real_escape_string($conn, $_POST['priority']);
        $due_date   = mysqli_real_escape_string($conn, $_POST['due_date']);
        $time_spent = mysqli_real_escape_string($conn, $_POST['time_spent']);

        $sql = "INSERT INTO tasks (task_name, category, priority, due_date, time_spent)
                VALUES ('$task_name', '$category', '$priority', '$due_date', '$time_spent')";

        if (mysqli_query($conn, $sql)) {
            // Redirect back to index after successful insert
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
    <title>Add Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Add New Task</h1>

    <!-- Display errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="mb-0"><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_task.php">

        <div class="mb-3">
            <label>Task Name</label>
            <input type="text" name="task_name" class="form-control" required
                   value="<?php echo isset($_POST['task_name']) ? htmlspecialchars($_POST['task_name']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required
                   value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label>Priority</label>
            <select name="priority" class="form-control" required>
                <option value="">-- Select Priority --</option>
                <option value="high"   <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'high')   ? 'selected' : ''; ?>>High</option>
                <option value="medium" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                <option value="low"    <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'low')    ? 'selected' : ''; ?>>Low</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control" required
                   value="<?php echo isset($_POST['due_date']) ? $_POST['due_date'] : ''; ?>">
        </div>

        <div class="mb-3">
            <label>Time Spent (hours)</label>
            <input type="number" name="time_spent" class="form-control" step="0.01" min="0" required
                   value="<?php echo isset($_POST['time_spent']) ? $_POST['time_spent'] : ''; ?>">
        </div>

        <!-- Google reCAPTCHA -->
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="6LeCfG8sAAAAACNSqIrBjMYM31KyTqM4nbgynC-6"></div>
        </div>

        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Add Task</button>

    </form>
</div>

</body>
</html>