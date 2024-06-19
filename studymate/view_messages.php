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
$sql = "SELECT messages.id, messages.content, messages.created_at, 
        sender.name as sender_name, receiver.name as receiver_name
        FROM messages
        JOIN users as sender ON messages.sender_id = sender.id
        JOIN users as receiver ON messages.receiver_id = receiver.id
        WHERE messages.sender_id = '$user_id' OR messages.receiver_id = '$user_id'
        ORDER BY messages.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
       
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
            margin-right: 15px;
        }
        header ul {
            padding: 0;
            list-style: none;
            text-align: center;
        }
        header li {
            display: inline;
            padding: 0 20px;
        }
        header #branding {
            text-align: center;
        }
        .container {
            margin-top: 20px;
        }
        .message-card {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message-card h5 {
            margin-top: 0;
        }
        .message-card small {
            color: #666;
        }
    </style>

<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(80, 179, 162, 0.5);
            border-radius: 8px;
        }
        header {
            background: #50b3a2;
            color: #ffffff;
            padding: 20px 0;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
            margin-right: 15px;
        }
        header ul {
            padding: 0;
            list-style: none;
            text-align: center;
        }
        header li {
            display: inline;
            padding: 0 20px;
        }
        header #branding {
            text-align: center;
        }
        .content {
            margin-top: 20px;
        }
        .content h2 {
            color: #333;
        }
        .group {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .group h3 {
            margin-top: 0;
        }
        .group p {
            color: #666;
        }
        a.button {
            display: inline-block;
            background-color: #50b3a2;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
        a.button:hover {
            background-color: #e8491d;
        }
        ul {
            padding: 0;
            list-style: none;
        }
        ul li a {
            display: inline-block;
            background-color: #50b3a2;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px 0;
        }
        ul li a:hover {
            background-color: #e8491d;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <br>
                <h1 style="color:black">StudyHub</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../profile/profile.php">Profile</a></li>
                    <li><a href="../groups/groups.php">Study Groups</a></li>
                    <li><a href="view_messages.php">Messages</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Your Messages</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($message = $result->fetch_assoc()): ?>
                    <div class="col-md-12">
                        <div class="message-card">
                            <h5><strong>From:</strong> <?php echo htmlspecialchars($message['sender_name']); ?> <strong>To:</strong> <?php echo htmlspecialchars($message['receiver_name']); ?></h5>
                            <p><?php echo htmlspecialchars($message['content']); ?></p>
                            <small>Sent on <?php echo htmlspecialchars($message['created_at']); ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
