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
    $content = $conn->real_escape_string($_POST['content']);
    $parent_id = isset($_POST['parent_id']) ? $conn->real_escape_string($_POST['parent_id']) : 'NULL';

    $sql_insert_comment = "INSERT INTO comments (post_id, user_id, content, parent_id) VALUES ('$post_id', '$user_id', '$content', $parent_id)";
    if ($conn->query($sql_insert_comment) === TRUE) {
        echo "Comment added successfully.";
    } else {
        echo "Error adding comment: " . $conn->error;
    }

    // Redirect back to the group view page
    header("Location: view_group.php?group_id=" . $_POST['group_id']);
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
