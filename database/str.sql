-- =====================================================================================
-- A2Z Engineering Operational Database — Schema Specification v3.0
-- =====================================================================================
--
-- CHANGELOG v3.0:
--   • Added lookup/reference tables to replace hardcoded ENUMs
--   • Added soft-delete (is_deleted, deleted_at) to all core tables
--   • Added created_at / updated_at timestamps to every table
--   • job_materials calculated columns are now GENERATED ALWAYS AS ... STORED
--   • employee_payments.total_amount is now a GENERATED column
--   • operational_expenses.emp_id is now NULLABLE (supports company-level expenses)
--   • Removed denormalized emp_name from employee_bank_details
--   • Added CHECK constraint on jobs.completion (0–100)
--   • Added UNIQUE constraint on invoice_data.invoice_no
--   • Added composite UNIQUE on employee_payment_rates(emp_id, effective_date)
--   • Added DEFAULT 0 on operational_expenses.paid
--   • Changed maintenance_schedule.cycle_number to TINYINT UNSIGNED
--   • Added composite indexes on high-traffic query paths
--
-- NOTE ON NAMING: Existing column names (emp_id, job_id, etc.) are preserved for
-- backward compatibility with the PHP application layer. A future migration should
-- standardize to {table_singular}_id for all FKs and `id` for all PKs.
--
-- NOTE ON ATTENDANCE: The CHECK constraint on attendance.presence requires MySQL 8.0.16+.
-- On older versions, enforce at the application layer.
--
-- =====================================================================================

CREATE DATABASE IF NOT EXISTS operational_db;
USE operational_db;

-- =====================================================================================
-- DROP TABLES (REVERSE DEPENDENCY ORDER)
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
DROP TABLE IF EXISTS schedule_statuses;
DROP TABLE IF EXISTS payment_categories;
DROP TABLE IF EXISTS increment_types;
DROP TABLE IF EXISTS payment_types;
DROP TABLE IF EXISTS roles;

-- =====================================================================================
-- LOOKUP / REFERENCE TABLES
-- These replace hardcoded ENUMs so new values can be added without ALTER TABLE.
-- =====================================================================================

-- Replaces: users.user_type ENUM('admin','manager','viewer')
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO roles (role_name, description) VALUES
('admin',   'Full system access including user and schema management'),
('manager', 'Operational access — manage data, run reports'),
('viewer',  'Read-only access to dashboards and reports');

-- Replaces: employees.payment_type / employee_payment_rates.rate_type
CREATE TABLE payment_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO payment_types (type_name) VALUES ('Fixed'), ('Daily');

-- Replaces: salary_increments.increment_type
CREATE TABLE increment_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO increment_types (type_name) VALUES ('Promotion'), ('Merit'), ('Annual'), ('Other');

-- Replaces: employee_payments.payment_type
CREATE TABLE payment_categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO payment_categories (category_name) VALUES
('Monthly Salary'), ('Daily Wage'), ('Advance'), ('Other');

-- Replaces: maintenance_schedule.status ENUM
CREATE TABLE schedule_statuses (
    status_id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO schedule_statuses (status_name) VALUES
('scheduled'), ('completed'), ('overdue'), ('cancelled');

-- =====================================================================================
-- 1. users
-- Purpose : Authentication credentials and role-based access control.
-- Links   : roles (FK) → determines dashboard/report visibility.
-- Reports : Governs which menu items, tables, and actions a logged-in user can access.
-- =====================================================================================
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- =====================================================================================
-- 2. projects
-- Purpose : High-level commercial contracts grouping multiple jobs.
-- Links   : Referenced by jobs.project_id.
-- Reports : Project-level summaries, A2Z Engineering job filters (project_id = 5).
-- =====================================================================================
CREATE TABLE projects (
    project_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    project_description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    company_reference VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================================================
-- 3. employees
-- Purpose : Core HR staff directory — engineers, technicians, installers.
-- Links   : payment_types (FK). Referenced by attendance, payments, expenses, bank_details.
-- Reports : HR Summary, Salary Reports, Attendance Reports.
-- =====================================================================================
CREATE TABLE employees (
    emp_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_name VARCHAR(100) NOT NULL,
    emp_nic VARCHAR(20) UNIQUE NOT NULL,
    date_of_birth DATE,
    address TEXT,
    date_of_joined DATE NOT NULL,
    date_of_resigned DATE DEFAULT NULL,
    payment_type_id INT NOT NULL,
    designation VARCHAR(50),
    etf_number VARCHAR(20),
    nic_photo VARCHAR(255),              -- Stores file path to NIC scan
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_type_id) REFERENCES payment_types(type_id)
);

-- =====================================================================================
-- 4. employee_payment_rates
-- Purpose : Tracks wage/salary rate revisions per employee over time.
-- Links   : employees (FK), payment_types (FK).
-- Reports : Referenced by attendance-based salary calculations.
-- Constraint: Composite unique prevents duplicate rates on the same effective date.
-- =====================================================================================
CREATE TABLE employee_payment_rates (
    rate_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    rate_type_id INT NOT NULL,
    rate_amount DECIMAL(10,2) NOT NULL CHECK (rate_amount > 0),
    effective_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (rate_type_id) REFERENCES payment_types(type_id),
    UNIQUE KEY uq_emp_rate_date (emp_id, effective_date)
);

-- =====================================================================================
-- 5. jobs
-- Purpose : Individual engineering tasks (e.g. 5kW solar installation at a customer site).
-- Columns :
--   selling_price  — The actual final price charged to the client for the whole system.
--                    Used by Material Cost Calculation to compute real profit vs. item quotes.
--   completion     — 0–100 percentage of job progress.
-- Links   : projects (FK). Referenced by job_materials, attendance, invoices, expenses, maintenance.
-- Reports : Material Cost Calculation, Job Summary, Invoice Tracking, Cost vs. Revenue.
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
    completion DECIMAL(5,2) NULL CHECK (completion BETWEEN 0 AND 100),
    selling_price DECIMAL(12,2) DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE SET NULL
);

-- =====================================================================================
-- 6. job_materials
-- Purpose : Per-item material breakdown for a job, with automatic cost calculations.
-- Columns :
--   profit_margin — Whole-number percentage (e.g. 5 = 5%). Values 0 < x < 1 are
--                   auto-scaled by the application (×100) to handle Excel decimal inputs.
--   total_cost    — GENERATED: quantity × unit_price (base material spend).
--   profit_amount — GENERATED: total_cost × profit_margin / 100 (item-level markup).
--   final_price   — GENERATED: total_cost + profit_amount (estimated quote per item).
-- Links   : jobs (FK).
-- Reports : Material Cost Calculation module — KPI cards, item list, Excel import.
-- =====================================================================================
CREATE TABLE job_materials (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    material_name VARCHAR(255) NOT NULL,
    quantity DECIMAL(12,4) NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(12,2) NOT NULL CHECK (unit_price >= 0),
    profit_margin DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_cost DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    profit_amount DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price * profit_margin / 100) STORED,
    final_price DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price * (1 + profit_margin / 100)) STORED,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 7. attendance
-- Purpose : Daily site attendance for each employee on a specific job.
-- Columns :
--   presence — 0.0 = absent, 0.5 = half-day, 1.0 = full day.
--              NOTE: CHECK constraint requires MySQL 8.0.16+.
-- Links   : employees (FK), jobs (FK).
-- Reports : Monthly Attendance Report, Daily Wage Calculation.
-- =====================================================================================
CREATE TABLE attendance (
    attendance_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    job_id INT(11) DEFAULT NULL,
    attendance_date DATE NOT NULL,
    presence DECIMAL(3,1) NOT NULL CHECK (presence IN (0.0, 0.5, 1.0)),
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    remarks TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE SET NULL
);

-- =====================================================================================
-- 8. salary_increments
-- Purpose : Audit trail of employee wage/salary revisions.
-- Links   : employees (FK), increment_types (FK).
-- Reports : HR Increment History.
-- =====================================================================================
CREATE TABLE salary_increments (
    increment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    increment_type_id INT NOT NULL,
    increment_date DATE NOT NULL,
    increment_amount DECIMAL(10,2) NOT NULL CHECK (increment_amount > 0),
    reason TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (increment_type_id) REFERENCES increment_types(type_id)
);

-- =====================================================================================
-- 9. employee_payments
-- Purpose : Log of actual salary, wage, advance, and bonus disbursements.
-- Columns :
--   total_amount — GENERATED: paid_amount - deduction_amount.
-- Links   : employees (FK), payment_categories (FK).
-- Reports : Payroll Summary, Monthly Salary Report.
-- =====================================================================================
CREATE TABLE employee_payments (
    payment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    payment_date DATE NOT NULL,
    payment_category_id INT NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL CHECK (paid_amount >= 0),
    deduction_amount DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (deduction_amount >= 0),
    total_amount DECIMAL(10,2) GENERATED ALWAYS AS (paid_amount - deduction_amount) STORED,
    remarks TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_category_id) REFERENCES payment_categories(category_id)
);

-- =====================================================================================
-- 10. invoice_data
-- Purpose : Customer billing, receivables tracking, and payment reconciliation.
-- Columns :
--   invoice — Stores file path or URL to the invoice document (PDF/image).
-- Constraint: invoice_no is UNIQUE to prevent duplicate invoice numbers.
-- Links   : jobs (FK).
-- Reports : Invoice Summary, Cash Flow Report, Outstanding Payments.
-- =====================================================================================
CREATE TABLE invoice_data (
    invoice_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    invoice_no VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    invoice_date DATE NOT NULL,
    invoice_value DECIMAL(10,2) NOT NULL,
    invoice TEXT COLLATE utf8mb4_general_ci NULL,       -- File path to invoice document
    receiving_payment DECIMAL(10,2) NULL,
    received_amount DECIMAL(10,2) NULL,
    payment_received_date DATE NULL,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    UNIQUE KEY uq_invoice_no (invoice_no)
);

-- =====================================================================================
-- 11. operational_expenses
-- Purpose : Direct site expenses — fuel, food, materials, transport, tools.
-- Columns :
--   emp_id — NULLABLE: NULL for company-level expenses not tied to a specific employee.
--   bill   — Stores file path or URL to the bill/receipt scan.
--   paid   — 0 = unpaid, 1 = paid. Defaults to 0.
-- Links   : jobs (FK), employees (FK, nullable).
-- Reports : Expense Report, Job Profitability (subtracted from revenue).
-- =====================================================================================
CREATE TABLE operational_expenses (
    expense_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    emp_id INT(11) NULL,
    expensed_date DATE NOT NULL,
    expenses_category VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    expense_amount DECIMAL(10,2) NOT NULL,
    paid TINYINT(1) NOT NULL DEFAULT 0,
    remarks TEXT COLLATE utf8mb4_general_ci NULL,
    voucher_number VARCHAR(50) COLLATE utf8mb4_general_ci NULL,
    bill TEXT COLLATE utf8mb4_general_ci NULL,           -- File path to bill/receipt scan
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE SET NULL
);

-- =====================================================================================
-- 12. employee_bank_details
-- Purpose : Bank account information for digital salary/wage transfers.
-- Note    : emp_name removed — join to employees table when display name is needed.
-- Links   : employees (FK).
-- Reports : Payroll Processing, Bank Transfer Exports.
-- =====================================================================================
CREATE TABLE employee_bank_details (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    emp_id INT(11) NOT NULL,
    acc_no VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    bank VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    branch VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
);

-- =====================================================================================
-- 13. maintenance_schedule
-- Purpose : Periodic maintenance visits for completed solar installations.
-- Links   : jobs (FK), schedule_statuses (FK).
-- Reports : Upcoming Maintenance, Overdue Visits.
-- =====================================================================================
CREATE TABLE maintenance_schedule (
    schedule_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    cycle_number TINYINT UNSIGNED NOT NULL,
    scheduled_date DATE NOT NULL,
    actual_date DATE NULL,
    status_id INT NOT NULL DEFAULT 1,
    description TEXT COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES schedule_statuses(status_id)
);

-- =====================================================================================
-- INDEXES — Composite indexes on high-traffic query paths
-- =====================================================================================
CREATE INDEX idx_attendance_emp_date    ON attendance (emp_id, attendance_date);
CREATE INDEX idx_job_materials_job      ON job_materials (job_id);
CREATE INDEX idx_invoice_job            ON invoice_data (job_id);
CREATE INDEX idx_expenses_job           ON operational_expenses (job_id);
CREATE INDEX idx_expenses_emp           ON operational_expenses (emp_id);
CREATE INDEX idx_payments_emp_date      ON employee_payments (emp_id, payment_date);
CREATE INDEX idx_rates_emp_date         ON employee_payment_rates (emp_id, effective_date);
CREATE INDEX idx_maintenance_job        ON maintenance_schedule (job_id);
CREATE INDEX idx_jobs_project           ON jobs (project_id);

-- =====================================================================================
-- DEFAULT ADMINISTRATIVE SEED
-- NOTE: The password hash below is a placeholder. Generate a proper bcrypt hash
-- with password_hash('your_password', PASSWORD_BCRYPT) before production use.
-- =====================================================================================
INSERT INTO users (username, password, role_id) VALUES
('admin', '$2y$10$wT/p7n1H2C5m1wT13mR1keQ5K7TzGj7fD2z3l5mR1keQ5K7TzGj7f', 1);
