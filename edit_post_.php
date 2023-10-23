<?php
// Start the session to access session variables
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['UID'])) {
    // Redirect to the login page if the user is not authenticated
    header("Location: index.php");
    exit();
}

// Establish a database connection
require 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = "";

if (isset($_GET['post_id'])) {
    // Get the post ID to be edited
    $post_id = $_GET['post_id'];

    // Retrieve the post content from the database based on the post_id
    $sql = "SELECT * FROM Posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch the post's current content
        $row = $result->fetch_assoc();
        $current_topic = $row['topic'];
        $current_category = $row['category'];
        $current_date = $row['date'];
        $current_content = $row['content'];
    } else {
        // Handle the case where the post is not found
        $error_message = "Post not found.";
    }
}

if (isset($_POST['update_post'])) {
    // Get updated post data
    $new_topic = $_POST['topic'];
    $new_category = $_POST['category'];
    $new_date = $_POST['date'];
    $new_content = $_POST['content'];

    // Update the post in the database
    $sql = "UPDATE Posts SET topic = ?, category = ?, date = ?, content = ? WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $new_topic, $new_category, $new_date, $new_content, $post_id);
    if ($stmt->execute()) {
        // Redirect back to the newsfeed page after updating
        header("Location: newsfeed.php");
        exit();
    } else {
        $error_message = "Error updating the post: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="CSS/Post.css">
    <link rel="stylesheet" href="CSS/nf.css">
</head>

<style>
    body {
        background-image: url("RES/bground.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-attachment: fixed;
        margin: 0;
    }
</style>

<body>
    <div class="container">
        <h2 class="logo">Logo</h2>
        <nav>
            <a href="newsfeed.php"><div id="navicon" class="fa-solid fa-house"></div></a>
            <a href="friend.php"><div id="navicon" class="fa-solid fa-users"></div></a>
            <?php
            if (!empty($loggedInUID)) {
                // Determine which profile to direct to based on the UID
                if ($loggedInUID == 1) {
                    echo '<a href="profile1.php"><div id="navicon" class="fa-solid fa-user"></div></a>';
                } elseif ($loggedInUID == 2) {
                    echo '<a href="profile2.php"><div id="navicon" class="fa-solid fa-user"></div></a>';
                } elseif ($loggedInUID == 3) {
                    echo '<a href="profile3.php"><div id="navicon" class="fa-solid fa-user"></div></a>';
                }
            }
            ?>
            <a href="post.php"><div id="navicon" class="fa-solid fa-file-pen"></div></a>
        </nav>
        <a href="index.php"><div id="logout" class="fa-solid fa-right-from-bracket"></div></a>
    </div>
    <div class="loginbox1">
        <img src="RES/ICon.png" class="avatar" id="photo">
        <h1>Edit Post</h1>
        <?php if (isset($error_message)) { ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="post" action="">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <p>
                <div class="awit">
                    Topic
                </div>
            </p>
            <input type="text" name="topic" placeholder="Enter your topic" value="<?php echo $current_topic; ?>" required>
            <p>
                <div class="awit">
                    Category
                </div>
            </p>
            <select name="category">
                <option value="HOME" <?php if ($current_category === 'HOME') echo 'selected'; ?>>Home</option>
                <option value="PERSONAL" <?php if ($current_category === 'PERSONAL') echo 'selected'; ?>>Personal</option>
                <option value="SCHOOL" <?php if ($current_category === 'SCHOOL') echo 'selected'; ?>>School</option>
                <option value="PEERS" <?php if ($current_category === 'PEERS') echo 'selected'; ?>>Peers</option>
                <option value="Others" <?php if ($current_category === 'Others') echo 'selected'; ?>>Others</option>
            </select>
            <p>
                <div class="awit">
                    Date
                </div>
            </p>
            <input type="date" name="date" value="<?php echo $current_date; ?>" required>
            <p>
                <div class="awit">
                    Content
                </div>
            </p>
            <textarea name="content" placeholder="Enter your content" required><?php echo $current_content; ?></textarea>
            <input type="submit" name="update_post" value="Update">
        </form>
    </div>
    <script>
        document.getElementById("logoutBtn").addEventListener("click", function() {
            var logoutConfirm = confirm("Are you sure you want to log out?");
            if (logoutConfirm) {
                window.location.href = "index.php"; // Redirect to the logout page
            }
        });
    </script>
</body>
</html>
