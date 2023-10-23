<?php
// Start the session to access session variables
session_start();

// Establish a database connection. Replace with your database credentials.
require 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['post'])) {
    $topic = $_POST['topic'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $content = $_POST['content'];

    // Check if the user is authenticated and has a valid UID in the session
    if (isset($_SESSION['UID'])) {
        $user_id = $_SESSION['UID'];

        // Insert data into the Posts table.
        $sql = "INSERT INTO Posts (user_id, topic, category, date, content) VALUES ('$user_id', '$topic', '$category', '$date', '$content')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to newsfeed.php after successful submission.
            header("Location: newsfeed.php");
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    } else {
        $error_message = "User is not authenticated."; // Handle authentication issues.
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="CSS/Post.css">
     <link rel="stylesheet" href="CSS/nf.css">
    <title>post</title>
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
    <a href="login.php"><div id="logout" class="fa-solid fa-right-from-bracket"></div></a>
    </div>
    <div class="loginbox1">
        <img src="RES/ICon.png" class="avatar" id="photo">
        <h1>Post Something</h1>
        <form method="post" action="">
            <?php if (isset($error_message)) { ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php } ?>
            <p>
                <div class="awit">
                    Topic
                </div>
            </p>
            <input type="text" name="topic" placeholder="Enter your topic" required>
            <p>
                <div class="awit">
                    Category
                </div>
            </p>
            <select name="category">
                <option value="HOME">Home</option>
                <option value="PERSONAL">Personal</option>
                <option value="SCHOOL">School</option>
                <option value="PEERS">Peers</option>
                <option value="Others">Others</option>
            </select>
              <p>
                <div class="awit">
                    Date
                </div>
            </p>
            <input type="date" name="date" required>
            <p>
                <div class="awit">
                    Content
                </div>
            </p>
            <textarea name="content" placeholder="Enter your content" required></textarea>
            <input type="submit" name="post" value="Post">
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
