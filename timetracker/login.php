<?php
session_start();

// If already logged in, redirect to index
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require "includes/connect.php";
require "includes/header.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usernameOrEmail === '' || $password === '') {
        $error = "Username/email and password are required.";
    } else {
        // Look up user by username or email
        $sql = "SELECT id, username, email, password
                FROM users
                WHERE username = :login OR email = :login
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $usernameOrEmail);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Store user info in session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid credentials. Please try again.";
        }
    }
}
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h2>Login</h2>

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-3">

                <label for="username_or_email" class="form-label">Username or Email</label>
                <input
                    type="text"
                    id="username_or_email"
                    name="username_or_email"
                    class="form-control mb-3"
                    required
                >

                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control mb-4"
                    required
                >

                <button type="submit" class="btn btn-primary">Login</button>
                <a href="register.php" class="btn btn-secondary">Create Account</a>
            </form>
        </div>
    </div>
</main>

<?php require "includes/footer.php"; ?>