
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

CREATE TABLE invoice_data (
    invoice_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    invoice_no VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    invoice_date DATE NOT NULL,
    invoice_value DECIMAL(10,2) NOT NULL,
    invoice TEXT COLLATE utf8mb4_general_ci NULL,
    receiving_payment DECIMAL(10,2) NULL,
    received_amount DECIMAL(10,2) NULL,
    payment_received_date DATE NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE RESTRICT
);

-- Create Operational Expenses Table
CREATE TABLE operational_expenses (
    expense_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    emp_id INT(11) NOT NULL,
    expensed_date DATE NOT NULL,
    expenses_category VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    expense_amount DECIMAL(10,2) NOT NULL,
    paid TINYINT(1) NOT NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    voucher_number VARCHAR(50) COLLATE utf8mb4_general_ci NULL,
    bill TEXT COLLATE utf8mb4_general_ci NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE RESTRICT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT
);

-- Create Projects Table
CREATE TABLE projects (
    project_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    project_description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    company_reference VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL
);

-- Create Employee Bank Details Table
CREATE TABLE employee_bank_details (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    emp_name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    acc_no VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    bank VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    branch VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE RESTRICT
);


-- Create Jobs Table
CREATE TABLE jobs (
    job_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    project_id INT(11) NULL,
    engineer VARCHAR(100) COLLATE utf8mb4_general_ci NULL,
    date_started DATE NULL,
    date_completed DATE NULL,
    customer_reference TEXT COLLATE utf8mb4_general_ci NULL,
    location TEXT COLLATE utf8mb4_general_ci NULL,
    job_capacity VARCHAR(50) COLLATE utf8mb4_general_ci NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    completion DECIMAL(10,2) NULL,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE SET NULL
);
