<?php
// Include the site header (navigation, Bootstrap, etc.)
require "includes/header.php";
?>

<main class="container mt-4">
    <h1>Upload Profile Picture</h1>

    <!-- 
        The form must include:
        - method="POST" to send data securely
        - enctype="multipart/form-data" is REQUIRED for file uploads
          (without this setting, the file data won't be sent to the server properly)
        - action points to the PHP file that will process the upload
    -->
    <form method="POST" action="process-upload.php" enctype="multipart/form-data" class="mt-3">

        <!-- File input allows the user to select an image from their computer -->
        <label for="profile_picture" class="form-label">Select an image:</label>
        <input
            type="file"
            id="profile_picture"
            name="profile_picture"
            class="form-control mb-4"
            accept="image/*"
        >

        <!-- Submit button sends the form data (including the file) to the server -->
        <button type="submit" name="submit" class="btn btn-primary">Upload Image</button>
    </form>
</main>

<?php require "includes/footer.php"; ?>