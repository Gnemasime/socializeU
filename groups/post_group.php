<?php/*
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
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $user_id = $_SESSION['user_id'];
    $content = $conn->real_escape_string($_POST['content']);

    if (!empty($content)) {
        $sql = "INSERT INTO group_posts (group_id, user_id, content) VALUES ('$group_id', '$user_id', '$content')";
        if ($conn->query($sql) === TRUE) {
            header("Location: view_group.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Content cannot be empty.";
    }
}

$conn->close();*/
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $user_id = $_SESSION['user_id'];
    $content = $conn->real_escape_string($_POST['content']);

    if (!empty($content)) {
        $sql = "INSERT INTO group_posts (group_id, user_id, content) VALUES ('$group_id', '$user_id', '$content')";
        if ($conn->query($sql) === TRUE) {
            $post_id = $conn->insert_id;

            // Handle file upload
            if (!empty($_FILES['file']['name'])) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

                $sql_file = "INSERT INTO files (post_id, file_path) VALUES ('$post_id', '$target_file')";
                $conn->query($sql_file);
            }

            header("Location: view_group.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Content cannot be empty.";
    }
}

$conn->close();
?>
