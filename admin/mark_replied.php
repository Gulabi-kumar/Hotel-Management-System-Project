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

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $now = date('Y-m-d H:i:s');
    mysqli_query($conn, "UPDATE contact_messages SET replied_at = '$now' WHERE id = $id");
    echo "Updated";
}
?>