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

// Function to fetch user data
function fetchUserData($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id='$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Fetch user data
$user = fetchUserData($conn, $user_id);

// Update profile form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $university = $conn->real_escape_string($_POST['university']);
    $major = $conn->real_escape_string($_POST['major']);
    $year_of_study = $conn->real_escape_string($_POST['year_of_study']);
    $bio = $conn->real_escape_string($_POST['bio']);

    $update_sql = "UPDATE users SET name='$name', email='$email', university='$university', major='$major', year_of_study='$year_of_study', bio='$bio' WHERE id='$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        // Refresh user data after update
        $user = fetchUserData($conn, $user_id);
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        li strong {
            font-size: 1.2em;
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
            <?php require_once 'nav.php';?>
        </div>
    </header>

    <div class="container mt-5">
        <div class="group">
            <div class="card-header content">
                <h2>User Profile</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="university">University:</label>
                        <input type="text" class="form-control" id="university" name="university" value="<?php echo htmlspecialchars($user['university']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="major">Major:</label>
                        <input type="text" class="form-control" id="major" name="major" value="<?php echo htmlspecialchars($user['major']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="year_of_study">Year of Study:</label>
                        <input type="text" class="form-control" id="year_of_study" name="year_of_study" value="<?php echo htmlspecialchars($user['year_of_study']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <textarea class="form-control" id="bio" name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
