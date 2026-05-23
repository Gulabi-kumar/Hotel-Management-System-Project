<?php
// reset_admin_password.php - Use once then DELETE!
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "hotel_db";

// Connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate new password
function generateStrongPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

$new_password = generateStrongPassword();
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update admin password
$sql = "UPDATE users 
        SET password = ? 
        WHERE email = 'admin@hotel.com' 
        OR username = 'admin' 
        OR is_admin = 1 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<h2>✅ Admin Password Reset Successful!</h2>";
        echo "<p><strong>New Password:</strong> " . htmlspecialchars($new_password) . "</p>";
        echo "<p><strong>Hashed Password:</strong> " . htmlspecialchars($hashed_password) . "</p>";
        echo "<p><strong>⚠️ IMPORTANT:</strong> </p>";
        echo "<ul>";
        echo "<li>Save this password immediately</li>";
        echo "<li>Login and change it after first login</li>";
        echo "<li>Delete this file from server</li>";
        echo "</ul>";
    } else {
        echo "No admin user found. Creating new admin...<br>";
        
        // Create admin if not exists
        $create_sql = "INSERT INTO users (username, email, password, is_admin, is_active) 
                       VALUES ('Admin', 'admin@hotel.com', ?, 1, 1)";
        $create_stmt = $conn->prepare($create_sql);
        $create_stmt->bind_param("s", $hashed_password);
        
        if ($create_stmt->execute()) {
            echo "<h2>✅ Admin Created!</h2>";
            echo "<p><strong>Email:</strong> admin@hotel.com</p>";
            echo "<p><strong>Password:</strong> " . htmlspecialchars($new_password) . "</p>";
        }
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>