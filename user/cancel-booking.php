<?php
// user/cancel-booking.php - FIXED SIMPLE VERSION
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$booking_id) {
    $_SESSION['error'] = "Invalid booking ID";
    header("Location: history.php");
    exit();
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_cancel'])) {

    // Simple update query - Use the correct booking ID from URL
    $update_query = "UPDATE bookings SET status = 'Cancelled' WHERE id = $booking_id AND user_id = $user_id";

    if (mysqli_query($conn, $update_query)) {
        $affected_rows = mysqli_affected_rows($conn);

        if ($affected_rows > 0) {
            // Success - redirect to history page
            $_SESSION['success'] = "Booking #$booking_id has been cancelled successfully!";
            header("Location: history.php");
            exit();
        } else {
            // No rows affected - booking doesn't exist or not user's
            $_SESSION['error'] = "Booking #$booking_id not found or you don't have permission.";
            header("Location: history.php");
            exit();
        }
    } else {
        // Database error
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: cancel-booking.php?id=$booking_id");
        exit();
    }
}

// Get booking details for display
$query = "SELECT b.*, r.room_type, r.room_number 
          FROM bookings b 
          JOIN rooms r ON b.room_id = r.id 
          WHERE b.id = $booking_id AND b.user_id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    $_SESSION['error'] = "Database error.";
    header("Location: history.php");
    exit();
}

$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    $_SESSION['error'] = "Booking #$booking_id not found.";
    header("Location: history.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .booking-info p {
            margin: 10px 0;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Cancel Booking #<?php echo $booking_id; ?></h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($booking['status'] == 'Cancelled'): ?>
            <div class="booking-info">
                <h3>This booking is already cancelled</h3>
                <p><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></p>
                <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
                <p><strong>Status:</strong> <span style="color: red; font-weight: bold;">Cancelled</span></p>
            </div>
            <div style="text-align: center;">
                <a href="history.php" class="btn btn-secondary">Back to History</a>
            </div>
        <?php else: ?>
            <div class="booking-info">
                <h3>Booking Details</h3>
                <p><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></p>
                <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['room_type']); ?> (<?php echo htmlspecialchars($booking['room_number']); ?>)</p>
                <p><strong>Check-in:</strong> <?php echo date('d M Y', strtotime($booking['check_in'])); ?></p>
                <p><strong>Check-out:</strong> <?php echo date('d M Y', strtotime($booking['check_out'])); ?></p>
                <p><strong>Amount:</strong> ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                <p><strong>Current Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <p><strong>Note:</strong> This will cancel your booking and make the room available for others.</p>
            </div>

            <form method="POST" action="">
                <div style="text-align: center;">
                    <button type="submit" name="confirm_cancel" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel booking #<?php echo $booking_id; ?>?')">
                        Confirm Cancellation
                    </button>
                    <a href="history.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>