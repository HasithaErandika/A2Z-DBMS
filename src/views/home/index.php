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

    <nav class="navbar navbar-expand-lg" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fas fa-solar-panel" aria-hidden="true"></i> A2Z Engineering
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/about-us-2/">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/services/">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://a2zengineering.lk/contact/">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1>A2Z Engineering DBMS</h1>
            <p>Next-generation database solutions for solar, AC, and electrical power management</p>
            <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Enter System
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
                        <li><i class="fas fa-check text-success me-2" aria-hidden="true"></i> AI-powered project analytics</li>
                        <li><i class="fas fa-check text-success me-2" aria-hidden="true"></i> Blockchain-level security</li>
                        <li><i class="fas fa-check text-success me-2" aria-hidden="true"></i> Real-time collaboration tools</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <p>Elevate your engineering operations with our state-of-the-art database management system, designed for the future of solar, AC, and electrical power solutions.</p>
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
                        <div class="service-icon"><i class="fas fa-bolt" aria-hidden="true"></i></div>
                        <h3>Smart Management</h3>
                        <p>AI-driven project tracking and resource allocation.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-database" aria-hidden="true"></i></div>
                        <h3>Advanced Analytics</h3>
                        <p>Predictive insights and performance metrics.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
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
                        <div><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span>116/E/1, Pitumpe, Padukka, Sri Lanka</span></div>
                        <div><i class="fas fa-phone" aria-hidden="true"></i><span>+94 11 234 5678</span></div>
                        <div><i class="fas fa-envelope" aria-hidden="true"></i><span>support@a2zengineering.lk</span></div>
                        <div><i class="fas fa-clock" aria-hidden="true"></i><span>Mon - Fri: 9:00 AM - 6:00 PM</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

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

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.service-card, .contact-info').forEach(el => {
            observer.observe(el);
        });

        function createParticle() {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 3 + 1}px;
                height: ${Math.random() * 3 + 1}px;
                background: rgba(245, 158, 11, ${Math.random() * 0.3 + 0.1});
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
</body>
</html>