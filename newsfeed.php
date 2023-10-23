<?php
// Start the session
session_start();

if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();

    // Redirect to index.php after logout
    header("Location: index.php");
    exit();
}

$loggedInUsername = "";

// Check if the user is logged in
// Check if the user is logged in
if (isset($_SESSION['UID'])) {
    $loggedInUID = $_SESSION['UID'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/nf.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Newsfeed</title>
</head> 
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
        <a href="?logout=1"><div id="logout" class="fa-solid fa-right-from-bracket"></div></a>
    </div>
    <div class="newsfeed">
        <?php
        // Connect to the database
require 'db_connection.php';

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve posts from the database with username, ordered by date (latest first)
        $sql = "SELECT Posts.*, Users.username, Users.pfp FROM Posts
                LEFT JOIN Users ON Posts.user_id = Users.UID
                ORDER BY Posts.date DESC"; // Order by date in descending order
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Output the post content
                echo '<div class="post">';
                echo '<div class="info">';
                echo '<img src="' . ($row['pfp'] ? 'data:image/jpeg;base64,' . base64_encode($row['pfp']) : 'RES/default_profile_picture.jpg') . '" alt="Profile Picture">';
                echo '<p class="username">' . $row['username'] . '</p>'; // Display the username of the post author
                echo '</div>';
                echo '<p class="topic">Topic: ' . $row['topic'] . '</p>';
                echo '<p class "category">Category: ' . $row['category'] . '</p>';
                echo '<p class="content">' . $row['content'] . '</p>';
                echo '<i id="posticon" class="fa-regular fa-heart"></i>';
                echo '<i id="posticon" class="fa-regular fa-comment"></i>';
                echo '<i id="posticon" class="fa-regular fa-share-from-square"></i>';
                echo '<p class="date">Date: ' . $row['date'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No posts available.</p>';
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>
</html>
