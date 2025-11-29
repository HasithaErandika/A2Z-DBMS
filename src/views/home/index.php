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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-12 w-auto" src="<?php echo BASE_PATH; ?>/src/assets/images/longLogoB.png" alt="A2Z Engineering Logo">
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">About Us</a>
                    <a href="https://a2zengineering.lk/services/" target="_blank" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">Services</a>
                    <a href="https://a2zengineering.lk/contact/" target="_blank" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">Contact</a>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#about" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">About Us</a>
                <a href="https://a2zengineering.lk/services/" target="_blank" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Services</a>
                <a href="https://a2zengineering.lk/contact/" target="_blank" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Contact</a>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 hover:bg-blue-700">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-20 pb-16 md:pt-32 md:pb-24 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                        <i class="fas fa-database mr-2"></i>Internal Database System
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        <span class="block">A2Z Engineering</span>
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-700">Database Management</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-lg">
                        Streamline your internal operations with our comprehensive database management system. 
                        Manage employees, track projects, and maintain company records efficiently.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-300 shadow-lg hover:shadow-xl text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Enter System
                        </a>
                        <a href="#about" class="bg-white text-blue-700 border-2 border-blue-600 px-8 py-4 rounded-lg font-medium hover:bg-blue-50 transition-all duration-300 shadow hover:shadow-md text-center">
                            <i class="fas fa-arrow-down mr-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl w-80 h-80 md:w-96 md:h-96 flex items-center justify-center shadow-2xl">
                            <div class="bg-white bg-opacity-20 rounded-full w-64 h-64 md:w-80 md:h-80 flex items-center justify-center">
                                <div class="bg-white bg-opacity-30 rounded-full w-48 h-48 md:w-64 md:h-64 flex items-center justify-center">
                                    <i class="fas fa-database text-white text-6xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 bg-yellow-400 rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-3xl"></i>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-green-500 rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">About A2Z Engineering</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-indigo-700 mx-auto"></div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Our Company</h3>
                        <p class="text-gray-600 mb-6">
                            A2Z Engineering is a leading provider of comprehensive engineering solutions in Sri Lanka, 
                            specializing in solar power systems, air conditioning, and electrical power solutions. 
                            With years of experience and a team of certified professionals, we deliver innovative and 
                            sustainable solutions to meet the diverse needs of our clients.
                        </p>
                        <p class="text-gray-600 mb-6">
                            Our internal database management system is designed to streamline operations, 
                            enhance productivity, and ensure seamless management of all company resources and projects.
                        </p>
                        <a href="https://a2zengineering.lk/" target="_blank" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                            Visit Our Website
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <div class="md:w-1/2">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                            <i class="fas fa-lightbulb text-3xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Innovation</h4>
                            <p class="text-blue-100">Cutting-edge solutions for modern challenges</p>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
                            <i class="fas fa-award text-3xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Quality</h4>
                            <p class="text-indigo-100">Uncompromising standards in all our work</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                            <i class="fas fa-leaf text-3xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Sustainability</h4>
                            <p class="text-green-100">Eco-friendly solutions for a better future</p>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg">
                            <i class="fas fa-handshake text-3xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Reliability</h4>
                            <p class="text-yellow-100">Trusted by clients across Sri Lanka</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-indigo-700 mx-auto"></div>
                <p class="text-gray-600 mt-6 max-w-2xl mx-auto">
                    Explore our comprehensive range of engineering services designed to meet your specific needs
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6">
                        <i class="fas fa-sun text-4xl text-white"></i>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Solar Power Systems</h3>
                        <p class="text-gray-600 mb-6">
                            Comprehensive solar power solutions including design, installation, and maintenance of 
                            photovoltaic systems for residential, commercial, and industrial applications.
                        </p>
                        <a href="https://a2zengineering.lk/services/" target="_blank" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                            Learn More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                        <i class="fas fa-wind text-4xl text-white"></i>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Air Conditioning</h3>
                        <p class="text-gray-600 mb-6">
                            Professional installation and maintenance of energy-efficient air conditioning systems 
                            for homes, offices, and commercial spaces with a focus on comfort and sustainability.
                        </p>
                        <a href="https://a2zengineering.lk/services/" target="_blank" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                            Learn More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                        <i class="fas fa-bolt text-4xl text-white"></i>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Electrical Power Solutions</h3>
                        <p class="text-gray-600 mb-6">
                            Complete electrical engineering services including power distribution, lighting systems, 
                            backup power solutions, and energy efficiency optimization for all types of facilities.
                        </p>
                        <a href="https://a2zengineering.lk/services/" target="_blank" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                            Learn More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Database System Features</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-indigo-700 mx-auto"></div>
                <p class="text-gray-600 mt-6 max-w-2xl mx-auto">
                    Our internal database management system is designed to streamline company operations
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-200 transition-colors duration-300">
                        <i class="fas fa-users text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Employee Management</h3>
                    <p class="text-gray-600">Comprehensive employee database with attendance tracking and performance metrics</p>
                </div>
                
                <div class="text-center group">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-green-200 transition-colors duration-300">
                        <i class="fas fa-project-diagram text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Project Tracking</h3>
                    <p class="text-gray-600">Real-time project monitoring with milestone tracking and resource allocation</p>
                </div>
                
                <div class="text-center group">
                    <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-yellow-200 transition-colors duration-300">
                        <i class="fas fa-file-invoice-dollar text-yellow-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Financial Management</h3>
                    <p class="text-gray-600">Expense tracking, invoicing, and financial reporting for all company operations</p>
                </div>
                
                <div class="text-center group">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-200 transition-colors duration-300">
                        <i class="fas fa-chart-line text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Analytics & Reports</h3>
                    <p class="text-gray-600">Data-driven insights with customizable reports and performance dashboards</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Get In Touch</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-indigo-700 mx-auto"></div>
                <p class="text-gray-600 mt-6 max-w-2xl mx-auto">
                    For inquiries about our engineering services or support with the database system
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto">
                <div class="md:flex">
                    <div class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 p-12 text-white">
                        <h3 class="text-2xl font-bold mb-6">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-xl mt-1 mr-4"></i>
                                <div>
                                    <h4 class="font-bold text-lg mb-1">Our Office</h4>
                                    <p class="text-blue-100">Colombo, Sri Lanka</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-phone-alt text-xl mt-1 mr-4"></i>
                                <div>
                                    <h4 class="font-bold text-lg mb-1">Phone</h4>
                                    <p class="text-blue-100">+94 11 234 5678</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-xl mt-1 mr-4"></i>
                                <div>
                                    <h4 class="font-bold text-lg mb-1">Email</h4>
                                    <p class="text-blue-100">info@a2zengineering.lk</p>
                                </div>
                            </div>
                        </div>
                        <a href="https://a2zengineering.lk/contact/" target="_blank" class="inline-block mt-8 bg-white text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-blue-50 transition-colors duration-300">
                            <i class="fas fa-external-link-alt mr-2"></i>Visit Contact Page
                        </a>
                    </div>
                    <div class="md:w-1/2 p-12">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Database System Access</h3>
                        <p class="text-gray-600 mb-8">
                            For access to the internal database system, please contact your system administrator 
                            or use the login button below.
                        </p>
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-300 shadow-lg hover:shadow-xl block text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login to Database
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center mb-6">
                        <img class="h-10 w-auto" src="<?php echo BASE_PATH; ?>/src/assets/images/longLogoB.png" alt="A2Z Engineering Logo">
                    </div>
                    <p class="text-gray-400 mb-6">
                        Internal Database Management System for streamlined company operations and improved productivity.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Home</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> About Us</a></li>
                        <li><a href="https://a2zengineering.lk/services/" target="_blank" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Services</a></li>
                        <li><a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-6">System Features</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Employee Management</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Project Tracking</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Performance Analytics</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2"></i> Security & Access</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-6">Connect With Us</h4>
                    <ul class="space-y-3">
                        <li class="text-gray-400 flex items-center"><i class="fas fa-envelope mr-3"></i> info@a2zengineering.lk</li>
                        <li class="text-gray-400 flex items-center"><i class="fas fa-phone mr-3"></i> +94 11 234 5678</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 mb-4 md:mb-0">&copy; 2024 A2Z Engineering. Internal Database System - Company Use Only.</p>
                    <p class="text-gray-400">Version 2.1.0 | Last Updated: <?php echo date('M Y'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-xl');
            } else {
                navbar.classList.remove('shadow-xl');
            }
        });
    </script>
</body>
</html>