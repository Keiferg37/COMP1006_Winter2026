<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        /* Style the form elements */
        form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Upload Profile Picture</h1>

    <!-- 
        The form must include:
        - method="POST" to send data securely
        - enctype="multipart/form-data" which is REQUIRED for file uploads
          (without this, the file data won't be sent properly)
        - action points to the PHP file that will process the upload
    -->
    <form method="POST" action="process-upload.php" enctype="multipart/form-data">
        
        <!-- File input allows the user to select an image from their computer -->
        <label for="profile_picture">Select an image:</label>
        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
        <br>

        <!-- Submit button sends the form data (including the file) to the server -->
        <input type="submit" name="submit" value="Upload Image">
    </form>
</body>
</html>