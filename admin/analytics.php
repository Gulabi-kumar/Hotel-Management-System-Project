<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

// Get date range filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Analytics Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container" style="padding: 2rem 0;">
        <h2><i class="fas fa-chart-line"></i> Hotel Analytics Dashboard</h2>
        
        <!-- Date Filter -->
        <div class="card" style="margin-bottom: 2rem;">
            <h3>Filter Data</h3>
            <form method="GET" action="">
                <div style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div>
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    </div>
                    <div>
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="analytics.php" class="btn">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Key Performance Indicators -->
        <div class="stats-grid">
            <?php
            // 1. Total Revenue
            $revenue_query = "SELECT SUM(total_amount) as revenue FROM bookings 
                             WHERE status IN ('Confirmed', 'Completed') 
                             AND booking_date BETWEEN '$start_date' AND '$end_date'";
            $revenue_result = mysqli_query($conn, $revenue_query);
            $revenue = mysqli_fetch_assoc($revenue_result)['revenue'] ?? 0;
            
            // 2. Total Bookings
            $bookings_query = "SELECT COUNT(*) as bookings FROM bookings 
                              WHERE booking_date BETWEEN '$start_date' AND '$end_date'";
            $bookings_result = mysqli_query($conn, $bookings_query);
            $bookings = mysqli_fetch_assoc($bookings_result)['bookings'] ?? 0;
            
            // 3. Occupancy Rate
            $total_rooms_query = "SELECT COUNT(*) as total_rooms FROM rooms";
            $total_rooms_result = mysqli_query($conn, $total_rooms_query);
            $total_rooms = mysqli_fetch_assoc($total_rooms_result)['total_rooms'] ?? 1;
            
            $occupied_query = "SELECT COUNT(DISTINCT room_id) as occupied FROM bookings 
                              WHERE status IN ('Confirmed', 'Completed') 
                              AND check_in <= '$end_date' 
                              AND check_out >= '$start_date'";
            $occupied_result = mysqli_query($conn, $occupied_query);
            $occupied = mysqli_fetch_assoc($occupied_result)['occupied'] ?? 0;
            $occupancy_rate = round(($occupied / $total_rooms) * 100, 2);
            
            // 4. Average Booking Value
            $avg_value_query = "SELECT AVG(total_amount) as avg_value FROM bookings 
                               WHERE status IN ('Confirmed', 'Completed') 
                               AND booking_date BETWEEN '$start_date' AND '$end_date'";
            $avg_value_result = mysqli_query($conn, $avg_value_query);
            $avg_value = mysqli_fetch_assoc($avg_value_result)['avg_value'] ?? 0;
            
            // 5. Cancellation Rate
            $cancelled_query = "SELECT COUNT(*) as cancelled FROM bookings 
                               WHERE status = 'Cancelled' 
                               AND booking_date BETWEEN '$start_date' AND '$end_date'";
            $cancelled_result = mysqli_query($conn, $cancelled_query);
            $cancelled = mysqli_fetch_assoc($cancelled_result)['cancelled'] ?? 0;
            $cancellation_rate = $bookings > 0 ? round(($cancelled / $bookings) * 100, 2) : 0;
            
            // 6. Revenue per Available Room (RevPAR)
            $revpar = $total_rooms > 0 ? round($revenue / $total_rooms, 2) : 0;
            ?>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3><i class="fas fa-money-bill-wave"></i> Total Revenue</h3>
                <div class="number">₹<?php echo number_format($revenue, 2); ?></div>
                <small>From <?php echo $bookings; ?> bookings</small>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <h3><i class="fas fa-bed"></i> Occupancy Rate</h3>
                <div class="number"><?php echo $occupancy_rate; ?>%</div>
                <small><?php echo $occupied; ?>/<?php echo $total_rooms; ?> rooms occupied</small>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <h3><i class="fas fa-chart-bar"></i> Avg Booking Value</h3>
                <div class="number">₹<?php echo number_format($avg_value, 2); ?></div>
                <small>Per booking</small>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: black;">
                <h3><i class="fas fa-chart-pie"></i> RevPAR</h3>
                <div class="number">₹<?php echo number_format($revpar, 2); ?></div>
                <small>Revenue per available room</small>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: black;">
                <h3><i class="fas fa-times-circle"></i> Cancellation Rate</h3>
                <div class="number"><?php echo $cancellation_rate; ?>%</div>
                <small><?php echo $cancelled; ?> cancellations</small>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white;">
                <h3><i class="fas fa-calendar-check"></i> Confirmed Bookings</h3>
                <div class="number"><?php echo $bookings; ?></div>
                <small>Total bookings in period</small>
            </div>
        </div>
        
        <!-- Detailed Analytics Tables -->
        <div class="card" style="margin-top: 2rem;">
            <h3><i class="fas fa-trophy"></i> Top Performing Rooms</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                        <th>Occupancy</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $top_rooms_query = "SELECT 
                        r.room_number,
                        r.room_type,
                        COUNT(b.id) as bookings_count,
                        SUM(b.total_amount) as revenue,
                        ROUND((COUNT(b.id) / DATEDIFF('$end_date', '$start_date')) * 100, 2) as occupancy_rate
                        FROM rooms r
                        LEFT JOIN bookings b ON r.id = b.room_id 
                        AND b.status IN ('Confirmed', 'Completed')
                        AND b.booking_date BETWEEN '$start_date' AND '$end_date'
                        GROUP BY r.id
                        ORDER BY revenue DESC
                        LIMIT 10";
                    
                    $top_rooms_result = mysqli_query($conn, $top_rooms_query);
                    while($room = mysqli_fetch_assoc($top_rooms_result)):
                        $performance = '';
                        $color = '';
                        if($room['occupancy_rate'] > 80) {
                            $performance = 'Excellent';
                            $color = '#27ae60';
                        } elseif($room['occupancy_rate'] > 50) {
                            $performance = 'Good';
                            $color = '#f39c12';
                        } else {
                            $performance = 'Needs Attention';
                            $color = '#e74c3c';
                        }
                    ?>
                    <tr>
                        <td><?php echo $room['room_number']; ?></td>
                        <td><?php echo $room['room_type']; ?></td>
                        <td><?php echo $room['bookings_count']; ?></td>
                        <td>₹<?php echo number_format($room['revenue'] ?? 0, 2); ?></td>
                        <td><?php echo $room['occupancy_rate']; ?>%</td>
                        <td>
                            <span style="padding: 5px 10px; border-radius: 20px; background: <?php echo $color; ?>; color: white;">
                                <?php echo $performance; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Monthly Comparison -->
        <div class="card" style="margin-top: 2rem;">
            <h3><i class="fas fa-calendar-alt"></i> Monthly Performance Comparison</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                        <th>Occupancy</th>
                        <th>Avg. Stay</th>
                        <th>Growth %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $monthly_query = "SELECT 
                        DATE_FORMAT(booking_date, '%Y-%m') as month,
                        COUNT(*) as bookings,
                        SUM(total_amount) as revenue,
                        AVG(DATEDIFF(check_out, check_in)) as avg_stay
                        FROM bookings 
                        WHERE status IN ('Confirmed', 'Completed')
                        AND booking_date >= DATE_SUB('$start_date', INTERVAL 6 MONTH)
                        GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
                        ORDER BY month DESC
                        LIMIT 6";
                    
                    $monthly_result = mysqli_query($conn, $monthly_query);
                    $prev_revenue = 0;
                    while($month = mysqli_fetch_assoc($monthly_result)):
                        $growth = $prev_revenue > 0 ? 
                            round((($month['revenue'] - $prev_revenue) / $prev_revenue) * 100, 2) : 0;
                        $prev_revenue = $month['revenue'];
                    ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></td>
                        <td><?php echo $month['bookings']; ?></td>
                        <td>₹<?php echo number_format($month['revenue'], 2); ?></td>
                        <td><?php echo round(($month['bookings'] / 30) * 100, 2); ?>%</td>
                        <td><?php echo round($month['avg_stay'], 1); ?> days</td>
                        <td style="color: <?php echo $growth >= 0 ? '#27ae60' : '#e74c3c'; ?>;">
                            <i class="fas fa-arrow-<?php echo $growth >= 0 ? 'up' : 'down'; ?>"></i>
                            <?php echo $growth; ?>%
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Customer Analytics -->
        <div class="card" style="margin-top: 2rem;">
            <h3><i class="fas fa-users"></i> Customer Analytics</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h4>Top Customers by Spending</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Bookings</th>
                                <th>Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $top_customers_query = "SELECT 
                                u.full_name,
                                COUNT(b.id) as bookings,
                                SUM(b.total_amount) as total_spent
                                FROM users u
                                JOIN bookings b ON u.id = b.user_id
                                WHERE b.status IN ('Confirmed', 'Completed')
                                AND b.booking_date BETWEEN '$start_date' AND '$end_date'
                                GROUP BY u.id
                                ORDER BY total_spent DESC
                                LIMIT 5";
                            
                            $top_customers_result = mysqli_query($conn, $top_customers_query);
                            while($customer = mysqli_fetch_assoc($top_customers_result)):
                            ?>
                            <tr>
                                <td><?php echo $customer['full_name']; ?></td>
                                <td><?php echo $customer['bookings']; ?></td>
                                <td>₹<?php echo number_format($customer['total_spent'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div>
                    <h4>Booking Sources Analysis</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking Type</th>
                                <th>Count</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Assuming you have a 'source' column in bookings table
                            // If not, you can add it or modify this query
                            $sources_query = "SELECT 
                                'Direct' as source,
                                COUNT(*) as count,
                                SUM(total_amount) as revenue
                                FROM bookings 
                                WHERE status IN ('Confirmed', 'Completed')
                                AND booking_date BETWEEN '$start_date' AND '$end_date'";
                            
                            $sources_result = mysqli_query($conn, $sources_query);
                            while($source = mysqli_fetch_assoc($sources_result)):
                            ?>
                            <tr>
                                <td><?php echo $source['source']; ?></td>
                                <td><?php echo $source['count']; ?></td>
                                <td>₹<?php echo number_format($source['revenue'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="card" style="margin-top: 2rem; text-align: center;">
            <h3><i class="fas fa-download"></i> Export Reports</h3>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem;">
                <a href="export-reports.php?type=revenue&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                   class="btn btn-success">
                   <i class="fas fa-file-excel"></i> Export Revenue Report
                </a>
                <a href="export-reports.php?type=bookings&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                   class="btn btn-primary">
                   <i class="fas fa-file-csv"></i> Export Bookings Report
                </a>
                <a href="export-reports.php?type=customers&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                   class="btn btn-warning">
                   <i class="fas fa-file-pdf"></i> Export Customer Report
                </a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>