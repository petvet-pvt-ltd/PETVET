<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | PETVET</title>
    <link rel="stylesheet" href="/PETVET/public/css/errors/404.css">
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <!-- Animated Pet Illustration -->
            <div class="pet-illustration">
                <div class="pet-face">
                    <div class="pet-ears">
                        <div class="ear left"></div>
                        <div class="ear right"></div>
                    </div>
                    <div class="pet-head">
                        <div class="pet-eyes">
                            <div class="eye left">
                                <div class="pupil"></div>
                            </div>
                            <div class="eye right">
                                <div class="pupil"></div>
                            </div>
                        </div>
                        <div class="pet-nose"></div>
                        <div class="pet-mouth"></div>
                    </div>
                </div>
                <div class="question-marks">
                    <span class="q-mark q1">?</span>
                    <span class="q-mark q2">?</span>
                    <span class="q-mark q3">?</span>
                </div>
            </div>

            <!-- Error Message -->
            <div class="error-message">
                <h1 class="error-code">404</h1>
                <h2 class="error-title">Oops! This pet has wandered off...</h2>
                <p class="error-description">
                    <?php 
                    $message = $message ?? "The page you're looking for seems to have gone on an adventure. Don't worry, we'll help you find your way back home!";
                    echo htmlspecialchars($message);
                    ?>
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="error-actions">
                <!-- <a href="/PETVET/" class="btn btn-primary">
                    <span class="btn-icon home-icon"></span>
                    Go Home
                </a> -->
                <button onclick="history.back()" class="btn btn-secondary">
                    <span class="btn-icon back-icon"></span>
                    Go Back
                </button>
                <!-- <a href="/PETVET/?module=guest&page=contact" class="btn btn-outline">
                    <span class="btn-icon contact-icon"></span>
                    Contact Support
                </a> -->
            </div>

            <!-- Helpful Links -->
            <!-- <div class="helpful-links">
                <h3>You might be looking for:</h3>
                <div class="links-grid">
                    <a href="/PETVET/?module=guest&page=home" class="link-card">
                        <div class="link-icon clinic-icon"></div>
                        <span>Veterinary Services</span>
                    </a>
                    <a href="/PETVET/?module=guest&page=shop" class="link-card">
                        <div class="link-icon shop-icon"></div>
                        <span>Pet Shop</span>
                    </a>
                    <a href="/PETVET/?module=guest&page=adopt" class="link-card">
                        <div class="link-icon heart-icon"></div>
                        <span>Pet Adoption</span>
                    </a>
                    <a href="/PETVET/?module=guest&page=about" class="link-card">
                        <div class="link-icon info-icon"></div>
                        <span>About Us</span>
                    </a>
                </div>
            </div>
        </div> -->

        <!-- Background Elements -->
        <div class="bg-elements">
            <div class="paw-print paw1"></div>
            <div class="paw-print paw2"></div>
            <div class="paw-print paw3"></div>
            <div class="paw-print paw4"></div>
            <div class="paw-print paw5"></div>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Animated eyes that follow cursor
            const eyes = document.querySelectorAll('.pupil');
            
            document.addEventListener('mousemove', function(e) {
                eyes.forEach(eye => {
                    const rect = eye.getBoundingClientRect();
                    const eyeCenterX = rect.left + rect.width / 2;
                    const eyeCenterY = rect.top + rect.height / 2;
                    
                    const angle = Math.atan2(e.clientY - eyeCenterY, e.clientX - eyeCenterX);
                    const distance = Math.min(3, Math.sqrt(Math.pow(e.clientX - eyeCenterX, 2) + Math.pow(e.clientY - eyeCenterY, 2)) / 10);
                    
                    const x = distance * Math.cos(angle);
                    const y = distance * Math.sin(angle);
                    
                    eye.style.transform = `translate(${x}px, ${y}px)`;
                });
            });

            // Animate question marks
            function animateQuestionMarks() {
                const qMarks = document.querySelectorAll('.q-mark');
                qMarks.forEach((mark, index) => {
                    setTimeout(() => {
                        mark.style.opacity = '1';
                        mark.style.transform = 'translateY(-10px) scale(1.1)';
                        setTimeout(() => {
                            mark.style.opacity = '0.7';
                            mark.style.transform = 'translateY(0) scale(1)';
                        }, 500);
                    }, index * 200);
                });
            }

            // Start question mark animation
            animateQuestionMarks();
            setInterval(animateQuestionMarks, 3000);
        });
    </script>
</body>
</html>