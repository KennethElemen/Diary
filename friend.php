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
if (isset($_SESSION['UID'])) {
    $loggedInUID = $_SESSION['UID'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/friend.css">
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

  <div class="user-post" id="friends">
    <h2>Friends</h2>
    <div class="friends-list center-friend-profiles">
        <?php
        // Define an array of names for the corresponding UIDs
        $friendNames = [
            1 => 'John Kenneth Elemen',
            2 => 'Sharmaine Soriano',
            3 => 'Shaina Dela Cruz',
        ];

        // Iterate through the UIDs and display friend profiles
        foreach ($friendNames as $friendUID => $friendName) {
            // Check if the current UID should be hidden (the logged-in user's profile)
            if ($friendUID != $loggedInUID) {
                echo '<div class="friend-profile">';
                echo '<div class="profile-picture2">';
                echo '<img src="RES/pic3.png" alt="Friend ' . $friendUID . '">';
                echo '</div>';
                echo '<div class="friend-details">';
                echo '<h2><a href="' . $friendUID . '.php">' . $friendName . '</a></h2>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>

</body>
</html>
