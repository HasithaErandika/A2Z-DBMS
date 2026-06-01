-- =====================================================================================
-- A2Z Engineering Material Management and DBMS Schema Specification (database/str.sql)
-- =====================================================================================
-- 
-- SYSTEM ARCHITECTURE OVERVIEW:
-- This database structure manages the core operations of A2Z Engineering, focusing on:
-- 1. User Authentication & RBAC (users table)
-- 2. Project & Site Management (projects, jobs, maintenance_schedule)
-- 3. Human Resource Management & Payroll (employees, payment_rates, attendance, salary_increments, employee_payments, bank_details)
-- 4. Financial & Material Management (job_materials, invoice_data, operational_expenses)
--
-- SYSTEM FUNCTION LINKING:
-- - Material Cost Calculation: Managed by the `job_materials` table linked directly to `jobs`.
--   Provides real-time mathematical calculations for base costs, margin markups, and client quotes.
--   Integrates with spreadsheet parsing (PHPSpreadsheet) to allow bulk import.
-- - Job Profitability & System Selling Price: The `selling_price` column on `jobs` stores the actual final
--   sale price to the client, allowing the system to calculate real margins and delta against estimated item quotes.
-- - HR & Daily Wages: Attendance presenza (0.0, 0.5, 1.0) maps with the active rate in `employee_payment_rates`
--   to compute monthly salary or daily wages, paid out via bank details.
-- - Invoicing and Cash Flow: Invoices are linked to jobs to track receivables, receipts, and outstanding amounts.
--
-- =====================================================================================

CREATE DATABASE IF NOT EXISTS operational_db;
USE operational_db;

-- =====================================================================================
-- DROP TABLES (REVERSE ORDER OF DEPENDENCY CHAIN)
-- =====================================================================================
DROP TABLE IF EXISTS maintenance_schedule;
DROP TABLE IF EXISTS employee_bank_details;
DROP TABLE IF EXISTS operational_expenses;
DROP TABLE IF EXISTS invoice_data;
DROP TABLE IF EXISTS job_materials;
DROP TABLE IF EXISTS employee_payments;
DROP TABLE IF EXISTS salary_increments;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS employee_payment_rates;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

-- =====================================================================================
-- 1. users
-- Description: Stores credentials and role definition for system login.
-- System Flow: Authenticates access to admin dashboard, operational reports, and database tables.
-- =====================================================================================
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'manager', 'viewer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================================
-- 2. projects
-- Description: High-level categorization of commercial contracts.
-- System Flow: Groups multiple individual installations or installation phases (jobs) under one project.
-- =====================================================================================
CREATE TABLE projects (
    project_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    project_description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    company_reference VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL
);

-- =====================================================================================
-- 3. employees
-- Description: Core HR staff directory.
-- System Flow: Identifies technical staff, engineers, and installers. Used for wages, attendance, and expense assignments.
-- =====================================================================================
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

-- =====================================================================================
-- 4. employee_payment_rates
-- Description: Tracks rate revisions for daily and monthly workers.
-- System Flow: Automatically referenced by the attendance module to calculate base pay.
-- =====================================================================================
CREATE TABLE employee_payment_rates (
    rate_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    rate_type ENUM('Fixed', 'Daily') NOT NULL,
    rate_amount DECIMAL(10,2) NOT NULL CHECK (rate_amount > 0),
    effective_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 5. jobs
-- Description: Specific engineering projects (e.g. Solar installation at customer sites).
-- Columns:
--   * selling_price: Stores the final, actual project sale price to the client (LKR).
-- System Flow: Core entity that links materials, expenses, scheduling, and billing together.
-- =====================================================================================
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
    selling_price DECIMAL(12,2) DEFAULT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE SET NULL
);

-- =====================================================================================
-- 6. job_materials
-- Description: Materials allocated to specific jobs. Includes base cost and markup parameters.
-- Columns:
--   * profit_margin: Stored as a whole number (e.g., 5 for 5%). Margins between 0 and 1 are autoscaled to whole numbers.
--   * total_cost: Calculated quantity * unit_price (base cost).
--   * profit_amount: Calculated markup profit based on margin percentage.
--   * final_price: Calculated final estimated price for the item.
-- System Flow: Supplies data to the Material Cost Calculation module and calculates total system quotes.
-- =====================================================================================
CREATE TABLE job_materials (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    material_name VARCHAR(255) NOT NULL,
    quantity DECIMAL(12,4) NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(12,2) NOT NULL CHECK (unit_price >= 0),
    profit_margin DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_cost DECIMAL(12,2) NOT NULL,
    profit_amount DECIMAL(12,2) NOT NULL,
    final_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 7. attendance
-- Description: Daily labor attendance and site tracking.
-- System Flow: Maps active site workers to jobs and forms the base data for HR salary calculations.
-- =====================================================================================
CREATE TABLE attendance (
    attendance_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    job_id INT(11) DEFAULT NULL,
    attendance_date DATE NOT NULL,
    presence DECIMAL(10,2) NOT NULL CHECK (presence IN (0.0, 0.5, 1.0)),
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    remarks TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE SET NULL
);

-- =====================================================================================
-- 8. salary_increments
-- Description: Audit trail of employee wage revisions.
-- System Flow: Documentation log for wage promotions and annual increases.
-- =====================================================================================
CREATE TABLE salary_increments (
    increment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    increment_type ENUM('Promotion', 'Merit', 'Annual', 'Other') NOT NULL,
    increment_date DATE NOT NULL,
    increment_amount DECIMAL(10,2) NOT NULL CHECK (increment_amount > 0),
    reason TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 9. employee_payments
-- Description: Log of actual salary, wage, advance, and bonus transfers to employees.
-- System Flow: Audits payroll expenditures.
-- =====================================================================================
CREATE TABLE employee_payments (
    payment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    payment_date DATE NOT NULL,
    payment_type ENUM('Monthly Salary', 'Daily Wage', 'Advance', 'Other') NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL CHECK (paid_amount >= 0),
    deduction_amount DECIMAL(10,2) DEFAULT 0 CHECK (deduction_amount >= 0),
    total_amount DECIMAL(10,2),
    remarks TEXT,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 10. invoice_data
-- Description: Customer billing and receivables log.
-- System Flow: Feeds directly to cash flow, billing, and job-level financial dashboards.
-- =====================================================================================
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
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 11. operational_expenses
-- Description: Direct site-specific expenses (e.g. food, fuel, structural items).
-- System Flow: Aggregated on job reports to subtract from total contract price for net profit math.
-- =====================================================================================
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
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 12. employee_bank_details
-- Description: Stores employee bank accounts for digital wage transfers.
-- System Flow: Referenced during monthly salary processing.
-- =====================================================================================
CREATE TABLE employee_bank_details (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    emp_name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    acc_no VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    bank VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    branch VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 13. maintenance_schedule
-- Description: Automated maintenance scheduling for completed solar sites.
-- System Flow: Reminds operators to perform periodic visits and logs outcomes.
-- =====================================================================================
CREATE TABLE maintenance_schedule (
    schedule_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    cycle_number INT(2) NOT NULL,
    scheduled_date DATE NOT NULL,
    actual_date DATE NULL,
    status ENUM('scheduled', 'completed', 'overdue', 'cancelled') DEFAULT 'scheduled',
    description TEXT COLLATE utf8mb4_general_ci NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- =====================================================================================
-- DEFAULT ADMINISTRATIVE SEED
-- =====================================================================================
INSERT INTO users (username, password, user_type) VALUES 
('admin', '$2y$10$wT/p7n1H2C5m1wT13mR1keQ5K7TzGj7fD2z3l5mR1keQ5K7TzGj7f', 'admin');
