<?php
// process.php
// COMP1006 – Lab 3
// Handles server-side validation, sanitization, email sending,
// and displays a confirmation message to the user.

// ------------------------------------
// Ensure the form was submitted via POST
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// ------------------------------------
// Helper functions
// ------------------------------------

// Cleans user input by trimming whitespace
function clean(string $value): string {
    return trim($value);
}

// Escapes output to prevent XSS attacks
function safe(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

// ------------------------------------
// Read form data from $_POST
// ------------------------------------
$firstName = isset($_POST["firstName"]) ? clean($_POST["firstName"]) : "";
$lastName  = isset($_POST["lastName"])  ? clean($_POST["lastName"])  : "";
$email     = isset($_POST["email"])     ? clean($_POST["email"])     : "";
$message   = isset($_POST["message"])   ? clean($_POST["message"])   : "";

// ------------------------------------
// Server-side validation
// ------------------------------------
$errors = [];

// Required field checks
if ($firstName === "") $errors[] = "First name is required.";
if ($lastName === "")  $errors[] = "Last name is required.";
if ($email === "")     $errors[] = "Email is required.";
if ($message === "")   $errors[] = "Message is required.";

// Length validation
if (strlen($firstName) > 40) $errors[] = "First name is too long.";
if (strlen($lastName) > 40)  $errors[] = "Last name is too long.";
if (strlen($email) > 80)     $errors[] = "Email is too long.";
if (strlen($message) > 1000) $errors[] = "Message is too long.";

// Email format validation
if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email address is not valid.";
}

// ------------------------------------
// Display errors if validation fails
// ------------------------------------
if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Form Error</title>
      <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
      >
    </head>
    <body>
      <div class="container my-5" style="max-width: 600px;">
        <div class="alert alert-danger">
          <h1 class="h5">Please fix the following:</h1>
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
              <li><?= safe($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <a class="btn btn-secondary" href="index.php">Go back</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// ------------------------------------
// Send email
// ------------------------------------
$to = "webdev992@gmail.com"; // Updated to actual email
$subject = "New Contact Form Submission";

// Build email message
$body =
"First Name: {$firstName}\n" .
"Last Name: {$lastName}\n" .
"Email: {$email}\n\n" .
"Message:\n{$message}\n";

// Email headers - Fixed header injection vulnerability
$safeEmail = str_replace(["\r", "\n"], '', $email); // Remove newlines to prevent header injection
$headers = "From: noreply@bakeittilyoumakeit.com\r\n";
$headers .= "Reply-To: {$safeEmail}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";

// Attempt to send email
$mailSent = mail($to, $subject, $body, $headers);

// ------------------------------------
// Confirmation page
// ------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form Submitted</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body>
  <div class="container my-5" style="max-width: 600px;">

    <div class="alert alert-success">
      <h1 class="h5">Submission received</h1>
      <p>Your form was successfully submitted.</p>
    </div>

    <!-- Echo sanitized user input -->
    <p><strong>First Name:</strong> <?= safe($firstName) ?></p>
    <p><strong>Last Name:</strong> <?= safe($lastName) ?></p>
    <p><strong>Email:</strong> <?= safe($email) ?></p>
    <p><strong>Message:</strong><br><?= nl2br(safe($message)) ?></p>

    <!-- Email status message -->
    <?php if ($mailSent): ?>
      <div class="alert alert-info">Email was sent successfully.</div>
    <?php else: ?>
      <div class="alert alert-warning">
        Email could not be sent (common on local servers).
      </div>
    <?php endif; ?>

    <a class="btn btn-primary" href="index.php">Submit another message</a>

  </div>
</body>
</html>