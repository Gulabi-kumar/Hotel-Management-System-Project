<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireUser();

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if (!$booking_id) {
    header("Location: history.php");
    exit();
}

// Function to get booking details
function getBookingDetails($conn, $booking_id, $user_id)
{
    $query = "SELECT b.*, 
                     u.full_name, u.email, u.mobile,
                     r.room_number, r.room_type, r.ac_type, r.capacity, r.description,
                     r.price_per_night
              FROM bookings b
              JOIN users u ON b.user_id = u.id
              JOIN rooms r ON b.room_id = r.id
              WHERE b.id = ? AND b.user_id = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $booking;
}

// Get initial booking details
$booking = getBookingDetails($conn, $booking_id, $user_id);

if (!$booking) {
    header("Location: history.php");
    exit();
}

// Calculate number of nights
$checkin = new DateTime($booking['check_in']);
$checkout = new DateTime($booking['check_out']);
$nights = $checkin->diff($checkout)->days;

// Handle payment submission
$payment_message = '';
$show_congratulations = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_payment'])) {
    // For CAD (Cash At Desk), we'll use 'Cash' as the payment method
    $payment_method = 'Cash'; // Always set to Cash for CAD

    // Check if booking is already completed
    if (strtolower($booking['status']) == 'completed') {
        $payment_message = '<div class="alert alert-info">This booking is already completed and paid.</div>';
    } else {
        // Update booking status and payment method
        $update_query = "UPDATE bookings SET 
                        status = 'Completed', 
                        payment_method = ?, 
                        booking_date = NOW() 
                        WHERE id = ? AND user_id = ?";

        $update_stmt = mysqli_prepare($conn, $update_query);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sii", $payment_method, $booking_id, $user_id);

            if (mysqli_stmt_execute($update_stmt)) {
                $affected_rows = mysqli_stmt_affected_rows($update_stmt);
                mysqli_stmt_close($update_stmt);

                if ($affected_rows > 0) {
                    // Payment successful - refresh booking data
                    $booking = getBookingDetails($conn, $booking_id, $user_id);

                    $show_congratulations = true;
                    $payment_message = '<div class="alert alert-success">Payment successful! Your booking is now completed.</div>';

                    // Also update the session if needed
                    $_SESSION['payment_success'] = true;
                } else {
                    $payment_message = '<div class="alert alert-danger">No booking found or already processed.</div>';
                }
            } else {
                $payment_message = '<div class="alert alert-danger">Error executing payment: ' . mysqli_error($conn) . '</div>';
                mysqli_stmt_close($update_stmt);
            }
        } else {
            $payment_message = '<div class="alert alert-danger">Database error: ' . mysqli_error($conn) . '</div>';
        }
    }
}

    // Calculate amounts with Tax 

    $room = $booking['price_per_night'];
    $nights = $nights;
    $subtotal = $room * $nights;
    $tax = $subtotal * 0.18;
    $totalAmount = $tax + $subtotal

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .invoice-section {
            margin-bottom: 1.5rem;
        }

        .price-breakdown {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        .status-pending {
            background: #f39c12;
            color: white;
        }

        .status-confirmed {
            background: #27ae60;
            color: white;
        }

        .status-cancelled {
            background: #e74c3c;
            color: white;
        }

        .status-completed {
            background: #3498db;
            color: white;
        }

        .print-btn {
            margin-top: 2rem;
            text-align: center;
        }

        /* Payment Method Styles */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .payment-method {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .payment-method:hover {
            border-color: #3498db;
            transform: translateY(-2px);
        }

        .payment-method.selected {
            border-color: #27ae60;
            background: #e8f4fc;
        }

        .payment-method i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #3498db;
        }

        .payment-method.cad i {
            color: #27ae60;
        }

        .payment-method.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .payment-method.disabled:hover {
            border-color: #ddd;
            transform: none;
        }

        .coming-soon {
            font-size: 0.8rem;
            color: #e74c3c;
            margin-top: 5px;
            font-weight: bold;
        }

        .payment-info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            border-left: 4px solid #3498db;
        }

        .congratulations-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .congratulations-modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .congratulations-content {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .congratulations-content i {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1rem;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .btn-pay {
            background: #27ae60;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: block;
            margin: 2rem auto;
            width: 100%;
            max-width: 300px;
        }

        .btn-pay:hover {
            background: #219653;
            transform: scale(1.05);
        }

        .btn-close-modal {
            background: #3498db;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
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

        .payment-info {
            background: #e8f4fc;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            border-left: 4px solid #3498db;
        }

        .cad-explanation {
            background: #f0f8ff;
            border: 1px dashed #3498db;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="padding: 2rem 0;">
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <h1>Booking Invoice</h1>
                <p>Booking ID: #<?php echo $booking['id']; ?></p>
                <p>Date: <?php echo date('F d, Y H:i:s', strtotime($booking['booking_date'])); ?></p>

                <div class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                    <?php echo $booking['status']; ?>
                </div>
            </div>

            <!-- Display payment messages -->
            <?php echo $payment_message; ?>

            <!-- Booking Details -->
            <div class="invoice-details">
                <!-- Guest Information -->
                <div class="invoice-section">
                    <h3>Guest Information</h3>
                    <p><strong>Name:</strong> <?php echo $booking['full_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $booking['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $booking['mobile']; ?></p>
                </div>

                <!-- Room Information -->
                <div class="invoice-section">
                    <h3>Room Information</h3>
                    <p><strong>Room Number:</strong> <?php echo $booking['room_number']; ?></p>
                    <p><strong>Room Type:</strong> <?php echo $booking['room_type']; ?></p>
                    <p><strong>AC Type:</strong> <?php echo $booking['ac_type']; ?></p>
                    <p><strong>Capacity:</strong> <?php echo $booking['capacity']; ?> persons</p>
                </div>

                <!-- Booking Dates -->
                <div class="invoice-section">
                    <h3>Booking Dates</h3>
                    <p><strong>Check-in:</strong> <?php echo date('F d, Y', strtotime($booking['check_in'])); ?></p>
                    <p><strong>Check-out:</strong> <?php echo date('F d, Y', strtotime($booking['check_out'])); ?></p>
                    <p><strong>Number of Nights:</strong> <?php echo $nights; ?></p>
                </div>

                <!-- Price Breakdown -->
                <div class="invoice-section">
                    <h3>Price Breakdown</h3>
                    <div class="price-breakdown">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Room Price (per night):</span>
                            <span>₹<?php echo $booking['price_per_night']; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Number of Nights:</span>
                            <span><?php echo $nights; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Tax :</span>
                            <span><?php echo $tax; ?></span>
                        </div>
                        <hr>
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">

                            <span>Total Amount:</span>
                            <span>₹<?php echo $totalAmount ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <?php if (strtolower($booking['status']) == 'confirmed' || strtolower($booking['status']) == 'pending'): ?>
                <div class="invoice-section">
                    <h3>Complete Payment</h3>
                    <?php if ($booking['payment_method'] && strtolower($booking['status']) == 'completed'): ?>
                        <div class="payment-info">
                            <p><strong>Payment Status:</strong> <span style="color: green;">Completed</span></p>
                            <p><strong>Payment Method:</strong> <?php echo $booking['payment_method']; ?></p>
                            <?php if ($booking['booking_date']): ?>
                                <p><strong>Payment Date:</strong> <?php echo date('F d, Y H:i', strtotime($booking['booking_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="cad-explanation">
                            <h4 style="color: #27ae60; margin-top: 0;"><i class="fas fa-info-circle"></i> Cash at Desk (CAD) Payment</h4>
                            <p>Pay directly at the hotel reception when you check-in. Your booking will be marked as completed.</p>
                            <p><strong>Amount to pay:</strong> ₹<?php echo $totalAmount ?></p>
                            <p><small><i class="fas fa-clock"></i> Online payment methods will be available soon.</small></p>
                        </div>

                        <form method="POST" action="">
                            <div class="payment-info-box">
                                <h4>Select Payment Method</h4>
                                <div class="payment-methods">
                                    <!-- Only CAD/Cash option enabled -->
                                    <div class="payment-method cad selected" id="cadOption">
                                        <i class="fas fa-hotel"></i>
                                        <div><strong>Cash at Desk (CAD)</strong></div>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">
                                            Pay at hotel reception
                                        </div>
                                    </div>

                                    <!-- Disabled online payment methods -->
                                    <div class="payment-method disabled">
                                        <i class="fas fa-credit-card"></i>
                                        <div>Credit Card</div>
                                        <div class="coming-soon">Coming Soon</div>
                                    </div>

                                    <div class="payment-method disabled">
                                        <i class="fas fa-credit-card"></i>
                                        <div>Debit Card</div>
                                        <div class="coming-soon">Coming Soon</div>
                                    </div>

                                    <div class="payment-method disabled">
                                        <i class="fas fa-mobile-alt"></i>
                                        <div>UPI</div>
                                        <div class="coming-soon">Coming Soon</div>
                                    </div>

                                    <div class="payment-method disabled">
                                        <i class="fas fa-university"></i>
                                        <div>Net Banking</div>
                                        <div class="coming-soon">Coming Soon</div>
                                    </div>

                                    <div class="payment-method disabled">
                                        <i class="fas fa-wallet"></i>
                                        <div>Wallet</div>
                                        <div class="coming-soon">Coming Soon</div>
                                    </div>
                                </div>

                                <div style="text-align: center; margin-top: 2rem;">
                                    <button type="submit" name="complete_payment" class="btn-pay">
                                        <i class="fas fa-check-circle"></i> Confirm CAD Payment
                                    </button>
                                    <p style="font-size: 0.9rem; color: #666; margin-top: 10px;">
                                        <i class="fas fa-info-circle"></i> This will mark your booking as "Completed" with "Cash" payment method
                                    </p>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="invoice-section">
                <h3>Additional Information</h3>
                <p><strong>Booking Date:</strong> <?php echo date('F d, Y H:i', strtotime($booking['booking_date'])); ?></p>

                <?php if ($booking['special_requests']): ?>
                    <p><strong>Special Requests:</strong> <?php echo $booking['special_requests']; ?></p>
                <?php endif; ?>

                <p><strong>Payment Status:</strong>
                    <span style="color: <?php echo $booking['status'] == 'Completed' ? 'green' : 'orange'; ?>">
                        <?php echo $booking['status'] == 'Completed' ? 'Paid' : 'Pending'; ?>
                    </span>
                </p>
            </div>

            <!-- Hotel Information -->
            <div class="invoice-section" style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                <h3>Hotel Information</h3>
                <p><strong>Smart Hotel</strong></p>
                <p>75 Hotel Street, Bihar, India</p>
                <p>Phone: +91 9876543210</p>
                <p>Email: info@smarthotel.com</p>
                <p>GSTIN: 27AAAAA0000A1Z5</p>
            </div>

            <!-- Terms and Conditions -->
            <div class="invoice-section">
                <h3>Terms & Conditions</h3>
                <ul style="font-size: 0.9rem;">
                    <li>Check-in time: 2:00 PM | Check-out time: 12:00 PM</li>
                    <li>Cancellation must be made 24 hours before check-in for full refund</li>
                    <li>Late check-out may be subject to additional charges</li>
                    <li>Damage to hotel property will be charged to the guest</li>
                    <li>Payment can be made at the hotel reception during check-in</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="print-btn">
                <a href="invoice.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm" style="background: #6c757d;">
                    <i class="fas fa-file-invoice"></i>Print Invoice
                </a>

                <?php if ($booking['status'] == 'Pending' || $booking['status'] == 'Confirmed'): ?>
                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                        <i class="fas fa-times"></i> Cancel Booking
                    </a>
                <?php endif; ?>

                <a href="history.php" class="btn" style="background: #6c757d;">
                    <i class="fas fa-arrow-left"></i> Back to History
                </a>
            </div>
        </div>
    </div>

    <!-- Congratulations Modal -->
    <?php if ($show_congratulations): ?>
        <div class="congratulations-modal active" id="congratulationsModal">
            <div class="congratulations-content">
                <i class="fas fa-check-circle"></i>
                <h2>Payment Confirmed!</h2>
                <p>Your booking has been marked as <strong>Completed</strong>.</p>
                <p>Payment Method: <strong>Cash at Desk (CAD)</strong></p>
                <p>Amount to pay at reception: <strong>₹<?php echo $totalAmount; ?></strong></p>
                <p>Booking ID: <strong>#<?php echo $booking['id']; ?></strong></p>
                <p>Please bring this invoice when you check-in at the hotel.</p>

                <div style="margin-top: 2rem;">
                    <button class="btn-close-modal" onclick="closeCongratulations()">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Only CAD is selectable, others are disabled
        document.getElementById('cadOption').addEventListener('click', function() {
            // Remove selected class from all (though only CAD is enabled)
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });

            // Add selected class to CAD
            this.classList.add('selected');
        });

        // Close congratulations modal
        function closeCongratulations() {
            const modal = document.getElementById('congratulationsModal');
            if (modal) {
                modal.classList.remove('active');
            }
        }

        // Auto close modal after 10 seconds
        <?php if ($show_congratulations): ?>
            setTimeout(() => {
                closeCongratulations();
            }, 10000);
        <?php endif; ?>

        // Print specific styles
        const printStyles = `
        @media print {
            .header, .footer, .nav-links, .print-btn,
            .payment-info-box, .btn-pay,
            .congratulations-modal {
                display: none !important;
            }
            
            body {
                font-size: 12pt;
                background: white !important;
            }
            
            .invoice-container {
                box-shadow: none !important;
                padding: 0 !important;
            }
            
            .btn {
                display: none !important;
            }
        }
    `;

        // Add print styles
        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = printStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>

</html>