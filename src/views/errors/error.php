<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $errorCode; ?> - A2Z Engineering DBMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://a2zengineering.lk/wp-content/uploads/2023/03/Solar_4.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #ff4444;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #fff;
        }
        .back-home {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .back-home:hover {
            background: #0056b3;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?php echo $errorCode; ?></div>
        <div class="error-message"><?php echo $errorMessage; ?></div>
        <a href="/" class="back-home">
            <i class="fas fa-home me-2"></i>Back to Home
        </a>
    </div>
</body>
</html> 