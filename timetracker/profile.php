<?php
// Make sure user is logged in
require "includes/auth.php";

// Connect to database
require "includes/connect.php";

$errors = [];
$success = "";

// Fetch current user data
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    header("Location: logout.php");
    exit;
}

// --------------------------------------------------
// Handle profile update
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $newPass  = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Validate username
    if ($username === '') {
        $errors[] = "Username is required.";
    }

    // Validate email
    if ($email === '') {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must be a valid email address.";
    }

    // Check if username/email is taken by another user
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        if ($stmt->fetch()) {
            $errors[] = "That username or email is already taken.";
        }
    }

    // Validate new password if provided
    if ($newPass !== '') {
        if (strlen($newPass) < 8) {
            $errors[] = "New password must be at least 8 characters long.";
        }
        if ($newPass !== $confirmPass) {
            $errors[] = "New passwords do not match.";
        }
    }

    // Handle profile image upload
    $profileImage = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['profile_image']['type'];
        $fileSize = $_FILES['profile_image']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Profile image must be a JPEG, PNG, GIF, or WebP file.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $errors[] = "Profile image must be under 2MB.";
        } else {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $newFilename = "profile_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
            $destination = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                // Delete old profile image
                if ($profileImage && file_exists("uploads/" . $profileImage)) {
                    unlink("uploads/" . $profileImage);
                }
                $profileImage = $newFilename;
            } else {
                $errors[] = "Failed to upload profile image.";
            }
        }
    }

    // Update if no errors
    if (empty($errors)) {

        if ($newPass !== '') {
            $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = :username, email = :email, password = :password, profile_image = :image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':image', $profileImage);
            $stmt->bindParam(':id', $_SESSION['user_id']);
        } else {
            $sql = "UPDATE users SET username = :username, email = :email, profile_image = :image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':image', $profileImage);
            $stmt->bindParam(':id', $_SESSION['user_id']);
        }

        $stmt->execute();

        // Update session username
        $_SESSION['username'] = $username;
        $success = "Profile updated successfully!";

        // Refresh user data
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->fetch();
    }
}

// --------------------------------------------------
// Handle account deletion
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {

    // Delete profile image file
    if ($user['profile_image'] && file_exists("uploads/" . $user['profile_image'])) {
        unlink("uploads/" . $user['profile_image']);
    }

    // Delete user (tasks will cascade delete)
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();

    // Destroy session and redirect
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("Location: register.php");
    exit;
}

// Include header after processing
require "includes/header.php";
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2 class="mb-4">Profile Settings</h2>

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

            <!-- Display success -->
            <?php if ($success !== ""): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Profile image preview -->
            <div class="text-center mb-3">
                <?php if ($user['profile_image'] && file_exists("uploads/" . $user['profile_image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($user['profile_image']); ?>"
                         alt="Profile Image" class="rounded-circle mb-2" width="100" height="100"
                         style="object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                    </div>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="mt-3">

                <label for="profile_image" class="form-label">Profile Image</label>
                <input
                    type="file"
                    id="profile_image"
                    name="profile_image"
                    class="form-control mb-3"
                    accept=".jpg,.jpeg,.png,.gif,.webp"
                >

                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($user['username']); ?>"
                    required
                >

                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($user['email']); ?>"
                    required
                >

                <hr>
                <p class="text-muted">Leave password fields blank to keep your current password.</p>

                <label for="new_password" class="form-label">New Password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="form-control mb-3"
                >

                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-control mb-4"
                >

                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
            </form>

            <!-- Delete Account -->
            <div class="card border-danger mt-4 mb-4">
                <div class="card-body">
                    <h5 class="text-danger">Danger Zone</h5>
                    <p>Deleting your account will permanently remove all your tasks and data.</p>
                    <form method="post"
                          onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                        <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require "includes/footer.php"; ?>