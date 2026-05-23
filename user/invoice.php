<?php
// user/invoice.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireUser();

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$booking_id) {
    header("Location: history.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fixed query - use correct column names
$query = "SELECT 
            b.*,
            r.room_number,
            r.room_type,
            r.price_per_night,
            r.ac_type,
            r.capacity,
            r.amenities,
            u.full_name,
            u.email,
            u.mobile,
            p.transaction_id,
            p.payment_date,
            p.payment_method
          FROM bookings b
          JOIN rooms r ON b.room_id = r.id
          JOIN users u ON b.user_id = u.id
          LEFT JOIN payments p ON b.id = p.booking_id
          WHERE b.id = ? AND b.user_id = ?";

$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

// Bind parameters
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<div style='text-align:center; padding:50px; background:#f8f9fa; border-radius:10px; margin:20px;'>
            <h3 style='color:#dc3545;'>Booking Not Found</h3>
            <p>The requested booking does not exist or you don't have permission to view it.</p>
            <a href='history.php' style='display:inline-block; margin-top:15px; padding:10px 20px; 
               background:#007bff; color:white; text-decoration:none; border-radius:5px;'>
               Go Back to History
            </a>
          </div>";
    exit();
}

$booking = mysqli_fetch_assoc($result);

// Calculate number of nights
$check_in = new DateTime($booking['check_in']);
$check_out = new DateTime($booking['check_out']);
$interval = $check_in->diff($check_out);
$total_nights = $interval->days;

// Calculate amounts
$subtotal = $booking['price_per_night'] * $total_nights;
$tax = $subtotal * 0.18;
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Booking #<?php echo $booking_id; ?></title>
    <style>
        /* Simple CSS with small and medium font sizes */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
            font-size: 14px; /* Base font size - small */
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }
        
        .hotel-name {
            color: #333;
            font-size: 20px; /* Medium */
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .invoice-title {
            font-size: 18px; /* Medium */
            color: #007bff;
            margin: 8px 0;
            font-weight: bold;
        }
        
        .invoice-number {
            color: #666;
            font-size: 14px; /* Small */
            margin-bottom: 10px;
        }
        
        .section-title {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 3px;
            margin: 18px 0 12px 0;
            font-size: 16px; /* Medium */
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px; /* Small */
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px; /* Medium */
            color: #333;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            border: 1px solid #ddd;
            font-size: 14px; /* Small */
        }
        
        .invoice-table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 14px; /* Small */
            font-weight: bold;
        }
        
        .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 13px; /* Small */
        }
        
        .invoice-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .total-row {
            background: #e9ecef !important;
            font-weight: bold;
            font-size: 15px; /* Medium */
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 12px; /* Small */
            font-weight: bold;
        }
        
        .status-confirmed {
            background: #28a745;
            color: white;
        }
        
        .status-completed {
            background: #007bff;
            color: white;
        }
        
        .status-pending {
            background: #ffc107;
            color: #212529;
        }
        
        .status-cancelled {
            background: #dc3545;
            color: white;
        }
        
        .invoice-footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 13px; /* Small */
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px; /* Medium */
            margin: 8px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 4px;
            font-size: 13px; /* Small */
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 12px; /* Smaller for print */
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
            }
            
            .no-print {
                display: none;
            }
            
            .action-buttons {
                display: none;
            }
            
            .invoice-table {
                font-size: 12px; /* Smaller for print */
            }
            
            .invoice-table th,
            .invoice-table td {
                padding: 6px;
            }
        }
        
        .invoice-date {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 13px; /* Small */
        }
        
        .amount-highlight {
            font-size: 16px; /* Medium */
            font-weight: bold;
            color: #007bff;
        }
        
        /* Medium text class */
        .text-medium {
            font-size: 14px;
        }
        
        /* Small text class */
        .text-small {
            font-size: 12px;
        }
        
        /* Section headers */
        h1, h2, h3 {
            margin: 0 0 10px 0;
        }
        
        h1 {
            font-size: 20px; /* Medium */
        }
        
        h2 {
            font-size: 18px; /* Medium */
        }
        
        h3 {
            font-size: 16px; /* Medium */
        }
        
        p {
            margin: 0 0 10px 0;
            font-size: 14px; /* Medium */
        }
        
        /* Invoice details */
        .invoice-details {
            font-size: 13px; /* Small */
            color: #666;
            margin-bottom: 15px;
        }
        
        /* Strong/bold text */
        strong {
            font-weight: bold;
        }
        
        /* Table amounts */
        .table-amount {
            font-size: 13px; /* Small */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1 class="hotel-name">SMART HOTEL</h1>
            <h2 class="invoice-title">BOOKING INVOICE</h2>
            <p class="invoice-number">Invoice #<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></p>
            <div class="invoice-date">
                <p class="text-small"><strong>Invoice Date:</strong> <?php echo date('F d, Y'); ?></p>
                <p class="text-small"><strong>Invoice Time:</strong> <?php echo date('h:i A'); ?></p>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="section-title">CUSTOMER INFORMATION</div>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Customer Name</div>
                <div class="info-value text-medium"><?php echo htmlspecialchars($booking['full_name']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Email Address</div>
                <div class="info-value text-medium"><?php echo htmlspecialchars($booking['email']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Phone Number</div>
                <div class="info-value text-medium"><?php echo htmlspecialchars($booking['mobile'] ?? 'N/A'); ?></div>
            </div>
        </div>
        
        <!-- Booking Information -->
        <div class="section-title">BOOKING DETAILS</div>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Booking ID</div>
                <div class="info-value text-medium">#<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Booking Date</div>
                <div class="info-value text-medium"><?php echo date('F d, Y', strtotime($booking['booking_date'])); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Booking Status</div>
                <div class="info-value">
                    <span class="status-badge 
                        <?php echo strtolower($booking['status']) == 'confirmed' ? 'status-confirmed' : ''; ?>
                        <?php echo strtolower($booking['status']) == 'completed' ? 'status-completed' : ''; ?>
                        <?php echo strtolower($booking['status']) == 'pending' ? 'status-pending' : ''; ?>
                        <?php echo strtolower($booking['status']) == 'cancelled' ? 'status-cancelled' : ''; ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Room Information -->
        <div class="section-title">ROOM DETAILS</div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Room Number</th>
                    <th>AC Type</th>
                    <th>Capacity</th>
                    <th>Price/Night</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-small"><?php echo htmlspecialchars($booking['room_type']); ?></td>
                    <td class="text-small">#<?php echo htmlspecialchars($booking['room_number']); ?></td>
                    <td class="text-small"><?php echo htmlspecialchars($booking['ac_type'] ?? 'Standard'); ?></td>
                    <td class="text-small"><?php echo htmlspecialchars($booking['capacity'] ?? '2'); ?> Persons</td>
                    <td class="text-small table-amount">₹<?php echo number_format($booking['price_per_night'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Stay Details -->
        <div class="section-title">STAY INFORMATION</div>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Check-in Date</div>
                <div class="info-value text-medium"><?php echo date('F d, Y', strtotime($booking['check_in'])); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Check-out Date</div>
                <div class="info-value text-medium"><?php echo date('F d, Y', strtotime($booking['check_out'])); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Total Nights</div>
                <div class="info-value text-medium"><?php echo $total_nights; ?> Nights</div>
            </div>
            <div class="info-card">
                <div class="info-label">Stay Duration</div>
                <div class="info-value text-small"><?php echo date('d M', strtotime($booking['check_in'])); ?> - <?php echo date('d M Y', strtotime($booking['check_out'])); ?></div>
            </div>
        </div>
        
        <!-- Payment Details -->
        <div class="section-title">PAYMENT SUMMARY</div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-small"><?php echo htmlspecialchars($booking['room_type']); ?> Room</td>
                    <td class="text-small"><?php echo $total_nights; ?> Nights</td>
                    <td class="text-small table-amount">₹<?php echo number_format($booking['price_per_night'], 2); ?></td>
                    <td class="text-small table-amount">₹<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;" class="text-small"><strong>Subtotal:</strong></td>
                    <td class="text-medium"><strong>₹<?php echo number_format($subtotal, 2); ?></strong></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;" class="text-small"><strong>Tax (18%):</strong></td>
                    <td class="text-medium"><strong>₹<?php echo number_format($tax, 2); ?></strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;" class="text-medium"><strong>TOTAL AMOUNT:</strong></td>
                    <td class="amount-highlight">₹<?php echo number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Payment Information -->
        <?php if (!empty($booking['payment_method'])): ?>
        <div class="section-title">PAYMENT INFORMATION</div>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Payment Method</div>
                <div class="info-value text-medium"><?php echo htmlspecialchars($booking['payment_method']); ?></div>
            </div>
            <?php if (!empty($booking['transaction_id'])): ?>
            <div class="info-card">
                <div class="info-label">Transaction ID</div>
                <div class="info-value text-small"><?php echo htmlspecialchars($booking['transaction_id']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($booking['payment_date'])): ?>
            <div class="info-card">
                <div class="info-label">Payment Date</div>
                <div class="info-value text-small"><?php echo date('F d, Y, h:i A', strtotime($booking['payment_date'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Special Requests -->
        <?php if (!empty($booking['special_requests'])): ?>
        <div class="section-title">SPECIAL REQUESTS</div>
        <div class="info-card">
            <div class="info-value text-small"><?php echo htmlspecialchars($booking['special_requests']); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <p class="text-medium"><strong>Thank you for choosing Smart Hotel!</strong></p>
            <p class="text-small">For any queries, contact: support@smarthotel.com | +91-9876543210</p>
            <p class="text-small">75 Hotel Street, City Center, Patna - 800024</p>
            <p class="text-small" style="margin-top: 10px;">This is a computer-generated invoice. No signature required.</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="print-btn">
                Print Invoice
            </button>
            <a href="history.php" class="btn btn-secondary">
                Back to History
            </a>
            <a href="booking-details.php?id=<?php echo $booking_id; ?>" class="btn btn-primary">
                View Booking
            </a>
        </div>
    </div>
    
    <!-- Simple JavaScript for auto-print -->
    <script>
        if (window.location.search.includes('print=true')) {
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>