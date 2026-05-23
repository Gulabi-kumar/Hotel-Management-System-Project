<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hotel Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    
</head>

<style>
    /* Responsive About Page Styles */

    /* Base responsive container */
    .container {
        width: 100%;
        max-width: 100%;
        padding: 0 15px;
        margin: 0 auto;
        display: block;
    }

    /* Hero Section Responsive */
    .hero-about {
        padding: 3rem 1rem !important;
        background-attachment: scroll !important;
        display: block;
        text-align: center;
    }

    .hero-about h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .hero-about p {
        font-size: 1rem !important;
        margin: 1rem auto 2rem !important;
        max-width: 800px;
    }

    .hero-about .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 0.9rem;
        margin: 5px;
    }

    /* Story Section */
    .container>section:first-child>div {
        display: block;
    }

    /* Section Title Responsive */
    .section-title {
        margin-bottom: 2rem !important;
        text-align: center;
    }

    .section-title h2 {
        font-size: 1.5rem;
        display: inline-block;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: var(--secondary);
        margin: 10px auto;
    }

    /* Grid Layouts - Default Mobile (Single Column) */
    .feature-grid,
    .team-grid,
    .stats-grid,
    .awards-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin: 2rem 0;
    }

    /* Feature Cards */
    .feature-card {
        padding: 1.5rem 1rem;
        text-align: center;
        background: white;
        border-radius: 10px;
        box-shadow: var(--shadow);
        transition: transform 0.3s;
    }

    .feature-card:hover {
        transform: translateY(-10px);
    }

    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        color: var(--secondary);
    }

    .feature-card h3 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    /* Team Section */
    .team-card {
        text-align: center;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .team-img {
        width: 100%;
        height: 230px;
        object-fit: cover;
    }

    img{
        height: 300px;
    }

    .team-info {
        padding: 1.5rem;
    }

    /* Stats Section */
    .stats-container {
        background: var(--primary);
        color: white;
        padding: 4rem 0;
        margin: 3rem 0;
    }

    .stats-grid>div {
        text-align: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--secondary);
    }

    /* Timeline */
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 3rem auto;
    }

    .timeline:before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--secondary);
        transform: translateX(-50%);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 3rem;
    }

    .timeline-content {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: var(--shadow);
        width: 45%;
    }

    .timeline-item:nth-child(odd) .timeline-content {
        margin-left: auto;
    }

    .timeline-item:nth-child(even) .timeline-content {
        margin-right: auto;
    }

    .timeline-dot {
        position: absolute;
        left: 50%;
        top: 0;
        width: 20px;
        height: 20px;
        background: var(--secondary);
        border-radius: 50%;
        transform: translateX(-50%);
    }

    /* Awards */
    .award-card {
        text-align: center;
    }

    .award-icon {
        font-size: 2rem;
        color: gold;
        margin-bottom: 1rem;
    }

    /* Testimonial */
    .testimonial-slider {
        max-width: 800px;
        margin: 3rem auto;
    }

    .testimonial-card {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: var(--shadow);
        text-align: center;
    }

    /* ============ RESPONSIVE BREAKPOINTS ============ */

    /* Small Tablets (481px - 600px) */
    @media (min-width: 481px) {
        .feature-grid,
        .awards-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .team-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tablets (601px - 768px) */
    @media (min-width: 601px) {
        .container {
            max-width: 95%;
            padding: 0 20px;
        }

        .hero-about {
            padding: 4rem 2rem !important;
        }

        .container>section:first-child>div {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .feature-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Laptops (769px - 1024px) - KEEP SINGLE COLUMN FOR ROWS */
    @media (min-width: 769px) and (max-width: 1024px) {
        .container {
            max-width: 90%;
        }

        /* On laptops, show grids in rows (single column) */
        .feature-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 columns for feature grid only */
            grid-template-rows: auto;
        }

        .team-grid {
            grid-template-columns: 1fr; /* Single column for team */
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 columns for stats */
            grid-template-rows: repeat(2, 1fr);
        }

        .awards-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 columns for awards */
            grid-template-rows: repeat(2, 1fr);
        }

        /* Timeline stays centered */
        .timeline-content {
            width: 45%;
        }
    }

    /* Desktop (1025px - 1366px) - Show columns */
    @media (min-width: 1025px) {
        .container {
            max-width: 1200px;
            padding: 0 20px;
        }

        /* On desktop, show multiple columns */
        .feature-grid {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: 1fr;
        }

        .team-grid {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: 1fr;
        }

        .awards-grid {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: 1fr;
        }

        .hero-about {
            padding: 5rem 0 !important;
        }
    }

    /* Extra Large Screens (1367px+) */
    @media (min-width: 1367px) {
        .container {
            max-width: 1400px;
        }

        .feature-grid {
            gap: 2rem;
        }
    }

    /* Mobile (320px - 480px) */
    @media (max-width: 480px) {
        .container {
            padding: 0 5px;
        }

        .stats-grid{
            height: 310px;
        }

        .hero-about {
            padding: 2rem 0.5rem !important;
        }

        .hero-about h1 {
            font-size: 1.75rem;
        }

        .hero-about .btn {
            display: block;
            width: 90%;
            margin: 10px auto;
            max-width: 250px;
        }

        /* Timeline for mobile */
        .timeline:before {
            left: 30px;
        }

        .timeline-content {
            width: calc(100% - 60px);
            margin-left: 60px !important;
            margin-right: 0 !important;
        }

        .timeline-dot {
            left: 30px;
        }
        .image-box img{
            height: 10rem;
            width: 15rem;
            object-fit: cover;
        }
    }

    /* Touch Device Optimization */
    @media (hover: none) and (pointer: coarse) {
        .feature-card:hover {
            transform: none;
        }

        .btn {
            min-height: 44px;
            line-height: 44px;
        }
    }

    /* Print Styles */
    @media print {
        .hero-about {
            background: none !important;
            color: black !important;
            padding: 1rem 0 !important;
        }

        .btn {
            display: none !important;
        }

        .container>section:last-child {
            background: none !important;
            color: black !important;
            border: 1px solid #000;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .feature-card {
            transition: none !important;
        }

        .feature-card:hover {
            transform: none !important;
        }
    }
</style>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container" >
            <h1>About Smart Hotel</h1>
            <p style="max-width: 800px; margin: 1rem auto 2rem; font-size: 1.2rem;">
                Experience luxury and comfort at its finest. Since 2010, we've been providing exceptional hospitality 
                services to travelers from around the world.
            </p>
            <a href="contact.php" class="btn" style="background-color :aquamarine; color:#000">Contact Us</a>
        </div>
    </section>
    
    <!-- About Content -->
    <div class="container" style="padding: -40rem 0;">
        <!-- Introduction -->
        <section>
            <div class="section-title">
                <h2>Our Story</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; align-items: center;">
                <div>
                    <h3>Welcome to Smart Hotel</h3>
                    <p style="margin: 1rem 0; line-height: 1.8;">
                        Founded in 2010 by Mr. Rajesh Sharma, Smart Hotel started as a small 10-room establishment 
                        in the heart of Patna. Today, we have grown into one of the most prestigious hotel chains 
                        in India, with properties in 5 major cities.
                    </p>
                    <p style="margin: 1rem 0; line-height: 1.8;">
                        Our mission is to provide exceptional hospitality experiences that exceed guest expectations. 
                        We believe in creating memorable stays through personalized service, luxurious accommodations, 
                        and attention to detail.
                    </p>
                    <p style="margin: 1rem 0; line-height: 1.8;">
                        With over 16 years of experience in the hospitality industry, we understand what makes a 
                        perfect stay. From business travelers to vacationing families, we cater to all with the 
                        same level of dedication and excellence.
                    </p>
                </div>
                
                <div class="image-box">
                    <img src="assets/images/hotel-building.jpg" alt="Smart Hotel" style="background-size: contain;"/>
                </div>
            </div>
        </section>
        
        <!-- Stats Section -->
        <section class="stats-container">
            <div class="container">
                <div class="stats-grid" >
                    <div>
                        <div class="stat-number" style="padding-top:-100px">16+</div>
                        <h4>Years of Excellence</h4>
                    </div>
                    
                    <div>
                        <div class="stat-number">100+</div>
                        <h4>Rooms Across India</h4>
                    </div>
                    
                    <div>
                        <div class="stat-number">10K+</div>
                        <h4>Happy Guests</h4>
                    </div>
                    
                    <div>
                        <div class="stat-number">150+</div>
                        <h4>Dedicated Staff</h4>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Our Values -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>Our Core Values</h2>
            </div>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Hospitality</h3>
                    <p>Treating every guest like family with warm, personalized service.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Excellence</h3>
                    <p>Maintaining the highest standards in service and facilities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Integrity</h3>
                    <p>Conducting business with honesty and transparency.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Sustainability</h3>
                    <p>Implementing eco-friendly practices for a better future.</p>
                </div>
            </div>
        </section>
        
        <!-- Timeline -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>Our Journey</h2>
            </div>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2010</h3>
                        <p>Smart Hotel founded in Patna with 10 rooms</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2015</h3>
                        <p>Expanded to 50 rooms and added conference facilities</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2018</h3>
                        <p>Opened second property in Deoghar </p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2015</h3>
                        <p>Launched online booking system</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2020</h3>
                        <p>Implemented contactless check-in and digital services</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3>2023</h3>
                        <p>5th property opened in Ranchi</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Team Section -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>Meet Our Leadership</h2>
            </div>
            
            <div class="team-grid">
                <div class="team-card">
                    <img src="assets/images/team1.jpg" alt="CEO" class="team-img">
                    <div class="team-info">
                        <h3>Rajesh Sharma</h3>
                        <p style="color: var(--secondary); margin-bottom: 1rem;">Founder & CEO</p>
                        <p>16+ years in hospitality industry</p>
                    </div>
                </div>
                
                <div class="team-card">
                    <img src="assets/images/team2.jpg" alt="Manager" class="team-img">
                    <div class="team-info">
                        <h3>Priya Patel</h3>
                        <p style="color: var(--secondary); margin-bottom: 1rem;">General Manager</p>
                        <p>10 years of hotel management experience</p>
                    </div>
                </div>
                
                <div class="team-card">
                    <img src="assets/images/team3.jpg" alt="Chef" class="team-img">
                    <div class="team-info">
                        <h3>Vikram Singh</h3>
                        <p style="color: var(--secondary); margin-bottom: 1rem;">Executive Chef</p>
                        <p>Award-winning culinary expert</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Awards -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>Awards & Recognition</h2>
            </div>
            
            <div class="awards-grid">
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Best Luxury Hotel 2022</h4>
                    <p>Hospitality Awards</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>5-Star Rating</h4>
                    <p>Ministry of Tourism</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4>Eco-Friendly Hotel 2021</h4>
                    <p>Green Hospitality Awards</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4>Customer Service Excellence</h4>
                    <p>Travel & Leisure Magazine</p>
                </div>
            </div>
        </section>
        
        <!-- Testimonials -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>What Our Guests Say</h2>
            </div>
            
            <div class="testimonial-slider">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Absolutely wonderful experience! The staff went above and beyond to make our stay memorable. 
                        The rooms were luxurious and the food was exceptional. Highly recommended!"
                    </div>
                    <div class="testimonial-author">- Amit Verma, Business Traveler</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="#" class="btn" style="margin-right: 10px;"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="btn"><i class="fas fa-chevron-right"></i></a>
            </div>
        </section>
        
        <!-- Facilities -->
        <section style="margin: 4rem 0;">
            <div class="section-title">
                <h2>Our Facilities</h2>
            </div>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h3>Swimming Pool</h3>
                    <p>Temperature-controlled outdoor pool</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Fine Dining</h3>
                    <p>Multi-cuisine restaurant & bar</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3>Spa & Wellness</h3>
                    <p>Full-service spa and fitness center</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h3>High-Speed WiFi</h3>
                    <p>Free high-speed internet access</p>
                </div>
            </div>
        </section>
        
        <!-- Call to Action -->
        <section style="background: linear-gradient(135deg, var(--primary), #1a252f); 
                       color: white; padding: 4rem; border-radius: 10px; text-align: center;">
            <h2 style="color: white;">Experience Luxury With Us</h2>
            <p style="max-width: 600px; margin: 1rem auto 2rem;">
                Book your stay at Grand Hotel and experience world-class hospitality in the heart of the city.
            </p>
            <a href="auth/register.php" class="btn" style="background: white; color: var(--primary);">
                Book Your Stay Now
            </a>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
    // Testimonial slider functionality
    document.addEventListener('DOMContentLoaded', function() {
        const testimonials = [
            {
                text: "Absolutely wonderful experience! The staff went above and beyond to make our stay memorable. The rooms were luxurious and the food was exceptional. Highly recommended!",
                author: "- Amit Verma, Business Traveler"
            },
            {
                text: "Perfect location, excellent service, and beautiful rooms. The hotel staff was extremely helpful throughout our stay. Will definitely return!",
                author: "- Priya Sharma, Family Vacation"
            },
            {
                text: "As a business traveler, I appreciate the excellent WiFi, comfortable workspace, and efficient service. The conference facilities were top-notch.",
                author: "- Rohit Mehta, Corporate Client"
            }
        ];
        
        let currentTestimonial = 0;
        const testimonialText = document.querySelector('.testimonial-text');
        const testimonialAuthor = document.querySelector('.testimonial-author');
        const prevBtn = document.querySelector('.fa-chevron-left').parentElement;
        const nextBtn = document.querySelector('.fa-chevron-right').parentElement;
        
        function updateTestimonial() {
            testimonialText.textContent = testimonials[currentTestimonial].text;
            testimonialAuthor.textContent = testimonials[currentTestimonial].author;
        }
        
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
            updateTestimonial();
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            updateTestimonial();
        });
        
        // Auto-rotate testimonials every 5 seconds
        setInterval(function() {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            updateTestimonial();
        }, 5000);
    });
    </script>
</body>
</html>