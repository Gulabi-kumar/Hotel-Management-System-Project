<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$error = '';
$success = '';

// Handle user actions
if (isset($_GET['action'])) {
    $user_id = sanitize($_GET['id']);
    $action = sanitize($_GET['action']);
    
    switch ($action) {
        case 'activate':
            $query = "UPDATE users SET is_active = 1 WHERE id = $user_id";
            if (mysqli_query($conn, $query)) {
                $success = "User activated successfully!";
            }
            break;
            
        case 'deactivate':
            $query = "UPDATE users SET is_active = 0 WHERE id = $user_id";
            if (mysqli_query($conn, $query)) {
                $success = "User deactivated successfully!";
            }
            break;
            
        case 'delete':
            // Check if user has bookings
            $check_query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id";
            $check_result = mysqli_query($conn, $check_query);
            $check_data = mysqli_fetch_assoc($check_result);
            
            if ($check_data['count'] > 0) {
                $error = "Cannot delete user with existing bookings!";
            } else {
                $query = "DELETE FROM users WHERE id = $user_id";
                if (mysqli_query($conn, $query)) {
                    $success = "User deleted successfully!";
                }
            }
            break;
            
        case 'make_admin':
            // For admin promotion (you might want a separate admin table)
            break;
    }
}

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$query = "SELECT u.*, 
                 (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings,
                 (SELECT SUM(total_amount) FROM bookings WHERE user_id = u.id AND status IN ('Confirmed', 'Completed')) as total_spent
          FROM users u
          WHERE 1=1";
          
if ($search) {
    $query .= " AND (u.full_name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.mobile LIKE '%$search%')";
}

if ($status_filter == 'active') {
    $query .= " AND u.is_active = 1";
} elseif ($status_filter == 'inactive') {
    $query .= " AND u.is_active = 0";
} elseif ($status_filter == 'verified') {
    $query .= " AND u.is_verified = 1";
} elseif ($status_filter == 'unverified') {
    $query .= " AND u.is_verified = 0";
}

$query .= " ORDER BY u.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container" style="padding: 2rem 0;">
        <h2>Manage Users</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Search and Filter -->
        <div class="card">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <form method="GET" action="" style="flex: 1; min-width: 300px;">
                    <div class="form-group">
                        <label>Search Users</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search by name, email, or mobile" value="<?php echo $search; ?>">
                            <button type="submit" class="btn">Search</button>
                            <a href="users.php" class="btn" style="background: #6c757d;">Reset</a>
                        </div>
                    </div>
                </form>
                
                <div>
                    <label>Filter by Status</label>
                    <select class="form-control" onchange="window.location.href='users.php?status='+this.value">
                        <option value="">All Users</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="verified" <?php echo $status_filter == 'verified' ? 'selected' : ''; ?>>Verified</option>
                        <option value="unverified" <?php echo $status_filter == 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="card" style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Registered Users</h3>
                <span>
                    Total: <?php echo mysqli_num_rows($result); ?> users
                </span>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>User Details</th>
                        <th>Contact Info</th>
                        <th>Account Info</th>
                        <th>Booking Stats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo $user['full_name']; ?></strong><br>
                                <small>Joined: <?php echo date('d M Y', strtotime($user['created_at'])); ?></small>
                            </td>
                            <td>
                                <i class="fas fa-envelope"></i> <?php echo $user['email']; ?><br>
                                <i class="fas fa-phone"></i> <?php echo $user['mobile']; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $user['is_verified'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $user['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                </span>
                                <br>
                                <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                Bookings: <?php echo $user['total_bookings']; ?><br>
                                Spent: ₹<?php echo number_format($user['total_spent'] ?? 0, 2); ?>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 5px;">
                                    <?php if ($user['is_active']): ?>
                                        <a href="users.php?action=deactivate&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-warning btn-sm"
                                           onclick="return confirm('Deactivate this user?')">
                                            Deactivate
                                        </a>
                                    <?php else: ?>
                                        <a href="users.php?action=activate&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-success btn-sm">
                                            Activate
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this user permanently?')">
                                        Delete
                                    </a>
                                    
                                    <a href="user-details.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-info btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>