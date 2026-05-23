<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$error = '';
$success = '';

// Handle booking actions
if (isset($_GET['action'])) {
    $booking_id = sanitize($_GET['id']);
    $action = sanitize($_GET['action']);

    switch ($action) {
        case 'approve':
            $query = "UPDATE bookings SET status = 'Confirmed' WHERE id = $booking_id";
            if (mysqli_query($conn, $query)) {
                $success = "Booking approved successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            break;

        case 'cancel':
            $query = "UPDATE bookings SET status = 'Cancelled' WHERE id = $booking_id";
            if (mysqli_query($conn, $query)) {
                $success = "Booking cancelled successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            break;

        case 'complete':
            $query = "UPDATE bookings SET status = 'Completed' WHERE id = $booking_id";
            if (mysqli_query($conn, $query)) {
                $success = "Booking marked as completed!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            break;

        case 'delete':
            $query = "DELETE FROM bookings WHERE id = $booking_id";
            if (mysqli_query($conn, $query)) {
                $success = "Booking deleted successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            break;
    }

    // Redirect to avoid form resubmission
    header("Location: bookings.php?success=" . urlencode($success) . "&error=" . urlencode($error));
    exit();
}

// Check for success/error in URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Build query with filters
$query = "SELECT b.*, u.full_name, u.email, u.mobile, 
                 r.room_number, r.room_type, r.ac_type, r.price_per_night
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN rooms r ON b.room_id = r.id
          WHERE 1=1";

if ($status_filter) {
    $query .= " AND b.status = '$status_filter'";
}

if ($date_from) {
    $query .= " AND DATE(b.booking_date) >= '$date_from'";
}

if ($date_to) {
    $query .= " AND DATE(b.booking_date) <= '$date_to'";
}

$query .= " ORDER BY b.booking_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="padding: 2rem 0;">
        <h2>Manage Bookings</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="card">
            <h3>Filter Bookings</h3>
            <form method="GET" action="">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                    <div>
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                    </div>

                    <div>
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                    </div>

                    <div>
                        <button type="submit" class="btn">Filter</button>
                        <a href="bookings.php" class="btn" style="background: #6c757d;">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="card" style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>All Bookings</h3>
                <span>
                    Total: <?php echo mysqli_num_rows($result); ?> bookings
                </span>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Room Details</th>
                        <th>Dates</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo $booking['full_name']; ?></strong><br>
                                    <small><?php echo $booking['email']; ?></small><br>
                                    <small><?php echo $booking['mobile']; ?></small>
                                </td>
                                <td>
                                    <?php echo $booking['room_type']; ?> - <?php echo $booking['ac_type']; ?><br>
                                    Room: <?php echo $booking['room_number']; ?><br>
                                    ₹<?php echo $booking['price_per_night']; ?>/night
                                </td>
                                <td>
                                    Check-in: <?php echo date('d M Y', strtotime($booking['check_in'])); ?><br>
                                    Check-out: <?php echo date('d M Y', strtotime($booking['check_out'])); ?><br>
                                    <small>Booked: <?php echo date('d M Y H:i', strtotime($booking['booking_date'])); ?></small>
                                </td>
                                <td>₹<?php echo $booking['total_amount']; ?></td>
                                <td>
                                    <?php
                                    $status_colors = [
                                        'Pending' => '#f39c12',
                                        'Confirmed' => '#27ae60',
                                        'Cancelled' => '#e74c3c',
                                        'Completed' => '#3498db'
                                    ];
                                    ?>
                                    <span style="padding: 5px 10px; border-radius: 20px; 
                                      background: <?php echo $status_colors[$booking['status']]; ?>; 
                                      color: white;">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 5px;">
                                        <?php if ($booking['status'] == 'Pending'): ?>
                                            <!-- FIXED: Link to SAME FILE (bookings.php) -->
                                            <a href="bookings.php?action=approve&id=<?php echo $booking['id']; ?>"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('Approve booking #<?php echo $booking['id']; ?>?')">
                                                Approve
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($booking['status'] != 'Cancelled' && $booking['status'] != 'Completed'): ?>
                                            <!-- FIXED: Link to SAME FILE (bookings.php) -->
                                            <a href="bookings.php?action=cancel&id=<?php echo $booking['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Cancel booking #<?php echo $booking['id']; ?>?')">
                                                Cancel
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($booking['status'] == 'Confirmed'): ?>
                                            <!-- FIXED: Link to SAME FILE (bookings.php) -->
                                            <a href="bookings.php?action=complete&id=<?php echo $booking['id']; ?>"
                                                class="btn btn-info btn-sm"
                                                onclick="return confirm('Mark booking #<?php echo $booking['id']; ?> as completed?')">
                                                Complete
                                            </a>
                                        <?php endif; ?>

                                        <a href="../user/booking-details.php?id=<?php echo $booking['id']; ?>"
                                            target="_blank"
                                            class="btn btn-sm" style="background: #6c757d;">
                                            View Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                No bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistics -->
        <div class="card" style="margin-top: 2rem;">
            <h3>Booking Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <?php
                $stats_queries = [
                    'Total Bookings' => "SELECT COUNT(*) as count FROM bookings",
                    'Pending Bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'",
                    'Confirmed Bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status = 'Confirmed'",
                    'Today\'s Bookings' => "SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = CURDATE()",
                    'Monthly Revenue' => "SELECT SUM(total_amount) as amount FROM bookings 
                                         WHERE status IN ('Confirmed', 'Completed') 
                                         AND MONTH(booking_date) = MONTH(CURDATE())",
                    'Total Revenue' => "SELECT SUM(total_amount) as amount FROM bookings 
                                       WHERE status IN ('Confirmed', 'Completed')"
                ];

                foreach ($stats_queries as $label => $sql):
                    $result2 = mysqli_query($conn, $sql);
                    $data = mysqli_fetch_assoc($result2);
                    $value = $data['count'] ?? $data['amount'] ?? 0;
                ?>
                    <div class="stat-card">
                        <h4><?php echo $label; ?></h4>
                        <div class="number">
                            <?php echo strpos($label, 'Revenue') ? '₹' . number_format($value, 2) : $value; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Confirmation for all action buttons
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelectorAll('a[href*="action="]');

            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    const actionMatch = href.match(/action=(\w+)/);
                    const idMatch = href.match(/id=(\d+)/);

                    if (actionMatch && idMatch) {
                        const action = actionMatch[1];
                        const id = idMatch[1];

                        let message = '';
                        switch (action) {
                            case 'approve':
                                message = `Are you sure you want to APPROVE booking #${id}?`;
                                break;
                            case 'cancel':
                                message = `Are you sure you want to CANCEL booking #${id}?`;
                                break;
                            case 'complete':
                                message = `Are you sure you want to mark booking #${id} as COMPLETED?`;
                                break;
                            case 'delete':
                                message = `Are you sure you want to DELETE booking #${id}? This cannot be undone!`;
                                break;
                            default:
                                return true;
                        }

                        if (!confirm(message)) {
                            e.preventDefault();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>