<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/A2Z-DBMS');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering - Advanced Database Management System for Solar, AC, and Electrical Power Solutions">
    <title>A2Z Engineering - Advanced DBMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/index.css">
</head>
<body>
    <div id="particles"></div>
    <div id="floating-shapes"></div>

    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="brand-icon">
                    <i class="fas fa-solar-panel" aria-hidden="true"></i>
                </div>
                <span class="brand-text">A2Z Engineering</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="fas fa-info-circle me-1"></i>About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">
                            <i class="fas fa-cogs me-1"></i>Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-cta" href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Enhanced Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-background-pattern"></div>
        <div class="container hero-content text-center">
            <div class="hero-badge">
                <i class="fas fa-database"></i>
                Internal Database System
            </div>
            <h1 class="hero-title">
                <span class="title-line">A2Z Engineering</span>
                <span class="title-line">Database Management</span>
            </h1>
            <p class="hero-subtitle">
                Streamline your internal operations with our comprehensive database management system. 
                Manage employees, track projects, and maintain company records efficiently.
            </p>
            <div class="hero-features">
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Employee Management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-project-diagram"></i>
                    <span>Project Tracking</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Performance Analytics</span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-hero">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                    <span>Enter System</span>
                </a>
                <a href="#about" class="btn btn-outline btn-hero">
                    <i class="fas fa-arrow-down" aria-hidden="true"></i> 
                    <span>Learn More</span>
                </a>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="scroll-arrow"></div>
        </div>
    </section>

    <!-- Enhanced About Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-info-circle"></i>
                    About Our System
                </div>
                <h2 class="section-title">Internal Database Management</h2>
                <p class="section-subtitle">
                    Our internal database system is designed to streamline company operations and improve productivity
                </p>
            </div>
            <div class="about-content">
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Secure & Private</h4>
                            <p>Internal company data stays within your organization with enterprise-grade security</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Real-time Updates</h4>
                            <p>Instant synchronization across all departments and team members</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Mobile Access</h4>
                            <p>Access company data from anywhere, on any device</p>
                        </div>
                    </div>
                </div>
                <div class="about-visual">
                    <div class="visual-card">
                        <div class="card-header">
                            <h4>System Statistics</h4>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number">500+</span>
                                <span class="stat-label">Employees</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">50+</span>
                                <span class="stat-label">Projects</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">99.9%</span>
                                <span class="stat-label">Uptime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Services Section -->
    <section id="services" class="section services-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-cogs"></i>
                    System Features
                </div>
                <h2 class="section-title">What Our System Provides</h2>
                <p class="section-subtitle">
                    Comprehensive tools for managing internal company operations
                </p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="card-header">
                            <div class="service-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-badge">Core</div>
                        </div>
                        <div class="card-body">
                            <h4>Employee Management</h4>
                            <p>Comprehensive employee database with profiles, roles, and performance tracking</p>
                            <div class="feature-list-mini">
                                <span><i class="fas fa-check"></i> Employee Profiles</span>
                                <span><i class="fas fa-check"></i> Role Management</span>
                                <span><i class="fas fa-check"></i> Performance Reviews</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card featured">
                        <div class="card-header">
                            <div class="service-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="card-badge">Featured</div>
                        </div>
                        <div class="card-body">
                            <h4>Project Management</h4>
                            <p>Track project progress, assign tasks, and monitor deadlines across teams</p>
                            <div class="feature-list-mini">
                                <span><i class="fas fa-check"></i> Task Assignment</span>
                                <span><i class="fas fa-check"></i> Progress Tracking</span>
                                <span><i class="fas fa-check"></i> Resource Allocation</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="card-header">
                            <div class="service-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="card-badge">Analytics</div>
                        </div>
                        <div class="card-body">
                            <h4>Reporting & Analytics</h4>
                            <p>Generate insights with comprehensive reports and performance analytics</p>
                            <div class="feature-list-mini">
                                <span><i class="fas fa-check"></i> Custom Reports</span>
                                <span><i class="fas fa-check"></i> Performance Metrics</span>
                                <span><i class="fas fa-check"></i> Data Visualization</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-envelope"></i>
                    Get Support
                </div>
                <h2 class="section-title">Need Help?</h2>
                <p class="section-subtitle">
                    Contact our IT support team for assistance with the database system
                </p>
            </div>
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="contact-details">
                            <h5>IT Support</h5>
                            <span>support@a2zengineering.com</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Help Desk</h5>
                            <span>Ext. 1234</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Support Hours</h5>
                            <span>Mon-Fri 8AM-6PM</span>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control">
                                <option>Select Issue Type</option>
                                <option>Login Problem</option>
                                <option>Data Entry Issue</option>
                                <option>Report Generation</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="4" placeholder="Describe your issue..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-brand">
                            <h5><i class="fas fa-database"></i> A2Z Engineering</h5>
                            <p>Internal Database Management System for streamlined company operations and improved productivity.</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6>Quick Links</h6>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="#"><i class="fas fa-table"></i> Data Tables</a></li>
                            <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h6>System Features</h6>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-users"></i> Employee Management</a></li>
                            <li><a href="#"><i class="fas fa-project-diagram"></i> Project Tracking</a></li>
                            <li><a href="#"><i class="fas fa-chart-line"></i> Performance Analytics</a></li>
                            <li><a href="#"><i class="fas fa-shield-alt"></i> Security & Access</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h6>Connect With Us</h6>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fas fa-envelope"></i></a>
                            <a href="#" class="social-link"><i class="fas fa-phone"></i></a>
                            <a href="#" class="social-link"><i class="fas fa-headset"></i></a>
                        </div>
                        <p class="mt-3">Internal IT Support<br>Ext. 1234</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p>&copy; 2024 A2Z Engineering. Internal Database System - Company Use Only.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>Version 2.1.0 | Last Updated: <?php echo date('M Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        // Enhanced scroll effects
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('.nav-link[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Enhanced intersection observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.service-card, .contact-info, .feature-item, .about-visual').forEach(el => {
            observer.observe(el);
        });

        // Enhanced particle system
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: linear-gradient(45deg, rgba(245, 158, 11, ${Math.random() * 0.4 + 0.2}), rgba(16, 185, 129, ${Math.random() * 0.4 + 0.2}));
                border-radius: 50%;
                pointer-events: none;
                animation: particleMove ${Math.random() * 6 + 4}s linear infinite;
                box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
            `;
            particle.style.left = Math.random() * 100 + 'vw';
            particle.style.top = Math.random() * 100 + 'vh';
            document.getElementById('particles').appendChild(particle);
            setTimeout(() => particle.remove(), 10000);
        }

        // Floating shapes animation
        function createFloatingShape() {
            const shape = document.createElement('div');
            const shapes = ['circle', 'square', 'triangle'];
            const randomShape = shapes[Math.floor(Math.random() * shapes.length)];
            shape.className = `floating-shape ${randomShape}`;
            shape.style.cssText = `
                position: absolute;
                opacity: ${Math.random() * 0.1 + 0.05};
                animation: float ${Math.random() * 20 + 15}s linear infinite;
                left: ${Math.random() * 100}vw;
                top: ${Math.random() * 100}vh;
            `;
            document.getElementById('floating-shapes').appendChild(shape);
            setTimeout(() => shape.remove(), 25000);
        }

        setInterval(createParticle, 300);
        setInterval(createFloatingShape, 5000);

        // Enhanced button interactions
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-background-pattern');
            if (parallax) {
                parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>