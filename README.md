# A2Z Engineering - DBMS (Database Management System)

A modernized, high-performance Database Management System designed for internal operations, site cost calculation, wages tracking, maintenance schedule tracking, and operational expenses management. 

This project uses a decoupled, clean MVC architecture featuring a Service-Repository layer, Custom Middlewares, Pipeline Router, and secure Session Management.

---

## 🚀 Key Features & Architecture

* **Decoupled Business Logic**: Services like `WageCalculationService` and `ExportService` contain core calculations, keeping controllers lean.
* **Separation of Concerns**: Database operations are encapsulated inside domain-specific repositories (`EmployeeRepository`, `JobRepository`, `ExpenseRepository`).
* **Secure Pipeline Middleware**: Includes an `AuthMiddleware` to check authentication, and a `RateLimitMiddleware` to prevent login brute force.
* **Modern Design System**: Styled with vibrant colors, glassmorphism UI components, Google Fonts (`Poppins`), and subtle hover animations for a premium user experience.

---

## 🛠️ Local Installation & Setup

Follow these steps to run the application locally on your PC:

### 1. Prerequisites
Ensure you have the following installed:
* **PHP 8.2+** (with `pdo`, `pdo_mysql`, `openssl` extensions enabled)
* **MySQL Server** (or MySQL Workbench)
* **Composer** (PHP dependency manager)

### 2. Database Initialization
Use MySQL Workbench or command-line client to import the schema and seed data.
The password for `root` on your PC is `Login@123456`.

```bash
# 1. Import Schema (Creates database 'operational_db' and tables)
mysql -u root -p'Login@123456' < database/str.sql

# 2. Import Seed Data (Inserts mock employees, projects, jobs, and attendance for testing)
mysql -u root -p'Login@123456' < database/seed.sql
```

### 3. Dependency Autoloading
Generate the autoloader map inside the project root directory:
```bash
composer install
# or if dependencies are already installed
composer dump-autoload
```

### 4. Configuration (`.env`)
Create/edit the `.env` file in the root directory:
```env
# Application Config
APP_KEY=83b7f16f5c88ea7651a2d5be32a2491a
APP_ENV=development
APP_DEBUG=true
BASE_PATH=/A2Z-DBMS
FULL_BASE_URL=http://localhost:8000/A2Z-DBMS

# Database Config
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=operational_db
DB_CHARSET=utf8mb4

# Database Credentials
DB_USER=root
DB_PASS=Login@123456
```

### 5. Run the Local Server
Start the built-in PHP development server using the index router:
```bash
php -S localhost:8000 public/index.php
```

Open your browser and navigate to:
👉 **[http://localhost:8000/A2Z-DBMS/](http://localhost:8000/A2Z-DBMS/)**

### 🔓 Default Admin Credentials
* **Username**: `admin`
* **Password**: `admin123`

---

## 📊 Business Logic & Report Calculations

### 1. Wages Report & Calculations
Wages are handled by the `WageCalculationService`:
* **Fixed Rate Employees**: Earn a set monthly salary. If they work less than a full month, their salary is prorated based on attendance days vs. total days.
* **Daily Rate Employees**: Calculated as `Total Presence Days × Daily Rate`.
* **Deductions (EPF/ETF)**:
  * **Employee EPF**: 8% deduction from the base salary.
  * **Employer EPF**: 12% contributed by the employer (not deducted from employee's net wage).
  * **Employer ETF**: 3% contributed by the employer (not deducted from employee's net wage).
* **Net Wage**: calculated as `Gross Wage + Allowances - Deductions (Advances, Employee EPF)`.

### 2. Operational Expenses
Expenses are grouped by categories (e.g. `Transport`, `Materials`, `Tools`, `Subcontracting`):
* Sums total expenses per project and per site.
* Provides inline actions to mark expenses as paid/unpaid.

### 3. Maintenance Reports
The maintenance scheduler runs periodic safety/check cycles:
* Tracks cycle count, status (`scheduled`, `completed`, `overdue`), and dates.
* Auto-generates maintenance schedules when a job is marked as active.

### 4. Cost Calculations of Sites
Combines:
* Total employee labor costs (based on attendance records and payment rates).
* Total operational expenses logged for a specific job/site.
* Invoiced values vs. received payments to calculate real-time profitability per site.

---

## ☁️ Connecting Supabase (PostgreSQL Integration)

Yes, you can connect the system to **Supabase**! Because Supabase uses **PostgreSQL** instead of MySQL, you will need to perform the following transitions:

### 1. Database Schema Translation
Translate the MySQL DDL in `database/str.sql` to PostgreSQL dialect:
* Replace `INT(11) AUTO_INCREMENT` with `SERIAL` or `BIGSERIAL`.
* Replace `VARCHAR(x) COLLATE utf8mb4_general_ci` with `VARCHAR(x)`.
* Change `TINYINT(1)` to `BOOLEAN`.
* Run the converted SQL script inside the Supabase SQL Editor.

### 2. Update Configuration
Change `.env` file credentials to point to your Supabase PostgreSQL connection pool:
```env
DB_HOST=aws-0-us-east-1.pooler.supabase.com  # Your Supabase host
DB_PORT=6543                                 # Postgres port (standard/transaction pooler)
DB_NAME=postgres
DB_USER=postgres.your-project-id
DB_PASS=your-supabase-db-password
```

### 3. Update PDO Driver DSN
In `src/core/Database.php`, change the connection string DSN prefix from `mysql:` to `pgsql:`:
```php
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname};options='--client_encoding=UTF8'";
```
PostgreSQL will then connect seamlessly via the PDO PGSQL driver.