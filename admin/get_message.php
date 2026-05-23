<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hotel_db';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM contact_messages WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        // Mark as read when viewed
        mysqli_query($conn, "UPDATE contact_messages SET is_read = 1 WHERE id = $id");
        
        echo '
        <h2>Message Details</h2>
        
        <p><strong>ID:</strong> ' . $row['id'] . '</p>
        <p><strong>Name:</strong> ' . htmlspecialchars($row['name']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>
        <p><strong>Subject:</strong> ' . htmlspecialchars($row['subject']) . '</p>
        
        <p><strong>Status:</strong> ' . ($row['is_read'] ? 'Read' : 'Unread') . '</p>
        <p><strong>Replied At:</strong> ' . ($row['replied_at'] ? date('Y-m-d H:i', strtotime($row['replied_at'])) : 'Not replied') . '</p>
        <p><strong>Created At:</strong> ' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</p>
        
        <div class="message-box">
            <strong>Message:</strong><br>
            ' . nl2br(htmlspecialchars($row['message'])) . '
        </div>
        
        <div style="margin-top: 20px;">
            <button onclick="markReplied(' . $row['id'] . ')" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Mark as Replied
            </button>
            <button onclick="closeModal()" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Close
            </button>
        </div>';
    } else {
        echo '<p>Message not found</p>';
    }
}
?>