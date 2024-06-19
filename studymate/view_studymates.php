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
$sql = "SELECT users.id, users.name, users.email
        FROM users
        JOIN group_memberships ON users.id = group_memberships.user_id
        JOIN study_groups ON group_memberships.group_id = study_groups.id
        WHERE study_groups.id IN (
            SELECT group_id FROM group_memberships WHERE user_id = '$user_id'
        ) AND users.id != '$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Study Mates</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .mate-card {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .mate-card h5 {
            margin-top: 0;
        }
        .message-form {
            display: none;
            margin-top: 10px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
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
                    <li><a href="../groups/view_messages.php">Messages</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>My Study Mates</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-6'>";
                    echo "<div class='mate-card'>";
                    echo "<h5>" . htmlspecialchars($row["name"]) . "</h5>";
                    echo "<p>" . htmlspecialchars($row["email"]) . "</p>";
                    echo "<button class='btn btn-custom' onclick='showMessageForm(" . $row["id"] . ")'>Message</button>";
                    echo "<form action='send_message.php' method='post' class='message-form' id='message-form-" . $row["id"] . "'>";
                    echo "<input type='hidden' name='recipient_id' value='" . $row["id"] . "'>";
                    echo "<textarea class='form-control' name='message' placeholder='Write your message here...' required></textarea><br>";
                    echo "<button type='submit' class='btn btn-primary'>Send</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='col-12'><p>You have no study mates.</p></div>";
            }
            ?>
        </div>
    </div>

    <script>
        function showMessageForm(userId) {
            var form = document.getElementById('message-form-' + userId);
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
