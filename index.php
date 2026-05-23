<?php
session_start();
require_once 'config/database.php';

// Get rooms with pagination for showing always 10 rooms
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rooms_per_page = 12;
$offset = ($page - 1) * $rooms_per_page;

// Get total count of available rooms
$count_query = "SELECT COUNT(*) as total FROM rooms WHERE is_available = 1";
$count_result = mysqli_query($conn, $count_query);
if ($count_result) {
    $total_rooms = mysqli_fetch_assoc($count_result)['total'];
} else {
    $total_rooms = 0;
}
$total_pages = ceil($total_rooms / $rooms_per_page);

// Get rooms for current page
$query = "SELECT * FROM rooms WHERE is_available = 1 LIMIT $rooms_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
if (!$result) {
    $result = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for the updated booking system */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
        }

        body {
            background-color: #ffffff;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;

        }

        /* Booking Container */
        .booking-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 1rem;
            margin: 1rem auto;
            max-width: 1400px;
        }

        .booking-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .booking-header h2 {
            color: var(--dark-color);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .booking-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Room Grid - Responsive */
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Room Card */
        .room-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .room-image-container {
            height: 220px;
            overflow: hidden;
        }

        .room-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .room-card:hover .room-image {
            transform: scale(1.05);
        }

        .room-details {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .room-details h3 {
            color: var(--dark-color);
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
        }

        .room-features {
            margin-bottom: 1.2rem;
            flex-grow: 1;
        }

        .room-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.6rem;
            color: #64748b;
        }

        .room-feature i {
            margin-right: 10px;
            color: var(--accent-color);
            width: 20px;
        }

        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.2rem;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-book {
            width: 100%;
            margin-top: auto;
        }

        /* Availability Badge */
        .availability-badge {
            display: inline-block;
            background-color: var(--success-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2.5rem;
            gap: 0.8rem;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .pagination a {
            background-color: #f1f5f9;
            color: var(--dark-color);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .pagination .current {
            background-color: var(--primary-color);
            color: white;
        }

        /* Room counter */
        .room-counter {
            text-align: center;
            color: #64748b;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        /* No rooms message */
        .no-rooms {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background-color: #f8fafc;
            border-radius: 12px;
            color: #64748b;
        }

        .no-rooms i {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: #cbd5e1;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .room-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .room-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 1.5rem;
            }

            .booking-container {
                padding: 1.5rem;
            }

            .booking-header h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .room-grid {
                grid-template-columns: 1fr;
            }

            .pagination {
                flex-wrap: wrap;
            }
        }

        /* Hotel Banner Styles */

        /* Hotel Banner - Responsive Hero Section */
        .hero#hotel-banner {
            background: url('assets/images/hotel-hero.jpg') no-repeat center center;
            color: white;
            text-align: center;
            background-size: cover;
            padding: 6rem 2rem;
            position: relative;
            min-height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0;
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Dark overlay for better text readability */
        .hero#hotel-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        /* Container for content */
        .hero .container {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Banner title */
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
            line-height: 1.2;
        }

        /* Banner subtitle */
        .hero p {
            font-size: clamp(1rem, 2vw, 1.3rem);
            margin-bottom: 2rem;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
        }

        /* Banner buttons */
        .hero .btn {
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            padding: 14px 32px;
            border-radius: var(--radius-lg);
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
            border: none;
            cursor: pointer;
            margin: 0 10px 10px;
            min-width: 140px;
            text-align: center;
        }

        .hero .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(58, 134, 255, 0.4);
        }

        /* Login button specific styling */
        .hero .btn[style*="background: transparent"] {
            background: transparent !important;
            border: 2px solid white;
            position: relative;
            overflow: hidden;
        }

        .hero .btn[style*="background: transparent"]:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            transform: translateY(-3px);
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 768px) {
            .hero#hotel-banner {
                padding: 5rem 1.5rem;
                min-height: 50vh;
                background-size: cover;
                /* Ensures image covers entire area on mobile */
                background-attachment: scroll;
                /* Prevents parallax effect on mobile */
            }

            .hero .container {
                padding: 0 15px;
            }

            .hero h1 {
                font-size: 2.2rem;
                margin-bottom: 0.8rem;
            }

            .hero p {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
                padding: 0 10px;
            }

            .hero .btn {
                padding: 12px 24px;
                font-size: 1rem;
                margin: 5px;
                min-width: 120px;
                display: inline-block;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .hero#hotel-banner {
                padding: 4rem 1rem;
                min-height: 45vh;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .hero p {
                font-size: 1rem;
                margin-bottom: 1.2rem;
            }

            .hero .btn {
                padding: 10px 20px;
                font-size: 0.95rem;
                margin: 3px;
                min-width: 110px;
            }

            /* Stack buttons vertically on very small screens */
            @media (max-width: 380px) {
                .hero .btn {
                    display: block;
                    margin: 8px auto;
                    width: 80%;
                    max-width: 200px;
                }
            }
        }

        /* Very Small Mobile */
        @media (max-width: 360px) {
            .hero#hotel-banner {
                padding: 1rem 1rem;
                max-height: 5vh;
            }

            .hero .headline {
                margin-top: -30px;
                font-size: 20px;
            }
            .hero .note{
                font-size: 10px;
                margin-top: -20px;
                
            }
            .hero .hero-buttons a{
                font-size: 7px;
            }
        }

        /* Fix for iOS Safari */
        @supports (-webkit-touch-callout: none) {
            .hero#hotel-banner {
                background-attachment: scroll;
            }
        }

        /* Landscape mode adjustments */
        @media (max-height: 500px) and (orientation: landscape) {
            .hero#hotel-banner {
                min-height: 70vh;
                padding: 4rem 2rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body style="background-color: white;">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero" id="hotel-banner" role="banner" aria-label="Hotel Hero Banner">
        <div class="container">
            <P class="headline">Welcome to Smart Hotel</P>
            <p class="note">Experience luxury and comfort at affordable prices. Book your stay with us today!</p>
            <div class="hero-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="auth/register.php" class="btn btn-primary">Book Now</a>
                    <a href="auth/login.php" class="btn btn-secondary">Login</a>
                <?php else: ?>
                    <a href="user/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Booking Container -->
    <section class="booking-container">
        <div class="booking-header">
            <h2>Available Rooms</h2>
            <p>Choose from our selection of luxurious rooms for your perfect stay</p>
        </div>

        <div class="room-counter">
            Showing <?php echo min($rooms_per_page, $total_rooms - $offset); ?> of <?php echo $total_rooms; ?> available rooms
        </div>

        <?php
        // Temporary debug - show what rooms we're getting
        echo "<!-- DEBUG: Total rooms query returned: " . mysqli_num_rows($result) . " rooms -->";
        ?>

        <div class="room-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($room = mysqli_fetch_assoc($result)):
                    // Debug each room
                    echo "<!-- DEBUG Room: #" . $room['room_number'] . " | Image: " . $room['image_path'] . " -->";

                    // Handle image path properly
                    $image_path = $room['image_path'];

                    // If image_path is empty or null, use default
                    if (empty($image_path)) {
                        $image_path = 'assets/uploads/rooms/default.jpg';
                    } else {
                        // Check if it already has the full path
                        if (strpos($image_path, 'assets/uploads/rooms/') === false) {
                            // Check if it's just a filename
                            if (strpos($image_path, '/') === false && strpos($image_path, '\\') === false) {
                                // It's just a filename, add the full path
                                $image_path = 'assets/uploads/rooms/' . $image_path;
                            } else {
                                // It has some other path structure, try to fix it
                                if (strpos($image_path, 'uploads/rooms/') !== false) {
                                    $image_path = 'assets/' . $image_path;
                                }
                            }
                        }
                    }
            ?>
                    <div class="room-card">
                        <div class="room-image-container">
                            <img src="<?php echo htmlspecialchars($image_path); ?>"
                                alt="<?php echo htmlspecialchars($room['room_type']); ?> Room"
                                class="room-image"
                                onerror="this.onerror=null; this.src='assets/uploads/rooms/default.jpg';">
                        </div>
                        <div class="room-details">
                            <span class="availability-badge">Available</span>
                            <h3><?php echo htmlspecialchars($room['room_type']); ?> Room</h3>

                            <div class="room-features">
                                <div class="room-feature">
                                    <i class="fas fa-snowflake"></i>
                                    <span><?php echo htmlspecialchars($room['ac_type']); ?></span>
                                </div>
                                <div class="room-feature">
                                    <i class="fas fa-user-friends"></i>
                                    <span>Capacity: <?php echo $room['capacity']; ?> persons</span>
                                </div>
                                <div class="room-feature">
                                    <i class="fas fa-bed"></i>
                                    <span><?php echo $room['bed_type'] ?? 'King Bed'; ?></span>
                                </div>
                                <div class="room-feature">
                                    <i class="fas fa-wifi"></i>
                                    <span>Free WiFi</span>
                                </div>
                            </div>

                            <p class="price">₹<?php echo number_format($room['price_per_night'], 0); ?>/night</p>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="user/booking.php?room_id=<?php echo $room['id']; ?>"
                                    class="btn btn-book">Book Now</a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-book">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                endwhile;
            } else {
                echo '<div class="no-rooms">';
                echo '<i class="fas fa-door-closed"></i>';
                echo '<h3>No Rooms Available</h3>';
                echo '<p>All rooms are currently booked. Please check back later.</p>';

                // Debug: Show what's in the database
                $all_rooms_query = "SELECT id, room_number, is_available, image_path FROM rooms LIMIT 5";
                $all_rooms_result = mysqli_query($conn, $all_rooms_query);
                if (mysqli_num_rows($all_rooms_result) > 0) {
                    echo '<div style="background: #f8f9fa; padding: 10px; margin-top: 20px; border-radius: 5px;">';
                    echo '<small><strong>Debug: Database has rooms but they might not be marked as available:</strong><br>';
                    while ($db_room = mysqli_fetch_assoc($all_rooms_result)) {
                        echo "Room #" . $db_room['room_number'] . " - Available: " . $db_room['is_available'] . "<br>";
                    }
                    echo '</small></div>';
                }

                echo '</div>';
            }
            ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                // Show page numbers
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                for ($i = $start_page; $i <= $end_page; $i++):
                    if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif;
                endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <style>
        /* Gallery Section - Card Column Layout */
        .gallery-section {
            padding: 4rem 0;
            background: #ffffff;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 0.8rem;
            font-weight: 700;
        }

        .section-header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Column Layout */
        .gallery-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .gallery-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Gallery Card */
        .gallery-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            position: relative;
            height: 280px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-card:hover .card-overlay {
            opacity: 1;
        }

        .view-btn {
            background: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #2563eb;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .view-btn:hover {
            transform: scale(1.1);
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-content h4 {
            color: #1e293b;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .card-date {
            color: #64748b;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-date i {
            color: #94a3b8;
        }

        .share-btn {
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .share-btn:hover {
            background: #2563eb;
            color: white;
        }

        /* View All Button */
        .view-all-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: #2563eb;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid #2563eb;
        }

        .view-all-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        .view-all-btn i {
            transition: transform 0.3s ease;
        }

        .view-all-btn:hover i {
            transform: translateX(5px);
        }

        /* Empty State */
        .no-gallery {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state {
            background: #f8fafc;
            padding: 4rem 2rem;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
        }

        .empty-state i {
            font-size: 4rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #94a3b8;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 600px;
            width: 80%;
            max-height: 80vh;
            overflow: hidden;
            position: relative;
            margin-top: 50px;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            color: #1e293b;
            z-index: 10;
        }

        .modal-body {
            height: 100%;
        }

        .modal-body img {
            padding-top: 1px;
            width: 100%;
            height: 60vh;
            object-fit: contain;
            background: #f8fafc;
        }

        .modal-caption {
            padding: 1.5rem;
            background: white;
        }

        .modal-caption h3 {
            color: #1e293b;
            margin: 0;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .gallery-columns {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .card-image {
                height: 250px;
            }
        }

        @media (max-width: 768px) {
            .gallery-section {
                padding: 3rem 0;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .card-image {
                height: 220px;
            }

            .card-content {
                padding: 1.25rem;
            }
        }

        @media (max-width: 640px) {
            .gallery-columns {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .gallery-column {
                gap: 1.5rem;
            }

            .card-image {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .card-image {
                height: 200px;
            }

            .card-content h4 {
                font-size: 1.1rem;
            }

            .view-all-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Text Center Utility */
        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 2rem;
        }
    </style>
    <section class="gallery-section">
        <div class="container">
            <div class="section-header">
                <h2>Hotel Gallery</h2>
                <p>Experience our luxury through these beautiful moments</p>
            </div>

            <div class="gallery-columns">
                <?php
                $gallery_query = "SELECT * FROM gallery LIMIT 9";
                $gallery_result = mysqli_query($conn, $gallery_query);

                if ($gallery_result && mysqli_num_rows($gallery_result) > 0):
                    $images = array();
                    while ($image = mysqli_fetch_assoc($gallery_result)) {
                        $images[] = $image;
                    }

                    // Create 3 columns for desktop, 2 for tablet, 1 for mobile
                    $columns = 3;
                    $images_per_column = ceil(count($images) / $columns);

                    for ($col = 0; $col < $columns; $col++):
                        $start_index = $col * $images_per_column;
                ?>
                        <div class="gallery-column">
                            <?php
                            for ($i = $start_index; $i < $start_index + $images_per_column; $i++):
                                if (isset($images[$i])):
                                    $image = $images[$i];
                            ?>
                                    <div class="gallery-card">
                                        <div class="card-image">
                                            <img src="assets/uploads/gallery/<?php echo $image['image_path']; ?>"
                                                alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                                loading="lazy">
                                            <div class="card-overlay">
                                                <button class="view-btn" onclick="openModal('<?php echo $image['image_path']; ?>', '<?php echo htmlspecialchars($image['caption']); ?>')">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <h4><?php echo htmlspecialchars($image['caption']); ?></h4>
                                            <div class="card-actions">
                                                <span class="card-date">
                                                    <i class="far fa-calendar"></i>
                                                    <?php echo date('M d, Y', strtotime($image['created_at'] ?? 'now')); ?>
                                                </span>
                                                <button class="share-btn" onclick="shareImage('<?php echo $image['image_path']; ?>', '<?php echo htmlspecialchars($image['caption']); ?>')">
                                                    <i class="fas fa-share-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endfor;
                            ?>
                        </div>
                    <?php
                    endfor;
                else:
                    ?>
                    <div class="no-gallery">
                        <div class="empty-state">
                            <i class="fas fa-camera"></i>
                            <h3>No Gallery Images Yet</h3>
                            <p>Gallery images will appear here soon</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($gallery_result && mysqli_num_rows($gallery_result) > 0): ?>
                <div class="text-center mt-4">

                    <a href="<?php echo $base_path; ?>auth/login.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                        View All Gallery Photos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal for Image Preview -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-body">
                <img id="modalImage" src="" alt="">
                <div class="modal-caption">
                    <h3 id="modalCaption"></h3>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(imagePath, caption) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');

            modalImg.src = 'assets/uploads/gallery/' + imagePath;
            modalCaption.textContent = caption;
            modal.style.display = 'flex';
        }

        function shareImage(imagePath, caption) {
            if (navigator.share) {
                navigator.share({
                    title: 'Hotel Gallery',
                    text: caption,
                    url: 'assets/uploads/gallery/' + imagePath
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const shareUrl = 'assets/uploads/gallery/' + imagePath;
                navigator.clipboard.writeText(caption + '\n' + shareUrl).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }

        // Close modal when clicking X or outside
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('imageModal').style.display = 'none';
        });

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('imageModal').style.display = 'none';
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>