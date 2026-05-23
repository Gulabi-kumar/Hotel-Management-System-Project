<?php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

$error = '';
$success = '';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_image'])) {
    $caption = sanitize($_POST['caption']);
    $category = sanitize($_POST['category']);

    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $file_type = $_FILES['gallery_image']['type'];
        $file_size = $_FILES['gallery_image']['size'];

        if (in_array($file_type, $allowed_types)) {
            if ($file_size <= 5 * 1024 * 1024) { // 5MB max
                $image_name = time() . '_' . basename($_FILES['gallery_image']['name']);
                $target_path = "../assets/uploads/gallery/" . $image_name;

                if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $target_path)) {
                    $query = "INSERT INTO gallery (image_path, caption, category) 
                              VALUES ('$image_name', '$caption', '$category')";

                    if (mysqli_query($conn, $query)) {
                        $success = "Image uploaded successfully!";
                    } else {
                        $error = "Error saving image details!";
                    }
                } else {
                    $error = "Error uploading image!";
                }
            } else {
                $error = "Image size must be less than 5MB!";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed!";
        }
    } else {
        $error = "Please select an image!";
    }
}

// Handle image deletion
if (isset($_GET['delete'])) {
    $image_id = sanitize($_GET['delete']);

    // Get image path
    $query = "SELECT image_path FROM gallery WHERE id = $image_id";
    $result = mysqli_query($conn, $query);
    $image = mysqli_fetch_assoc($result);

    if ($image) {
        // Delete from database
        $delete_query = "DELETE FROM gallery WHERE id = $image_id";
        if (mysqli_query($conn, $delete_query)) {
            // Delete file
            $file_path = "../assets/uploads/gallery/" . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $success = "Image deleted successfully!";
        }
    }
}

// Get all gallery images
$query = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Hotel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Base responsive settings */
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

        .container {
            width: 100%;
            max-width: 100%;
            padding: 20px 15px;
            margin: 0 auto;
        }

        /* Headings with responsive font sizes */
        h2 {
            font-size: clamp(1.25rem, 4vw, 1.8rem);
            color: var(--dark);
            margin-bottom: 1.5rem;
        }

        h3 {
            font-size: clamp(1rem, 3vw, 1.3rem);
            margin-bottom: 1.25rem;
            color: var(--dark);
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f5f9;
        }

        h4 {
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        /* Card styles */
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        small {
            font-size: clamp(0.75rem, 1.5vw, 0.8rem);
            color: #666;
            display: block;
            margin-top: 0.25rem;
        }

        /* Buttons */
        .btn {
            display: inline-block;
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

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 0.5rem 0.875rem;
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
        }

        /* Alert messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            font-size: clamp(0.85rem, 2vw, 1rem);
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

        /* Gallery grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .gallery-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery-info {
            padding: 1rem;
        }

        /* Category badges */
        .category-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: clamp(0.75rem, 1.5vw, 0.8rem);
            margin-bottom: 0.5rem;
        }

        .category-room {
            background: #3498db;
            color: white;
        }

        .category-reception {
            background: #e74c3c;
            color: white;
        }

        .category-restaurant {
            background: #27ae60;
            color: white;
        }

        .category-other {
            background: #f39c12;
            color: white;
        }

        /* Stat cards */
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            color: #666;
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            color: var(--dark);
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

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 1.25rem;
            }

            .gallery-item img {
                height: 180px;
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

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1.5rem;
            }

            .gallery-item img {
                height: 160px;
            }

            .form-group label,
            .form-control {
                font-size: 0.95rem;
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

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                gap: 1.75rem;
            }

            .gallery-item img {
                height: 180px;
            }

            h2 {
                font-size: 1.8rem;
            }

            h3 {
                font-size: 1.4rem;
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

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 2rem;
            }

            .gallery-item img {
                height: 200px;
            }

            .card {
                padding: 2rem;
            }

            .stat-card {
                padding: 1.5rem;
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

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }

            .gallery-item img {
                height: 220px;
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

            #gallery-headline {
                padding-left: 30px;
            }

            .card {
                padding: 1.25rem;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-top: 1.5rem;
            }

            .gallery-item {
                margin-bottom: 0.5rem;
            }

            .gallery-item img {
                height: 180px;
            }

            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                width: 100%;
            }

            .btn-sm {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }

            .form-control {
                padding: 0.625rem 0.875rem;
                font-size: 0.9rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card h4 {
                font-size: 0.85rem;
            }

            .stat-card .number {
                font-size: 1.25rem;
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

            #gallery-headline {
                padding-left: 30px;
            }

            .card {
                padding: 1rem;
            }

            .gallery-item img {
                height: 150px;
            }

            .gallery-info {
                padding: 0.75rem;
            }

            .gallery-info h4 {
                font-size: 0.9rem;
            }

            .category-badge {
                font-size: 0.7rem;
                padding: 2px 6px;
            }

            .btn {
                padding: 0.625rem 0.75rem;
                font-size: 0.85rem;
            }

            .form-control {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
        }

        /* Form grid responsiveness */
        @media (max-width: 768px) {
            form>div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            #gallery-headline {
                padding-left: 30px;
            }

        }

        /* Gallery header */
        div[style*="justify-content: space-between"] {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }

        div[style*="justify-content: space-between"] h3 {
            margin-bottom: 0;
        }

        div[style*="justify-content: space-between"] span {
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            color: #666;
        }

        @media (min-width: 768px) {
            div[style*="justify-content: space-between"] {
                flex-direction: row;
                align-items: center !important;
            }
        }

        /* Statistics grid */
        div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        @media (max-width: 480px) {
            div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 320px) {
            div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
                grid-template-columns: 1fr;
            }
        }

        /* Touch device optimization */
        @media (hover: none) and (pointer: coarse) {
            .gallery-item:hover {
                transform: none;
            }

            .btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .form-control {
                min-height: 44px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="padding: 2rem 0;">
        <h2 id="gallery-headline">Manage Gallery</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="card">
            <h3>Upload New Image</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label>Image Caption *</label>
                        <input type="text" class="form-control" name="caption" required>
                    </div>

                    <div class="form-group">
                        <label>Category *</label>
                        <select class="form-control" name="category" required>
                            <option value="Room">Room</option>
                            <option value="Reception">Reception</option>
                            <option value="Restaurant">Restaurant</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Image *</label>
                        <input type="file" class="form-control" name="gallery_image" accept="image/*" required>
                        <small>Max size: 5MB | Formats: JPG, PNG, GIF</small>
                    </div>
                </div>

                <button type="submit" name="add_image" class="btn" style="margin-top: 1rem;">Upload Image</button>
            </form>
        </div>

        <!-- Gallery Images -->
        <div class="card" style="margin-top: 2rem;" id="gallery-headline">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Gallery Images</h3>
                <span>
                    Total: <?php echo mysqli_num_rows($result); ?> images
                </span>
            </div>

            <div class="gallery-grid">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($image = mysqli_fetch_assoc($result)): ?>
                        <div class="gallery-item">
                            <img src="../assets/uploads/gallery/<?php echo $image['image_path']; ?>"
                                alt="<?php echo $image['caption']; ?>">

                            <div class="gallery-info">
                                <span class="category-badge category-<?php echo strtolower($image['category']); ?>">
                                    <?php echo $image['category']; ?>
                                </span>

                                <h4><?php echo $image['caption']; ?></h4>
                                <small>
                                    Uploaded: <?php echo date('d M Y', strtotime($image['uploaded_at'])); ?>
                                </small>

                                <div style="margin-top: 10px;">
                                    <a href="gallery.php?delete=<?php echo $image['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this image?')">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                        No images in gallery. Upload some images!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Image Statistics -->
        <div class="card" style="margin-top: 2rem;">
            <h3>Gallery Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <?php
                $category_stats = [
                    'Room' => "SELECT COUNT(*) as count FROM gallery WHERE category = 'Room'",
                    'Reception' => "SELECT COUNT(*) as count FROM gallery WHERE category = 'Reception'",
                    'Restaurant' => "SELECT COUNT(*) as count FROM gallery WHERE category = 'Restaurant'",
                    'Other' => "SELECT COUNT(*) as count FROM gallery WHERE category = 'Other'"
                ];

                foreach ($category_stats as $category => $sql):
                    $result = mysqli_query($conn, $sql);
                    $data = mysqli_fetch_assoc($result);
                ?>
                    <div class="stat-card">
                        <h4><?php echo $category; ?> Images</h4>
                        <div class="number"><?php echo $data['count']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>