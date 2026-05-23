<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* ===== MODERN FOOTER CSS ===== */
    /* ===== MODERN FOOTER CSS - WHITE BACKGROUND ===== */

    .footer {
        background: #ffffff;
        /* WHITE BACKGROUND */
        color: #333333;
        /* Dark text for white background */
        padding: 60px 0 20px;
        margin-top: auto;
        border-top: 2px solid #f0f0f0;
        /* Light gray border */
        position: relative;
        overflow: hidden;
        box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.05);
        /* Subtle shadow on top */
    }

    .footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(90deg, #3a86ff, #8338ec, #ff006e);
    z-index: 1;
}


    .container {
        max-width: 1200px;
        margin: 10px auto;
        padding: 0 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-section {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    .footer-section:nth-child(1) {
        animation-delay: 0.1s;
    }

    .footer-section:nth-child(2) {
        animation-delay: 0.2s;
    }

    .footer-section:nth-child(3) {
        animation-delay: 0.3s;
    }

    .footer-section:nth-child(4) {
        animation-delay: 0.4s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .footer-section h3 {
        color: #000000;
        /* Black headings */
        font-size: 1.4rem;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
        font-weight: 600;
    }

    .footer-section h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, #3a86ff, #8338ec);
        border-radius: 2px;
    }

    .footer-section p {
        color: #666666;
        /* Gray text */
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    /* Social Icons - Dark theme for white background */
    .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        /* Light gray background */
        border-radius: 50%;
        color: #333333;
        /* Dark gray icons */
        font-size: 1.2rem;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 1px solid #e0e0e0;
        /* Light border */
    }

    .social-icons a:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, #3a86ff, #8338ec);
        color: #ffffff;
        /* White icons on hover */
        box-shadow: 0 5px 15px rgba(58, 134, 255, 0.2);
        border-color: transparent;
    }

    /* Quick Links - Dark text */
    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section ul li {
        margin-bottom: 12px;
    }

    .footer-section ul li a {
        color: #555555;
        /* Dark gray links */
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
    }

    .footer-section ul li a::before {
        content: '→';
        color: #3a86ff;
        /* Blue arrow */
        font-weight: bold;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: #000000;
        /* Black on hover */
        transform: translateX(5px);
    }

    .footer-section ul li a:hover::before {
        opacity: 1;
        transform: translateX(0);
    }

    /* Contact Info */
    .footer-section p i {
        color: #3a86ff;
        /* Blue icons */
        margin-right: 10px;
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }

    .footer-section p {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    /* Payment Methods - Dark icons */
    .payment-methods {
        display: flex;
        gap: 15px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .payment-methods i {
        font-size: 2.2rem;
        color: #666666;
        /* Gray icons */
        transition: all 0.3s ease;
        padding: 8px;
        background: #f8f9fa;
        /* Light background */
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        /* Light border */
    }

    .payment-methods i:hover {
        transform: translateY(-3px);
        background: #ffffff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .payment-methods .fa-cc-visa:hover {
        color: #1a1f71;
    }

    .payment-methods .fa-cc-mastercard:hover {
        color: #eb001b;
    }

    .payment-methods .fa-cc-paypal:hover {
        color: #003087;
    }

    .payment-methods .fa-rupee-sign:hover {
        color: #FFD700;
    }

    /* Footer Bottom - Light gray background */
    .footer-bottom {
        padding: 25px 0 10px;
        border-top: 1px solid #f0f0f0;
        /* Light border */
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8f9fa;
        /* Light gray background */
        border-radius: 10px;
        margin-top: 30px;
    }

    .footer-bottom p {
        color: #666666;
        /* Gray text */
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    .footer-bottom p:first-child {
        font-size: 1rem;
        color: #333333;
        /* Darker text */
        font-weight: 500;
    }

    .footer-bottom p:last-child {
        color: #3a86ff;
        /* Blue text */
        font-style: italic;
        font-size: 0.85rem;
    }

    /* Back to Top Button - Blue gradient */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3a86ff, #8338ec);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        text-decoration: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 100;
        box-shadow: 0 4px 15px rgba(58, 134, 255, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .back-to-top.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(58, 134, 255, 0.4);
    }

    /* ===== RESPONSIVE DESIGN ===== */

    /* Tablet */
    @media (max-width: 992px) {
        .footer-content {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .footer {
            padding: 50px 0 20px;
        }

        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }

    /* Mobile */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
            text-align: center;
        }

        .footer-section h3::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .social-icons {
            justify-content: center;
        }

        .footer-section p {
            justify-content: center;
            text-align: center;
            flex-direction: column;
            gap: 5px;
        }

        .footer-section p i {
            margin-right: 0;
            margin-bottom: 5px;
        }

        .footer-section ul li a {
            justify-content: center;
        }

        .payment-methods {
            justify-content: center;
        }

        .back-to-top {
            bottom: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .footer-bottom {
            padding: 20px 15px 10px;
        }
    }

    /* Small Mobile */
    @media (max-width: 480px) {
        .footer {
            padding: 40px 0 20px;
        }

        .container {
            padding: 0 15px;
        }

        .footer-section h3 {
            font-size: 1.2rem;
        }

        .social-icons a {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .payment-methods i {
            font-size: 1.8rem;
            padding: 6px;
        }

        .footer-bottom p {
            font-size: 0.85rem;
        }
    }

    /* Print Styles */
    @media print {
        .footer {
            background: #ffffff !important;
            color: #000000 !important;
            border-top: 2px solid #000000;
        }

        .footer-section h3 {
            color: #000000 !important;
        }

        .footer-section p,
        .footer-section ul li a {
            color: #333333 !important;
        }

        .social-icons,
        .back-to-top {
            display: none;
        }
    }

    /* Light Mode Support */
    @media (prefers-color-scheme: light) {
        .footer {
            background: #ffffff;
            color: #333333;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {

        .footer-section,
        .social-icons a,
        .footer-section ul li a,
        .payment-methods i,
        .back-to-top {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Hover Effects Enhancement */
    .footer-section ul li a,
    .social-icons a,
    .payment-methods i {
        position: relative;
        overflow: hidden;
    }

    .footer-section ul li a::after,
    .social-icons a::after,
    .payment-methods i::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(58, 134, 255, 0.2);
        opacity: 0;
        border-radius: 50%;
        transform: scale(1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .footer-section ul li a:active::after,
    .social-icons a:active::after,
    .payment-methods i:active::after {
        animation: ripple 0.6s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 0.5;
        }

        100% {
            transform: scale(20);
            opacity: 0;
        }
    }

    /* Custom Scrollbar for Footer */
    .footer ::-webkit-scrollbar {
        width: 6px;
    }

    .footer ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    .footer ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3a86ff, #8338ec);
        border-radius: 3px;
    }

    .footer ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #9555ef, #ff006e);
    }
</style>

<body>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Smart Hotel</h3>
                    <p>Luxury accommodation with world-class amenities. Your comfort is our priority.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Smart Hotel Patna, Bihar, India</p>
                    <p><i class="fas fa-phone"></i> +91 9876543210</p>
                    <p><i class="fas fa-envelope"></i> info@smarthotel.com</p>
                </div>

                <div class="footer-section">
                    <h3>Payment Methods</h3>
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-paypal"></i>
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?>Smart Hotel Management. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/booking.js"></script>

    <script>
        // Back to Top Button
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.createElement('a');
            backToTopBtn.href = '#';
            backToTopBtn.className = 'back-to-top';
            backToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
            backToTopBtn.title = 'Back to Top';
            document.body.appendChild(backToTopBtn);

            // Show/hide button on scroll
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            });

            // Smooth scroll to top
            backToTopBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Smooth scroll for footer links
            document.querySelectorAll('.footer-section a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>