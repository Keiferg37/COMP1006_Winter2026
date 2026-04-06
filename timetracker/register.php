<?php
// Start session so header can check login state
session_start();

// If already logged in, redirect to index
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
require "includes/connect.php";

// Include the site header
require "includes/header.php";

// Array to store validation errors
$errors = [];

// Variable to store a success message
$success = "";

// Check if the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve and sanitize the username
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));

    // Retrieve and sanitize the email
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

    // Retrieve passwords (no sanitizing - passwords may contain special characters)
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // -----------------------------
    // Server-side Validation
    // -----------------------------

    // Check that a username was entered
    if ($username === '') {
        $errors[] = "Username is required.";
    }

    // Check that an email was entered
    if ($email === '') {
        $errors[] = "Email is required.";
    }
    // Validate the email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must be a valid email address.";
    }

    // Check that a password was entered
    if ($password === '') {
        $errors[] = "Password is required.";
    }

    // Check that the confirm password field was filled in
    if ($confirmPassword === '') {
        $errors[] = "Please confirm your password.";
    }

    // Check that both passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Enforce a minimum password length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
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

    // --------------------------------------------------
    // Check if the username or email already exists
    // --------------------------------------------------
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->fetch()) {
            $errors[] = "That username or email is already in use.";
        }
    }

    // --------------------------------------------------
    // Insert the new user into the database
    // --------------------------------------------------
    if (empty($errors)) {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password)
                VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        $success = "Account created successfully. You can now log in.";
    }
}
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h2>Register</h2>

            <!-- Display validation errors -->
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

            <!-- Display success message -->
            <?php if ($success !== ""): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success); ?>
                    <br>
                    <a href="login.php" class="btn btn-sm btn-success mt-2">Go to Login</a>
                </div>
            <?php endif; ?>

            <!-- Registration form -->
            <form method="post" class="mt-3">

                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($username ?? ''); ?>"
                    required
                >

                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($email ?? ''); ?>"
                    required
                >

                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control mb-3"
                    required
                >

                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-control mb-3"
                    required
                >

                <!-- Google reCAPTCHA -->
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6LeCfG8sAAAAACNSqIrBjMYM31KyTqM4nbgynC-6"></div>
                </div>

                <button type="submit" class="btn btn-primary">Create Account</button>
                <a href="login.php" class="btn btn-secondary">Login Instead</a>
            </form>
        </div>
    </div>
</main>

<!-- reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php require "includes/footer.php"; ?>