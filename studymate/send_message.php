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

$user_id = $_SESSION['user_id'];
$receiver_id = $conn->real_escape_string($_POST['receiver_id']);
$content = $conn->real_escape_string($_POST['content']);

$sql = "INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES ('$user_id', '$receiver_id', '$content', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Message sent successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: view_studymates.php");
exit();
?>
