<?php
// Get the error code from the URL or default to 500
$errorCode = isset($_GET['code']) ? $_GET['code'] : '500';

// Set error messages based on error code
switch($errorCode) {
    case '404':
        $errorTitle = 'Page Not Found';
        $errorDescription = 'The page you\'re looking for doesn\'t exist in our internal database system.';
        $errorSuggestions = [
            'Check the URL for typos',
            'Navigate using the main menu',
            'Contact IT support if the issue persists'
        ];
        break;
    case '403':
        $errorTitle = 'Access Denied';
        $errorDescription = 'You don\'t have permission to access this resource. Please contact your administrator.';
        $errorSuggestions = [
            'Verify your user permissions',
            'Contact your department manager',
            'Request access through HR'
        ];
        break;
    case '500':
        $errorTitle = 'System Error';
        $errorDescription = 'Something went wrong with our internal system. Our IT team has been notified.';
        $errorSuggestions = [
            'Try refreshing the page',
            'Clear your browser cache',
            'Contact IT support immediately'
        ];
        break;
    default:
        $errorTitle = 'System Error';
        $errorDescription = 'An unexpected error occurred in our database management system.';
        $errorSuggestions = [
            'Try refreshing the page',
            'Check your internet connection',
            'Contact IT support for assistance'
        ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $errorCode; ?> - A2Z Engineering DBMS</title>
    <meta name="description" content="Error page for A2Z Engineering DBMS">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .error-bg {
            background: 
                radial-gradient(circle at 20% 80%, rgba(30, 58, 138, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 error-bg bg-gray-50">
    <div class="max-w-4xl w-full">
        <!-- Main Error Container -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transition-all duration-300 hover:shadow-3xl">
            <div class="p-8 md:p-12">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <!-- Error Content -->
                    <div class="flex-1 text-center md:text-left">
                        <!-- Error Code -->
                        <div class="text-9xl font-black bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent mb-4 pulse">
                            <?php echo $errorCode; ?>
                        </div>
                        
                        <!-- Error Title -->
                        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo $errorTitle; ?></h1>
                        
                        <!-- Error Description -->
                        <p class="text-lg text-gray-600 mb-8 max-w-2xl">
                            <?php echo $errorDescription; ?>
                        </p>
                        
                        <!-- Custom Design Element -->
                        <div class="relative mb-8">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl w-32 h-32 md:w-40 md:h-40 flex items-center justify-center shadow-xl mx-auto md:mx-0">
                                <div class="bg-white bg-opacity-20 rounded-full w-24 h-24 md:w-32 md:h-32 flex items-center justify-center">
                                    <div class="bg-white bg-opacity-30 rounded-full w-16 h-16 md:w-24 md:h-24 flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-white text-3xl md:text-4xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -top-2 -right-2 bg-yellow-400 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                                <i class="fas fa-bolt text-white text-lg"></i>
                            </div>
                            <div class="absolute -bottom-2 -left-2 bg-green-500 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                        </div>
                        
                        <!-- Error Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                            <a href="<?php echo BASE_PATH; ?>" class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-6 py-3 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-home"></i>
                                Go to Dashboard
                            </a>
                            <a href="javascript:history.back()" class="bg-white border-2 border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-blue-50 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                Go Back
                            </a>
                        </div>
                    </div>
                    
                    <!-- Custom Illustration -->
                    <div class="hidden md:block floating">
                        <div class="relative">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl w-64 h-64 flex items-center justify-center shadow-2xl">
                                <div class="bg-white bg-opacity-20 rounded-full w-48 h-48 flex items-center justify-center">
                                    <div class="bg-white bg-opacity-30 rounded-full w-36 h-36 flex items-center justify-center">
                                        <i class="fas fa-database text-white text-6xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -top-4 -right-4 bg-yellow-400 rounded-full w-20 h-20 flex items-center justify-center shadow-lg">
                                <i class="fas fa-cogs text-white text-2xl"></i>
                            </div>
                            <div class="absolute -bottom-4 -left-4 bg-green-500 rounded-full w-20 h-20 flex items-center justify-center shadow-lg">
                                <i class="fas fa-server text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Error Suggestions -->
                <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-blue-600"></i>
                        What you can do:
                    </h3>
                    <ul class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php foreach ($errorSuggestions as $suggestion): ?>
                        <li class="flex items-start gap-2 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span class="text-gray-700"><?php echo $suggestion; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div class="mt-8 bg-white rounded-xl p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-headset text-blue-600"></i>
                        Need Help?
                    </h4>
                    <p class="text-gray-600 mb-4">If you continue to experience issues, our IT support team is here to help. Please provide the error code above when contacting support.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                            <span class="text-gray-700">support@a2zengineering.com</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-phone text-blue-600 text-xl"></i>
                            <span class="text-gray-700">Ext. 1234 (Help Desk)</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                            <span class="text-gray-700">Mon-Fri 8AM-6PM</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                            <span class="text-gray-700">Create Support Ticket</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-500 text-sm">This is an internal system error. If you believe this is a mistake, please contact the IT department.</p>
                    <p class="text-gray-500 text-sm mt-2">
                        <a href="<?php echo BASE_PATH; ?>" class="text-blue-600 hover:text-blue-800 transition-colors">A2Z Engineering Database System</a> | Internal Use Only
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced button interactions
        document.querySelectorAll('a').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>