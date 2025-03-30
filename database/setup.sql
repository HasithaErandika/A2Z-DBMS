-- Create the database
CREATE DATABASE IF NOT EXISTS `operational_db`;
USE `operational_db`;

-- Creating employee table
CREATE TABLE operational_db.employees (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_name VARCHAR(100) NOT NULL,
    emp_nic VARCHAR(20) UNIQUE,
    date_of_birth DATE,
    address TEXT,
    date_of_joined DATE,
    date_of_resigned DATE,
    designation VARCHAR(50),
    etf_number VARCHAR(20),
    daily_wage DECIMAL(10,2),
    basic_salary DECIMAL(10,2),
    nic_photo VARCHAR(255)
);

-- Creating jobs table with company_reference column and relationship to projects
CREATE TABLE operational_db.jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    service_category VARCHAR(50),
    date_started DATE,
    date_completed DATE,
    company_reference VARCHAR(50),  -- Renamed from client_reference to company_reference
    engineer VARCHAR(100),
    location TEXT,
    job_capacity VARCHAR(50),
    remarks TEXT,
    project_id INT,  -- Adding relationship to the projects table
    FOREIGN KEY (project_id) REFERENCES operational_db.projects(project_id) ON DELETE SET NULL
);

-- Creating attendance table
CREATE TABLE operational_db.attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT,
    job_id INT,
    attendance_date DATE,
    presence ENUM('Present', 'Absent') DEFAULT 'Present',
    start_time TIME,
    end_time TIME,
    remarks TEXT,
    FOREIGN KEY (emp_id) REFERENCES operational_db.employees(emp_id) ON DELETE SET NULL,
    FOREIGN KEY (job_id) REFERENCES operational_db.jobs(job_id) ON DELETE SET NULL
);

-- Creating expenses table
CREATE TABLE operational_db.expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    emp_id INT,
    expensed_date DATE,
    expenses_category VARCHAR(50),
    description TEXT,
    expense_amount DECIMAL(10,2),
    paid ENUM('Yes', 'No') DEFAULT 'No',
    remarks TEXT,
    voucher_number VARCHAR(50),
    bill VARCHAR(255),
    FOREIGN KEY (job_id) REFERENCES operational_db.jobs(job_id) ON DELETE SET NULL,
    FOREIGN KEY (emp_id) REFERENCES operational_db.employees(emp_id) ON DELETE SET NULL
);

-- Creating employee bank details table
CREATE TABLE operational_db.employee_bank_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    emp_name VARCHAR(255) NOT NULL,
    acc_no VARCHAR(50) NOT NULL,
    bank VARCHAR(100) NOT NULL,
    branch VARCHAR(100) NOT NULL
);

-- Creating employee payments table
CREATE TABLE operational_db.employee_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_date DATE NOT NULL,
    emp_id INT NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    remarks TEXT
);

-- Creating invoice data table
CREATE TABLE operational_db.invoice_data (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    invoice_no VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    invoice_value DECIMAL(10,2) NOT NULL,
    invoice TEXT,
    receiving_payment DECIMAL(10,2),
    received_amount DECIMAL(10,2),
    payment_received_date DATE,
    remarks TEXT
);

-- Creating projects table with company_reference column
CREATE TABLE operational_db.projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    project_description TEXT NOT NULL,
    company_reference VARCHAR(255) NOT NULL,  -- Renamed from customer_reference to company_reference
    remarks TEXT
);

-- Creating operational expenses table
CREATE TABLE operational_db.operational_expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    emp_id INT NOT NULL,
    expensed_date DATE NOT NULL,
    expenses_category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    expense_amount DECIMAL(10,2) NOT NULL,
    paid BOOLEAN NOT NULL,
    remarks TEXT,
    voucher_number VARCHAR(50),
    bill TEXT
);

-- Creating salary increments table
CREATE TABLE operational_db.salary_increments (
    increment_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    increment_type VARCHAR(50) NOT NULL,
    increment_date DATE NOT NULL,
    increment_amount DECIMAL(10,2) NOT NULL,
    new_salary DECIMAL(10,2) NOT NULL,
    reason TEXT,
    FOREIGN KEY (emp_id) REFERENCES operational_db.employees(emp_id) ON DELETE CASCADE
);


-- Create Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    user_type ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, user_type) VALUES 
('admin', 'admin123', 'admin');

-- Insert default user (password: user123)
INSERT INTO users (username, password, user_type) VALUES 
('user', 'user123', 'user');