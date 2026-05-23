<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

// Define sanitize function if not already defined
if (!function_exists('sanitize')) {
    function sanitize($input) {
        global $conn;
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        $input = mysqli_real_escape_string($conn, $input);
        return $input;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address!";
    } else {
        // Save contact message to database
        $query = "INSERT INTO contact_messages (name, email, subject, message) 
                  VALUES ('$name', '$email', '$subject', '$message')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Thank you for contacting us! We'll get back to you soon.";
            
            // Send email notification (implement this)
            // sendContactEmail($name, $email, $subject, $message);
            
            // Clear form
            $_POST = array();
        } else {
            $error = "Error sending message. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hotel Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Global Styles */
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --light: #f8fafc;
            --dark: #1e293b;
            --danger: #dc2626;
            --success: #10b981;
        }
        
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: var(--success);
            border: 1px solid #a7f3d0;
        }
        
        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        
        h1, h2, h3, h4 {
            color: var(--dark);
            margin-top: 0;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        /* Contact Container */
        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin: 3rem 0;
        }
        
        /* Contact Info Card */
        .contact-info {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
            height: 700px;
        }
        
        .contact-info h3 {
            color: white;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .contact-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .contact-icon {
            font-size: 1.5rem;
            margin-right: 1.5rem;
            color: #93c5fd;
            min-width: 30px;
        }
        
        .contact-content h4 {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .contact-content p {
            color: #dbeafe;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Social Media */
        .social-media {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .social-media h4 {
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        /* Button Styles */
        .btn {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }
        
        /* Map Container */
        .map-container {
            margin: 3rem 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
        
        /* FAQ Section */
        .faq-section {
            margin: 3rem 0;
        }
        
        .faq-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.1);
        }
        
        .faq-item h4 {
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        
        .faq-item p {
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .contact-info {
                padding: 2rem;
            }
            
            .map-container {
                margin: 2rem 0;
            }
            
            .card {
                padding: 1.5rem;
            }
            #headline{
                margin-left: 20px;
            }

            #info-contact{
                padding-left: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }
            #headline{
                margin-left: 20px;
            }

            #info-contact{
                padding-left: 20px;
            }
            .contact-info {
                padding: 20px;

            }
            
            .contact-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .contact-icon {
                margin-bottom: 0.10rem;
                margin-right: 0;
            }
            
            .social-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container" style="padding: 1rem 0;">
        <h1 id="headline">Contact Us</h1>
        <p id="info-contact" style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">
            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="contact-container">
            <!-- Contact Form -->
            <div>
                <div class="card">
                    <h3>Send us a Message</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Your Name *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Your Email *</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Subject *</label>
                            <input type="text" class="form-control" name="subject" 
                                   value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea class="form-control" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                <h3>Get in Touch</h3>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-content">
                        <h4>Address</h4>
                        <p>75 Hotel Street, Marine Drive<br>Patna, Bihar 800024</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-content">
                        <h4>Phone Numbers</h4>
                        <p>Reservation: +91 9876543210<br>Reception: +91 9876543211</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-content">
                        <h4>Email Address</h4>
                        <p>reservation@smarthotel.com<br>info@smarthotel.com</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-content">
                        <h4>Working Hours</h4>
                        <p>24/7 Reception<br>Restaurant: 7:00 AM - 11:00 PM</p>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="social-media">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3773.581321274582!2d72.82109131471783!3d18.929009987171266!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7d1e801f5b3db%3A0x5c7c2a7d7e0f4b3a!2sMarine%20Drive%2C%20Mumbai!5e0!3m2!1sen!2sin!4v1628234567890!5m2!1sen!2sin" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="card">
                <h3>Frequently Asked Questions</h3>
                
                <div style="margin-top: 1.5rem;">
                    <div class="faq-item">
                        <h4>What are the check-in and check-out times?</h4>
                        <p>Check-in time is 2:00 PM and check-out time is 12:00 PM. Early check-in and late check-out may be available upon request, subject to availability and additional charges.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h4>Do you offer airport pickup service?</h4>
                        <p>Yes, we offer airport pickup and drop services at an additional charge. Please contact us in advance to arrange this service.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h4>What payment methods do you accept?</h4>
                        <p>We accept credit cards, debit cards, net banking, UPI, and cash payments.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h4>Do you have wheelchair accessible rooms?</h4>
                        <p>Yes, we have specially designed rooms for guests with disabilities. Please mention this requirement while booking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>