<?php
// A utility script to securely reset the admin password.
// Place this in your root project folder and run it once by visiting the URL in your browser.
// IMPORTANT: Delete this file after you have successfully logged in.

require_once 'config/db.php';

// --- Configuration ---
$admin_username = 'admin';
$new_password = 'admin'; // The new password you want to set.

echo "<h1>Admin Password Reset Utility</h1>";

// Hash the new password securely
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

if ($new_password_hash === false) {
    die("<p style='color:red;'>Error: Could not hash the password. Please check your PHP installation.</p>");
}

// Prepare the update statement
$stmt = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");

if ($stmt === false) {
    die("<p style='color:red;'>Error preparing statement: " . htmlspecialchars($conn->error) . ". Please ensure the 'admin_users' table and its columns exist.</p>");
}

// Bind parameters and execute
$stmt->bind_param("ss", $new_password_hash, $admin_username);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green; font-weight:bold;'>Success! The password for user '" . htmlspecialchars($admin_username) . "' has been reset.</p>";
        echo "<p>You can now log in with:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> " . htmlspecialchars($admin_username) . "</li>";
        echo "<li><strong>Password:</strong> " . htmlspecialchars($new_password) . "</li>";
        echo "</ul>";
        echo "<p style='color:orange; font-weight:bold;'>IMPORTANT: Please delete this file (force_password_reset.php) from your server immediately for security reasons.</p>";
    } else {
        echo "<p style='color:red;'>Error: The query executed, but no rows were updated. Please make sure a user with the username '" . htmlspecialchars($admin_username) . "' exists in your 'admin_users' table.</p>";
    }
} else {
    echo "<p style='color:red;'>Error executing statement: " . htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$conn->close();
?>
