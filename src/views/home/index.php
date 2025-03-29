<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Database Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://a2zengineering.lk/wp-content/uploads/2023/03/Solar_4.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .service-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .service-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .contact-section {
            background: var(--light-bg);
        }

        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .contact-info i {
            color: var(--secondary-color);
            margin-right: 1rem;
        }

        footer {
            background: var(--dark-bg);
            color: white;
            padding: 2rem 0;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav-link {
            color: var(--primary-color);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_PATH; ?>">
                <i class="fas fa-database me-2"></i>A2Z Engineering
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to A2Z Engineering</h1>
            <p>Your trusted partner in database management solutions</p>
            <a href="<?php echo BASE_PATH; ?>/login" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Access Database System
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="container">
            <div class="section-title">
                <h2>About Us</h2>
                <p>Your trusted partner in engineering solutions since 2010</p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>A2Z Engineering is a leading provider of comprehensive engineering solutions, specializing in:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Electrical Power and Solar PV Systems</li>
                        <li><i class="fas fa-check text-success me-2"></i>Air Conditioning</li>
                        <li><i class="fas fa-check text-success me-2"></i>Networking & Construction</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <p>With over a decade of experience, we've built a reputation for excellence and reliability in delivering high-quality engineering solutions to our clients.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Comprehensive engineering solutions for your needs</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Electrical Power</h3>
                        <p>Professional electrical power solutions and solar PV systems installation and maintenance.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-snowflake"></i>
                        </div>
                        <h3>Air Conditioning</h3>
                        <p>Expert air conditioning installation, maintenance, and repair services.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h3>Networking</h3>
                        <p>Professional networking and construction services for modern infrastructure.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-title">
                <h2>Contact Us</h2>
                <p>Get in touch with our team</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="contact-info">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Engineering Street, Colombo, Sri Lanka</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-phone"></i>
                            <span>+94 11 234 5678</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope"></i>
                            <span>info@a2zengineering.lk</span>
                        </div>
                        <div>
                            <i class="fas fa-clock"></i>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>A2Z Engineering</h5>
                    <p>Your trusted partner in engineering solutions</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; <?php echo date('Y'); ?> A2Z Engineering. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>