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

$group_id = $_GET['group_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM study_groups WHERE id='$group_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $group = $result->fetch_assoc();
} else {
    echo "Group not found";
    exit();
}

$sql_members = "SELECT users.id, users.name FROM users 
                JOIN group_memberships ON users.id = group_memberships.user_id 
                WHERE group_memberships.group_id = '$group_id'";
$result_members = $conn->query($sql_members);

$is_member = false;
$sql_check_membership = "SELECT * FROM group_memberships WHERE group_id='$group_id' AND user_id='$user_id'";
$result_check_membership = $conn->query($sql_check_membership);
if ($result_check_membership->num_rows > 0) {
    $is_member = true;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2><?php echo $group['name']; ?></h2>
            </div>
            <div class="card-body">
                <p><?php echo $group['description']; ?></p>
                <p><em>Created on: <?php echo $group['created_at']; ?></em></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3>Members</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    if ($result_members->num_rows > 0) {
                        while($member = $result_members->fetch_assoc()) {
                            echo "<li class='list-group-item'>" . $member['name'] . "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No members found.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <?php if (!$is_member) { ?>
            <form action="join_group.php" method="post" class="mt-4">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <button type="submit" class="btn btn-primary">Join Group</button>
            </form>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
