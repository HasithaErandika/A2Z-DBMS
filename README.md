# A2Z Engineering - Database Management System (DBMS)

## Overview

A2Z Engineering DBMS is an internal database management system designed for Solar, AC, and Electrical Power companies. This system provides comprehensive management capabilities for employees, projects, jobs, invoices, expenses, and payroll data. The system offers a modern web interface built with PHP, Tailwind CSS, and JavaScript.

## Features

- **Employee Management**: Track employee information, payment rates, attendance, and salary increments
- **Project Management**: Manage solar, AC, and electrical power projects with detailed tracking
- **Job Tracking**: Monitor job progress, completion status, and associated invoices
- **Financial Management**: Handle operational expenses, employee payments, and invoice data
- **Reporting**: Generate detailed reports on expenses, wages, and cost calculations
- **Data Export**: Export data to CSV format for further analysis
- **Responsive Design**: Works seamlessly on desktop and mobile devices

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- Composer (for dependency management)
- Node.js and npm (for frontend asset compilation)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/a2z-dbms.git
cd a2z-dbms
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE a2z_dbms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u your_username -p a2z_dbms < database/schema.sql
```

3. Import sample data (optional):
```bash
mysql -u your_username -p a2z_dbms < database/sample_data.sql
```

### 4. Configuration

1. Copy the configuration file:
```bash
cp config/config.example.php config/config.php
```

2. Update the database credentials in `config/config.php`:
```php
<?php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'a2z_dbms',
        'username' => 'your_database_username',
        'password' => 'your_database_password',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => 'http://localhost/a2z-dbms',
        'timezone' => 'Asia/Colombo'
    ]
];
```

### 5. Web Server Configuration

#### Apache

Ensure mod_rewrite is enabled and use the provided `.htaccess` file in the public directory.

#### Nginx

Add the following configuration to your Nginx server block:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/a2z-dbms/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Asset Compilation

Compile the frontend assets:

```bash
npm run build
```

## Directory Structure

```
a2z-dbms/
├── config/                 # Application configuration files
├── database/               # Database schema and sample data
├── public/                 # Publicly accessible files
│   ├── index.php           # Entry point
│   └── .htaccess           # Apache rewrite rules
├── src/                    # Application source code
│   ├── Controllers/        # Controller classes
│   ├── Models/             # Model classes
│   ├── Views/              # View templates
│   │   ├── admin/          # Admin panel views
│   │   ├── auth/           # Authentication views
│   │   ├── errors/         # Error pages
│   │   ├── home/           # Homepage views
│   │   └── reports/        # Report views
│   ├── assets/             # Static assets
│   │   ├── css/            # Stylesheets
│   │   ├── js/             # JavaScript files
│   │   └── images/         # Image files
│   └── Core/               # Core framework files
├── vendor/                 # Composer dependencies
├── .env.example            # Environment variables example
├── composer.json           # PHP dependencies
└── package.json            # Frontend dependencies
```

## Usage

### Authentication

1. Navigate to the login page: `http://your-domain.com/login`
2. Enter your database credentials to access the system
3. Upon successful authentication, you'll be redirected to the admin dashboard

### Admin Dashboard

The dashboard provides an overview of:
- Total employees
- Active jobs
- Total projects
- Financial summaries
- System information

### Data Management

Access different data tables through the sidebar navigation:
- **Employees**: Manage employee information and payment rates
- **Attendance**: Track employee attendance records
- **Projects**: Manage project details
- **Jobs**: Track job progress and completion
- **Invoices**: Handle invoice data and payments
- **Expenses**: Manage operational expenses
- **Payments**: Track employee payments
- **Bank Details**: Store employee bank information

### Reports

Generate various reports from the Reports section:
- **Expense Reports**: Analyze operational expenses
- **Wage Reports**: Review employee payments
- **Cost Calculations**: Calculate project profitability

## Development

### Coding Standards

- Follow PSR-12 coding standards for PHP
- Use Tailwind CSS for styling
- Maintain consistent naming conventions
- Write clear, commented code

### Adding New Features

1. Create a new branch for your feature:
```bash
git checkout -b feature/new-feature-name
```

2. Implement your feature following the existing code patterns

3. Test thoroughly and commit your changes:
```bash
git add .
git commit -m "Add new feature: description"
```

4. Push to the repository and create a pull request:
```bash
git push origin feature/new-feature-name
```

## Security

- All database queries use prepared statements to prevent SQL injection
- User inputs are sanitized and validated
- Passwords are hashed using bcrypt
- Session management follows security best practices
- CSRF protection is implemented for forms

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `config/config.php`
   - Ensure MySQL service is running
   - Check database user permissions

2. **404 Errors**
   - Verify web server rewrite rules are configured correctly
   - Check that the `public` directory is set as the document root

3. **Permission Issues**
   - Ensure the web server has read/write permissions for:
     - `logs/` directory
     - `cache/` directory
     - `uploads/` directory (if applicable)

### Logging

Application logs are stored in the `logs/` directory. Check these files for error messages and debugging information.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a pull request

## License

This project is proprietary software developed for A2Z Engineering. All rights reserved.

## Support

For support, contact the development team at:
- Email: support@a2zengineering.com
- Phone: +94 XXX XXX XXX

## Version History

### v2.1.0 (Current)
- Enhanced UI/UX with Tailwind CSS
- Improved data management capabilities
- Added comprehensive reporting features
- Implemented responsive design
- Enhanced security measures

### v2.0.0
- Major refactor of the codebase
- Migration to modern PHP practices
- Improved database structure
- Added new data tables

### v1.0.0
- Initial release
- Basic CRUD operations
- Simple reporting capabilities
- Authentication system