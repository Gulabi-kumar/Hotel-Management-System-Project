-- Database: hotel_db
-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Create database
CREATE DATABASE IF NOT EXISTS `hotel_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hotel_db`;

-- --------------------------------------------------------
-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `users`
INSERT INTO `users` (`id`, `full_name`, `email`, `mobile`, `password`, `is_verified`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@hotel.com', '9876543210', '$2y$10$nzVoxidxczDAhdkRc4B/1OGBudKYKdnbDDPspqZi6zJYeDH8vWk6S', 1, 1, '2026-01-18 22:00:03', '2026-01-17 13:04:14', '2026-01-18 16:30:03'),
(2, 'Rajesh Sharma', 'rajesh@gmail.com', '9876543211', '$2y$10$7rLSvRVyTQORapkKOqT7e.5M8vFJ2d8F3J8f9QJ2j8K9L8M7N6B5V4C', 1, 1, '2026-01-18 10:00:00', '2026-01-17 13:04:14', '2026-01-18 10:00:00'),
(3, 'Priya Patel', 'priya@gmail.com', '9876543212', '$2y$10$7rLSvRVyTQORapkKOqT7e.5M8vFJ2d8F3J8f9QJ2j8K9L8M7N6B5V4C', 1, 1, '2026-01-18 11:00:00', '2026-01-17 13:04:14', '2026-01-18 11:00:00'),
(4, 'Amit Verma', 'amit@gmail.com', '9876543213', '$2y$10$7rLSvRVyTQORapkKOqT7e.5M8vFJ2d8F3J8f9QJ2j8K9L8M7N6B5V4C', 1, 1, '2026-01-18 12:00:00', '2026-01-17 13:04:14', '2026-01-18 12:00:00'),
(5, 'Gulabi Kumar', 'kumargulabi31@gmail.com', '9388393834', '$2y$10$nzVoxidxczDAhdkRc4B/1OGBudKYKdnbDDPspqZi6zJYeDH8vWk6S', 1, 1, '2026-01-18 19:42:22', '2026-01-17 13:36:34', '2026-01-18 14:12:22');


-- --------------------------------------------------------
-- Table structure for table `rooms`
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) NOT NULL,
  `room_type` enum('Single','Double','Deluxe','Suite','Family','Executive','Presidential') NOT NULL,
  `ac_type` enum('AC','Non-AC') NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `amenities` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `idx_rooms_available` (`is_available`),
  KEY `idx_rooms_price` (`price_per_night`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `rooms`
INSERT INTO `rooms` (`id`, `room_number`, `room_type`, `ac_type`, `price_per_night`, `capacity`, `amenities`, `description`, `image_path`, `is_available`, `created_at`, `updated_at`) VALUES
(1, '101', 'Single', 'AC', 2500.00, 1, 'WiFi,TV,AC,Heater,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access', 'Comfortable single room with city view', 'assets/uploads/rooms/single-room-ac.jpg', 1, '2026-01-17 13:04:14', '2026-01-18 14:05:09'),
(2, '102', 'Single', 'Non-AC', 1800.00, 1, 'WiFi,TV,Mini Fridge,Room Service,Breakfast,Parking', 'Economy single room with basic amenities', 'assets/uploads/rooms/Single-Non-AC.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:39:01'),
(3, '103', 'Single', 'AC', 2700.00, 1, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access', 'Deluxe single room with city view', 'assets/uploads/rooms/Deluxe-Single-Room.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:39:30'),
(4, '104', 'Double', 'AC', 4500.00, 2, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking', 'Spacious double room with AC and king-size bed', 'assets/uploads/rooms/double-bed-ac-room.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:39:54'),
(5, '105', 'Double', 'Non-AC', 3500.00, 2, 'WiFi,TV,Room Service,Breakfast,Parking', 'Standard double room with twin beds', 'assets/uploads/rooms/double-bed-non-ac-room.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:41:09'),
(6, '106', 'Executive', 'AC', 5000.00, 2, 'WiFi,TV,AC,Room Service,Breakfast,Parking,Swimming Pool Access', 'Premium double room with balcony', 'assets/uploads/rooms/Executive-Suite.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:42:01'),
(7, '107', 'Deluxe', 'AC', 6000.00, 3, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access', 'Luxurious deluxe room with sitting area', 'assets/uploads/rooms/Deluxe-AC-Room.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:42:21'),
(8, '108', 'Deluxe', 'AC', 5500.00, 3, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access,Jacuzzi', 'Deluxe room with jacuzzi', 'assets/uploads/rooms/Deluxe-Ac.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:43:45'),
(9, '109', 'Executive-Suite', 'AC', 8000.00, 4, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access,Kitchenette', 'Executive suite with living area and kitchenette', 'assets/uploads/rooms/Executive-Suite.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:44:23'),
(10, '110', 'Presidential-Suite', 'AC', 9500.00, 4, 'WiFi,TV,AC,Mini Fridge,Room Service,Breakfast,Parking,Swimming Pool Access,Panoramic View', 'Presidential suite with panoramic view', 'assets/uploads/rooms/Presidential-Suite.jpg', 1, '2026-01-17 13:04:14', '2026-01-17 19:44:47'),
(11, '111', 'Family', 'Non-AC', 3000.00, 2, 'WiFi,TV,Mini Fridge,Room Service', 'Beautiful room with modern amenities', 'assets/uploads/rooms/Family-Room.jpg', 1, '2026-01-18 07:43:52', '2026-01-18 07:43:52'),
(12, '112', 'Single', 'Non-AC', 1500.00, 2, 'WiFi,TV,Basic Amenities', 'Good room for budget travelers', 'assets/uploads/rooms/Single-Non-ac Room.jpg', 1, '2026-01-18 07:54:31', '2026-01-18 07:54:31');

-- --------------------------------------------------------
-- Table structure for table `bookings`
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `special_requests` text DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed') DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bookings_user_id` (`user_id`),
  KEY `idx_bookings_room_id` (`room_id`),
  KEY `idx_bookings_status` (`status`),
  KEY `idx_bookings_dates` (`check_in`,`check_out`),
  KEY `idx_bookings_payment_status` (`payment_status`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `bookings`
INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `check_in`, `check_out`, `total_amount`, `status`, `booking_date`, `special_requests`, `payment_status`, `payment_method`, `payment_id`) VALUES
(1, 2, 1, '2026-01-18', '2026-01-20', 5000.00, 'Completed', '2026-01-17 13:04:14', 'Early check-in requested', 'Paid', 'Credit Card', 'TXN001'),
(2, 3, 4, '2026-01-19', '2026-01-22', 12000.00, 'Confirmed', '2026-01-17 13:04:14', 'Need extra blanket', 'Paid', 'Debit Card', 'TXN002'),
(3, 4, 7, '2026-01-22', '2026-01-24', 12000.00, 'Confirmed', '2026-01-17 13:04:14', 'Anniversary celebration', 'Paid', 'UPI', 'TXN003'),
(4, 2, 3, '2026-01-12', '2026-01-15', 8100.00, 'Cancelled', '2026-01-17 13:04:14', 'Business trip', 'Paid', 'Credit Card', 'TXN004'),
(5, 3, 5, '2026-01-07', '2026-01-10', 9000.00, 'Completed', '2026-01-17 13:04:14', 'Family vacation', 'Paid', 'Net Banking', 'TXN005'),


-- --------------------------------------------------------
-- Table structure for table `cancellation_logs`
CREATE TABLE IF NOT EXISTS `cancellation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancellation_charge` decimal(10,2) DEFAULT 0.00,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `idx_cancellation_user` (`user_id`),
  KEY `idx_cancellation_date` (`cancelled_at`),
  CONSTRAINT `cancellation_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cancellation_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `cancellation_logs`
INSERT INTO `cancellation_logs` (`id`, `booking_id`, `user_id`, `cancellation_reason`, `cancellation_charge`, `refund_amount`, `cancelled_at`) VALUES
(1, 1, 2, 'Plans changed', 1250.00, 3750.00, '2026-01-17 14:54:52'),
(2, 4, 2, 'Business trip cancelled', 2025.00, 6075.00, '2026-01-17 20:17:15'),
(3, 8, 1, 'Found better hotel', 1250.00, 3750.00, '2026-01-17 19:05:28'),
(4, 9, 5, 'Emergency at home', 675.00, 2025.00, '2026-01-17 19:00:46'),
(5, 18, 7, 'Changed travel dates', 2250.00, 6750.00, '2026-01-18 19:00:00');

-- --------------------------------------------------------
-- Table structure for table `payments`
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('UPI','Credit Card','Debit Card','Net Banking','Cash') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('Success','Failed','Pending') DEFAULT 'Pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `payments`
INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date`) VALUES
(1, 1, 5000.00, 'Credit Card', 'TXN001', 'Success', '2026-01-17 13:05:00'),
(2, 2, 12000.00, 'Debit Card', 'TXN002', 'Success', '2026-01-17 13:10:00'),
(3, 3, 12000.00, 'UPI', 'TXN003', 'Success', '2026-01-17 13:15:00'),
(4, 4, 8100.00, 'Credit Card', 'TXN004', 'Success', '2026-01-17 13:20:00'),
(5, 5, 9000.00, 'Net Banking', 'TXN005', 'Success', '2026-01-17 13:25:00'),
(6, 7, 12500.00, 'Cash', 'TXN006', 'Success', '2026-01-17 14:25:00'),
(7, 8, 5000.00, 'Credit Card', 'TXN007', 'Success', '2026-01-17 14:45:00'),
(8, 9, 2700.00, 'Cash', 'TXN008', 'Success', '2026-01-17 15:05:00'),
(9, 11, 2500.00, 'UPI', 'TXN009', 'Success', '2026-01-18 09:55:00'),
(10, 12, 1800.00, 'UPI', 'TXN010', 'Success', '2026-01-18 10:10:00'),
(11, 13, 1800.00, 'Cash', 'TXN011', 'Success', '2026-01-18 15:15:00'),
(12, 15, 3000.00, 'UPI', 'TXN012', 'Success', '2026-01-18 12:45:00'),
(13, 16, 6000.00, 'UPI', 'TXN013', 'Success', '2026-01-18 12:50:00'),
(14, 17, 2700.00, 'Cash', 'TXN014', 'Success', '2026-01-18 15:10:00'),
(15, 19, 40000.00, 'Credit Card', 'TXN015', 'Success', '2026-01-18 18:05:00');

-- --------------------------------------------------------
-- Table structure for table `reviews`
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  KEY `booking_id` (`booking_id`),
  KEY `idx_reviews_rating` (`rating`),
  KEY `idx_reviews_approved` (`is_approved`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `reviews`
INSERT INTO `reviews` (`id`, `user_id`, `room_id`, `booking_id`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 2, 1, 1, 5, 'Excellent service and comfortable room. Staff was very helpful!', 1, '2026-01-17 13:04:14'),
(2, 3, 4, 2, 4, 'Good experience, room was clean and spacious. Would stay again.', 1, '2026-01-17 13:04:14'),
(3, 4, 7, 3, 5, 'Perfect for our anniversary celebration! Beautiful room and great amenities.', 1, '2026-01-17 13:04:14'),
(4, 2, 3, 4, 4, 'Business trip went smoothly, good amenities and wifi connection.', 1, '2026-01-17 13:04:14'),
(5, 5, 1, 7, 5, 'Amazing stay! The view was breathtaking and service was top-notch.', 1, '2026-01-18 10:00:00'),
(6, 5, 3, 9, 4, 'Clean room, comfortable bed. Breakfast could be better.', 1, '2026-01-18 11:00:00'),
(7, 6, 8, 14, 5, 'Jacuzzi was fantastic! Luxury at its best.', 1, '2026-01-18 17:00:00'),
(8, 8, 10, 19, 5, 'Presidential suite lived up to its name. Worth every penny!', 1, '2026-01-18 19:00:00');

-- --------------------------------------------------------
-- Table structure for table `services`
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `services`
INSERT INTO `services` (`id`, `service_name`, `description`, `icon`, `is_available`, `created_at`) VALUES
(1, 'Room Service', '24/7 in-room dining service with extensive menu', 'fas fa-concierge-bell', 1, '2026-01-17 13:04:14'),
(2, 'Laundry Service', 'Same-day laundry and dry cleaning available', 'fas fa-tshirt', 1, '2026-01-17 13:04:14'),
(3, 'Airport Transfer', 'Pickup and drop from airport with luxury cars', 'fas fa-plane', 1, '2026-01-17 13:04:14'),
(4, 'Car Rental', 'Car rental services with professional drivers', 'fas fa-car', 1, '2026-01-17 13:04:14'),
(5, 'Spa & Wellness', 'Full-service spa, massage, and wellness center', 'fas fa-spa', 1, '2026-01-17 13:04:14'),
(6, 'Conference Facilities', 'Meeting and conference rooms with latest technology', 'fas fa-chalkboard', 1, '2026-01-17 13:04:14'),
(7, 'Swimming Pool', 'Temperature-controlled infinity pool with bar', 'fas fa-swimming-pool', 1, '2026-01-17 13:04:14'),
(8, 'Free WiFi', 'High-speed internet access throughout hotel', 'fas fa-wifi', 1, '2026-01-17 13:04:14'),
(9, 'Parking', 'Secure underground parking facility', 'fas fa-parking', 1, '2026-01-17 13:04:14'),
(10, 'Child Care', 'Professional babysitting and child care services', 'fas fa-baby', 1, '2026-01-17 13:04:14'),
(11, 'Gym & Fitness', 'Modern fitness center with personal trainers', 'fas fa-dumbbell', 1, '2026-01-17 13:04:14'),
(12, 'Business Center', 'Computers, printers, and secretarial services', 'fas fa-briefcase', 1, '2026-01-17 13:04:14');

-- --------------------------------------------------------
-- Table structure for table `contact_messages`
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_unread` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `contact_messages`
INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `replied_at`, `created_at`) VALUES
(1, 'Rahul Kumar', 'rahul@gmail.com', 'Room Availability', 'Do you have rooms available for December 25-27? We are a family of 4.', 1, '2026-01-18 07:43:41', '2026-01-17 13:04:14'),
(2, 'Sneha Singh', 'sneha@gmail.com', 'Conference Booking', 'I want to book conference hall for 50 people next month. Please share rates.', 1, '2026-01-18 08:00:00', '2026-01-17 13:04:14'),
(3, 'Vikram Joshi', 'vikram@gmail.com', 'Special Request', 'Can you arrange airport pickup for international guests?', 1, '2026-01-18 09:00:00', '2026-01-17 13:04:14'),
(4, 'Anjali Mehta', 'anjali@gmail.com', 'Feedback', 'Great service during our stay! Special thanks to the staff.', 1, '2026-01-18 10:00:00', '2026-01-17 13:04:14'),
(5, 'Nitish', 'nitish@gmail.com', 'Room Booking', 'Do you have any rooms available on 26th January 2026?', 1, '2026-01-18 07:39:12', '2026-01-18 05:48:02'),
(6, 'Mohan Sharma', 'mohan@gmail.com', 'Wedding Booking', 'Looking to book 10 rooms for wedding guests in February.', 0, NULL, '2026-01-18 14:00:00'),
(7, 'Priyanka Reddy', 'priyanka@gmail.com', 'Corporate Rates', 'Can we discuss corporate rates for regular bookings?', 0, NULL, '2026-01-18 15:00:00'),
(8, 'Arun Gupta', 'arun@gmail.com', 'Special Diet', 'Do you provide vegan food options in room service?', 1, '2026-01-18 16:00:00', '2026-01-18 16:00:00');

-- --------------------------------------------------------
-- Table structure for table `gallery`
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(200) DEFAULT NULL,
  `category` enum('Room','Reception','Restaurant','Other') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gallery_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `gallery`
-- First, delete existing gallery data

-- Reset AUTO_INCREMENT
ALTER TABLE `gallery` AUTO_INCREMENT = 1;

-- Now insert your data
INSERT INTO `gallery` (`image_path`, `caption`, `category`, `uploaded_at`) VALUES
('Hotel-Eclat-Taipei-reception.jpg', 'Elegant Hotel Reception', 'Reception', '2026-01-17 13:04:14'),
('Enjoy_Restaurant.jpg', 'Fine Dining Restaurant', 'Restaurant', '2026-01-17 13:04:14'),
('pool.jpg', 'Temperature-controlled Swimming Pool', 'Other', '2026-01-17 13:04:14'),
('Relex-luxury-Spa.jpg', 'Relaxing Spa Center', 'Other', '2026-01-17 13:04:14'),
('gym.jpg', 'Modern Fitness Center', 'Other', '2026-01-17 13:04:14'),
('conference.jpg', 'Conference Hall', 'Other', '2026-01-17 13:04:14'),
('lobby-interior.jpg', 'Grand Hotel Lobby', 'Reception', '2026-01-17 13:04:14'),
('bar.jpg', 'Sky Lounge Bar', 'Restaurant', '2026-01-17 13:04:14'),
('view-city-room.jpg', 'City View from Room', 'Room', '2026-01-17 13:04:14'),
('Luxury-suite-interior.jpg', 'Luxury Suite Interior', 'Room', '2026-01-17 13:04:14'),
('infinity-pool-view.jpg', 'Infinity Pool View', 'Other', '2026-01-18 07:41:17'),
('Deluxe-room.jpg', 'Deluxe Room Interior', 'Room', '2026-01-18 08:17:12'),
('main-dinning-area.jpg', 'Main Dining Area', 'Restaurant', '2026-01-18 09:00:00'),
('Massage-room.jpg', 'Massage Room', 'Other', '2026-01-18 10:00:00'),
('CardioArea.jpg', 'Cardio Section', 'Other', '2026-01-18 11:00:00');

-- --------------------------------------------------------
-- Table structure for table `notifications`
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('Booking','Payment','System','Promotion') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `notifications`
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 2, 'Booking Confirmed', 'Your booking #1 has been confirmed. Check-in: Jan 18, 2026', 'Booking', 1, '2026-01-17 13:04:14'),
(2, 3, 'Payment Successful', 'Payment of ₹12,000 for booking #2 received successfully', 'Payment', 1, '2026-01-17 13:04:14'),
(3, 4, 'Special Offer', 'Get 20% off on your next booking! Valid until Jan 31, 2026', 'Promotion', 0, '2026-01-17 13:04:14'),
(4, NULL, 'System Maintenance', 'System will be down for maintenance on Sunday 2 AM to 4 AM', 'System', 0, '2026-01-17 13:04:14'),
(5, 5, 'Review Submitted', 'Thank you for your review! Your feedback is appreciated.', 'System', 1, '2026-01-18 11:00:00'),
(6, 6, 'Booking Pending', 'Your booking #14 is pending confirmation. Please complete payment.', 'Booking', 0, '2026-01-18 16:00:00'),
(7, 8, 'Payment Receipt', 'Payment receipt for booking #19 has been emailed to you.', 'Payment', 1, '2026-01-18 18:05:00'),
(8, NULL, 'New Year Promotion', 'Book 3 nights get 1 night free! Limited period offer.', 'Promotion', 0, '2026-01-18 19:00:00');

-- --------------------------------------------------------
-- Table structure for table `settings`
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `settings`
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'hotel_name', 'Grand Hotel & Resorts', 'Name of the hotel', '2026-01-18 10:00:00'),
(2, 'hotel_email', 'info@grandhotel.com', 'Hotel contact email', '2026-01-17 13:04:14'),
(3, 'hotel_phone', '+91 9876543210', 'Hotel contact number', '2026-01-17 13:04:14'),
(4, 'hotel_address', '123 Hotel Street, Marine Drive, Mumbai, India', 'Hotel physical address', '2026-01-18 11:00:00'),
(5, 'checkin_time', '14:00:00', 'Standard check-in time', '2026-01-17 13:04:14'),
(6, 'checkout_time', '12:00:00', 'Standard check-out time', '2026-01-17 13:04:14'),
(7, 'tax_rate', '18.00', 'GST tax rate in percentage', '2026-01-17 13:04:14'),
(8, 'currency', 'INR', 'Default currency', '2026-01-17 13:04:14'),
(9, 'otp_expiry_minutes', '5', 'OTP validity in minutes', '2026-01-17 13:04:14'),
(10, 'max_login_attempts', '5', 'Maximum login attempts before lock', '2026-01-17 13:04:14'),
(11, 'booking_confirmation_email', '1', 'Send booking confirmation email (1=Yes, 0=No)', '2026-01-17 13:04:14'),
(12, 'payment_gateway', 'razorpay', 'Default payment gateway', '2026-01-17 13:04:14'),
(13, 'cancellation_policy', 'Free cancellation 48hrs before check-in', 'Cancellation policy description', '2026-01-18 12:00:00'),
(14, 'breakfast_timing', '7:00 AM - 10:30 AM', 'Breakfast serving hours', '2026-01-18 13:00:00'),
(15, 'support_email', 'support@grandhotel.com', 'Customer support email', '2026-01-18 14:00:00'),

-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table structure for table `password_resets`
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `password_resets`
INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'rajesh@gmail.com', 'abc123def456', '2026-01-18 14:05:00', '2026-01-18 13:05:00'),
(2, 'priya@gmail.com', 'xyz789uvw012', '2026-01-18 15:10:00', '2026-01-18 14:10:00');

-- --------------------------------------------------------

-- Table structure for table `audit_log`
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_table` (`table_name`),
  KEY `idx_audit_record` (`table_name`,`record_id`),
  KEY `idx_audit_date` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert dummy data into `audit_log`
INSERT INTO `audit_log` (`id`, `table_name`, `record_id`, `action`, `old_value`, `new_value`, `changed_by`, `changed_at`) VALUES
(1, 'bookings', 4, 'STATUS_CHANGE', 'Confirmed', 'Cancelled', 'admin@hotel.com', '2026-01-17 20:17:15'),
(2, 'rooms', 1, 'PRICE_UPDATE', '2500.00', '2700.00', 'admin@hotel.com', '2026-01-18 09:00:00'),
(3, 'users', 5, 'PROFILE_UPDATE', 'Gulabi kumar', 'Gulabi Kumar', 'kumargulabi31@gmail.com', '2026-01-18 10:00:00'),
(4, 'settings', 1, 'UPDATE', 'Grand Hotel', 'Grand Hotel & Resorts', 'admin@hotel.com', '2026-01-18 10:00:00'),
(5, 'bookings', 18, 'STATUS_CHANGE', 'Pending', 'Cancelled', 'system', '2026-01-18 19:00:00');

-- Stored Procedures
DELIMITER $$

CREATE PROCEDURE `sp_cancel_booking` (
  IN `p_booking_id` INT,
  IN `p_user_id` INT,
  IN `p_reason` TEXT,
  IN `p_charge` DECIMAL(10,2),
  IN `p_refund` DECIMAL(10,2)
)
BEGIN
    DECLARE v_room_id INT;
    DECLARE v_current_status VARCHAR(20);
    DECLARE v_error_msg VARCHAR(255);
    
    START TRANSACTION;
    
    SELECT room_id, status INTO v_room_id, v_current_status
    FROM bookings 
    WHERE id = p_booking_id AND user_id = p_user_id
    FOR UPDATE;
    
    IF v_room_id IS NULL THEN
        SET v_error_msg = 'Booking not found or does not belong to user';
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    IF v_current_status IN ('Cancelled', 'Completed') THEN
        SET v_error_msg = CONCAT('Cannot cancel booking with status: ', v_current_status);
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    UPDATE bookings 
    SET status = 'Cancelled'
    WHERE id = p_booking_id;
    
    UPDATE rooms 
    SET is_available = 1 
    WHERE id = v_room_id;
    
    INSERT INTO cancellation_logs (booking_id, user_id, cancellation_reason, 
                                  cancellation_charge, refund_amount)
    VALUES (p_booking_id, p_user_id, p_reason, p_charge, p_refund);
    
    UPDATE users 
    SET last_activity = NOW() 
    WHERE id = p_user_id;
    
    COMMIT;
    
    SELECT 'SUCCESS' as result, 
           p_booking_id as booking_id,
           'Booking cancelled successfully' as message;
END$$

-- --------------------------------------------------------
-- Functions
CREATE FUNCTION `fn_calculate_cancellation_charge` (
  `p_booking_id` INT,
  `p_cancel_date` DATE
) RETURNS DECIMAL(10,2) DETERMINISTIC
BEGIN
    DECLARE v_total_amount DECIMAL(10,2);
    DECLARE v_check_in DATE;
    DECLARE v_days_before INT;
    DECLARE v_charge DECIMAL(10,2);
    
    SELECT total_amount, check_in 
    INTO v_total_amount, v_check_in
    FROM bookings 
    WHERE id = p_booking_id;
    
    SET v_days_before = DATEDIFF(v_check_in, p_cancel_date);
    
    IF v_days_before > 2 THEN
        SET v_charge = 0;
    ELSEIF v_days_before BETWEEN 1 AND 2 THEN
        SET v_charge = v_total_amount * 0.5;
    ELSEIF v_days_before = 0 THEN
        SET v_charge = v_total_amount * 0.75;
    ELSE
        SET v_charge = v_total_amount;
    END IF;
    
    RETURN v_charge;
END$$

CREATE FUNCTION `fn_calculate_refund_amount` (
  `p_booking_id` INT,
  `p_cancel_date` DATE
) RETURNS DECIMAL(10,2) DETERMINISTIC
BEGIN
    DECLARE v_total_amount DECIMAL(10,2);
    DECLARE v_charge DECIMAL(10,2);
    DECLARE v_refund DECIMAL(10,2);
    
    SELECT total_amount INTO v_total_amount
    FROM bookings 
    WHERE id = p_booking_id;
    
    SET v_charge = fn_calculate_cancellation_charge(p_booking_id, p_cancel_date);
    
    SET v_refund = v_total_amount - v_charge;
    
    IF v_refund < 0 THEN
        SET v_refund = 0;
    END IF;
    
    RETURN v_refund;
END$$

DELIMITER ;

-- --------------------------------------------------------
-- Triggers
DELIMITER $$

CREATE TRIGGER `trg_after_booking_cancel` 
AFTER UPDATE ON `bookings` 
FOR EACH ROW 
BEGIN
    IF OLD.status != 'Cancelled' AND NEW.status = 'Cancelled' THEN
        UPDATE rooms 
        SET is_available = 1 
        WHERE id = NEW.room_id;
        
        IF NOT EXISTS (SELECT 1 FROM cancellation_logs WHERE booking_id = NEW.id) THEN
            INSERT INTO cancellation_logs (booking_id, user_id, cancellation_reason)
            VALUES (NEW.id, NEW.user_id, 'Cancelled via system');
        END IF;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------
-- Views
CREATE VIEW `vw_available_rooms` AS
SELECT `r`.`id` AS `id`, 
       `r`.`room_number` AS `room_number`, 
       `r`.`room_type` AS `room_type`, 
       `r`.`ac_type` AS `ac_type`, 
       `r`.`price_per_night` AS `price_per_night`, 
       `r`.`capacity` AS `capacity`, 
       `r`.`description` AS `description`, 
       `r`.`image_path` AS `image_path`, 
       `r`.`is_available` AS `is_available`, 
       `r`.`created_at` AS `created_at`, 
       `r`.`updated_at` AS `updated_at`, 
       CASE WHEN `r`.`is_available` = 1 THEN 'Available' ELSE 'Occupied' END AS `availability_status`
FROM `rooms` AS `r`
WHERE `r`.`is_available` = 1
ORDER BY `r`.`price_per_night` ASC;

CREATE VIEW `vw_booking_details` AS
SELECT `b`.`id` AS `booking_id`, 
       `b`.`booking_date` AS `booking_date`, 
       `b`.`check_in` AS `check_in`, 
       `b`.`check_out` AS `check_out`, 
       `b`.`total_amount` AS `total_amount`, 
       `b`.`status` AS `booking_status`, 
       `b`.`payment_status` AS `payment_status`, 
       `u`.`full_name` AS `guest_name`, 
       `u`.`email` AS `guest_email`, 
       `u`.`mobile` AS `guest_phone`, 
       `r`.`room_number` AS `room_number`, 
       `r`.`room_type` AS `room_type`, 
       `r`.`ac_type` AS `ac_type`, 
       `r`.`price_per_night` AS `price_per_night`, 
       DATEDIFF(`b`.`check_out`, `b`.`check_in`) AS `nights_stay`
FROM ((`bookings` `b` 
JOIN `users` `u` ON `b`.`user_id` = `u`.`id`) 
JOIN `rooms` `r` ON `b`.`room_id` = `r`.`id`);

CREATE VIEW `vw_cancellation_summary` AS
SELECT `cl`.`id` AS `log_id`, 
       `cl`.`booking_id` AS `booking_id`, 
       `b`.`room_id` AS `room_id`, 
       `r`.`room_number` AS `room_number`, 
       `r`.`room_type` AS `room_type`, 
       `u`.`full_name` AS `guest_name`, 
       `u`.`email` AS `guest_email`, 
       `cl`.`cancellation_reason` AS `cancellation_reason`, 
       `b`.`total_amount` AS `total_amount`, 
       `cl`.`cancellation_charge` AS `cancellation_charge`, 
       `cl`.`refund_amount` AS `refund_amount`, 
       `cl`.`cancelled_at` AS `cancelled_at`, 
       DATEDIFF(`b`.`check_in`, DATE(`cl`.`cancelled_at`)) AS `days_before_checkin`
FROM (((`cancellation_logs` `cl` 
JOIN `bookings` `b` ON `cl`.`booking_id` = `b`.`id`) 
JOIN `rooms` `r` ON `b`.`room_id` = `r`.`id`) 
JOIN `users` `u` ON `cl`.`user_id` = `u`.`id`)
ORDER BY `cl`.`cancelled_at` DESC;

CREATE VIEW `vw_customer_stats` AS
SELECT `u`.`id` AS `id`, 
       `u`.`full_name` AS `full_name`, 
       `u`.`email` AS `email`, 
       `u`.`mobile` AS `mobile`, 
       `u`.`created_at` AS `member_since`, 
       COUNT(`b`.`id`) AS `total_bookings`, 
       SUM(CASE WHEN `b`.`status` IN ('Confirmed','Completed') THEN `b`.`total_amount` ELSE 0 END) AS `total_spent`, 
       MAX(`b`.`booking_date`) AS `last_booking_date`
FROM (`users` `u` 
LEFT JOIN `bookings` `b` ON `u`.`id` = `b`.`user_id`)
GROUP BY `u`.`id`, `u`.`full_name`, `u`.`email`, `u`.`mobile`, `u`.`created_at`
ORDER BY SUM(CASE WHEN `b`.`status` IN ('Confirmed','Completed') THEN `b`.`total_amount` ELSE 0 END) DESC;

CREATE VIEW `vw_monthly_revenue` AS
SELECT DATE_FORMAT(`bookings`.`booking_date`,'%Y-%m') AS `month`, 
       COUNT(*) AS `total_bookings`, 
       SUM(`bookings`.`total_amount`) AS `total_revenue`, 
       AVG(`bookings`.`total_amount`) AS `avg_booking_value`
FROM `bookings`
WHERE `bookings`.`status` IN ('Confirmed','Completed')
GROUP BY DATE_FORMAT(`bookings`.`booking_date`,'%Y-%m')
ORDER BY DATE_FORMAT(`bookings`.`booking_date`,'%Y-%m') DESC;

CREATE VIEW `vw_room_occupancy` AS
SELECT `r`.`room_number` AS `room_number`, 
       `r`.`room_type` AS `room_type`, 
       `r`.`ac_type` AS `ac_type`, 
       COUNT(`b`.`id`) AS `total_bookings`, 
       SUM(CASE WHEN `b`.`status` IN ('Confirmed','Completed') THEN 1 ELSE 0 END) AS `successful_bookings`, 
       AVG(`b`.`total_amount`) AS `avg_booking_value`
FROM (`rooms` `r` 
LEFT JOIN `bookings` `b` ON `r`.`id` = `b`.`room_id`)
GROUP BY `r`.`id`, `r`.`room_number`, `r`.`room_type`, `r`.`ac_type`
ORDER BY COUNT(`b`.`id`) DESC;

-- --------------------------------------------------------
-- Events
DELIMITER $$

CREATE EVENT `ev_auto_cancel_pending_bookings`
ON SCHEDULE EVERY 1 HOUR
STARTS '2026-01-17 20:10:05'
DO
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_booking_id INT;
    DECLARE v_user_id INT;
    DECLARE v_room_id INT;
    DECLARE cur CURSOR FOR 
        SELECT id, user_id, room_id 
        FROM bookings 
        WHERE status = 'Pending' 
        AND TIMESTAMPDIFF(HOUR, booking_date, NOW()) >= 24;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_booking_id, v_user_id, v_room_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        START TRANSACTION;
        
        UPDATE bookings 
        SET status = 'Cancelled' 
        WHERE id = v_booking_id;
        
        UPDATE rooms 
        SET is_available = 1 
        WHERE id = v_room_id;
        
        INSERT INTO cancellation_logs (booking_id, user_id, cancellation_reason)
        VALUES (v_booking_id, v_user_id, 'Auto-cancelled: 24 hours pending');
        
        COMMIT;
    END LOOP;
    
    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------
-- Reset AUTO_INCREMENT values
ALTER TABLE `users` AUTO_INCREMENT = 9;
ALTER TABLE `rooms` AUTO_INCREMENT = 13;
ALTER TABLE `bookings` AUTO_INCREMENT = 20;
ALTER TABLE `cancellation_logs` AUTO_INCREMENT = 6;
ALTER TABLE `payments` AUTO_INCREMENT = 16;
ALTER TABLE `reviews` AUTO_INCREMENT = 9;
ALTER TABLE `services` AUTO_INCREMENT = 13;
ALTER TABLE `contact_messages` AUTO_INCREMENT = 9;
ALTER TABLE `gallery` AUTO_INCREMENT = 16;
ALTER TABLE `notifications` AUTO_INCREMENT = 9;
ALTER TABLE `settings` AUTO_INCREMENT = 18;
ALTER TABLE `audit_log` AUTO_INCREMENT = 6;
ALTER TABLE `password_resets` AUTO_INCREMENT = 3;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;