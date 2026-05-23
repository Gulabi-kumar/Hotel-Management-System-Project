<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$success = '';
$error = '';

// Handle room actions (delete)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $room_id = intval($_GET['id']);

    // Check if room has any bookings
    $check_query = "SELECT COUNT(*) as count FROM bookings WHERE room_id = $room_id";
    $result = mysqli_query($conn, $check_query);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        $error = "Cannot delete room. There are existing bookings for this room.";
    } else {
        $delete_query = "DELETE FROM rooms WHERE id = $room_id";
        if (mysqli_query($conn, $delete_query)) {
            $success = "Room deleted successfully!";
        } else {
            $error = "Error deleting room: " . mysqli_error($conn);
        }
    }
}

// Handle room status toggle
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $room_id = intval($_GET['id']);

    $query = "UPDATE rooms SET is_available = NOT is_available WHERE id = $room_id";
    if (mysqli_query($conn, $query)) {
        $success = "Room status updated successfully!";
    } else {
        $error = "Error updating room status: " . mysqli_error($conn);
    }
}

// Fetch all rooms
$query = "SELECT * FROM rooms ORDER BY room_number ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: var(--dark);
        background: #f8fafc;
        font-size: 14px;
    }

    /* Container */
    .container {
        width: 100%;
        max-width: 100%;
        padding: 20px 15px;
        margin: 0 auto;
    }

    /* Header Section */
    .container>div:first-child {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .container h2 {
        font-size: clamp(1.25rem, 4vw, 1.8rem);
        color: var(--dark);
        margin: 0;
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: clamp(0.85rem, 2vw, 1rem);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #0da271;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-sm {
        padding: 0.5rem 0.875rem;
        font-size: clamp(0.8rem, 1.5vw, 0.9rem);
        min-width: 36px;
        height: 36px;
    }

    /* Alert Messages */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        animation: slideIn 0.3s ease;
        font-size: clamp(0.85rem, 2vw, 1rem);
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
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Stats Grid - Mobile First */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-card h3 {
        font-size: clamp(0.8rem, 2vw, 0.95rem);
        color: var(--gray);
        margin-bottom: 0.5rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .number {
        font-size: clamp(1.5rem, 5vw, 2rem);
        font-weight: 700;
        color: var(--dark);
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .card h3 {
        font-size: clamp(1rem, 3vw, 1.3rem);
        margin-bottom: 1.25rem;
        color: var(--dark);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }

    /* Rooms Table - Scrollable Feature */
    .card:has(.table) {
        overflow: hidden;
        padding: 0;
    }

    .card h3 {
        padding: 1.5rem 1.5rem 0.75rem;
        margin-bottom: 0;
    }

    .table-container {
        overflow-x: auto;
        max-height: 500px;
        overflow-y: auto;
        margin-top: 0.75rem;
    }

    .table {
        width: 100%;
        min-width: 1000px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f1f5f9;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: clamp(0.75rem, 2vw, 0.9rem);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: clamp(0.85rem, 2vw, 0.95rem);
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    /* Badge Styles */
    .badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: clamp(0.75rem, 1.5vw, 0.85rem);
        font-weight: 500;
        text-align: center;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Amenities Tags */
    .table td span[style*="background: #f1f1f1"] {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: #f1f5f9 !important;
        border-radius: 4px;
        font-size: clamp(0.7rem, 1.5vw, 0.8rem);
        margin: 0.125rem;
        color: var(--gray);
    }

    /* Room Types Overview Grid */
    .card:last-child>div {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .card:last-child>div>div {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease;
    }

    .card:last-child>div>div:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card:last-child h4 {
        font-size: clamp(0.9rem, 2.5vw, 1.1rem);
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .card:last-child p {
        font-size: clamp(0.8rem, 2vw, 0.9rem);
    }

    /* ============ RESPONSIVE BREAKPOINTS ============ */

    /* Small Tablets (481px - 600px) */
    @media (min-width: 481px) {
        body {
            font-size: 15px;
        }

        .container {
            padding: 25px 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .card:last-child>div {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-container {
            max-height: 450px;
        }

        .container h2 {
            font-size: clamp(1.5rem, 4vw, 1.9rem);
        }
    }

    /* Tablets (601px - 768px) */
    @media (min-width: 601px) {
        body {
            font-size: 15.5px;
        }

        .container {
            max-width: 95%;
        }

        .container>div:first-child {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card {
            padding: 1.75rem;
        }

        .card {
            padding: 2rem;
        }

        .card h3 {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
        }

        .btn {
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
    }

    /* Laptops (769px - 1024px) */
    @media (min-width: 769px) {
        body {
            font-size: 16px;
        }

        .container {
            padding: 30px;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .table-container {
            max-height: 500px;
        }

        .table {
            min-width: 1100px;
        }

        .card:last-child>div {
            grid-template-columns: repeat(3, 1fr);
        }

        .container h2 {
            font-size: clamp(1.6rem, 4vw, 2rem);
        }
    }

    /* Desktop (1025px - 1366px) */
    @media (min-width: 1025px) {
        body {
            font-size: 16.5px;
        }

        .container {
            max-width: 1200px;
            padding: 40px;
        }

        .stats-grid {
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            padding: 2rem;
        }

        .stat-card h3 {
            font-size: clamp(0.9rem, 1.5vw, 1rem);
        }

        .stat-card .number {
            font-size: clamp(2rem, 5vw, 2.5rem);
        }

        .card {
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .card h3 {
            font-size: clamp(1.3rem, 3vw, 1.6rem);
            margin-bottom: 1.5rem;
        }

        .card:last-child>div {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Large Desktop (1367px and above) */
    @media (min-width: 1367px) {
        body {
            font-size: 17px;
        }

        .container {
            max-width: 1400px;
        }

        .table-container {
            max-height: 550px;
        }

        .table {
            min-width: 1200px;
        }

        .table th {
            font-size: 0.95rem;
        }

        .table td {
            font-size: 1rem;
        }
    }

    /* Mobile (≤ 480px) */
    @media (max-width: 480px) {
        body {
            font-size: 13px;
        }

        .container {
            padding: 15px 10px;
        }

        #card-room {
            padding: 1.5rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .card {
            padding: 1.25rem;
        }

        .btn {
            padding: 0.75rem 1rem;
        }

        .btn-sm {
            padding: 0.375rem 0.5rem;
            min-width: 32px;
            height: 32px;
        }

        .table-container {
            max-height: 400px;
            margin: 0 -1.25rem;
            width: calc(100% + 2.5rem);
        }

        .table th,
        .table td {
            padding: 0.75rem;
        }

        .badge {
            padding: 0.25rem 0.5rem;
        }
    }

    /* Very Small Phones (≤ 320px) */
    @media (max-width: 320px) {
        body {
            font-size: 12px;
        }

        .container {
            padding: 10px 5px;
        }

        .stat-card {
            padding: 1rem;
        }

        .card {
            padding: 1rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
        }

        .btn {
            padding: 0.625rem 0.75rem;
        }
    }

    /* Touch Device Optimization */
    @media (hover: none) and (pointer: coarse) {
        .btn:hover {
            transform: none;
        }

        .btn {
            min-height: 44px;
        }

        .btn-sm {
            min-height: 36px;
        }

        /* Larger scrollbar for touch */
        .table-container::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
    }

    /* High DPI Screens */
    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }
</style>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="padding: 2rem 0;" id="room-admin">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Rooms</h2>
            <a href="add-room.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Room
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Room Statistics -->
        <div class="stats-grid" style="margin-bottom: 2rem;">
            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as booked,
                AVG(price_per_night) as avg_price
            FROM rooms";
            $stats_result = mysqli_query($conn, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            ?>
            <div class="stat-card">
                <h3>Total Rooms</h3>
                <div class="number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Available</h3>
                <div class="number" style="color: #27ae60;"><?php echo $stats['available']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Booked</h3>
                <div class="number" style="color: #e74c3c;"><?php echo $stats['booked']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg. Price</h3>
                <div class="number">₹<?php echo number_format($stats['avg_price'], 2); ?></div>
            </div>
        </div>

        <!-- Rooms Table -->
        <div class="card" id="card-room">
            <h3>All Rooms</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>AC Type</th>
                        <th>Capacity</th>
                        <th>Amenities</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($room = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $room['room_number']; ?></td>
                                <td><?php echo $room['room_type']; ?></td>
                                <td><?php echo $room['ac_type']; ?></td>
                                <td><?php echo $room['capacity']; ?> Persons</td>
                                <td>
                                    <?php
                                    $amenities = explode(',', $room['amenities']);
                                    foreach ($amenities as $amenity):
                                        echo '<span style="background: #f1f1f1; padding: 3px 8px; border-radius: 20px; margin: 2px; display: inline-block; font-size: 0.9rem;">' . trim($amenity) . '</span>';
                                    endforeach;
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($room['price_per_night'], 2); ?></td>
                                <td>
                                    <?php if ($room['is_available'] == 1): ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Booked</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="edit-room.php?id=<?php echo $room['id']; ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="rooms.php?action=toggle_status&id=<?php echo $room['id']; ?>"
                                            class="btn btn-sm <?php echo $room['is_available'] == 1 ? 'btn-warning' : 'btn-success'; ?>"
                                            onclick="return confirm('Change room availability status?')">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <a href="rooms.php?action=delete&id=<?php echo $room['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this room? This action cannot be undone!')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem;">
                                No rooms found. <a href="add-room.php">Add your first room</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Room Types Overview -->
        <div class="card" style="margin-top: 2rem;">
            <h3>Room Types Overview</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <?php
                $types_query = "SELECT room_type, COUNT(*) as count, 
                               AVG(price_per_night) as avg_price,
                               SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available
                               FROM rooms 
                               GROUP BY room_type";
                $types_result = mysqli_query($conn, $types_query);

                while ($type = mysqli_fetch_assoc($types_result)):
                ?>
                    <div style="border: 1px solid #ddd; padding: 1rem; border-radius: 5px;">
                        <h4><?php echo $type['room_type']; ?></h4>
                        <p>Total: <?php echo $type['count']; ?> rooms</p>
                        <p>Available: <?php echo $type['available']; ?></p>
                        <p>Avg. Price: ₹<?php echo number_format($type['avg_price'], 2); ?>/night</p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>


<!-- JavaScript for table scroll feature -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wrap tables in scroll containers
        const tables = document.querySelectorAll('.card .table');

        tables.forEach(table => {
            const card = table.closest('.card');
            if (card && !card.querySelector('.table-container')) {
                const container = document.createElement('div');
                container.className = 'table-container';
                table.parentNode.insertBefore(container, table);
                container.appendChild(table);
            }
        });

        // Touch scrolling support
        const tableContainers = document.querySelectorAll('.table-container');

        tableContainers.forEach(container => {
            let isScrolling = false;
            let startX, scrollLeft;
            let startY, scrollTop;

            container.addEventListener('touchstart', (e) => {
                isScrolling = true;
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                startY = e.touches[0].pageY - container.offsetTop;
                scrollTop = container.scrollTop;
            });

            container.addEventListener('touchmove', (e) => {
                if (!isScrolling) return;
                e.preventDefault();

                const x = e.touches[0].pageX - container.offsetLeft;
                const y = e.touches[0].pageY - container.offsetTop;

                const walkX = (x - startX) * 2;
                const walkY = (y - startY) * 2;

                // Scroll both horizontally and vertically
                container.scrollLeft = scrollLeft - walkX;
                container.scrollTop = scrollTop - walkY;
            });

            container.addEventListener('touchend', () => {
                isScrolling = false;
            });
        });
    });
</script>