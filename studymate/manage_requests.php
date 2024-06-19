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

// Fetch incoming requests
$sql_requests = "SELECT connections.id, users.name FROM connections 
                 JOIN users ON connections.user_id = users.id 
                 WHERE connections.mate_id = '$user_id' AND connections.status = 'pending'";
$result_requests = $conn->query($sql_requests);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #50b3a2;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            list-style: none;
        }
        header li {
            display: inline;
            padding: 0 20px 0 20px;
        }
        .content {
            margin-top: 20px;
        }
        .request {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
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
                    <!--<li><a href="search_tutors.html">Find Tutors</a></li>
                    <li><a href="my_bookings.php">My Bookings</a></li>-->
                    <li><a href="../groups/view_messages.php">Messages</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content">
            <h2>Manage Connection Requests</h2>
            <?php if ($result_requests->num_rows > 0): ?>
                <?php while($request = $result_requests->fetch_assoc()): ?>
                    <div class="request">
                        <strong><?php echo htmlspecialchars($request['name']); ?></strong><br>
                        <form action="handle_request.php" method="post" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="action" value="accept">Accept</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending requests.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
