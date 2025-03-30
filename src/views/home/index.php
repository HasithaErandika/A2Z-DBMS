<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Advanced DBMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a2639;
            --secondary-color: #00aaff;
            --accent-color: #ff3366;
            --text-color: #2c3e50;
            --light-bg: #f5f7fa;
            --dark-bg: #1a2639;
            --gradient: linear-gradient(135deg, #00aaff, #0066cc);
            --glass-bg: rgba(255, 255, 255, 0.1);
        }

        * {
            scroll-behavior: smooth;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            line-height: 1.7;
            background: var(--light-bg);
            overflow-x: hidden;
            position: relative;
        }

        /* Background particles effect */
        #particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle, rgba(0,170,255,0.1) 0%, rgba(255,255,255,0) 70%);
        }

        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1.2rem 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .navbar.scrolled {
            padding: 0.8rem 0;
            background: rgba(255,255,255,0.95);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 900;
            color: var(--primary-color);
            font-size: 1.8rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: var(--secondary-color);
            transform: scale(1.05);
        }

        .nav-link {
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.7rem 1.2rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient);
            transform: translateX(-100%);
            transition: transform 0.4s ease;
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }

        .hero-section {
            background: linear-gradient(45deg, rgba(0,0,0,0.8), rgba(0,170,255,0.2)), url('https://a2zengineering.lk/wp-content/uploads/2023/03/Solar_4.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 180px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0.15;
            mix-blend-mode: overlay;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            animation: slideInLeft 1s ease-out;
        }

        .hero-section p {
            font-size: 1.4rem;
            max-width: 600px;
            margin: 0 auto 2.5rem;
            animation: slideInRight 1s ease-out 0.3s;
            animation-fill-mode: both;
        }

        .btn-primary {
            background: var(--gradient);
            border: none;
            padding: 1.2rem 3rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,170,255,0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0,170,255,0.4);
        }

        .section {
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        .section-title {
            text-align: center;
            margin-bottom: 5rem;
            position: relative;
        }

        .section-title h2 {
            color: var(--primary-color);
            font-size: 3rem;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }

        .section-title::before {
            content: attr(data-text);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(0,170,255,0.1);
            font-size: 6rem;
            font-weight: 900;
            z-index: 0;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,170,255,0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: var(--gradient);
            opacity: 0;
            transform: rotate(30deg);
            transition: all 0.5s ease;
        }

        .service-card:hover::before {
            opacity: 0.05;
            top: -20%;
            left: -20%;
        }

        .service-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            margin-bottom: 2.5rem;
            position: relative;
            transition: all 0.5s ease;
        }

        .service-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.2), transparent);
            transform: translate(-50%, -50%) scale(0);
            transition: all 0.4s ease;
        }

        .service-card:hover .service-icon::after {
            transform: translate(-50%, -50%) scale(1);
        }

        .contact-info {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            padding: 3.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.4s ease;
        }

        .contact-info:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .contact-info i {
            color: var(--secondary-color);
            margin-right: 1.5rem;
            transition: all 0.3s ease;
        }

        .contact-info div {
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-info div:hover {
            background: rgba(0,170,255,0.05);
        }

        .contact-info div:hover i {
            transform: scale(1.3) rotate(5deg);
        }

        footer {
            background: var(--dark-bg);
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0,170,255,0.1), transparent);
            transform: rotate(45deg);
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2.8rem; }
            .hero-section p { font-size: 1.1rem; }
            .section { padding: 80px 0; }
            .section-title h2 { font-size: 2.2rem; }
            .service-card { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div id="particles"></div>

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
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/about-us-2/">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/services/">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/contact/">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>/login">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1>A2Z Engineering DBMS</h1>
            <p>Next-generation database solutions for cutting-edge engineering management</p>
            <a href="<?php echo BASE_PATH; ?>/login" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Enter System
            </a>
        </div>
    </section>

    <section id="about" class="section">
        <div class="container">
            <div class="section-title" data-text="About">
                <h2>About Our System</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p>Our advanced DBMS offers:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>AI-powered project analytics</li>
                        <li><i class="fas fa-check text-success me-2"></i>Blockchain-level security</li>
                        <li><i class="fas fa-check text-success me-2"></i>Real-time collaboration tools</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <p>Elevate your engineering operations with our state-of-the-art database management system, built for the future of A2Z Engineering.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="section bg-light">
        <div class="container">
            <div class="section-title" data-text="Features">
                <h2>System Features</h2>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-bolt"></i></div>
                        <h3>Smart Management</h3>
                        <p>AI-driven project tracking and resource allocation.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-database"></i></div>
                        <h3>Advanced Analytics</h3>
                        <p>Predictive insights and performance metrics.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Next-Gen Security</h3>
                        <p>Quantum-ready encryption protocols.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-title" data-text="Support">
                <h2>Contact Support</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="contact-info">
                        <div class="mb-4"><i class="fas fa-map-marker-alt"></i><span>123 Engineering Street, Colombo, Sri Lanka</span></div>
                        <div class="mb-4"><i class="fas fa-phone"></i><span>+94 11 234 5678</span></div>
                        <div class="mb-4"><i class="fas fa-envelope"></i><span>support@a2zengineering.lk</span></div>
                        <div><i class="fas fa-clock"></i><span>Mon - Fri: 9:00 AM - 6:00 PM</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container position-relative">
            <div class="row">
                <div class="col-md-6">
                    <h5>A2Z Engineering DBMS</h5>
                    <p>Engineering the future of data management</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>© <?php echo date('Y'); ?> A2Z Engineering. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Advanced smooth scrolling with offset
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
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

        // Intersection Observer for fade-in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.service-card, .contact-info').forEach(el => {
            observer.observe(el);
        });

        // Simple particle effect
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 3 + 1}px;
                height: ${Math.random() * 3 + 1}px;
                background: rgba(0,170,255,${Math.random() * 0.3 + 0.1});
                border-radius: 50%;
                pointer-events: none;
                animation: particleMove ${Math.random() * 5 + 3}s linear infinite;
            `;
            particle.style.left = Math.random() * 100 + 'vw';
            particle.style.top = Math.random() * 100 + 'vh';
            document.getElementById('particles').appendChild(particle);
            
            setTimeout(() => particle.remove(), 8000);
        }

        setInterval(createParticle, 200);

    </script>
    <style>
        @keyframes particleMove {
            0% { transform: translate(0, 0); opacity: 1; }
            100% { transform: translate(${Math.random() * 100 - 50}px, ${Math.random() * 100 - 50}px); opacity: 0; }
        }

        .animate__animated {
            animation-duration: 1s;
        }

        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
    </style>
</body>
</html>