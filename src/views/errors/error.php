<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $errorCode; ?> - A2Z Engineering DBMS</title>
    <meta name="description" content="Error page for A2Z Engineering DBMS">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/error.css">
</head>
<body>
    <!-- Background Elements -->
    <div class="background-pattern"></div>
    <div class="floating-shapes">
        <div class="floating-shape circle" style="top: 10%; left: 10%; animation-delay: 0s;"></div>
        <div class="floating-shape square" style="top: 20%; right: 15%; animation-delay: 2s;"></div>
        <div class="floating-shape triangle" style="bottom: 20%; left: 20%; animation-delay: 4s;"></div>
        <div class="floating-shape circle" style="bottom: 10%; right: 10%; animation-delay: 6s;"></div>
    </div>

    <!-- Main Error Container -->
    <div class="error-container">
        <!-- Error Header -->
        <div class="error-header">
            <div class="error-code"><?php echo $errorCode; ?></div>
            <h1 class="error-title">
                <?php
                switch($errorCode) {
                    case '404':
                        echo 'Page Not Found';
                        break;
                    case '403':
                        echo 'Access Denied';
                        break;
                    case '500':
                        echo 'System Error';
                        break;
                    default:
                        echo 'System Error';
                }
                ?>
            </h1>
            <p class="error-subtitle">
                <?php
                switch($errorCode) {
                    case '404':
                        echo 'The page you\'re looking for doesn\'t exist in our internal database system.';
                        break;
                    case '403':
                        echo 'You don\'t have permission to access this resource. Please contact your administrator.';
                        break;
                    case '500':
                        echo 'Something went wrong with our internal system. Our IT team has been notified.';
                        break;
                    default:
                        echo 'An unexpected error occurred in our database management system.';
                }
                ?>
            </p>
        </div>

        <!-- Error Message -->
        <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
        <div class="error-message">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Error Details
            </h3>
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
        <?php endif; ?>

        <!-- Error Actions -->
        <div class="error-actions">
            <a href="<?php echo BASE_PATH; ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Go to Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
            <a href="<?php echo BASE_PATH; ?>/auth/login" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i>
                Login Again
            </a>
        </div>

        <!-- Error Details -->
        <div class="error-details">
            <h4>
                <i class="fas fa-lightbulb"></i>
                What you can do:
            </h4>
            <ul>
                <?php
                switch($errorCode) {
                    case '404':
                        echo '<li>Check the URL for typos</li>';
                        echo '<li>Navigate using the main menu</li>';
                        echo '<li>Contact IT support if the issue persists</li>';
                        break;
                    case '403':
                        echo '<li>Verify your user permissions</li>';
                        echo '<li>Contact your department manager</li>';
                        echo '<li>Request access through HR</li>';
                        break;
                    case '500':
                        echo '<li>Try refreshing the page</li>';
                        echo '<li>Clear your browser cache</li>';
                        echo '<li>Contact IT support immediately</li>';
                        break;
                    default:
                        echo '<li>Try refreshing the page</li>';
                        echo '<li>Check your internet connection</li>';
                        echo '<li>Contact IT support for assistance</li>';
                }
                ?>
            </ul>
        </div>

        <!-- Help Section -->
        <div class="help-section">
            <h4>
                <i class="fas fa-question-circle"></i>
                Need Help?
            </h4>
            <p>If you continue to experience issues, our IT support team is here to help. Please provide the error code above when contacting support.</p>
        </div>

        <!-- Contact Information -->
        <div class="contact-info">
            <h4>
                <i class="fas fa-headset"></i>
                Contact IT Support
            </h4>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>support@a2zengineering.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>Ext. 1234 (Help Desk)</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <span>Mon-Fri 8AM-6PM</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Create Support Ticket</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="error-footer">
            <p>This is an internal system error. If you believe this is a mistake, please contact the IT department.</p>
            <p><a href="<?php echo BASE_PATH; ?>">A2Z Engineering Database System</a> | Internal Use Only</p>
        </div>
    </div>

    <script>
        // Enhanced floating shapes animation
        function createFloatingShape() {
            const shapes = ['circle', 'square', 'triangle'];
            const randomShape = shapes[Math.floor(Math.random() * shapes.length)];
            const shape = document.createElement('div');
            
            shape.className = `floating-shape ${randomShape}`;
            shape.style.cssText = `
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 5}s;
                animation-duration: ${Math.random() * 10 + 15}s;
            `;
            
            document.querySelector('.floating-shapes').appendChild(shape);
            
            setTimeout(() => {
                if (shape.parentNode) {
                    shape.remove();
                }
            }, 25000);
        }

        // Create additional floating shapes
        setInterval(createFloatingShape, 3000);

        // Enhanced button interactions
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
            
            btn.addEventListener('mousedown', function() {
                this.style.transform = 'translateY(1px) scale(0.98)';
            });
        });

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to error code
            const errorCode = document.querySelector('.error-code');
            if (errorCode) {
                errorCode.addEventListener('click', function() {
                    this.style.animation = 'pulse 0.5s ease';
                    setTimeout(() => {
                        this.style.animation = 'pulse 2s infinite';
                    }, 500);
                });
            }

            // Add hover effects to contact items
            document.querySelectorAll('.contact-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html> 