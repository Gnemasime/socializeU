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
    $reaction = $conn->real_escape_string($_POST['reaction']);

    // Check if the reaction is valid
    if (!in_array($reaction, ['like', 'dislike'])) {
        echo "Invalid reaction.";
        exit();
    }

    // Check if the user has already reacted to this post
    $sql_check_reaction = "SELECT * FROM reactions WHERE user_id='$user_id' AND post_id='$post_id'";
    $result_check_reaction = $conn->query($sql_check_reaction);

    if ($result_check_reaction->num_rows > 0) {
        // Update the existing reaction
        $sql_update_reaction = "UPDATE reactions SET reaction_type='$reaction' WHERE user_id='$user_id' AND post_id='$post_id'";
        $result_update_reaction = $conn->query($sql_update_reaction);
    } else {
        // Insert a new reaction
        $sql_insert_reaction = "INSERT INTO reactions (user_id, post_id, reaction_type) VALUES ('$user_id', '$post_id', '$reaction')";
        $result_insert_reaction = $conn->query($sql_insert_reaction);
    }

    // Redirect back to the group view page
    header("Location: view_group.php?group_id=" . $_POST['group_id']);
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
