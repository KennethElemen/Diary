
<?php
// Start the session
session_start();
// Assuming you want to hide the logged-in user's profile
$loggedInUID = $_SESSION['UID'];

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

// Get the user's UID from the session
$userUID = $_SESSION['UID'];

// Query the database to retrieve user information, including the profile picture
$sql = "SELECT * FROM users WHERE UID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userUID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Fetch user information
    $row = $result->fetch_assoc();

    // Display the user's information
    $username = $row['username'];
    $name = $row['Name'];
    $address = $row['Address'];
    $course = $row['Course'];

    $coverPhotoURL = $row['cover'];

    // If the cover photo URL exists, use it; otherwise, use a default cover photo URL
    if ($coverPhotoURL) {
        $coverPhotoSrc = $coverPhotoURL;
    } else {
        // Use a default cover photo URL
        $coverPhotoSrc = 'default_cover_photo.jpg';
    }

    // Retrieve the profile picture BLOB data
    $profilePictureData = $row['pfp'];

    // If the profile picture BLOB data exists, convert it to a data URI
    if ($profilePictureData) {
        $profilePictureSrc = 'data:image/jpeg;base64,' . base64_encode($profilePictureData);
    } else {
        // Handle the case where there's no profile picture (you can display a default image)
        $profilePictureSrc = 'default_profile_picture.jpg';
    }
} else {
    // Handle the case where the user's information is not found
    // You can display an error message or redirect to an error page
    $error_message = "User information not found";
}

// Retrieve the user's posts from the database
// Retrieve the user's posts from the database, ordered by date (latest first)
$sql = "SELECT * FROM Posts WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userUID);
$stmt->execute();
$postsResult = $stmt->get_result();

$posts = [];
if ($postsResult->num_rows > 0) {
    while ($post = $postsResult->fetch_assoc()) {
        $posts[] = $post;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="CSS/profile2.css">
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
        <a href="index.php"><div id="logout" class="fa-solid fa-right-from-bracket"></div></a>
    </div>
</div>
    <div class "profile-container">
        <br><br>
        <div class="cover-photo">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($coverPhotoURL); ?>" alt="Cover Photo" style="max-width: 100%; max-height: 200px; ">
        </div>


        <div style="display: flex;">
            <div class="profile-picture">
                <img src="<?php echo $profilePictureSrc; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
        </div>

            <div class="user-info">
                <h1><?php echo $name; ?></h1>
                <!-- Display user's name and other information -->
                <p class="followers">13k followers</p>
            </div>
            <div class="info">
                <p><b>Address :</b> <?php echo $address; ?></p>
                <p><b>Course :</b> <?php echo $course; ?></p>
            </div>
        </div>
        <div class="navbar">
            <a href="#" id="timeline-link">Posts</a>
            <a href="#about">About Me</a>
            <a href="#friends">Friends</a>
            <a href="#gallery">Gallery</a>
        </div>
    </div>
  <div class="newsfeed">
    <?php foreach ($posts as $post) { ?>
        <div class="post">
            <div class="infopost">
                <!-- Display user information for each post -->
                <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
            </div>
            <p class="topic">Topic: <?php echo $post['topic']; ?></p>
            <p class="category">Category: <?php echo $post['category']; ?></p>
            <p class="content"><?php echo $post['content']; ?></p>
            <i id="posticon" class="fa-regular fa-heart"></i>
            <i id="posticon" class="fa-regular fa-comment"></i>
            <i id="posticon" class="fa-regular fa-share-from-square"></i>
            <p class="date">Date: <?php echo $post['date']; ?></p>
            <div class="icon-container">
                <div class="ellipsis-icon"><i class="fa-solid fa-ellipsis"></i></div>
                <div class="context-menu" id="context-menu">
                    <a href="edit_post_.php?post_id=<?php echo $post['post_id']; ?>">Edit</a>
                    <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>">Delete</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
    <div class="newsfeed" id="about">   
        <div class="post">
            <div class="infopost">
               <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
            </div>
            <p>I'm a BSIT student
                in Araullo University-South Campus,
                with specialization in Web
                Development. I like listening
                to music and All I do is study all day all night every day. My favorite sports is badminton</p>
            <i id="posticon" class="fa-regular fa-heart"></i>
            <i id="posticon" class="fa-regular fa-comment"></i> 
            <i id="posticon" class="fa-regular fa-share-from-square"></i>
        </div> 
    </div>
    <div style="margin-top: 250px;" class="newsfeed" id="music">
        <div class="post">
            <div class="infopost">
                <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
            </div>
            <div class="music-container">
                <div class="music-info">
                <img src="RES/ALBUM.jpg" alt="Album Cover">
                <div class="song-details">
                   <h3>HAWAK KAMAY</h3>
                   <p>By Yeng Constantino</p>
               </div>
            </div>
            <div class="audio-controls" >   
               <audio controls autoplay>
                   <source src="RES/SH1.mp3" type="audio/mpeg">
               </audio>
           </div>
        </div>
            <i id="posticon" class="fa-regular fa-heart"></i>
            <i id="posticon" class="fa-regular fa-comment"></i> 
            <i id="posticon" class="fa-regular fa-share-from-square"></i> 
    </div>
    <div class="post">
        <div class="infopost">
            <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
        </div>
        <div class="music-container">
            <div class="music-info">
            <img src="RES/SHA1.jpg" alt="Album Cover">
            <div class="song-details">
               <h3>NARDA</h3>
               <p>By KAMIKAZEE</p>
           </div>
        </div>
        <div class="audio-controls" >
           <audio controls autoplay>
               <source src="RES/SH2.mp3" type="audio/mpeg">
           </audio>
       </div>
    </div>
        <i id="posticon" class="fa-regular fa-heart"></i>
        <i id="posticon" class="fa-regular fa-comment"></i> 
        <i id="posticon" class="fa-regular fa-share-from-square"></i> 
</div>
<div class="post">
    <div class="infopost">
        <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
    </div>
    <div class="music-container">
        <div class="music-info">
        <img src="RES/SHA2.jpg" alt="Album Cover">
        <div class="song-details">
           <h3>BUWAN</h3>
           <p>By Juan Karlos</p>
       </div>
    </div>
    <div class="audio-controls" >
       <audio controls autoplay>
           <source src="RES/SH3.mp3" type="audio/mpeg">
       </audio>
   </div>
</div>
    <i id="posticon" class="fa-regular fa-heart"></i>
    <i id="posticon" class="fa-regular fa-comment"></i> 
    <i id="posticon" class="fa-regular fa-share-from-square"></i> 
</div>
    </div>
<div style="margin-top: 1250px;" class="newsfeed" id="iframe">
    <div class="post">
        <div class="infopost">
          <img src="<?php echo $profilePictureSrc; ?>" alt="User's Profile Picture">
                <p class="username"><?php echo $username; ?></p>
        </div>
        <div style="margin-top: 20px; margin-left: 50px;">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/BXhuVOlnYPc?si=WSQwQgt7w_Vh0k3q" title="YouTube video player"  allowfullscreen></iframe>
        </div>
        <i id="posticon" class="fa-regular fa-heart"></i>
            <i id="posticon" class="fa-regular fa-comment"></i> 
            <i id="posticon" class="fa-regular fa-share-from-square"></i> 
    </div>
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

    <div class="gallery" id="gallery">
        <h2>Gallery</h2>
        <div class="gallery-row">
        <div class="gallery-item">
            <img src="RES/pic1.jpg" alt="Photo 1">
            <div class="img-des">
            <p class="img-des">I like dogs</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="RES/pic2.jpg" alt="Photo 2">
            <div class="img-des">
            <p class="img-des">I am a coffee lover</p>
            </div>
        </div>
        </div>
        <div class="gallery-row">
        <div class="gallery-item">
            <img src="RES/pic3.jpg" alt="Photo 4">
            <div class="img-des">
            <p class="img-des">I like to bake</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="RES/pic4.jpg" alt="Photo 5">
            <div class="img-des">
            <p class="img-des">I like to listen with music specially pop songs </p>
            </div>
        </div>
        
        
    </div>
    <div class="gallery-row">
        <div class="gallery-item">
            <img src="RES/pic5.jpg" alt="Photo 4">
            <div class="img-des">
            <p class="img-des">Blackpink members are my K-POP idols</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="RES/pic6.jpg" alt="Photo 5">
            <div class="img-des">
            <p class="img-des">One of my favorite Anime.</p>
            </div>
        </div>
    </div>
    <div class="gallery-row">
        <div class="gallery-item">
            <img src="RES/pic7.jpg" alt="Photo 4">
            <div class="img-des">
            <p class="img-des">I like Travelling.</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="RES/Pic8.jpg" alt="Photo 5">  
            <div class="img-des">
            <p class="img-des">This is my Highschool friends</p>
            </div>
        </div>
    </div>
    <div class="gallery-row">
        <div class="gallery-item">
            <img src="RES/pic9.jpg" alt="Photo 4">
            <div class="img-des">
            <p class="img-des">I Like To Play Badminton</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="RES/pic10.jpg" alt="Photo 5">
            <div class="img-des">
            <p class="img-des">I like this movie.</p>
            </div>
        </div>
    </div>

</div>


    
</body>

<script>
    document.getElementById("timeline-link").addEventListener("click", function(event) {
        event.preventDefault();
        // Hide the "About" and friends list sections when the Timeline link is clicked
        document.getElementById("about").style.display = "none";
        document.getElementById("friends").style.display = "none";
        // Show the user post and the music div
        document.querySelector(".newsfeed").style.display = "block";
        document.querySelector(".user-post").style.display = "none"; 
        document.getElementById("music").style.display = "none";
        document.getElementById("iframe").style.display = "none";
        document.getElementById("gallery").style.display = "none";
    });

    document.querySelector("a[href='#about']").addEventListener("click", function(event) {
        event.preventDefault();
        // Show the "About" section and hide the friends list and user post sections when the About link is clicked
        document.querySelector(".newsfeed").style.display = "none";
        document.getElementById("about").style.display = "block";
        document.getElementById("friends").style.display = "none";
        document.querySelector(".user-post").style.display = "none";
        document.getElementById("music").style.display = "block";
        document.getElementById("iframe").style.display = "block";
        document.getElementById("gallery").style.display = "none";
    });

    document.querySelector("a[href='#friends']").addEventListener("click", function(event) {
        event.preventDefault();
        // Hide the "About" section, user post, and music div when the Friends link is clicked, and show the friends list
        document.querySelector(".newsfeed").style.display = "none";
        document.getElementById("about").style.display = "none";
        document.querySelector(".user-post").style.display = "none";
        document.getElementById("music").style.display = "none";
        document.getElementById("friends").style.display = "block";
        document.getElementById("iframe").style.display = "none";
        document.getElementById("gallery").style.display = "none";
    });

    document.querySelector("a[href='#gallery']").addEventListener("click", function(event) {
        event.preventDefault();
        // Hide the "About" section, user post, and music div when the Friends link is clicked, and show the friends list
        document.querySelector(".newsfeed").style.display = "none";
        document.getElementById("about").style.display = "none";
        document.querySelector(".user-post").style.display = "none";
        document.getElementById("music").style.display = "none";
        document.getElementById("friends").style.display = "none";
        document.getElementById("iframe").style.display = "none";
        document.getElementById("gallery").style.display = "block";
    });

const ellipsisIcon = document.querySelector('.ellipsis-icon');
const contextMenu = document.getElementById('context-menu');
const editOption = document.getElementById('edit-option');
const deleteOption = document.getElementById('delete-option');

ellipsisIcon.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent the click event from propagating to the document

    // Toggle the visibility of the context menu
    contextMenu.style.display = contextMenu.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', (e) => {
    // Hide the context menu when clicking outside of it
    if (e.target !== ellipsisIcon && e.target !== contextMenu) {
        contextMenu.style.display = 'none';
    }
});

// Define actions for the Edit and Delete options
editOption.addEventListener('click', () => {
    alert('Edit option clicked');
    contextMenu.style.display = 'none';
});

deleteOption.addEventListener('click', () => {
    alert('Delete option clicked');
    contextMenu.style.display = 'none';
});

</script>
</html>