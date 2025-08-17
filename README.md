# A2Z-DBMS

A comprehensive database management system built with PHP, featuring user authentication, table management, and reporting capabilities.

## Features

- Secure user authentication system
- Role-based access control
- Table management interface
- Report generation (Cost Calculation, Expenses, Wages)
- Modern and responsive UI
- Secure database operations using PDO

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/A2Z-DBMS.git
```

2. Configure your web server to point to the project root directory.

3. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p your_database < database/str.sql
```

4. Update the database configuration in `src/config/database.php`:
```php
define('DB_HOST', 'your_host');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

5. Ensure your web server is configured to serve from the project root directory.

## Directory Structure

```
A2Z-DBMS/
├── index.php              # Main entry point with routing
├── database/
│   └── str.sql           # Database structure
├── src/
│   ├── assets/           # Styling and JavaScript
│   │   ├── css/          # CSS files
│   │   │   ├── cost_calculation.css
│   │   │   ├── dashboard.css
│   │   │   ├── error.css
│   │   │   ├── expenses_report.css
│   │   │   ├── index.css
│   │   │   ├── login.css
│   │   │   ├── manage_table.css
│   │   │   ├── reports.css
│   │   │   ├── tables.css
│   │   │   └── wages_report.css
│   │   ├── images/       # Image files
│   │   │   ├── logo.png
│   │   │   └── longLogoB.png
│   │   └── js/           # JavaScript files
│   │       └── manage_table.js
│   ├── core/             # Core framework classes
│   │   ├── Controller.php # Base Controller class
│   │   ├── Database.php  # Database connection class
│   │   └── Model.php     # Base Model class
│   ├── models/           # Database models
│   │   ├── ReportManager.php # Report management model
│   │   ├── TableManager.php  # Table management model
│   │   └── User.php      # User model
│   ├── controllers/      # Controllers
│   │   ├── AdminController.php # Admin controller
│   │   ├── AuthController.php  # Authentication controller
│   │   ├── ErrorController.php # Error handling controller
│   │   └── HomeController.php  # Home controller
│   ├── views/            # View templates
│   │   ├── admin/        # Admin views
│   │   │   ├── dashboard.php
│   │   │   ├── manage_table.php
│   │   │   ├── reports.php
│   │   │   └── tables.php
│   │   ├── auth/         # Authentication views
│   │   │   └── login.php
│   │   ├── errors/       # Error pages
│   │   │   └── error.php
│   │   ├── home/         # Home views
│   │   │   └── index.php
│   │   ├── layouts/      # Layout templates
│   │   ├── partials/     # Partial view components
│   │   └── reports/      # Report views
│   │       ├── cost_calculation.php
│   │       ├── expenses_report.php
│   │       └── wages_report.php
│   └── config/           # Configuration files
│       └── database.php  # Database connection settings
```

## Security Features

- Password hashing using BCRYPT
- Prepared statements for all database queries
- Session security measures
- XSS protection
- CSRF protection
- Input validation and sanitization

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the GitHub repository or contact the maintainers. 