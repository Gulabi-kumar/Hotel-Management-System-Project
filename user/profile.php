<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireUser();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user details
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $mobile = sanitize($_POST['mobile']);
    
    if (empty($full_name) || empty($mobile)) {
        $error = "All fields are required!";
    } else {
        $update_query = "UPDATE users SET full_name = '$full_name', mobile = '$mobile' 
                        WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['user_name'] = $full_name;
            $success = "Profile updated successfully!";
            // Refresh user data
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating profile!";
        }
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long!";
    } else {
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
    
    @media (max-width: 480px) {
        #profile-headline{
            padding-left: 30px;
        }
        .container{
            font-size: 15px;
        }
        .card .stat-card h4{
            font-size: 15px;
        }
        .card .stat-card .number{
            font-size: 18px;
        }
        .btn{
            font-size: 10px;
        }
    }
</style>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container" style="padding: 2rem 0;">
        <h2 id="profile-headline">My Profile</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
                <!-- Profile Information -->
                <div style="flex: 1; min-width: 300px;">
                    <h3>Profile Information</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="full_name" 
                                   value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                            <small>Email cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" class="form-control" name="mobile" 
                                   value="<?php echo $user['mobile']; ?>" pattern="[0-9]{10}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo date('d M Y', strtotime($user['created_at'])); ?>" disabled>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn">Update Profile</button>
                    </form>
                </div>
                
                <!-- Change Password -->
                <div style="flex: 1; min-width: 300px;">
                    <h3>Change Password</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                            <small>Minimum 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Account Statistics -->
        <div class="card" style="margin-top: 2rem;">
            <h3>Account Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <?php
                // Get statistics
                $total_bookings = mysqli_fetch_assoc(mysqli_query($conn, 
                    "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id"))['count'];
                
                $completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, 
                    "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id AND status = 'Completed'"))['count'];
                
                $total_spent = mysqli_fetch_assoc(mysqli_query($conn, 
                    "SELECT SUM(total_amount) as total FROM bookings WHERE user_id = $user_id AND status = 'Completed'"))['total'] ?: 0;
                ?>
                
                <div class="stat-card">
                    <h4>Total Bookings</h4>
                    <div class="number"><?php echo $total_bookings; ?></div>
                </div>
                
                <div class="stat-card">
                    <h4>Completed Stays</h4>
                    <div class="number"><?php echo $completed_bookings; ?></div>
                </div>
                
                <div class="stat-card">
                    <h4>Total Spent</h4>
                    <div class="number">₹<?php echo number_format($total_spent, 2); ?></div>
                </div>
                
                <div class="stat-card">
                    <h4>Account Status</h4>
                    <div class="number" style="color: <?php echo $user['is_active'] ? 'green' : 'red'; ?>;">
                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>