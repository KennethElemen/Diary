<?php
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Establish a database connection
require 'db_connection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete the post from the database based on the post_id
    $sql = "DELETE FROM Posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        // Post deleted successfully, you can add a success message if needed
    } else {
        // An error occurred while deleting the post, you can handle the error
        // For example, you can redirect to an error page or display an error message
    }

    // Close the database connection
    $conn->close();

    // Redirect back to the newsfeed page after deletion
    header("Location: newsfeed.php");
    exit();
}
?>
