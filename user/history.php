<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireUser();

$user_id = $_SESSION['user_id'];

// Get user bookings
$query = "SELECT b.*, r.room_number, r.room_type, p.status as payment_status
          FROM bookings b
          JOIN rooms r ON b.room_id = r.id
          LEFT JOIN payments p ON b.id = p.booking_id
          WHERE b.user_id = $user_id
          ORDER BY b.booking_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container" style="padding: 2rem 0;">
        <h2 id="history-headline">My Booking History</h2>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
                <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // Determine message based on status
                    $status_message = '';
                    $message_class = '';
                    switch(strtolower($booking['status'])) {
                        case 'pending':
                            $status_message = '⏳ Please complete the booking process.';
                            $message_class = 'message-waiting';
                            break;
                        case 'confirmed':
                            $status_message = '✅ Booking confirmed! You can now make payment CAD.';
                            $message_class = 'message-ready';
                            break;
                        case 'completed':
                            $status_message = '✅ Booking completed! Your Booking Room is confirmed.';
                            $message_class = 'message-completed';
                            break;
                        case 'cancelled':
                            $status_message = '❌ This booking has been cancelled.';
                            $message_class = 'message-cancelled';
                            break;
                    }
                    ?>
                    
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h3><?php echo $booking['room_type']; ?> Room</h3>
                                <p style="color: #666;">Booking #<?php echo $booking['id']; ?></p>
                            </div>
                            <span class="badge 
                                <?php echo strtolower($booking['status']) == 'pending' ? 'badge-warning' : ''; ?>
                                <?php echo strtolower($booking['status']) == 'confirmed' ? 'badge-info' : ''; ?>
                                <?php echo strtolower($booking['status']) == 'completed' ? 'badge-success' : ''; ?>
                                <?php echo strtolower($booking['status']) == 'cancelled' ? 'badge-danger' : ''; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        
                        <!-- Status Message -->
                        <?php if ($status_message): ?>
                            <div class="status-message <?php echo $message_class; ?>">
                                <?php echo $status_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="margin-bottom: 1rem;">
                            <p><strong>Room:</strong> #<?php echo $booking['room_number']; ?></p>
                            <p><strong>Dates:</strong> 
                                <?php echo date('d M', strtotime($booking['check_in'])); ?> - 
                                <?php echo date('d M Y', strtotime($booking['check_out'])); ?>
                            </p>
                            <p><strong>Amount:</strong> ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                            
                            <?php if ($booking['payment_status']): ?>
                                <p><strong>Payment:</strong> 
                                    <span style="color: <?php echo $booking['payment_status'] == 'completed' ? '#10b981' : '#f59e0b'; ?>; font-weight: bold;">
                                        <?php echo ucfirst($booking['payment_status']); ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                            
                            <p><strong>Booking Date:</strong> <?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?></p>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
                            <!--Order  Button - Disabled for completed status -->
                            <?php if (strtolower($booking['status']) == 'completed'): ?>
                                <span class="btn btn-sm btn-disabled">
                                    <i class="fas fa-eye"></i>Order 
                                </span>
                            <?php else: ?>
                                <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm">
                                    <i class="fas fa-eye"></i>Order 
                                </a>
                            <?php endif; ?>
                            
                            <!-- Invoice Button - Always enabled -->
                            <a href="invoice.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm" style="background: #6c757d;">
                                <i class="fas fa-file-invoice"></i> Invoice
                            </a>
                            
                            <!-- Pay Now Button - Only for confirmed status -->
                            <?php if ($booking['status'] == 'confirmed'): ?>
                                <a href="payment-process.php?id=<?php echo $booking['id']; ?>" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </a>
                            <?php elseif ($booking['status'] == 'pending'): ?>
                                <!-- Show disabled pay button for pending -->
                                <span class="btn btn-sm btn-disabled">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </span>
                            <?php elseif ($booking['status'] == 'completed'): ?>
                                <!-- Show completed payment status -->
                                <span class="btn btn-sm" style="background: #10b981; color: white;">
                                    <i class="fas fa-check-circle"></i> Paid
                                </span>
                            <?php endif; ?>
                            
                            <!-- Cancel Button - Only for pending and confirmed -->
                            <?php if (strtolower($booking['status']) == 'pending' || strtolower($booking['status']) == 'confirmed'): ?>
                                <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>"
                                   class="btn btn-sm" style="background: #ef4444; color: white;">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Additional Status Info -->
                        <?php if (strtolower($booking['status']) == 'pending'): ?>
                            <div style="margin-top: 10px; font-size: 0.85rem; color: #6b7280;">
                                <i class="fas fa-info-circle"></i> Admin will review your booking within 24 hours.
                            </div>
                        <?php elseif (strtolower($booking['status']) == 'confirmed'): ?>
                            <div style="margin-top: 10px; font-size: 0.85rem; color: #059669;">
                                <i class="fas fa-clock"></i> Please make payment within 48 hours to confirm your booking.
                            </div>
                        <?php elseif (strtolower($booking['status']) == 'completed'): ?>
                            <div style="margin-top: 10px; font-size: 0.85rem; color: #2563eb;">
                                <i class="fas fa-check-circle"></i> Your booking is fully confirmed. Check-in on <?php echo date('d M Y', strtotime($booking['check_in'])); ?>.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 10px;">
                <i class="fas fa-calendar-times fa-3x" style="color: #6c757d; margin-bottom: 1rem;"></i>
                <h3>No Bookings Yet</h3>
                <p>You haven't made any bookings yet.</p>
                <a href="../rooms.php" class="btn" style="margin-top: 1rem;">
                    <i class="fas fa-bed"></i> Book a Room
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Booking Status Legend -->
        <div class="card" style="margin-top: 2rem; background: #f8fafc;">
            <h4>Booking Status Guide</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="badge badge-warning">Pending</span>
                    <span>Waiting for admin approval</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="badge badge-info">Confirmed</span>
                    <span>Ready for payment</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="badge badge-success">Completed</span>
                    <span>Payment received, booking confirmed</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="badge badge-danger">Cancelled</span>
                    <span>Booking cancelled</span>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        // Add confirmation for cancellation
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('a[href*="cancel-booking"]');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to cancel this booking?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Add tooltip for disabled buttons
            const disabledButtons = document.querySelectorAll('.btn-disabled');
            disabledButtons.forEach(button => {
                if (button.querySelector('.fa-credit-card')) {
                    button.title = 'Payment will be available after admin confirmation';
                } else if (button.querySelector('.fa-eye')) {
                    button.title = 'Details not available for completed bookings';
                }
            });
        });
    </script>
</body>
</html>