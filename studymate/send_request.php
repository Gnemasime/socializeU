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
    $mate_id = $conn->real_escape_string($_POST['mate_id']);
    
    // Check if a request already exists
    $sql_check = "SELECT * FROM connections WHERE user_id='$user_id' AND mate_id='$mate_id' AND status='pending'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 0) {
        // Insert connection request
        $sql_insert = "INSERT INTO connections (user_id, mate_id) VALUES ('$user_id', '$mate_id')";
        
        if ($conn->query($sql_insert) === TRUE) {
            echo "Request sent successfully.";
        } else {
            echo "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    } else {
        echo "You have already sent a request to this user.";
    }

    header("Location: list_users.php");
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
