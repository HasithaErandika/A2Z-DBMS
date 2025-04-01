-- Create the database (if not already created)
CREATE DATABASE IF NOT EXISTS operational_db;
USE operational_db;

-- 1. Employees Table
CREATE TABLE employees (
    emp_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_name VARCHAR(100) NOT NULL,
    emp_nic VARCHAR(20) UNIQUE NOT NULL,
    date_of_birth DATE,
    address TEXT,
    date_of_joined DATE NOT NULL,
    date_of_resigned DATE DEFAULT NULL,
    payment_type ENUM('Fixed', 'Daily') NOT NULL,
    designation VARCHAR(50),
    etf_number VARCHAR(20),
    nic_photo VARCHAR(255)
);

-- 2. Employee Payment Rates Table
CREATE TABLE employee_payment_rates (
    rate_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    rate_type ENUM('Fixed', 'Daily') NOT NULL,
    rate_amount DECIMAL(10,2) NOT NULL CHECK (rate_amount > 0),
    effective_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT
);

-- 4. Attendance Table (Refined from your existing table)
CREATE TABLE attendance (
    attendance_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    job_id INT(11) DEFAULT NULL,
    attendance_date DATE NOT NULL,
    presence DECIMAL(10,2) NOT NULL CHECK (presence IN (0.0, 0.5, 1.0)),
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    remarks TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE SET NULL
);

-- 5. Salary Increments Table
CREATE TABLE salary_increments (
    increment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    increment_type ENUM('Promotion', 'Merit', 'Annual', 'Other') NOT NULL,
    increment_date DATE NOT NULL,
    increment_amount DECIMAL(10,2) NOT NULL CHECK (increment_amount > 0),
    reason TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT
);

-- 6. Employee Payments Table
CREATE TABLE employee_payments (
    payment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    payment_date DATE NOT NULL,
    payment_type ENUM('Monthly Salary', 'Daily Wage', 'Advance', 'Other') NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL CHECK ,
    deduction_amount DECIMAL(10,2) DEFAULT 0 CHECK ,
    total_amount DECIMAL(10,2) ,
    remarks TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT
);

