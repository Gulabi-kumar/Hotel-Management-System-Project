<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user stats
$bookings_query = "SELECT COUNT(*) as total_bookings FROM bookings WHERE user_id = $user_id";
$bookings_result = mysqli_query($conn, $bookings_query);
$total_bookings = mysqli_fetch_assoc($bookings_result)['total_bookings'];

$active_query = "SELECT COUNT(*) as active_bookings FROM bookings 
                WHERE user_id = $user_id AND status = 'Confirmed' 
                AND check_out >= CURDATE()";
$active_result = mysqli_query($conn, $active_query);
$active_bookings = mysqli_fetch_assoc($active_result)['active_bookings'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>User Dashboard - Hotel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Responsive Dashboard Styles */
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --gold: #FFD700;
            --gold-dark: #DAA520;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .dashboard {
            padding: 20px 0;
            min-height: calc(100vh - 140px);
            width: 100%;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            width: 100%;
        }
        
        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--dark);
            text-align: center;
        }
        
        /* Responsive Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 150px;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        /* Total Bookings Card */
        .stat-card:nth-child(1) {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Active Bookings Card */
        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #27ae60 0%, #219653 100%);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        /* Wallet Balance Card */
        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 5px;
            line-height: 1.2;
        }
        
        .stat-card .number i {
            font-size: 1.5rem;
            margin-right: 5px;
        }
        
        /* Card Styles */
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f7;
            width: 100%;
        }
        
        .card h3 {
            margin: 0 0 20px 0;
            color: var(--dark);
            font-size: 1.5rem;
            border-bottom: 2px solid var(--light);
            padding-bottom: 15px;
            position: relative;
        }
        
        .card h3::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        /* Recent Bookings Scrollable Table */
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #eef2f7;
            max-height: 400px;
            overflow-y: auto;
            width: 100%;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 700px;
        }
        
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }
        
        .table th:last-child {
            border-right: none;
        }
        
        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
            font-size: 0.95rem;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background: linear-gradient(90deg, #f8f9fa 0%, #eef2f7 100%);
        }
        
        /* Status Badge */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 100px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            color: white;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);
            width: 100%;
            white-space: nowrap;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn-warning {
            background: var(--warning);
            box-shadow: 0 4px 6px rgba(243, 156, 18, 0.2);
        }
        
        .btn-warning:hover {
            background: #d68910;
        }
        
        .btn-success {
            background: var(--success);
            box-shadow: 0 4px 6px rgba(39, 174, 96, 0.2);
        }
        
        .btn-success:hover {
            background: #219653;
        }
        
        .btn-danger {
            background: var(--danger);
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2);
            padding: 8px 16px;
            font-size: 0.9rem;
            width: auto;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            box-shadow: 0 6px 12px rgba(231, 76, 60, 0.3);
        }
        
        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
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
        
        .alert-success {
            background: #d4ffd4;
            color: #155724;
            border: 1px solid #b5e6b5;
            border-left: 4px solid #27ae60;
        }
        
        /* Empty Booking Message */
        .empty-booking {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }
        
        .empty-booking i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        /* ===== RESPONSIVE BREAKPOINTS ===== */
        
        /* Large Desktops (1400px and above) */
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
                padding: 0 40px;
            }
            
            .stats-grid {
                gap: 25px;
            }
        }
        
        /* Desktops (1025px - 1399px) */
        @media (min-width: 1025px) and (max-width: 1399px) {
            .container {
                padding: 0 30px;
            }
        }
        
        /* Tablets Landscape (901px - 1024px) */
        @media (min-width: 901px) and (max-width: 1024px) {
            .container {
                padding: 0 25px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-card .number {
                font-size: 2.2rem;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Tablets Portrait (769px - 900px) */
        @media (min-width: 769px) and (max-width: 900px) {
            .container {
                padding: 0 20px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
            
            .stat-card {
                padding: 18px;
            }
            
            .stat-card .number {
                font-size: 2rem;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .table th,
            .table td {
                padding: 12px 10px;
                font-size: 0.9rem;
            }
        }
        
        /* Mobile Landscape (601px - 768px) */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .dashboard {
                padding: 15px 0;
                min-height: calc(100vh - 120px);
            }
            
            .container {
                padding: 0 15px;
            }
            
            h2 {
                font-size: 1.6rem;
                margin-bottom: 15px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin: 20px 0;
            }
            
            .stat-card:nth-child(3) {
                grid-column: span 2;
            }
            
            .stat-card {
                padding: 20px 15px;
                min-height: 130px;
            }
            
            .stat-card h3 {
                font-size: 0.9rem;
            }
            
            .stat-card .number {
                font-size: 2rem;
            }
            
            .card {
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 12px;
            }
            
            .card h3 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .table-container {
                margin: 0 -15px;
                width: calc(100% + 30px);
                border-radius: 0;
                border-left: none;
                border-right: none;
                max-height: 350px;
            }
            
            .btn {
                padding: 14px 20px;
                font-size: 0.95rem;
            }
            
            .btn-danger {
                padding: 8px 14px;
                font-size: 0.85rem;
            }
            
            .table th {
                padding: 14px 10px;
                font-size: 0.85rem;
            }
            
            .table td {
                padding: 12px 10px;
                font-size: 0.9rem;
            }
            
            .status-badge {
                padding: 6px 12px;
                font-size: 0.8rem;
                min-width: 90px;
            }
        }
        
        /* Mobile Portrait (481px - 600px) */
        @media (max-width: 600px) {
            .dashboard {
                padding: 12px 0;
            }
            
            .container {
                padding: 0 12px;
            }
            
            h2 {
                font-size: 1.4rem;
                margin-bottom: 12px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .stat-card:nth-child(3) {
                grid-column: span 1;
            }
            
            .stat-card {
                padding: 18px 12px;
                min-height: 120px;
            }
            
            .stat-card h3 {
                font-size: 0.85rem;
            }
            
            .stat-card .number {
                font-size: 1.8rem;
            }
            
            .card {
                padding: 18px;
            }
            
            .card h3 {
                font-size: 1.2rem;
                margin-bottom: 12px;
            }
            
            .btn {
                padding: 13px 18px;
                font-size: 0.9rem;
            }
            
            .table th {
                padding: 12px 8px;
                font-size: 0.8rem;
            }
            
            .table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            
            .status-badge {
                padding: 5px 10px;
                font-size: 0.75rem;
                min-width: 80px;
            }
        }
        
        /* Small Mobile (361px - 480px) */
        @media (max-width: 480px) {
            .dashboard {
                padding: 10px 0;
            }
            
            .container {
                padding: 0 10px;
            }
            
            h2 {
                font-size: 1.3rem;
                margin-bottom: 10px;
            }
            
            .stats-grid {
                gap: 10px;
            }
            
            .stat-card {
                padding: 16px 10px;
                min-height: 110px;
            }
            
            .stat-card h3 {
                font-size: 0.8rem;
                margin-bottom: 8px;
            }
            
            .stat-card .number {
                font-size: 1.6rem;
            }
            
            .card {
                padding: 16px;
                margin-bottom: 16px;
                border-radius: 10px;
            }
            
            .card h3 {
                font-size: 1.1rem;
                padding-bottom: 10px;
            }
            
            .quick-actions {
                gap: 10px;
            }
            
            .btn {
                padding: 12px 16px;
                font-size: 0.85rem;
            }
            
            .table th {
                padding: 10px 6px;
                font-size: 0.75rem;
            }
            
            .table td {
                padding: 9px 6px;
                font-size: 0.8rem;
            }
            
            .btn-danger {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            .status-badge {
                padding: 4px 8px;
                font-size: 0.7rem;
                min-width: 70px;
            }
        }
        
        /* Extra Small Mobile (up to 360px) */
        @media (max-width: 360px) {
            .container {
                padding: 0 8px;
            }
            
            h2 {
                font-size: 1.2rem;
            }
            
            .stat-card {
                padding: 14px 8px;
                min-height: 100px;
            }
            
            .stat-card h3 {
                font-size: 0.75rem;
                margin-bottom: 6px;
            }
            
            .stat-card .number {
                font-size: 1.4rem;
            }
            
            .card {
                padding: 14px;
            }
            
            .card h3 {
                font-size: 1rem;
            }
            
            .btn {
                padding: 10px 14px;
                font-size: 0.8rem;
            }
            
            .table th {
                padding: 8px 4px;
                font-size: 0.7rem;
            }
            
            .table td {
                padding: 8px 4px;
                font-size: 0.75rem;
            }
            
            .status-badge {
                padding: 3px 6px;
                font-size: 0.65rem;
                min-width: 65px;
            }
            
            .empty-booking {
                padding: 30px 15px;
            }
            
            .empty-booking i {
                font-size: 2.5rem;
            }
        }
        
        /* Mobile with Keyboard Open (Height constraints) */
        @media (max-height: 600px) and (orientation: landscape) {
            .dashboard {
                min-height: auto;
            }
            
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            
            .stat-card {
                min-height: 100px;
                padding: 15px 10px;
            }
            
            .card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .table-container {
                max-height: 250px;
            }
        }
        
        /* Custom scrollbar for table */
        .table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a3d96 100%);
        }
        
        /* Loading animation for cards */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card, .stat-card {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        
        /* Print styles */
        @media print {
            .card, .stat-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .btn {
                display: none;
            }
            
            .table-container {
                max-height: none;
                overflow: visible;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard">
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            
            <?php if(isset($_GET['verified'])): ?>
                <div class="alert alert-success">Email verified successfully!</div>
            <?php endif; ?>
            
            <!-- Stats Section -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="number"><i class="fas fa-calendar-check"></i><?php echo $total_bookings; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <div class="number"><i class="fas fa-bed"></i><?php echo $active_bookings; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Wallet Balance</h3>
                    <div class="number"><i class="fas fa-wallet"></i>₹0</div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <h3>Quick Actions</h3>
                <div class="quick-actions">
                    <a href="booking.php" class="btn">
                        <i class="fas fa-plus-circle"></i> Book New Room
                    </a>
                    <a href="history.php" class="btn btn-warning">
                        <i class="fas fa-history"></i> View History
                    </a>
                    <a href="profile.php" class="btn btn-success">
                        <i class="fas fa-user-edit"></i> Update Profile
                    </a>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="card">
                <h3>Recent Bookings</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Room Type</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_query = "SELECT b.*, r.room_type 
                                            FROM bookings b 
                                            JOIN rooms r ON b.room_id = r.id 
                                            WHERE b.user_id = $user_id 
                                            ORDER BY b.booking_date DESC LIMIT 5";
                            $recent_result = mysqli_query($conn, $recent_query);
                            
                            if(mysqli_num_rows($recent_result) > 0):
                            while($booking = mysqli_fetch_assoc($recent_result)):
                            ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['check_in'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['check_out'])); ?></td>
                                <td>₹<?php echo number_format($booking['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge" style="background: <?php 
                                        $status_color = [
                                            'Pending' => '#f39c12',
                                            'Confirmed' => '#27ae60',
                                            'Cancelled' => '#e74c3c',
                                            'Completed' => '#3498db'
                                        ];
                                        echo $status_color[$booking['status']] ?? '#95a5a6';
                                    ?>;">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($booking['status'] == 'Pending' || $booking['status'] == 'Confirmed'): ?>
                                        <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--gray); font-style: italic; font-size: 0.85rem;">No action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-booking">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No bookings found. Book your first room today!</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile touch support for table scrolling
            const tableContainer = document.querySelector('.table-container');
            if(tableContainer) {
                let isScrolling = false;
                let startX, scrollLeft;
                
                tableContainer.addEventListener('touchstart', (e) => {
                    isScrolling = true;
                    startX = e.touches[0].pageX - tableContainer.offsetLeft;
                    scrollLeft = tableContainer.scrollLeft;
                });
                
                tableContainer.addEventListener('touchmove', (e) => {
                    if(!isScrolling) return;
                    e.preventDefault();
                    const x = e.touches[0].pageX - tableContainer.offsetLeft;
                    const walk = (x - startX) * 2;
                    tableContainer.scrollLeft = scrollLeft - walk;
                });
                
                tableContainer.addEventListener('touchend', () => {
                    isScrolling = false;
                });
            }
            
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Observe all cards
            document.querySelectorAll('.card, .stat-card').forEach(card => {
                observer.observe(card);
            });
            
            // Add hover effects for cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if(window.innerWidth > 768) {
                        this.style.transform = 'translateY(-5px) scale(1.02)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if(window.innerWidth > 768) {
                        this.style.transform = 'translateY(0) scale(1)';
                    }
                });
            });
            
            // Responsive table actions for mobile
            function updateTableForMobile() {
                if(window.innerWidth <= 480) {
                    document.querySelectorAll('.table td:nth-child(4), .table th:nth-child(4)').forEach(el => {
                        el.style.display = 'none';
                    });
                } else {
                    document.querySelectorAll('.table td, .table th').forEach(el => {
                        el.style.display = '';
                    });
                }
            }
            
            // Initial check
            updateTableForMobile();
            
            // Update on resize
            window.addEventListener('resize', updateTableForMobile);
            
            // Prevent zoom on mobile
            document.addEventListener('touchstart', function(e) {
                if(e.touches.length > 1) {
                    e.preventDefault();
                }
            }, { passive: false });
            
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function(e) {
                const now = Date.now();
                if(now - lastTouchEnd <= 300) {
                    e.preventDefault();
                }
                lastTouchEnd = now;
            }, false);
        });
        
        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                window.scrollTo(0, 0);
            }, 100);
        });
    </script>
</body>
</html>