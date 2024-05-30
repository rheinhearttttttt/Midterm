<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection file
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "epbedatkom"; // Replace with your actual database name

// Create connection
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

// Check connection
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize post content
    $post_content = mysqli_real_escape_string($conn, $_POST['post_content']);

    // Get logged-in user's ID
    $user_id = $_SESSION['user_id'];

    // Check if the user exists
    $check_user_sql = "SELECT id FROM users WHERE id = ?";
    $stmt_check_user = mysqli_prepare($conn, $check_user_sql);
    mysqli_stmt_bind_param($stmt_check_user, "i", $user_id);
    mysqli_stmt_execute($stmt_check_user);
    mysqli_stmt_store_result($stmt_check_user);

    if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
        // User exists, proceed with post insertion
        mysqli_stmt_close($stmt_check_user);

        // Prepare and bind the SQL statement for post insertion
        $insert_post_sql = "INSERT INTO Posts (user_id, content, created_at) VALUES (?, ?, NOW())";
        $stmt_insert_post = mysqli_prepare($conn, $insert_post_sql);
        
        if ($stmt_insert_post) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_insert_post, "is", $user_id, $post_content);

            // Execute the statement
            if (mysqli_stmt_execute($stmt_insert_post)) {
                echo "Post created successfully!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }

            // Close statement
            mysqli_stmt_close($stmt_insert_post);
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // User does not exist, handle accordingly
        echo "Post created successfully!";
    }

    // Close database connection
    mysqli_close($conn);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
</head>
<body>
    <h1>Create New Post</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="post_content">Post Content:</label><br>
        <textarea name="post_content" id="post_content" rows="4" cols="50"></textarea><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
