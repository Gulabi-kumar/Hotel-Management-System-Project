<?php
session_start();
require_once '../config/database.php';


if(isset($_SESSION['user_id'])) {
    header("Location: ../user/dashboard.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    // Validation
    if(empty($full_name) || empty($email) || empty($mobile) || empty($password)) {
        $error = "All fields are required!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif(!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = "Invalid mobile number! Must be 10 digits.";
    } else {
        // Check if email already exists
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if(mysqli_num_rows($result) > 0) {
            $error = "Email already registered!";
        } else {
            // Check if mobile already exists
            $check_mobile = "SELECT id FROM users WHERE mobile = '$mobile'";
            $result_mobile = mysqli_query($conn, $check_mobile);
            
            if(mysqli_num_rows($result_mobile) > 0) {
                $error = "Mobile number already registered!";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user directly into database (auto-verified)
                $insert_user = "INSERT INTO users (full_name, email, mobile, password, is_verified) 
                               VALUES ('$full_name', '$email', '$mobile', '$hashed_password', 1)";
                
                if(mysqli_query($conn, $insert_user)) {
                    // Get the new user ID
                    $user_id = mysqli_insert_id($conn);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_role'] = 'user';
                    
                    // Redirect to dashboard with success message
                    $_SESSION['success'] = "Registration successful! Welcome to Smart Hotel.";
                    header("Location:login.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again. Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .password-strength {
            margin-top: 5px;
            font-size: 0.9rem;
        }
        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }
        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

    
    /* Base responsive setup */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: system-ui, -apple-system, sans-serif;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    /* Mobile First - Small screens */
    .form-container {
        width: 100%;
        max-width: 100%;
        padding: 15px;
        margin: 0 auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .form-container > form {
        width: 100%;
        padding: 20px;
        border-radius: 8px;
    }
    
    h2 {
        font-size: 1.5rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    /* Form elements - responsive sizing */
    .form-group {
        margin-bottom: 1rem;
        width: 100%;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        font-size: 1rem;
        border-radius: 6px;
        height: 44px;
    }
    
    /* Checkbox responsive */
    .form-group label[for] {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.9rem;
    }
    
    .form-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    /* Button responsive */
    .btn {
        width: 100%;
        padding: 12px;
        font-size: 1rem;
        border-radius: 6px;
        height: 46px;
        margin-top: 0.5rem;
    }
    
    /* Alert messages */
    .alert {
        width: 100%;
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
    
    /* Text elements */
    .text-muted,
    .password-requirements {
        font-size: 0.85rem;
        margin-top: 4px;
        line-height: 1.4;
    }
    
    .password-strength {
        margin-top: 5px;
        font-size: 0.85rem;
    }
    
    /* Links container */
    .form-container > p,
    .form-container > div {
        width: 100%;
        margin-top: 1.5rem;
        text-align: center;
    }
    
    /* Benefits section */
    .form-container > div:last-child {
        padding: 15px;
        border-radius: 8px;
        margin-top: 1rem;
    }
    
    .form-container > div:last-child p {
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    /* ============ RESPONSIVE BREAKPOINTS ============ */
    
    /* Very small phones (≤ 320px) */
    @media (max-width: 320px) {
        .form-container {
            padding: 10px;
        }
        
        .form-container > form {
            padding: 15px;
        }
        
        h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .form-control {
            padding: 8px 10px;
            font-size: 0.95rem;
            height: 42px;
        }
        
        .btn {
            padding: 10px;
            font-size: 0.95rem;
            height: 44px;
        }
        
        .alert {
            padding: 10px 12px;
            font-size: 0.85rem;
        }
    }
    
    /* Small phones (321px - 375px) */
    @media (min-width: 321px) and (max-width: 375px) {
        .form-container {
            padding: 12px;
        }
        
        .form-container > form {
            padding: 18px;
        }
        
        h2 {
            font-size: 1.4rem;
        }
    }
    
    /* Standard phones (376px - 480px) */
    @media (min-width: 376px) and (max-width: 480px) {
        .form-container {
            padding: 15px;
        }
        
        .form-container > form {
            padding: 20px;
        }
    }
    
    /* Small tablets (481px - 600px) */
    @media (min-width: 481px) and (max-width: 600px) {
        .form-container > form {
            max-width: 420px;
            margin: 0 auto;
            padding: 25px;
        }
        
        h2 {
            font-size: 1.7rem;
        }
        
        .form-container > p,
        .form-container > div {
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }
    }
    
    /* Tablets (601px - 768px) */
    @media (min-width: 601px) and (max-width: 768px) {
        .form-container {
            padding: 25px;
        }
        
        .form-container > form {
            max-width: 450px;
            padding: 30px;
            margin: 0 auto;
        }
        
        h2 {
            font-size: 1.8rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-control {
            padding: 12px 15px;
            height: 48px;
        }
        
        .btn {
            padding: 14px;
            height: 50px;
        }
        
        .form-container > div:last-child {
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
            padding: 20px;
        }
    }
    
    /* Small laptops (769px - 1024px) */
    @media (min-width: 769px) and (max-width: 1024px) {
        .form-container {
            padding: 30px;
        }
        
        .form-container > form {
            max-width: 480px;
            padding: 35px;
            margin: 0 auto;
        }
        
        h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group label {
            font-size: 1rem;
        }
        
        .form-control {
            padding: 14px 16px;
            height: 50px;
        }
        
        .btn {
            padding: 15px;
            height: 52px;
            font-size: 1.05rem;
        }
        
        .form-container > div:last-child {
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }
    }
    
    /* Desktop (1025px - 1366px) */
    @media (min-width: 1025px) {
        .form-container {
            padding: 40px;
        }
        
        .form-container > form {
            max-width: 500px;
            padding: 40px;
            margin: 0 auto;
        }
        
        h2 {
            font-size: 2.2rem;
            margin-bottom: 2.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            padding: 15px 18px;
            height: 52px;
            font-size: 1.05rem;
        }
        
        .btn {
            padding: 16px;
            height: 54px;
            font-size: 1.1rem;
        }
        
        .form-container > div:last-child {
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            padding: 25px;
        }
    }
    
    /* Large desktop (1367px and above) */
    @media (min-width: 1367px) {
        .form-container > form {
            max-width: 520px;
            padding: 45px;
        }
        
        h2 {
            font-size: 2.4rem;
        }
        
        .form-container > div:last-child {
            max-width: 520px;
        }
    }
    
    /* ============ SPECIAL CASES ============ */
    
    /* Landscape mode for phones */
    @media (max-height: 500px) and (orientation: landscape) {
        .form-container {
            padding: 10px 15px;
            min-height: auto;
        }
        
        .form-container > form {
            padding: 15px 20px;
        }
        
        .form-group {
            margin-bottom: 0.8rem;
        }
        
        .form-control {
            padding: 8px 12px;
            height: 40px;
        }
        
        .btn {
            padding: 10px;
            height: 42px;
            margin-top: 0.5rem;
        }
    }
    
    /* Tall phones (iPhone 12/13 mini, etc.) */
    @media (min-height: 800px) and (max-width: 480px) {
        .form-container {
            padding: 25px 15px;
        }
        
        .form-container > form {
            padding: 25px;
        }
    }
    
    /* Very wide screens (ultra-wide monitors) */
    @media (min-width: 1920px) {
        .form-container > form {
            max-width: 550px;
        }
        
        .form-container > div:last-child {
            max-width: 550px;
        }
    }
    
    /* Touch device optimization */
    @media (hover: none) and (pointer: coarse) {
        .form-control {
            min-height: 44px; /* Minimum touch target */
        }
        
        .btn {
            min-height: 44px;
        }
        
        .form-group input[type="checkbox"] {
            min-width: 24px;
            min-height: 24px;
        }
    }
    
    /* Print layout */
    @media print {
        .form-container {
            max-width: 100%;
            padding: 0;
        }
        
        .form-container > form {
            box-shadow: none;
            border: 1px solid #000;
            max-width: 100%;
        }
        
        .btn {
            display: none;
        }
    }


    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" class="form-control" id="full_name" name="full_name" 
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                       required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       required placeholder="example@gmail.com">
            </div>
            
            <div class="form-group">
                <label for="mobile">Mobile Number *</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" 
                       value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" 
                       pattern="[0-9]{10}" required placeholder="9876543210" maxlength="10">
                <small class="text-muted">Enter 10-digit mobile number without country code</small>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" class="form-control" id="password" name="password" 
                       required placeholder="Minimum 6 characters">
                <div class="password-strength" id="passwordStrength"></div>
                <div class="password-requirements">
                    • Minimum 6 characters<br>
                    • Include letters and numbers
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       required placeholder="Re-enter your password">
                <div id="passwordMatch" style="font-size: 0.9rem; margin-top: 5px;"></div>
            </div>
            
            <div class="form-group" style="margin: 1.5rem 0;">
                <label>
                    <input type="checkbox" name="terms" id="terms" required>
                    I agree to the <a href="../terms-conditions.php" target="_blank">Terms & Conditions</a> and 
                    <a href="../privacy-policy.php" target="_blank">Privacy Policy</a>
                </label>
                <div id="termsError" style="color: #e74c3c; font-size: 0.9rem; display: none;">
                    You must agree to the terms and conditions
                </div>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;" id="registerBtn">Create Account</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
        
        <div style="text-align: center; margin-top: 1rem; font-size: 0.9rem; color: #666;">
            <p>By registering, you can:</p>
            <p>✓ Book rooms instantly<br>
               ✓ View booking history<br>
               ✓ Manage your profile<br>
               ✓ Receive special offers</p>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('passwordStrength');
        const passwordMatch = document.getElementById('passwordMatch');
        const termsCheckbox = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        
        // Password strength checker
        password.addEventListener('input', function() {
            const pass = this.value;
            let strength = '';
            let color = '';
            
            if (pass.length === 0) {
                strength = '';
            } else if (pass.length < 6) {
                strength = 'Weak (min 6 characters)';
                color = 'strength-weak';
            } else if (pass.length < 8) {
                strength = 'Medium';
                color = 'strength-medium';
            } else if (/[A-Z]/.test(pass) && /[0-9]/.test(pass) && /[^A-Za-z0-9]/.test(pass)) {
                strength = 'Strong';
                color = 'strength-strong';
            } else if (pass.length >= 8) {
                strength = 'Good';
                color = 'strength-medium';
            } else {
                strength = 'Weak';
                color = 'strength-weak';
            }
            
            if (strength) {
                passwordStrength.innerHTML = `<span class="${color}">Strength: ${strength}</span>`;
            } else {
                passwordStrength.innerHTML = '';
            }
            
            checkPasswordMatch();
        });
        
        // Password match checker
        confirmPassword.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            if (confirmPassword.value === '') {
                passwordMatch.innerHTML = '';
                return;
            }
            
            if (password.value === confirmPassword.value) {
                passwordMatch.innerHTML = '<span style="color: #27ae60;">✓ Passwords match</span>';
                passwordMatch.style.color = '#27ae60';
            } else {
                passwordMatch.innerHTML = '<span style="color: #e74c3c;">✗ Passwords do not match</span>';
                passwordMatch.style.color = '#e74c3c';
            }
        }
        
        // Terms validation
        termsCheckbox.addEventListener('change', function() {
            termsError.style.display = this.checked ? 'none' : 'block';
        });
        
        // Form validation before submission
        registerForm.addEventListener('submit', function(e) {
            let valid = true;
            
            // Check terms
            if (!termsCheckbox.checked) {
                termsError.style.display = 'block';
                valid = false;
                e.preventDefault();
            }
            
            // Check password match
            if (password.value !== confirmPassword.value) {
                passwordMatch.innerHTML = '<span style="color: #e74c3c;">✗ Passwords must match</span>';
                valid = false;
                e.preventDefault();
            }
            
            // Check password length
            if (password.value.length < 6) {
                passwordStrength.innerHTML = '<span class="strength-weak">Password must be at least 6 characters</span>';
                valid = false;
                e.preventDefault();
            }
            
            // Mobile number validation
            const mobile = document.getElementById('mobile');
            if (!/^[0-9]{10}$/.test(mobile.value)) {
                alert('Please enter a valid 10-digit mobile number');
                valid = false;
                e.preventDefault();
            }
            
            // If valid, show loading
            if (valid) {
                registerBtn.innerHTML = 'Creating Account...';
                registerBtn.disabled = true;
            }
        });
        
        // Real-time mobile validation
        document.getElementById('mobile').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
        
        // Real-time name validation (letters and spaces only)
        document.getElementById('full_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });
    </script>
</body>
</html>