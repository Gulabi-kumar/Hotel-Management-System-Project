<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireUser();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get room if specified
$room = null;
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
if ($room_id) {
    $query = "SELECT * FROM rooms WHERE id = $room_id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $room = mysqli_fetch_assoc($result);
    }
}

// Handle AJAX availability check
if (isset($_POST['check_availability'])) {
    $room_id = intval($_POST['room_id']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);
    
    // Simple validation
    if (empty($check_in) || empty($check_out)) {
        echo 'Please select dates';
        exit;
    }
    
    // Check if room exists and is available
    $room_check = mysqli_query($conn, "SELECT is_available, price_per_night FROM rooms WHERE id = $room_id");
    if ($room_check && mysqli_num_rows($room_check) > 0) {
        $room_data = mysqli_fetch_assoc($room_check);
        
        if ($room_data['is_available'] == 0) {
            echo 'Room not available';
            exit;
        }
        
        $price_per_night = $room_data['price_per_night'];
    } else {
        echo 'Room not found';
        exit;
    }
    
    // Check for booking conflicts
    $query = "SELECT id FROM bookings 
              WHERE room_id = $room_id 
              AND status IN ('Confirmed', 'Pending')
              AND (
                  (check_in <= '$check_out' AND check_out >= '$check_in')
              )";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo 'Room already booked for these dates';
    } else {
        // Calculate price
        $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        if ($nights < 1) $nights = 1;
        $total = $nights * $price_per_night;
        
        echo "SUCCESS|Room is available!|$nights|" . number_format($total);
    }
    exit;
}

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_now'])) {
    $room_id = intval($_POST['room_id']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);
    $requests = isset($_POST['special_requests']) ? mysqli_real_escape_string($conn, $_POST['special_requests']) : '';
    
    // Final availability check
    $query = "SELECT id FROM bookings 
              WHERE room_id = $room_id 
              AND status IN ('Confirmed', 'Pending')
              AND (
                  (check_in <= '$check_out' AND check_out >= '$check_in')
              )";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $error = "Room is no longer available!";
    } else {
        // Get price
        $price_query = mysqli_query($conn, "SELECT price_per_night FROM rooms WHERE id = $room_id");
        if ($price_query && $price_data = mysqli_fetch_assoc($price_query)) {
            $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
            if ($nights < 1) $nights = 1;
            $total = $nights * $price_data['price_per_night'];
            
            // Create booking
            $insert = "INSERT INTO bookings (user_id, room_id, check_in, check_out, total_amount, special_requests, status) 
                       VALUES ($user_id, $room_id, '$check_in', '$check_out', $total, '$requests', 'Pending')";
            
            if (mysqli_query($conn, $insert)) {
                $booking_id = mysqli_insert_id($conn);
                header("Location: booking-details.php?id=$booking_id");
                exit;
            } else {
                $error = "Booking failed: " . mysqli_error($conn);
            }
        } else {
            $error = "Room not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Responsive Booking Page Styles */
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f7fa;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 15px;
        }
        
        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        h3 {
            font-size: 1.4rem;
            margin: 20px 0 15px 0;
            color: var(--dark);
        }
        
        h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 4px solid var(--danger);
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 4px solid var(--success);
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            border-left: 4px solid #17a2b8;
        }
        
        /* Card */
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f7;
            margin-bottom: 20px;
        }
        
        /* Room Summary */
        .room-summary {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .room-summary img {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .room-summary-content {
            flex: 1;
            min-width: 250px;
        }
        
        .room-summary h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .room-summary p {
            margin-bottom: 8px;
            color: var(--gray);
        }
        
        .price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 10px;
        }
        
        /* Availability Result Box */
        .availability-result {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid var(--primary);
            display: none;
        }
        
        .availability-result.available {
            background: #e8f6ef;
            border-left-color: var(--success);
        }
        
        .availability-result.not-available {
            background: #fdeaea;
            border-left-color: var(--danger);
        }
        
        .price-summary {
            background: #f0f8ff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .price-summary p {
            margin: 5px 0;
            font-size: 1.1rem;
        }
        
        .price-summary .total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn-check {
            background: var(--warning);
        }
        
        .btn-check:hover {
            background: #e67e22;
        }
        
        .btn-success {
            background: var(--success);
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .btn-disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .btn-disabled:hover {
            background: #7f8c8d;
            transform: none;
            box-shadow: none;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        /* Loading spinner */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Booking Steps */
        .booking-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .booking-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 10px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: var(--primary);
            color: white;
        }
        
        .step.completed .step-number {
            background: var(--success);
            color: white;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #777;
        }
        
        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }
        
        /* How It Works Section */
        .how-it-works-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .how-it-works-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        
        .how-it-works-item:hover {
            transform: translateY(-5px);
        }
        
        .how-it-works-item i {
            font-size: 2rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .how-it-works-item h4 {
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        /* Special Section */
        #specialSection {
            margin-top: 20px;
            display: none;
        }
        
        /* Result and Loading */
        #result {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        #priceDetails {
            background: #e8f4fc;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            display: none;
        }
        
        /* Responsive Breakpoints */
        
        /* Tablet (768px and up) */
        @media (min-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .room-summary {
                flex-wrap: nowrap;
            }
            
            .room-summary img {
                width: 300px;
                flex-shrink: 0;
            }
            
            .button-group {
                flex-wrap: nowrap;
            }
            
            .how-it-works-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* Desktop (1024px and up) */
        @media (min-width: 1024px) {
            .container {
                padding: 40px;
            }
            
            .card {
                padding: 30px;
            }
            
            .room-summary img {
                width: 350px;
                height: 250px;
            }
            
            .room-summary h3 {
                font-size: 1.8rem;
            }
            
            .price {
                font-size: 1.5rem;
            }
        }
        
        /* Large Desktop (1200px and up) */
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
            
            .room-summary img {
                width: 400px;
                height: 280px;
            }
        }
        
        /* Small Mobile (480px and below) */
        @media (max-width: 480px) {
            .container {
                padding: 15px 10px;
            }
            
            .card {
                padding: 20px 15px;
            }
            
            h2 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }
            
            .room-summary {
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .room-summary img {
                height: 180px;
            }
            
            .room-summary h3 {
                font-size: 1.3rem;
            }
            
            .btn {
                padding: 12px;
                font-size: 1rem;
            }
            
            .booking-steps {
                flex-direction: column;
                gap: 20px;
            }
            
            .booking-steps::before {
                display: none;
            }
            
            .step {
                display: flex;
                align-items: center;
                gap: 15px;
                text-align: left;
            }
            
            .step-number {
                margin: 0;
                flex-shrink: 0;
            }
            
            .how-it-works-item {
                padding: 10px;
            }
            
            .how-it-works-item i {
                font-size: 1.5rem;
            }
        }
        
        /* Extra small devices (phones less than 360px) */
        @media (max-width: 360px) {
            .container {
                padding: 10px 5px;
            }
            
            .card {
                padding: 15px 10px;
            }
            
            .room-summary img {
                height: 150px;
            }
            
            .form-control {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .button-group {
                gap: 10px;
            }
            
            .step-number {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            
            .step-label {
                font-size: 0.8rem;
            }
        }
        
        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                padding: 15px 20px;
                min-height: 44px;
            }
            
            .form-control {
                min-height: 44px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Book a Room</h2>
        
        <!-- Booking Steps -->
        <div class="booking-steps">
            <div class="step active" id="step1">
                <div class="step-number">1</div>
                <div class="step-label">Select Dates & Check Availability</div>
            </div>
            <div class="step" id="step2">
                <div class="step-number">2</div>
                <div class="step-label">Confirm Booking</div>
            </div>
            <div class="step" id="step3">
                <div class="step-number">3</div>
                <div class="step-label">Payment</div>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form id="bookingForm" method="POST" action="">
                <?php if ($room): ?>
                    <div class="room-summary">
                        <img src="../assets/uploads/rooms/<?php echo htmlspecialchars($room['image_path'] ?: 'default.jpg'); ?>" 
                             alt="Room"
                             onerror="this.src='../assets/uploads/rooms/default.jpg'">
                        
                        <div class="room-summary-content">
                            <h3><?php echo htmlspecialchars($room['room_type']); ?> Room</h3>
                            <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room['room_number']); ?></p>
                            <p><strong>AC Type:</strong> <?php echo htmlspecialchars($room['ac_type']); ?></p>
                            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> persons</p>
                            <p class="price">Price: ₹<?php echo number_format($room['price_per_night']); ?> per night</p>
                        </div>
                    </div>
                    <input type="hidden" name="room_id" id="room_id" value="<?php echo $room['id']; ?>">
                <?php else: ?>
                    <div class="form-group">
                        <label for="room_select">Select Room</label>
                        <select class="form-control" id="room_select" name="room_id" required>
                            <option value="">Select a room</option>
                            <?php
                            $rooms_query = "SELECT * FROM rooms WHERE is_available = 1";
                            $rooms_result = mysqli_query($conn, $rooms_query);
                            if ($rooms_result) {
                                while ($r = mysqli_fetch_assoc($rooms_result)):
                            ?>
                                <option value="<?php echo htmlspecialchars($r['id']); ?>" <?php echo isset($_GET['room_id']) && $_GET['room_id'] == $r['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($r['room_type']); ?> - 
                                    <?php echo htmlspecialchars($r['ac_type']); ?> - 
                                    ₹<?php echo number_format($r['price_per_night']); ?>/night -
                                    <?php echo htmlspecialchars($r['capacity']); ?> persons
                                </option>
                            <?php 
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <h3>Select Your Dates</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="check_in">Check-in Date</label>
                        <input type="date" class="form-control" id="check_in" name="check_in" 
                               min="<?php echo date('Y-m-d'); ?>" 
                               value="<?php echo isset($_GET['check_in']) ? htmlspecialchars($_GET['check_in']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="check_out">Check-out Date</label>
                        <input type="date" class="form-control" id="check_out" name="check_out" 
                               value="<?php echo isset($_GET['check_out']) ? htmlspecialchars($_GET['check_out']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Checking availability...</p>
                </div>
                
                <div id="result"></div>
                
                <div id="priceDetails">
                    <div class="price-summary">
                        <p id="nightsText"></p>
                        <p class="total" id="totalText"></p>
                    </div>
                </div>
                
                <div id="specialSection">
                    <div class="form-group">
                        <label>Special Requests (Optional)</label>
                        <textarea class="form-control" name="special_requests" rows="3" placeholder="Any special requests or requirements?"></textarea>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="button" id="checkBtn" class="btn btn-check">
                        <i class="fas fa-calendar-check"></i> Check Availability
                    </button>
                    
                    <button type="submit" name="book_now" id="bookBtn" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Confirm Booking
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>How It Works</h3>
            <div class="how-it-works-grid">
                <div class="how-it-works-item">
                    <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                    <h4>Step 1: Check Availability</h4>
                    <p>Select your desired room and dates, then click "Check Availability" to see if the room is free.</p>
                </div>
                <div class="how-it-works-item">
                    <i class="fas fa-check-circle" style="color: var(--success);"></i>
                    <h4>Step 2: Confirm Booking</h4>
                    <p>If the room is available, you'll see a confirmation with price details. Click "Confirm Booking" to proceed.</p>
                </div>
                <div class="how-it-works-item">
                    <i class="fas fa-credit-card" style="color: var(--warning);"></i>
                    <h4>Step 3: Make Payment</h4>
                    <p>You'll be redirected to the payment page to complete your booking.</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');
        const checkBtn = document.getElementById('checkBtn');
        const bookBtn = document.getElementById('bookBtn');
        const resultDiv = document.getElementById('result');
        const loadingDiv = document.getElementById('loading');
        const priceDetails = document.getElementById('priceDetails');
        const specialSection = document.getElementById('specialSection');
        const nightsText = document.getElementById('nightsText');
        const totalText = document.getElementById('totalText');
        const bookingForm = document.getElementById('bookingForm');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        
        // Initially hide booking button and special section
        bookBtn.style.display = 'none';
        specialSection.style.display = 'none';
        priceDetails.style.display = 'none';
        
        // Set minimum checkout date
        if (checkIn && checkOut) {
            checkIn.addEventListener('change', function() {
                checkOut.min = this.value;
                if (checkOut.value && checkOut.value < this.value) {
                    checkOut.value = this.value;
                }
                hideResult();
            });
        }
        
        function hideResult() {
            resultDiv.style.display = 'none';
            priceDetails.style.display = 'none';
            specialSection.style.display = 'none';
            bookBtn.style.display = 'none';
            checkBtn.style.display = 'block';
            step1.classList.add('active');
            step2.classList.remove('active', 'completed');
        }
        
        function showError(message) {
            resultDiv.innerHTML = `<strong>Error</strong><br>${message}`;
            resultDiv.className = 'error';
            resultDiv.style.display = 'block';
            hideResult();
        }
        
        checkBtn.addEventListener('click', function() {
            // Get room ID - either from hidden input or select dropdown
            let roomId;
            const hiddenRoomId = document.querySelector('input[name="room_id"]');
            const selectRoomId = document.getElementById('room_select');
            
            if (hiddenRoomId) {
                roomId = hiddenRoomId.value;
            } else if (selectRoomId) {
                roomId = selectRoomId.value;
            }
            
            const checkInVal = checkIn.value;
            const checkOutVal = checkOut.value;
            
            // Validation
            if (!roomId) {
                showError('Please select a room');
                return;
            }
            
            if (!checkInVal || !checkOutVal) {
                showError('Please select both check-in and check-out dates');
                return;
            }
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const checkInDate = new Date(checkInVal);
            
            if (checkInDate < today) {
                showError('Check-in date cannot be in the past');
                return;
            }
            
            const checkOutDate = new Date(checkOutVal);
            if (checkOutDate <= checkInDate) {
                showError('Check-out date must be after check-in date');
                return;
            }
            
            // Show loading
            loadingDiv.style.display = 'block';
            resultDiv.style.display = 'none';
            
            // Send AJAX request to the SAME PAGE
            const formData = new FormData();
            formData.append('check_availability', '1');
            formData.append('room_id', roomId);
            formData.append('check_in', checkInVal);
            formData.append('check_out', checkOutVal);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                loadingDiv.style.display = 'none';
                
                if (data.startsWith('SUCCESS|')) {
                    // Format: SUCCESS|message|nights|total
                    const parts = data.split('|');
                    const message = parts[1];
                    const nights = parts[2];
                    const total = parts[3];
                    
                    resultDiv.innerHTML = `<strong>✓ Available!</strong><br>${message}`;
                    resultDiv.className = 'success';
                    resultDiv.style.display = 'block';
                    
                    nightsText.textContent = `Number of nights: ${nights}`;
                    totalText.textContent = `Total Amount: ₹${total}`;
                    priceDetails.style.display = 'block';
                    
                    specialSection.style.display = 'block';
                    bookBtn.style.display = 'block';
                    checkBtn.style.display = 'none';
                    
                    // Update steps
                    step1.classList.remove('active');
                    step1.classList.add('completed');
                    step2.classList.add('active');
                    
                    // Scroll to result
                    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    resultDiv.innerHTML = `<strong>✗ Not Available</strong><br>${data}`;
                    resultDiv.className = 'error';
                    resultDiv.style.display = 'block';
                    
                    // Reset steps
                    step1.classList.add('active');
                    step2.classList.remove('active', 'completed');
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                console.error('Fetch error:', error);
                resultDiv.innerHTML = `<strong>Error</strong><br>Unable to check availability. Please try again.`;
                resultDiv.className = 'error';
                resultDiv.style.display = 'block';
            });
        });
        
        // Handle form submission for booking
        bookingForm.addEventListener('submit', function(e) {
            // If Book Now button is not visible, prevent form submission
            if (bookBtn.style.display === 'none') {
                e.preventDefault();
                checkBtn.click();
            }
            // If Book Now is visible, let the form submit normally
        });
        
        // Auto-check if dates are pre-filled from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('check_in') && urlParams.has('check_out')) {
            // Wait a bit for page to load, then check availability
            setTimeout(() => {
                if (checkIn.value && checkOut.value) {
                    checkBtn.click();
                }
            }, 1000);
        }
    });
    </script>
</body>
</html>