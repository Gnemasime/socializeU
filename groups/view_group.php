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

if (isset($_GET['group_id'])) {
    $group_id = $conn->real_escape_string($_GET['group_id']);
    
    // Get group details
    $sql_group = "SELECT * FROM study_groups WHERE id='$group_id'";
    $result_group = $conn->query($sql_group);
    $group = $result_group->fetch_assoc();
    
    // Get group posts
    $sql_posts = "SELECT group_posts.id, group_posts.content, group_posts.created_at, users.name 
                  FROM group_posts 
                  JOIN users ON group_posts.user_id = users.id 
                  WHERE group_posts.group_id='$group_id' 
                  ORDER BY group_posts.created_at DESC";
    $result_posts = $conn->query($sql_posts);
    
    // Check if the user is a member of the group
    $user_id = $_SESSION['user_id'];
    $sql_user_group = "SELECT * FROM group_memberships WHERE group_id='$group_id' AND user_id='$user_id'";
    $result_user_group = $conn->query($sql_user_group);
    $is_member = $result_user_group->num_rows > 0;
} else {
    echo "Invalid group.";
    exit();
}

// Function to display nested comments
function display_comments($conn, $post_id, $parent_id = null, $level = 0) {
    $sql_comments = "SELECT comments.id, comments.content, comments.created_at, users.name 
                     FROM comments 
                     JOIN users ON comments.user_id = users.id 
                     WHERE comments.post_id='$post_id' AND comments.parent_id " . ($parent_id ? "='$parent_id'" : "IS NULL") . " 
                     ORDER BY comments.created_at ASC";
    $result_comments = $conn->query($sql_comments);

    if ($result_comments->num_rows > 0) {
        while ($comment = $result_comments->fetch_assoc()) {
            echo '<div class="comment" style="margin-left: ' . ($level * 20) . 'px">';
            echo '<strong>' . htmlspecialchars($comment['name']) . '</strong><br>';
            echo '<p>' . htmlspecialchars($comment['content']) . '</p>';
            echo '<small>Commented on ' . htmlspecialchars($comment['created_at']) . '</small>';

            // Reply form
            echo '<form action="comment.php" method="post" class="comment-form">';
            echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($post_id) . '">';
            echo '<input type="hidden" name="parent_id" value="' . htmlspecialchars($comment['id']) . '">';
            echo '<textarea name="content" placeholder="Reply to this comment..." required></textarea><br>';
            echo '<input type="submit" value="Reply">';
            echo '</form>';

            // Recursively display replies
            display_comments($conn, $post_id, $comment['id'], $level + 1);

            echo '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Posts - <?php echo htmlspecialchars($group['name']); ?></title>
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
        h2 {
            color: #333;
        }
        .post, .comment {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .post small, .comment small {
            color: #666;
        }
        .comment {
            margin-left: 20px;
            margin-top: 10px;
            padding-left: 15px;
            border-left: 3px solid #50b3a2;
        }
        .comment-form {
            margin-top: 10px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            resize: vertical;
        }
        .comment-form input[type="submit"], .post form input[type="submit"], .post form button {
            background-color: #50b3a2;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .comment-form input[type="submit"]:hover, .post form input[type="submit"]:hover, .post form button:hover {
            background-color: #e8491d;
        }
        .group-details {
            margin-bottom: 20px;
        }
        .group-details p {
            font-size: 18px;
            color: #555;
        }
        .group-details h2 {
            margin-bottom: 10px;
        }
        .group-details h3 {
            margin-bottom: 10px;
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
                   <!-- <li><a href="search_tutors.html">Find Tutors</a></li>
                    <li><a href="my_bookings.php">My Bookings</a></li>-->
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content">
            <div class="group-details">
                <h2><?php echo htmlspecialchars($group['name']); ?></h2>
                <p><?php echo htmlspecialchars($group['description']); ?></p>
            </div>
            
            <a href="send_message.php?group_id=<?php echo htmlspecialchars($group_id); ?>">Send a Message</a>

            <?php if ($is_member): ?>
                <h3>Posts</h3>
                <form action="post_group.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">
                    <textarea name="content" placeholder="Post something..." required></textarea><br>
                    <input type="file" class="btn btn-default" name="file">
                    <br>
                    <input type="submit" class="btn btn-warning" value="Post">
                </form>

                <?php if ($result_posts->num_rows > 0): ?>
                    <?php while($post = $result_posts->fetch_assoc()): ?>
                        <div class="post">
                            <strong><?php echo htmlspecialchars($post['name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                            <small>Posted on <?php echo htmlspecialchars($post['created_at']); ?></small>

                            <!-- Display attached files if any -->
                            <?php
                            $sql_files = "SELECT * FROM files WHERE post_id='{$post['id']}'";
                            $result_files = $conn->query($sql_files);
                            if ($result_files->num_rows > 0): ?>
                                <p>Attached files:</p>
                                <ul>
                                    <?php while ($file = $result_files->fetch_assoc()): ?>
                                        <li>
                                           <!-- <a href="view_file.php?file_id=<?php echo htmlspecialchars($file['id']); ?>" target="_blank">
                                                <?php echo basename($file['file_path']); ?>
                                            </a>
                                            (<a href="<?php echo htmlspecialchars($file['file_path']); ?>" download>Download</a>)
                                        </li>-->
                                    <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>

                            <!-- Reactions form -->
                            <form action="react.php" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="reaction" value="like">Like</button>
                                <button type="submit" name="reaction" value="dislike">Dislike</button>
                            </form>

                            <!-- Save post form -->
                            <form action="save_post.php" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit">Save Post</button>
                            </form>

                            <!-- Comments Section -->
                            <div class="comments">
                                <h4>Comments:</h4>
                                <?php display_comments($conn, $post['id']); ?>
                            </div>

                            <!-- Comment form -->
                            <form action="comment.php" method="post" class="comment-form">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                                <textarea name="content" placeholder="Add a comment..." required></textarea><br>
                                <input type="submit" value="Comment">
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No posts yet.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>You are not a member of this group. <a href="join_group.php?group_id=<?php echo htmlspecialchars($group_id); ?>">Join this group</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection at the end of the script
$conn->close();
?>
