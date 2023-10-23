<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create a database connection (modify these with your actual database credentials)
require 'db_connection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prevent SQL injection by using prepared statements
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Authentication successful
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['UID'] = $row['UID']; // Store the user's UID in the session
        header('Location: newsfeed.php');
        exit();
    } else {
        $error_message = 'Invalid username or password';

        // Display the error message in an alert
        echo '<script>alert("' . $error_message . '");</script>';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/index.css">
    <title>Login</title>
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
<div class="loginbox">
    <img src="RES/ICon.png" class="avatar" id="photo">
    <h1>Log in</h1>
    <?php if (!empty($error_message)) { ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php } ?>
    <form method="post" action="index.php">
        <p>
            <div class="awit">
                Username
            </div>
        </p>
        <input type="text" name="username" placeholder="Enter Username" required>
        <p>Password</p>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="submit" name="login" value="Login">
    </form>
</div>
</body>
</html>
