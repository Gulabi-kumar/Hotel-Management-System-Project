<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get dashboard stats
$total_bookings = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM bookings"
))['count'];

$total_users = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM users WHERE is_verified = 1"
))['count'];

$available_rooms = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM rooms WHERE is_available = 1"
))['count'];

$monthly_income = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(total_amount) as income FROM bookings 
     WHERE MONTH(booking_date) = MONTH(CURDATE()) 
     AND YEAR(booking_date) = YEAR(CURDATE()) 
     AND status IN ('Confirmed', 'Completed')"
))['income'] ?: 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
    /* Add scrollable container for the table */
    .card:has(.table) {
        overflow: hidden;
        position: relative;
    }

    .card h3 {
        margin-bottom: 1rem;
    }

    .table-container {
        overflow-x: auto;
        max-height: 400px;
        overflow-y: auto;
        border-radius: 0 0 8px 8px;
        margin-top: 0;
    }

    /* Keep existing table styles, just add min-width */
    .table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        margin: 0;
    }

    /* Make table header sticky */
    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8f9fa;
    }

    /* Ensure proper table cell alignment */
    .table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        background: #f8f9fa;
        position: sticky;
        top: 0;
    }

    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }

    /* Custom scrollbar styling */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Keep existing status badge styles */
    .table td span[style*="padding: 5px 10px"] {
        display: inline-block;
        text-align: center;
        min-width: 90px;
    }

    /* Keep existing button styles */
    .table td .btn {
        padding: 5px 10px;
        font-size: 0.9rem;
        margin: 0 2px;
    }

    /* Responsive adjustments for scroll feature */
    @media (max-width: 768px) {
        .table-container {
            max-height: 350px;
            margin: 0 -15px;
            width: calc(100% + 30px);
            border-radius: 0;
        }

        .table {
            min-width: 800px;
        }

        .table th,
        .table td {
            padding: 10px 12px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .table-container {
            max-height: 300px;
        }

        .table th,
        .table td {
            padding: 8px 10px;
            font-size: 0.85rem;
        }

        .table td span[style*="padding: 5px 10px"] {
            padding: 3px 8px !important;
            font-size: 0.8rem;
            min-width: 80px;
        }

        .table td .btn {
            padding: 4px 8px;
            font-size: 0.8rem;
        }
    }

    /* Touch device support */
    @media (hover: none) and (pointer: coarse) {
        .table-container::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {

        .table thead,
        .table th {
            background: #2d3748;
        }

        .table-container::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #4a5568;
        }
    }
</style>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard">
        <div class="container">
            <h2>Admin Dashboard</h2>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="number"><?php echo $total_bookings; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="number"><?php echo $total_users; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Available Rooms</h3>
                    <div class="number"><?php echo $available_rooms; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Monthly Income</h3>
                    <div class="number">₹<?php echo number_format($monthly_income, 2); ?></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h3>Quick Actions</h3>
                <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                    <a href="rooms.php" class="btn">Manage Rooms</a>
                    <a href="bookings.php" class="btn" style="background: var(--warning);">Manage Bookings</a>
                    <a href="users.php" class="btn" style="background: var(--success);">Manage Users</a>
                    <a href="gallery.php" class="btn" style="background: var(--accent);">Manage Gallery</a>
                    <!-- In your header.php or sidebar -->
                    <a href="analytics.php" class="btn" style="background: var(--primary);" title="Analytics Dashboard">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>

                    <!-- In your header.php navigation -->
                    <a href="contacts.php" class="btn" style="background: var(--success);"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="card">
                <h3>Recent Bookings</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT b.*, u.full_name, r.room_number 
                                 FROM bookings b 
                                 JOIN users u ON b.user_id = u.id 
                                 JOIN rooms r ON b.room_id = r.id 
                                 ORDER BY b.booking_date DESC LIMIT 10";
                        $result = mysqli_query($conn, $query);

                        while ($booking = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td><?php echo $booking['full_name']; ?></td>
                                <td><?php echo $booking['room_number']; ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['check_in'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['check_out'])); ?></td>
                                <td>₹<?php echo $booking['total_amount']; ?></td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 20px; 
                                      background: <?php
                                                    $status_color = [
                                                        'Pending' => '#f39c12',
                                                        'Confirmed' => '#27ae60',
                                                        'Cancelled' => '#e74c3c',
                                                        'Completed' => '#3498db'
                                                    ];
                                                    echo $status_color[$booking['status']];
                                                    ?>; color: white;">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <?php if ($booking['status'] == 'Pending'): ?>
                                            <a href="approve-booking.php?id=<?php echo $booking['id']; ?>"
                                                class="btn btn-success" style="padding: 5px 10px; font-size: 0.9rem;">
                                                Approve
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($booking['status'] != 'Cancelled' && $booking['status'] != 'Completed'): ?>
                                            <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>"
                                                class="btn btn-danger" style="padding: 5px 10px; font-size: 0.9rem;">
                                                Cancel
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- In your header.php or sidebar -->

    <?php include '../includes/footer.php'; ?>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find the table in recent bookings card
        const tableCard = document.querySelector('.card:has(.table)');
        const table = tableCard?.querySelector('.table');

        if (table && !tableCard.querySelector('.table-container')) {
            // Create scroll container
            const container = document.createElement('div');
            container.className = 'table-container';

            // Wrap the table with the container
            table.parentNode.insertBefore(container, table);
            container.appendChild(table);

            console.log('Scroll container added to recent bookings table');
        }

        // Add touch scroll support for mobile
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            let isScrolling = false;
            let startX, scrollLeft;
            let startY, scrollTop;

            tableContainer.addEventListener('touchstart', (e) => {
                isScrolling = true;
                startX = e.touches[0].pageX - tableContainer.offsetLeft;
                scrollLeft = tableContainer.scrollLeft;
                startY = e.touches[0].pageY - tableContainer.offsetTop;
                scrollTop = tableContainer.scrollTop;
            });

            tableContainer.addEventListener('touchmove', (e) => {
                if (!isScrolling) return;
                e.preventDefault();

                const x = e.touches[0].pageX - tableContainer.offsetLeft;
                const y = e.touches[0].pageY - tableContainer.offsetTop;

                // Check if horizontal or vertical scrolling is needed
                const walkX = (x - startX) * 2;
                const walkY = (y - startY) * 2;

                // Scroll horizontally if needed
                if (Math.abs(walkX) > Math.abs(walkY)) {
                    tableContainer.scrollLeft = scrollLeft - walkX;
                } else {
                    tableContainer.scrollTop = scrollTop - walkY;
                }
            });

            tableContainer.addEventListener('touchend', () => {
                isScrolling = false;
            });
        }
    });
</script>