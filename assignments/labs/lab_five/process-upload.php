<?php
// Include the site header (navigation, Bootstrap, etc.)
require "includes/header.php";
?>

<main class="container mt-4">
    <h1>Upload Result</h1>

    <?php
    // Check if the form was submitted
    if (isset($_POST['submit'])) {

        // --- Access the uploaded file using the $_FILES superglobal ---
        // $_FILES is a PHP superglobal array that stores information about uploaded files
        // It works similar to $_POST but is specifically for file uploads
        //
        // $_FILES['profile_picture'] contains:
        //   ['name']     - the original filename (e.g. "photo.jpg")
        //   ['type']     - the MIME type (e.g. "image/jpeg")
        //   ['size']     - the file size in bytes
        //   ['tmp_name'] - the temporary location where PHP stored the file on the server
        //   ['error']    - error code (0 means no error)

        $fileName    = $_FILES['profile_picture']['name'];
        $fileTmpName = $_FILES['profile_picture']['tmp_name'];
        $fileSize    = $_FILES['profile_picture']['size'];
        $fileError   = $_FILES['profile_picture']['error'];
        $fileType    = $_FILES['profile_picture']['type'];

        // Check if there was an error during the upload
        if ($fileError === 0) {

            // --- Define the destination folder ---
            // The "uploads/" folder must exist inside the project directory
            // This is where uploaded profile pictures will be stored
            $uploadDirectory = 'uploads/';

            // Build the full destination path (e.g. "uploads/photo.jpg")
            $destination = $uploadDirectory . $fileName;

            // --- Move the file from its temporary location to the uploads folder ---
            // move_uploaded_file() takes two arguments:
            //   1. The temporary file path (where PHP stored it automatically)
            //   2. The destination path (where we want the file to be saved)
            // It is important to control where files are stored for security reasons
            // We don't want users uploading files to sensitive server directories
            if (move_uploaded_file($fileTmpName, $destination)) {

                // Display a success confirmation message
                echo '<div class="alert alert-success">';
                echo '<strong>Success!</strong> Your profile picture has been uploaded.';
                echo '</div>';

                // --- Display the uploaded image on the page ---
                echo '<h2 class="mt-3">Your Profile Picture:</h2>';
                echo '<img src="' . htmlspecialchars($destination) . '" alt="Profile Picture" class="img-fluid mt-2" style="max-width: 300px; border-radius: 8px;">';

            } else {
                // The file could not be moved (likely a folder permissions issue)
                echo '<div class="alert alert-danger">';
                echo '<strong>Error:</strong> There was a problem saving your file. Check folder permissions.';
                echo '</div>';
            }

        } else {
            // There was an error with the upload itself
            echo '<div class="alert alert-danger">';
            echo '<strong>Error:</strong> There was a problem uploading your file. Error code: ' . $fileError;
            echo '</div>';
        }

    } else {
        // If someone navigates to this page directly without submitting the form
        echo '<div class="alert alert-danger">';
        echo 'No file was submitted. Please go back and upload a file.';
        echo '</div>';
    }
    ?>

    <!-- Link back to the upload form -->
    <a href="upload.php" class="btn btn-secondary mt-4">&larr; Upload another image</a>
</main>

<?php require "includes/footer.php"; ?>
