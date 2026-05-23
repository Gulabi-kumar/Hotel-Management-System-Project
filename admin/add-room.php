<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$success = '';
$error = '';
$room_number = $room_type = $ac_type = $capacity = $price_per_night = '';
$amenities = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $ac_type = mysqli_real_escape_string($conn, $_POST['ac_type']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $price_per_night = mysqli_real_escape_string($conn, $_POST['price_per_night']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle amenities
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];
    $amenities_str = implode(',', $amenities);
    
    // Check if room number already exists
    $check_query = "SELECT id FROM rooms WHERE room_number = '$room_number'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Room number already exists!";
    } else {
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = $_FILES['room_image']['type'];
            $file_size = $_FILES['room_image']['size'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            // Check file type
            if (in_array($file_type, $allowed_types)) {
                // Check file size
                if ($file_size <= $max_size) {
                    $upload_dir = '../assets/uploads/rooms/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION);
                    $file_name = 'room_' . time() . '_' . uniqid() . '.' . strtolower($file_extension);
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_path)) {
                        // Store relative path from assets folder
                        $image_path = 'assets/uploads/rooms/' . $file_name;
                    } else {
                        $error = "Failed to upload image. Please try again.";
                    }
                } else {
                    $error = "Image size should be less than 2MB.";
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            }
        }
        
        // If there's no image upload error, proceed with room insertion
        if (!$error) {
            // Insert room
            $query = "INSERT INTO rooms (room_number, room_type, ac_type, capacity, price_per_night, 
                       amenities, description, is_available, image_path, created_at) 
                      VALUES ('$room_number', '$room_type', '$ac_type', '$capacity', '$price_per_night', 
                      '$amenities_str', '$description', '$is_available', '$image_path', NOW())";
            
            if (mysqli_query($conn, $query)) {
                $success = "Room added successfully!";
                // Clear form
                $room_number = $room_type = $ac_type = $capacity = $price_per_night = '';
                $amenities = [];
                $description = '';
            } else {
                $error = "Error adding room: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Room - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .image-preview {
            margin-top: 10px;
            display: none;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3a86ff;
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.1);
        }
        
        select.form-control {
            height: 45px;
        }
        
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.5rem;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .checkbox-label:hover {
            background: #e9ecef;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
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
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .checkbox-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container form-container">
        <h2 style="margin-bottom: 1.5rem; color: #333;">Add New Room</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <form method="POST" action="" enctype="multipart/form-data" id="roomForm">
                <div class="form-grid">
                    <!-- Left Column -->
                    <div>
                        <div class="form-group">
                            <label>Room Number *</label>
                            <input type="text" name="room_number" class="form-control" 
                                   value="<?php echo htmlspecialchars($room_number); ?>" required
                                   placeholder="e.g., 101">
                        </div>
                        
                        <div class="form-group">
                            <label>Room Type *</label>
                            <select name="room_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Single" <?php echo $room_type == 'Single' ? 'selected' : ''; ?>>Single</option>
                                <option value="Double" <?php echo $room_type == 'Double' ? 'selected' : ''; ?>>Double</option>
                                <option value="Deluxe" <?php echo $room_type == 'Deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                                <option value="Suite" <?php echo $room_type == 'Suite' ? 'selected' : ''; ?>>Suite</option>
                                <option value="Family" <?php echo $room_type == 'Family' ? 'selected' : ''; ?>>Family</option>
                                <option value="Executive" <?php echo $room_type == 'Executive' ? 'selected' : ''; ?>>Executive</option>
                                <option value="Presidential" <?php echo $room_type == 'Presidential' ? 'selected' : ''; ?>>Presidential</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>AC Type *</label>
                            <select name="ac_type" class="form-control" required>
                                <option value="">Select AC Type</option>
                                <option value="AC" <?php echo $ac_type == 'AC' ? 'selected' : ''; ?>>AC</option>
                                <option value="Non-AC" <?php echo $ac_type == 'Non-AC' ? 'selected' : ''; ?>>Non-AC</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Capacity (Persons) *</label>
                            <select name="capacity" class="form-control" required>
                                <option value="">Select Capacity</option>
                                <option value="1" <?php echo $capacity == '1' ? 'selected' : ''; ?>>1 Person</option>
                                <option value="2" <?php echo $capacity == '2' ? 'selected' : ''; ?>>2 Persons</option>
                                <option value="3" <?php echo $capacity == '3' ? 'selected' : ''; ?>>3 Persons</option>
                                <option value="4" <?php echo $capacity == '4' ? 'selected' : ''; ?>>4 Persons</option>
                                <option value="5" <?php echo $capacity == '5' ? 'selected' : ''; ?>>5+ Persons</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        <div class="form-group">
                            <label>Price Per Night (₹) *</label>
                            <input type="number" name="price_per_night" class="form-control" 
                                   value="<?php echo $price_per_night; ?>" min="0" step="0.01" required
                                   placeholder="e.g., 2500">
                        </div>
                        
                        <div class="form-group">
                            <label>Amenities</label>
                            <div class="checkbox-grid">
                                <?php
                                $all_amenities = [
                                    'WiFi' => 'fas fa-wifi',
                                    'TV' => 'fas fa-tv',
                                    'AC' => 'fas fa-snowflake',
                                    'Heater' => 'fas fa-temperature-high',
                                    'Mini Fridge' => 'fas fa-utensils',
                                    'Room Service' => 'fas fa-concierge-bell',
                                    'Breakfast' => 'fas fa-coffee',
                                    'Parking' => 'fas fa-parking',
                                    'Swimming Pool' => 'fas fa-swimming-pool',
                                    'Gym' => 'fas fa-dumbbell',
                                    'Spa' => 'fas fa-spa',
                                    'Laundry' => 'fas fa-tshirt'
                                ];
                                
                                foreach ($all_amenities as $amenity => $icon):
                                ?>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="amenities[]" value="<?php echo $amenity; ?>"
                                               <?php echo in_array($amenity, $amenities) ? 'checked' : ''; ?>>
                                        <i class="<?php echo $icon; ?>"></i>
                                        <?php echo $amenity; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Room Image</label>
                            <input type="file" name="room_image" class="form-control" accept="image/*" 
                                   id="roomImage" onchange="previewImage(this)">
                            <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Preview">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label" style="margin-top: 1rem;">
                                <input type="checkbox" name="is_available" value="1" checked>
                                <span>Available for booking</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" 
                              placeholder="Enter room description..."><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Room
                    </button>
                    <a href="rooms.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                previewImg.src = '';
            }
        }
        
        // Form validation
        document.getElementById('roomForm').addEventListener('submit', function(e) {
            const roomNumber = document.querySelector('input[name="room_number"]').value;
            const price = document.querySelector('input[name="price_per_night"]').value;
            
            if (!roomNumber.trim()) {
                e.preventDefault();
                alert('Room number is required!');
                return false;
            }
            
            if (!price || price <= 0) {
                e.preventDefault();
                alert('Please enter a valid price!');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>