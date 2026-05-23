<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';

if (isLoggedIn()) {
    header("Location: ../" . ($_SESSION['user_role'] == 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $user_type = sanitize($_POST['user_type']);
    
    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } else {
        // Check admin table first if admin login
        if ($user_type == 'admin') {
            // For simplicity, using same users table with role='admin'
            $query = "SELECT * FROM users WHERE email = '$email' AND is_active = 1 AND is_verified = 1";
        } else {
            $query = "SELECT * FROM users WHERE email = '$email' AND is_active = 1 AND is_verified = 1";
        }
        
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = ($user['email'] == 'admin@hotel.com') ? 'admin' : 'user';
                
                // Update last login
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = {$user['id']}";
                mysqli_query($conn, $update_query);
                
                // Redirect based on role
                if ($_SESSION['user_role'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../user/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Email not found or account not verified!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login to Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php displayFlash(); ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="user_type">Login As</label>
                <select class="form-control" id="user_type" name="user_type" required>
                    <option value="user">User (Customer)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <p>
                Don't have an account? <a href="register.php">Register here</a>
            </p>
            <p>
                <a href="forgot-password.php">Forgot Password?</a>
            </p>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>