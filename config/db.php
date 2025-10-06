<?php
// --- Database Configuration ---
// Replace these values with your actual database credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your database username
define('DB_PASS', '');     // Your database password
define('DB_NAME', 'temple_website'); // Your database name

// --- Establish Connection ---
// Create a new mysqli connection.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for a connection error. If one exists, stop the script and display the error.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support.
$conn->set_charset("utf8mb4");

// Start a session to manage user login state across pages.
// session_start();
?>
