<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studyhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $conn->real_escape_string($_POST['post_id']);

    // Check if the post is already saved
    $sql_check_save = "SELECT * FROM saved_posts WHERE user_id='$user_id' AND post_id='$post_id'";
    $result_check_save = $conn->query($sql_check_save);

    if ($result_check_save->num_rows == 0) {
        // Insert a new saved post entry
        $sql_insert_save = "INSERT INTO saved_posts (user_id, post_id) VALUES ('$user_id', '$post_id')";
        if ($conn->query($sql_insert_save) === TRUE) {
            echo "Post saved successfully.";
        } else {
            echo "Error saving post: " . $conn->error;
        }
    } else {
        echo "You have already saved this post.";
    }

    // Redirect back to the group view page
    header("Location: view_group.php?group_id=" . $_POST['group_id']);
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
