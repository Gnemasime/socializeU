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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO group_memberships (group_id, user_id) VALUES ('$group_id', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: group_detail.php?group_id=" . $group_id);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
