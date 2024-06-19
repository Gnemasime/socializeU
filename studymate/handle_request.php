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
    $request_id = $conn->real_escape_string($_POST['request_id']);
    $action = $conn->real_escape_string($_POST['action']);
    
    if ($action == 'accept') {
        $status = 'accepted';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    } else {
        echo "Invalid action.";
        exit();
    }

    // Update connection status
    $sql_update = "UPDATE connections SET status='$status' WHERE id='$request_id'";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "Request $status successfully.";
        header("Location: manage_requests.php");
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
