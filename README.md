# A2Z-DBMS

A comprehensive database management system built with PHP, featuring user authentication, table management, and reporting capabilities.

## Features

- Secure user authentication system
- Role-based access control
- Table management interface
- Report generation
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

2. Configure your web server to point to the `public` directory.

3. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p your_database < database/schema.sql
```

4. Update the database configuration in `config/database.php`:
```php
define('DB_HOST', 'your_host');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

5. Set up the application URL in `config/settings.php`:
```php
define('APP_URL', 'http://your-domain.com/A2Z-DBMS');
```

## Directory Structure

```
A2Z-DBMS/
├── index.php              # Main entry point with routing
├── database/
│   └── setup.sql         # Database structure
├── src/
│   ├── core/            # Core framework classes
│   │   ├── Model.php    # Base Model class
│   │   └── Controller.php # Base Controller class
│   │   └── Database.php # Database class
│   ├── models/          # Database models
│   │   └── User.php     # User model
│   ├── controllers/     # Controllers
│   │   ├── AuthController.php # Authentication controller
│   │   ├── AdminController.php # Admin controller
│   │   └── HomeController.php # Home controller
│   │   └── ErrorController.php # Error Controller 
│   ├── views/          # View templates
│   │   ├── errors/    # error page
│   │   │   ├── error.php
│   │   ├── auth/       # Authentication views
│   │   │   └── login.php
│   │   ├── admin/      # Admin views
│   │   │   └── dashboard.php
│   │   └── home/       # Home views
│   │       └── index.php
│   └── config/         # Configuration files
│       └── database.php # Database connection
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