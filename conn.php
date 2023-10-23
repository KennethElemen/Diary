<?php
// Start the session
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

// Rest of your code to fetch and display user information
