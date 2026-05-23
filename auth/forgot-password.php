<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    if (empty($email)) {
        $error = "Email is required!";
    } else {
        // Check if email exists
        $query = "SELECT id, full_name FROM users WHERE email = '$email' AND is_verified = 1";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Generate reset token
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $insert_query = "INSERT INTO password_resets (email, token, expires_at) 
                            VALUES ('$email', '$token', '$expires') 
                            ON DUPLICATE KEY UPDATE token = '$token', expires_at = '$expires'";
            
            if (mysqli_query($conn, $insert_query)) {
                // Send reset email (implement email sending)
                $_SESSION['reset_email'] = $email;
                $success = "Password reset link has been sent to your email!";
            } else {
                $error = "Error processing request. Please try again.";
            }
        } else {
            $error = "Email not found or not verified!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <small>We'll send a password reset link to this email</small>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Send Reset Link</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>