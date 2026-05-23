<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$type = $_GET['type'] ?? 'revenue';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

switch($type) {
    case 'revenue':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="revenue_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Room Type', 'Bookings', 'Revenue']);
        
        $query = "SELECT DATE(b.booking_date) as date, r.room_type, 
                 COUNT(*) as bookings, SUM(b.total_amount) as revenue
                 FROM bookings b
                 JOIN rooms r ON b.room_id = r.id
                 WHERE b.status IN ('Confirmed', 'Completed')
                 AND b.booking_date BETWEEN '$start_date' AND '$end_date'
                 GROUP BY DATE(b.booking_date), r.room_type
                 ORDER BY date DESC";
        break;
        
    case 'bookings':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Booking ID', 'Customer', 'Room', 'Check-in', 'Check-out', 'Amount', 'Status']);
        
        $query = "SELECT b.id, u.full_name, r.room_number, 
                 b.check_in, b.check_out, b.total_amount, b.status
                 FROM bookings b
                 JOIN users u ON b.user_id = u.id
                 JOIN rooms r ON b.room_id = r.id
                 WHERE b.booking_date BETWEEN '$start_date' AND '$end_date'
                 ORDER BY b.booking_date DESC";
        break;
        
    case 'customers':
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="customers_report_' . date('Y-m-d') . '.pdf"');
        // For PDF, you would need to install a PDF library like TCPDF or Dompdf
        echo "PDF export requires PDF library installation";
        exit;
}

$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}
fclose($output);
exit;
?>