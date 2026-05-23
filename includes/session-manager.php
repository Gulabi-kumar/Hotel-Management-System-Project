<?php
// includes/session-manager.php
// Always use this instead of session_start()

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get CSRF token
function csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

// Validate CSRF token
function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if user is regular user
function is_user() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

// Require login
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "Please login to access this page";
        header('Location: ' . get_base_url() . 'auth/login.php');
        exit();
    }
}

// Require admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        $_SESSION['error'] = "Access denied. Admin privileges required.";
        header('Location: ' . get_base_url() . 'index.php');
        exit();
    }
}

// Require user
function require_user() {
    require_login();
    if (!is_user()) {
        $_SESSION['error'] = "Access denied. User privileges required.";
        header('Location: ' . get_base_url() . 'index.php');
        exit();
    }
}

// Get base URL dynamically
function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $project_folder = 'Hotel%20Management%20System';
    
    return $protocol . '://' . $host . '/' . $project_folder . '/';
}

// Flash messages
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function display_flash() {
    $flash = get_flash();
    if ($flash) {
        echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
    }
}

// Get relative path helper
function get_relative_path($to) {
    $from = dirname($_SERVER['SCRIPT_FILENAME']);
    $to = dirname($from) . '/' . $to;
    
    $fromParts = explode(DIRECTORY_SEPARATOR, realpath($from));
    $toParts = explode(DIRECTORY_SEPARATOR, realpath($to));
    
    // Remove common parts
    while (count($fromParts) && count($toParts) && ($fromParts[0] == $toParts[0])) {
        array_shift($fromParts);
        array_shift($toParts);
    }
    
    // Go up for remaining from parts
    $path = str_repeat('../', count($fromParts));
    
    // Add to parts
    $path .= implode('/', $toParts);
    
    return $path;
}
?>