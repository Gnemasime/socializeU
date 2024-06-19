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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $conn->real_escape_string($_POST['receiver_id']);
    $content = $conn->real_escape_string($_POST['content']);
    
    $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES ('$sender_id', '$receiver_id', '$content')";
    if ($conn->query($sql) === TRUE) {
        header("Location: view_messages.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch study mates
$group_id = $conn->real_escape_string($_GET['group_id']);
$sql_mates = "SELECT users.id, users.name 
              FROM users 
              JOIN group_memberships ON users.id = group_memberships.user_id 
              WHERE group_memberships.group_id = '$group_id' AND users.id != {$_SESSION['user_id']}";
$result_mates = $conn->query($sql_mates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
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
                    <li><a href="groups.php">Study Groups</a></li>
                    <li><a href="view_messages.php">Messages</a></li>
                   <!-- <li><a href="tutors/search_tutors.html">Find Tutors</a></li>
                    <li><a href="bookings/my_bookings.php">My Bookings</a></li>-->
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container mt-4">
        <h2>Send a Message</h2>
        <form action="send_message.php?group_id=<?php echo htmlspecialchars($group_id); ?>" method="post">
            <div class="form-group">
                <label for="receiver">Send to:</label>
                <select name="receiver_id" id="receiver" class="form-control" required>
                    <?php while ($mate = $result_mates->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($mate['id']); ?>">
                            <?php echo htmlspecialchars($mate['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Message:</label>
                <textarea name="content" id="content" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
