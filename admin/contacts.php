<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hotel_db';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'delete') {
        mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $id");
    } elseif ($action == 'mark_read') {
        mysqli_query($conn, "UPDATE contact_messages SET is_read = 1 WHERE id = $id");
    } elseif ($action == 'mark_unread') {
        mysqli_query($conn, "UPDATE contact_messages SET is_read = 0 WHERE id = $id");
    }
}

// Get all contact messages
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f8fafc;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .unread {
            background: #ffd700;
            color: #333;
        }

        .read {
            background: #4CAF50;
            color: white;
        }

        .action-btn {
            padding: 3px 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.85rem;
        }

        .view-btn {
            background: #2196F3;
            color: white;
        }

        .read-btn {
            background: #4CAF50;
            color: white;
        }

        .unread-btn {
            background: #FF9800;
            color: white;
        }

        .delete-btn {
            background: #f44336;
            color: white;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        .close-btn {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }

        .message-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            white-space: pre-wrap;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Back and Dashboard Buttons -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <a href="javascript:history.back()" style="padding: 8px 16px; background: #666; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                ← Back
            </a>
            <a href="dashboard.php" style="padding: 8px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                ← Dashboard
            </a>
        </div>
        
        <h1>Contact Messages</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Replied At</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td>
                                <?php
                                $message = htmlspecialchars($row['message']);
                                echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                ?>
                            </td>
                            <td>
                                <span class="status <?php echo $row['is_read'] ? 'read' : 'unread'; ?>">
                                    <?php echo $row['is_read'] ? 'Read' : 'Unread'; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $row['replied_at'] ? date('Y-m-d H:i', strtotime($row['replied_at'])) : 'Not replied'; ?>
                            </td>
                            <td>
                                <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                            </td>
                            <td>
                                <button class="action-btn view-btn" onclick="viewMessage(<?php echo $row['id']; ?>)">
                                    View
                                </button>
                                <?php if (!$row['is_read']): ?>
                                    <a href="?action=mark_read&id=<?php echo $row['id']; ?>" class="action-btn read-btn">
                                        Mark Read
                                    </a>
                                <?php else: ?>
                                    <a href="?action=mark_unread&id=<?php echo $row['id']; ?>" class="action-btn unread-btn">
                                        Mark Unread
                                    </a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $row['id']; ?>"
                                    class="action-btn delete-btn"
                                    onclick="return confirm('Delete this message?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No contact messages found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div id="messageDetails"></div>
        </div>
    </div>

    <script>
        function viewMessage(id) {
            fetch('get_message.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('messageDetails').innerHTML = data;
                    document.getElementById('messageModal').style.display = 'block';
                });
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        function markReplied(id) {
            fetch('mark_replied.php?id=' + id)
                .then(response => {
                    if (response.ok) {
                        alert('Marked as replied!');
                        location.reload();
                    }
                });
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                closeModal();
            }
        }
    </script>
</body>
</html>