<?php
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $university = $_POST['university'];
    $major = $_POST['major'];
    $year_of_study = $_POST['year_of_study'];
    $bio = $_POST['bio'];
    $profile_picture = $_FILES['profile_picture']['name'];

    // Upload profile picture
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

    $sql = "INSERT INTO users (name, email, password, university, major, year_of_study, bio, profile_picture)
            VALUES ('$name', '$email', '$password', '$university', '$major', '$year_of_study', '$bio', '$target_file')";

    if ($conn->query($sql) === TRUE) {
         header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
    <h2 class="text-center mt-5">Register</h2> <hr> <br>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="university">University:</label>
                <input type="text" class="form-control" id="university" name="university">
            </div>
            <div class="form-group">
                <label for="major">Major:</label>
                <input type="text" class="form-control" id="major" name="major">
            </div>
            <div class="form-group">
                <label for="year_of_study">Year of Study:</label>
                <input type="number" class="form-control" id="year_of_study" name="year_of_study">
            </div>
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea class="form-control" id="bio" name="bio"></textarea>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
            </div>
            <button type="submit" class="btn btn-primary">Register</button> 
            <br><br>
            <p>Already Registered? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>
