<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$success = '';
$error = '';

// Get room ID from URL
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch room details
$room_query = "SELECT * FROM rooms WHERE id = $room_id";
$room_result = mysqli_query($conn, $room_query);
$room = mysqli_fetch_assoc($room_result);

if (!$room) {
    header("Location: rooms.php");
    exit();
}

// Initialize form variables
$room_number = $room['room_number'];
$room_type = $room['room_type'];
$ac_type = $room['ac_type'];
$capacity = $room['capacity'];
$price_per_night = $room['price_per_night'];
$description = $room['description'];
$is_available = $room['is_available'];
$amenities = explode(',', $room['amenities']);

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
    
    // Check if room number already exists (excluding current room)
    $check_query = "SELECT id FROM rooms WHERE room_number = '$room_number' AND id != $room_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Room number already exists!";
    } else {
        // Handle image upload
        $image_path = $room['image_path'];
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = $_FILES['room_image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = '../uploads/rooms/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['room_image']['name']);
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_path)) {
                    // Delete old image if exists
                    if ($image_path && file_exists('../' . $image_path)) {
                        unlink('../' . $image_path);
                    }
                    $image_path = 'uploads/rooms/' . $file_name;
                }
            }
        }
        
        // Update room
        $query = "UPDATE rooms SET 
                  room_number = '$room_number',
                  room_type = '$room_type',
                  ac_type = '$ac_type',
                  capacity = '$capacity',
                  price_per_night = '$price_per_night',
                  amenities = '$amenities_str',
                  description = '$description',
                  is_available = '$is_available',
                  image_path = '$image_path',
                  updated_at = NOW()
                  WHERE id = $room_id";
        
        if (mysqli_query($conn, $query)) {
            $success = "Room updated successfully!";
            // Refresh room data
            $room_result = mysqli_query($conn, $room_query);
            $room = mysqli_fetch_assoc($room_result);
            $amenities = explode(',', $room['amenities']);
        } else {
            $error = "Error updating room: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container" style="padding: 2rem 0; max-width: 800px;">
        <h2>Edit Room: <?php echo $room['room_number']; ?></h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" action="" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <!-- Left Column -->
                    <div>
                        <div class="form-group">
                            <label>Room Number *</label>
                            <input type="text" name="room_number" class="form-control" 
                                   value="<?php echo $room_number; ?>" required>
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
                                   value="<?php echo $price_per_night; ?>" min="0" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Amenities</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <?php
                                $all_amenities = [
                                    'WiFi', 'TV', 'AC', 'Heater', 'Mini Fridge', 
                                    'Room Service', 'Breakfast', 'Parking', 
                                    'Swimming Pool', 'Gym', 'Spa', 'Laundry'
                                ];
                                
                                foreach ($all_amenities as $amenity):
                                ?>
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="checkbox" name="amenities[]" value="<?php echo $amenity; ?>"
                                               <?php echo in_array($amenity, $amenities) ? 'checked' : ''; ?>>
                                        <?php echo $amenity; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Room Image</label>
                            <?php if ($room['image_path']): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="../<?php echo $room['image_path']; ?>" 
                                         style="max-width: 200px; max-height: 150px; border-radius: 5px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="room_image" class="form-control" accept="image/*">
                            <small>Leave empty to keep current image</small>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_available" value="1" 
                                       <?php echo $is_available ? 'checked' : ''; ?>>
                                Available for booking
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo $description; ?></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-success">Update Room</button>
                    <a href="rooms.php" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>