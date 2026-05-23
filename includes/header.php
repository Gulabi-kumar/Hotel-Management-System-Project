<?php
// Get current directory for relative paths
$current_dir = dirname($_SERVER['PHP_SELF']);
$is_auth_page = strpos($current_dir, 'auth') !== false;
$is_admin_page = strpos($current_dir, 'admin') !== false;
$is_user_page = strpos($current_dir, 'user') !== false;

// Determine base path
$base_path = '';
if ($is_auth_page) {
    $base_path = '../';
} elseif ($is_admin_page || $is_user_page) {
    $base_path = '../';
}

// Check user role
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Hotel Management'; ?></title>

    <!-- ALWAYS use absolute paths for assets -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<style>
    /* ===== MODERN NAVBAR CSS - DYNAMIC COLOR SYSTEM ===== */
    :root {
        --primary: #3a86ff;
        --primary-dark: #2667cc;
        --secondary: #8338ec;
        --accent: #ff006e;
        --success: #06d6a0;
        --warning: #ffd166;
        --danger: #ef476f;
        --light: #f8f9fa;
        --dark: #212529;
        --gray: #6c757d;
        --light-gray: #e9ecef;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
        --radius: 8px;
        --radius-sm: 4px;
        --radius-lg: 12px;
        --gradient-primary: linear-gradient(135deg, #3a86ff 0%, #8338ec 100%);
        --gradient-accent: linear-gradient(135deg, #ff006e 0%, #ffbe0b 100%);

        /* Navbar Color Variables - Default is Black */
        --navbar-bg: #000000;
        --navbar-text: #ffffff;
        --navbar-hover: rgba(255, 255, 255, 0.15);
        --navbar-border: rgba(255, 255, 255, 0.1);
        --navbar-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    }

    /* Reset and Base Styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        background-color: #ffffff;
        color: #333333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        min-height: 100vh;
        font-size: 16px;
    }

    /* Header - DYNAMIC COLOR NAVBAR */
    .header {
        background: var(--navbar-bg);
        color: var(--navbar-text);
        box-shadow: var(--navbar-shadow);
        position: sticky;
        top: 0;
        z-index: 1200;
        width: 100%;
        border-bottom: 1px solid var(--navbar-border);
        transition: background-color 0.3s ease;
    }

    /* Container */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        width: 100%;
    }

    /* Color Picker Controls */
    .color-picker-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1300;
        background: rgba(255, 255, 255, 0.95);
        border-radius: var(--radius-lg);
        padding: 15px;
        box-shadow: var(--shadow-lg);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.1);
        width: 250px;
        display: none;
    }

    .color-picker-container.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .color-picker-toggle {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1300;
        background: var(--gradient-primary);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .color-picker-toggle:hover {
        transform: scale(1.1) rotate(15deg);
    }

    .color-picker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .color-picker-header h4 {
        margin: 0;
        color: var(--dark);
        font-size: 1.1rem;
    }

    .color-close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--gray);
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .color-close-btn:hover {
        background: rgba(0, 0, 0, 0.1);
        color: var(--dark);
    }

    .color-presets {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 15px;
    }

    .color-preset {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: var(--transition);
    }

    .color-preset:hover {
        transform: scale(1.1);
        border-color: rgba(0, 0, 0, 0.2);
    }

    .color-preset.active {
        border-color: var(--dark);
        box-shadow: 0 0 0 2px white, 0 0 0 4px var(--dark);
    }

    .color-input-group {
        margin-bottom: 10px;
    }

    .color-input-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: var(--dark);
        font-size: 0.9rem;
    }

    .color-input {
        width: 100%;
        height: 40px;
        border-radius: var(--radius);
        border: 1px solid var(--light-gray);
        padding: 0 10px;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .color-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.1);
    }

    .color-reset {
        width: 100%;
        padding: 10px;
        background: var(--light-gray);
        border: none;
        border-radius: var(--radius);
        color: var(--dark);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 10px;
    }

    .color-reset:hover {
        background: var(--gray);
        color: white;
    }

    /* ===== NAVBAR LAYOUT ===== */

    /* Navbar - Desktop Layout */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        min-height: 70px;
        gap: 15px;
    }

    /* Logo */
    .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--navbar-text);
        transition: var(--transition);
        white-space: nowrap;
        margin-right: 30px;
        flex-shrink: 0;
    }

    .logo i {
        color: var(--primary);
        margin-right: 12px;
        font-size: 2.1rem;
        flex-shrink: 0;
    }

    .logo span {
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
    }

    .logo:hover {
        transform: translateY(-2px);
        color: var(--primary);
    }

    /* Mobile User Display - Initially hidden */
    .mobile-user {
        display: none;
        align-items: center;
        gap: 8px;
        color: var(--navbar-text);
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
        padding: 8px 12px;
    }

    .mobile-user i {
        color: var(--primary);
        font-size: 1rem;
    }

    .mobile-user small {
        background: var(--accent);
        color: white;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Menu Toggle - Initially hidden */
    .menu-toggle {
        display: none;
        background: var(--gradient-primary);
        color: var(--navbar-text);
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow);
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--navbar-border);
        z-index: 1001;
    }

    .menu-toggle:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, #4a94ff 0%, #9448ff 100%);
    }

    /* Navigation Container */
    .nav-container {
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 0;
    }

    /* Navigation Links */
    .nav-links {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 5px;
        flex-wrap: nowrap;
        flex: 1;
        justify-content: flex-start;
    }

    .nav-links li {
        position: relative;
        flex-shrink: 0;
    }

    .nav-links a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        text-decoration: none;
        color: var(--navbar-text);
        font-weight: 500;
        border-radius: var(--radius);
        transition: var(--transition);
        font-size: 0.95rem;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        background: transparent;
    }

    .nav-links a i {
        font-size: 1.15rem;
        width: 22px;
        text-align: center;
        flex-shrink: 0;
        color: rgba(255, 255, 255, 0.9);
    }

    .nav-links a:not(.active):hover {
        background: var(--navbar-hover);
        color: var(--navbar-text);
        transform: translateY(-2px);
    }

    .nav-links a:not(.active):hover i {
        color: var(--primary);
        transform: scale(1.1);
    }

    /* Active Link */
    .nav-links a.active {
        background: var(--gradient-primary);
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(58, 134, 255, 0.3);
        font-weight: 600;
    }

    .nav-links a.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 24px;
        height: 3px;
        background: #ffffff;
        border-radius: 2px;
    }

    .nav-links a.active i {
        color: #ffffff;
    }

    /* User Info Section */
    .user-info {
        margin-left: 15px;
        padding-left: 15px;
        border-left: 2px solid var(--navbar-border);
        flex-shrink: 0;
        min-width: 160px;
    }

    .user-welcome {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        font-weight: 500;
        color: var(--navbar-text);
        box-shadow: var(--shadow);
        transition: var(--transition);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid var(--navbar-border);
        font-size: 0.95rem;
    }

    .user-welcome:hover {
        background: var(--gradient-primary);
        color: #ffffff;
        transform: translateY(-2px);
        border-color: transparent;
    }

    .user-welcome i {
        font-size: 1.15rem;
        color: var(--primary);
        flex-shrink: 0;
    }

    .user-welcome:hover i {
        color: #ffffff;
    }

    .user-welcome small {
        background: var(--accent);
        color: #ffffff;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    /* ===== RESPONSIVE BREAKPOINTS ===== */

    /* Desktop (1025px and above) */
    @media (min-width: 1025px) {
        .container {
            padding: 0 25px;
        }

        .nav-links {
            gap: 8px;
        }
    }

    /* Tablet Landscape (769px - 1024px) */
    @media (max-width: 1024px) and (min-width: 769px) {
        .logo {
            font-size: 1.7rem;
            margin-right: 20px;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .nav-links a {
            padding: 9px 14px;
            font-size: 0.92rem;
        }

        .user-info {
            min-width: 140px;
        }

        .user-welcome {
            font-size: 0.9rem;
            padding: 9px 12px;
        }

        /* Adjust color picker position */
        .color-picker-toggle {
            top: 70px;
            right: 15px;
        }

        .color-picker-container {
            top: 70px;
            right: 15px;
            width: 230px;
        }
    }

    /* ===== MOBILE LAYOUT (768px and below) ===== */
    @media (max-width: 768px) {

        /* Container adjustments */
        .container {
            padding: 0 15px;
        }

        /* Navbar becomes vertical stack */
        .navbar {
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            min-height: 60px;
            gap: 10px;
            width: 100%;
        }

        /* Logo - Takes full width on first row */
        .logo {
            order: 1;
            font-size: 1.5rem;
            margin-right: 0;
            flex: 1;
            max-width: 100%;
            justify-content: center;
            padding: 5px 0;
        }

        .logo i {
            font-size: 1.6rem;
            margin-right: 8px;
        }

        .logo span {
            display: inline;
        }

        /* Mobile User Info - Second row, centered */
        .mobile-user {
            display: flex;
            order: 2;
            max-width: none;
            width: 100%;
            justify-content: center;
            padding: 5px 0;
            border-top: 1px solid var(--navbar-border);
            border-bottom: 1px solid var(--navbar-border);
            margin: 5px 0;
            font-size: 0.9rem;
        }

        .mobile-user i {
            display: inline-block;
            font-size: 1rem;
        }

        .mobile-user small {
            display: inline-block;
            font-size: 0.65rem;
        }

        /* Menu Toggle - Third row, centered */
        .menu-toggle {
            display: flex;
            order: 3;
            margin: 0 auto;
            width: 42px;
            height: 42px;
            font-size: 1.1rem;
        }

        /* Hide desktop user info */
        .user-info {
            display: none !important;
        }

        /* Navigation Menu - Dropdown style */
        .nav-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            padding: 80px 20px 20px;
            z-index: 999;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .nav-container.active {
            transform: translateX(0);
        }

        /* Navigation Links - Full width in menu */
        .nav-links {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            width: 100%;
            margin-top: 20px;
        }

        .nav-links li {
            width: 100%;
        }

        .nav-links a {
            justify-content: flex-start;
            padding: 16px 20px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius);
            margin-bottom: 5px;
            color: var(--navbar-text);
        }

        .nav-links a i {
            font-size: 1.2rem;
            width: 30px;
            margin-right: 15px;
        }

        /* Color picker adjustments for mobile */
        .color-picker-toggle {
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            font-size: 1rem;
            z-index: 1002;
        }

        .color-picker-container {
            top: 60px;
            right: 10px;
            width: 200px;
            padding: 12px;
            z-index: 1003;
        }

        /* Close button for mobile menu */
        .menu-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
    }

    /* Mobile Landscape (481px - 600px) */
    @media (max-width: 600px) and (min-width: 481px) {
        .logo {
            font-size: 1.4rem;
        }

        .logo i {
            font-size: 1.5rem;
        }

        .mobile-user {
            font-size: 0.85rem;
        }

        .menu-toggle {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .nav-container {
            padding: 70px 15px 15px;
        }

        .nav-links a {
            padding: 14px 18px;
            font-size: 1rem;
        }
    }

    /* Mobile Portrait (360px - 480px) */
    @media (max-width: 480px) {
        .navbar {
            min-height: 55px;
            padding: 6px 0;
        }

        .logo {
            font-size: 1.3rem;
            padding: 3px 0;
        }

        .logo i {
            font-size: 1.4rem;
            margin-right: 6px;
        }

        .mobile-user {
            font-size: 0.8rem;
            padding: 4px 0;
            margin: 3px 0;
        }

        .mobile-user i {
            font-size: 0.9rem;
        }

        .mobile-user small {
            font-size: 0.6rem;
            padding: 1px 4px;
        }

        .menu-toggle {
            width: 38px;
            height: 38px;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .nav-container {
            padding: 60px 12px 12px;
        }

        .nav-links a {
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .nav-links a i {
            font-size: 1.1rem;
            width: 25px;
            margin-right: 12px;
        }

        .color-picker-toggle {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
        }

        .color-picker-container {
            top: 50px;
            right: 8px;
            width: 180px;
            padding: 10px;
        }

        .color-presets {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .color-preset {
            width: 35px;
            height: 35px;
        }
    }

    /* Small Mobile (up to 360px) */
    @media (max-width: 360px) {
        .navbar {
            min-height: 50px;
            padding: 4px 0;
        }

        .logo {
            font-size: 1.2rem;
        }

        .logo i {
            font-size: 1.3rem;
            margin-right: 4px;
        }

        .logo span {
            font-size: 1.1rem;
        }

        .mobile-user {
            font-size: 0.75rem;
        }

        .mobile-user i {
            font-size: 0.8rem;
        }

        .mobile-user small {
            font-size: 0.55rem;
        }

        .menu-toggle {
            width: 35px;
            height: 35px;
            font-size: 0.8rem;
        }

        .nav-container {
            padding: 55px 10px 10px;
        }

        .nav-links a {
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .nav-links a i {
            font-size: 1rem;
            width: 22px;
            margin-right: 10px;
        }

        .color-picker-toggle {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .color-picker-container {
            top: 45px;
            right: 5px;
            width: 160px;
            padding: 8px;
        }

        .color-presets {
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
        }

        .color-preset {
            width: 30px;
            height: 30px;
        }
    }

    /* Always show desktop nav on large screens */
    @media (min-width: 769px) {
        .nav-container {
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
            max-height: none !important;
            position: static !important;
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            width: auto !important;
            overflow: visible !important;
        }

        .user-info {
            display: block !important;
        }

        .menu-toggle {
            display: none !important;
        }

        .mobile-user {
            display: none !important;
        }
    }

    /* Accessibility */
    .nav-links a:focus-visible,
    .menu-toggle:focus-visible,
    .logo:focus-visible {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
        border-radius: var(--radius);
    }

    /* Prevent horizontal scroll */
    @media (max-width: 1200px) {

        html,
        body {
            overflow-x: hidden;
        }
    }
</style>

<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo with correct link -->
                <a href="<?php echo $base_path; ?>index.php" class="logo" id="logo-tag">
                    <i class="fas fa-hotel"></i> Smart<span>Hotel</span>
                </a>

                <!-- Mobile User Display (only shows on mobile) -->
                <?php if ($user_role != 'guest'): ?>
                    <div class="mobile-user">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo isset($_SESSION['user_name']) ? substr($_SESSION['user_name'], 0, 15) : 'User'; ?></span>
                        <small><?php echo ucfirst(substr($user_role, 0, 1)); ?></small>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Navigation Container -->
                <div class="nav-container" id="navContainer">
                    <ul class="nav-links" id="navLinks">
                        <!-- Home Link -->
                        <li>
                            <a href="<?php echo $base_path; ?>index.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>

                        <?php if ($user_role == 'guest'): ?>
                            <!-- Guest Navigation -->
                            <li>
                                <a href="<?php echo $base_path; ?>auth/register.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-user-plus"></i> Register
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>auth/login.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>

                        <?php elseif ($user_role == 'user'): ?>
                            <!-- User Navigation -->
                            <li>
                                <a href="<?php echo $base_path; ?>user/dashboard.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>user/booking.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-calendar-plus"></i> Book Room
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>user/history.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-history"></i> History
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>user/profile.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>auth/logout.php"
                                    onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>

                        <?php elseif ($user_role == 'admin'): ?>
                            <!-- Admin Navigation -->
                            <li>
                                <a href="<?php echo $base_path; ?>admin/dashboard.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>admin/rooms.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-bed"></i> Rooms
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>admin/bookings.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-calendar-check"></i> Bookings
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>admin/users.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>admin/gallery.php"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-images"></i> Gallery
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>auth/logout.php"
                                    onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php if ($user_role != 'guest'): ?>
                        <div class="user-info">
                            <span class="user-welcome">
                                <i class="fas fa-user-circle"></i>
                                <?php echo $_SESSION['user_name']; ?>
                                <small>(<?php echo ucfirst($user_role); ?>)</small>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const navContainer = document.querySelector('.nav-container');
            const body = document.body;
            const colorPickerToggle = document.querySelector('.color-picker-toggle');
            const colorPickerContainer = document.querySelector('.color-picker-container');
            const colorCloseBtn = document.querySelector('.color-close-btn');

            // Color presets
            const colorPresets = [{
                    name: 'Black',
                    bg: '#000000',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Dark Blue',
                    bg: '#1a237e',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Dark Purple',
                    bg: '#4a148c',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Charcoal',
                    bg: '#263238',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Navy',
                    bg: '#0d47a1',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Burgundy',
                    bg: '#880e4f',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Forest Green',
                    bg: '#1b5e20',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                },
                {
                    name: 'Dark Teal',
                    bg: '#004d40',
                    text: '#ffffff',
                    hover: 'rgba(255,255,255,0.15)'
                }
            ];

            // Initialize color picker
            function initColorPicker() {
                const colorPresetContainer = document.querySelector('.color-presets');
                const colorInput = document.querySelector('.color-input');
                const colorResetBtn = document.querySelector('.color-reset');

                // Create color preset buttons
                colorPresets.forEach((color, index) => {
                    const presetBtn = document.createElement('div');
                    presetBtn.className = 'color-preset';
                    presetBtn.style.backgroundColor = color.bg;
                    presetBtn.title = color.name;
                    presetBtn.dataset.bg = color.bg;
                    presetBtn.dataset.text = color.text;
                    presetBtn.dataset.hover = color.hover;

                    if (index === 0) {
                        presetBtn.classList.add('active');
                    }

                    presetBtn.addEventListener('click', function() {
                        document.querySelectorAll('.color-preset').forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');

                        applyColors({
                            bg: this.dataset.bg,
                            text: this.dataset.text,
                            hover: this.dataset.hover
                        });
                    });

                    colorPresetContainer.appendChild(presetBtn);
                });

                // Color input change handler
                colorInput.addEventListener('input', function() {
                    const colorValue = this.value;
                    if (colorValue) {
                        applyColors({
                            bg: colorValue,
                            text: getContrastColor(colorValue),
                            hover: getHoverColor(colorValue)
                        });
                    }
                });

                // Reset colors
                colorResetBtn.addEventListener('click', function() {
                    applyColors(colorPresets[0]);
                    colorInput.value = colorPresets[0].bg;
                    document.querySelectorAll('.color-preset').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.color-preset')[0].classList.add('active');
                });

                // Load saved colors
                loadSavedColors();
            }

            // Apply colors to navbar
            function applyColors(colors) {
                const root = document.documentElement;
                root.style.setProperty('--navbar-bg', colors.bg);
                root.style.setProperty('--navbar-text', colors.text);
                root.style.setProperty('--navbar-hover', colors.hover);
                root.style.setProperty('--navbar-border', colors.hover.replace('0.15', '0.1'));

                // Save to localStorage
                saveColors(colors);
            }

            // Get contrast color for text
            function getContrastColor(hexcolor) {
                hexcolor = hexcolor.replace("#", "");
                const r = parseInt(hexcolor.substr(0, 2), 16);
                const g = parseInt(hexcolor.substr(2, 2), 16);
                const b = parseInt(hexcolor.substr(4, 2), 16);
                const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return brightness > 128 ? '#000000' : '#ffffff';
            }

            // Get hover color
            function getHoverColor(hexcolor) {
                hexcolor = hexcolor.replace("#", "");
                const r = parseInt(hexcolor.substr(0, 2), 16);
                const g = parseInt(hexcolor.substr(2, 2), 16);
                const b = parseInt(hexcolor.substr(4, 2), 16);
                const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return brightness > 128 ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.15)';
            }

            // Save colors to localStorage
            function saveColors(colors) {
                localStorage.setItem('navbarColors', JSON.stringify(colors));
            }

            // Load saved colors
            function loadSavedColors() {
                const savedColors = localStorage.getItem('navbarColors');
                if (savedColors) {
                    const colors = JSON.parse(savedColors);
                    applyColors(colors);
                    document.querySelector('.color-input').value = colors.bg;

                    // Activate matching preset if exists
                    document.querySelectorAll('.color-preset').forEach(preset => {
                        if (preset.dataset.bg === colors.bg) {
                            preset.classList.add('active');
                        }
                    });
                }
            }

            // Toggle color picker
            function toggleColorPicker() {
                colorPickerContainer.classList.toggle('show');
            }

            // Close color picker when clicking outside
            document.addEventListener('click', function(e) {
                if (colorPickerContainer.classList.contains('show') &&
                    !colorPickerContainer.contains(e.target) &&
                    !colorPickerToggle.contains(e.target)) {
                    colorPickerContainer.classList.remove('show');
                }
            });

            // Menu toggle functionality
            if (menuToggle && navContainer) {
                function toggleMenu() {
                    const isActive = navContainer.classList.contains('active');

                    if (!isActive) {
                        // Open menu
                        navContainer.classList.add('active');
                        menuToggle.classList.add('active');
                        menuToggle.querySelector('i').classList.replace('fa-bars', 'fa-times');
                        body.style.overflow = 'hidden';
                        // Close color picker when opening menu
                        if (colorPickerContainer) {
                            colorPickerContainer.classList.remove('show');
                        }
                    } else {
                        // Close menu
                        navContainer.classList.remove('active');
                        menuToggle.classList.remove('active');
                        menuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
                        body.style.overflow = '';
                    }
                }

                // Toggle menu on hamburger click
                menuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleMenu();
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (navContainer.classList.contains('active') &&
                        !navContainer.contains(e.target) &&
                        !menuToggle.contains(e.target)) {
                        toggleMenu();
                    }
                });

                // Close menu when clicking on a link (for mobile)
                const navLinks = document.querySelectorAll('.nav-links a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            toggleMenu();
                        }
                    });
                });

                // Close menu on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        if (navContainer.classList.contains('active')) {
                            toggleMenu();
                        }
                        if (colorPickerContainer && colorPickerContainer.classList.contains('show')) {
                            colorPickerContainer.classList.remove('show');
                        }
                    }
                });

                // Reset menu on window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (window.innerWidth > 768) {
                            // Reset mobile menu state on desktop
                            navContainer.classList.remove('active');
                            menuToggle.classList.remove('active');
                            if (menuToggle.querySelector('i')) {
                                menuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
                            }
                            body.style.overflow = '';
                        }
                    }, 250);
                });
            }

            // Initialize color picker if elements exist
            if (colorPickerToggle && colorPickerContainer) {
                colorPickerToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleColorPicker();
                });

                if (colorCloseBtn) {
                    colorCloseBtn.addEventListener('click', function() {
                        colorPickerContainer.classList.remove('show');
                    });
                }

                initColorPicker();
            }
        });
    </script>