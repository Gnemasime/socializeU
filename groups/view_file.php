<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['file_id'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "studyhub";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $file_id = $conn->real_escape_string($_GET['file_id']);
    $sql = "SELECT * FROM files WHERE id='$file_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $file = $result->fetch_assoc();
        $file_path = $file['file_path'];
        $file_type = mime_content_type($file_path);
    } else {
        echo "File not found.";
        exit();
    }
} else {
    echo "Invalid file.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .viewer {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="viewer">
        <?php if (strpos($file_type, 'image') !== false): ?>
            <img src="<?php echo htmlspecialchars($file_path); ?>" alt="File Image" style="max-width: 100%; height: auto;">
        <?php elseif (strpos($file_type, 'pdf') !== false): ?>
            <embed src="<?php echo htmlspecialchars($file_path); ?>" type="application/pdf" width="600" height="500">
        <?php else: ?>
            <p>Cannot display this file type.</p>
        <?php endif; ?>
    </div>
</body>
</html>
