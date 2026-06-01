-- database/seed.sql

USE operational_db;

-- 1. Insert Projects
INSERT INTO projects (project_id, project_description, company_reference, remarks) VALUES
(1, 'Commercial Solar Installation 50kW', 'REF-SOLAR-001', 'High priority project'),
(2, 'Multi-Split AC System Setup', 'REF-AC-092', 'Standard contract'),
(3, 'Industrial Electrical Wiring & Power Panel', 'REF-ELEC-442', 'Phase 3 expansion');

-- 2. Insert Employees
INSERT INTO employees (emp_id, emp_name, emp_nic, date_of_birth, address, date_of_joined, payment_type, designation, etf_number) VALUES
(1, 'John Doe', '199012345678', '1990-05-12', '123 Galle Road, Colombo 03', '2023-01-15', 'Fixed', 'Lead Solar Engineer', 'ETF-9912A'),
(2, 'Jane Smith', '199298765432', '1992-08-22', '45 Kandy Road, Kadawatha', '2023-06-01', 'Daily', 'Assistant Technician', 'ETF-8831B'),
(3, 'Ruwan Perera', '198834567890', '1988-11-30', '89 Negombo Road, Kurunegala', '2022-03-10', 'Fixed', 'AC Installer', 'ETF-1298C');

-- 3. Insert Payment Rates
INSERT INTO employee_payment_rates (rate_id, emp_id, rate_type, rate_amount, effective_date) VALUES
(1, 1, 'Fixed', 85000.00, '2023-01-15'),
(2, 2, 'Daily', 2500.00, '2023-06-01'),
(3, 3, 'Fixed', 60000.00, '2022-03-10');

-- 4. Insert Jobs
INSERT INTO jobs (job_id, project_id, engineer, date_started, date_completed, customer_reference, location, job_capacity, remarks, completion) VALUES
(1, 1, 'John Doe', '2026-05-01', NULL, 'CUST-SOLAR-01', 'Colombo 03', '50kW', 'Roof mounting complete', 75.00),
(2, 2, 'Ruwan Perera', '2026-05-10', '2026-05-25', 'CUST-AC-12', 'Kadawatha', '48000 BTU', 'Testing successfully done', 100.00),
(3, 3, 'John Doe', '2026-05-15', NULL, 'CUST-IND-99', 'Kurunegala', '100A Panel', 'Panel installation ongoing', 40.00);

-- 5. Insert Attendance for May 2026
-- John Doe (Fixed rate)
INSERT INTO attendance (emp_id, job_id, attendance_date, presence, start_time, end_time, remarks) VALUES
(1, 1, '2026-05-25', 1.0, '08:00:00', '17:00:00', 'Regular work'),
(1, 1, '2026-05-26', 1.0, '08:00:00', '17:00:00', 'Regular work'),
(1, 1, '2026-05-27', 1.0, '08:00:00', '17:00:00', 'Regular work'),
(1, 3, '2026-05-28', 1.0, '08:00:00', '17:00:00', 'Panel wiring'),
(1, 3, '2026-05-29', 1.0, '08:00:00', '17:00:00', 'Panel testing');

-- Jane Smith (Daily rate - 2500 per day)
INSERT INTO attendance (emp_id, job_id, attendance_date, presence, start_time, end_time, remarks) VALUES
(2, 1, '2026-05-25', 1.0, '08:00:00', '17:00:00', 'Helper duty'),
(2, 1, '2026-05-26', 1.0, '08:00:00', '17:00:00', 'Helper duty'),
(2, 2, '2026-05-27', 0.5, '08:00:00', '12:00:00', 'Half day work'),
(2, 3, '2026-05-28', 1.0, '08:00:00', '17:00:00', 'Helper duty'),
(2, 3, '2026-05-29', 0.0, NULL, NULL, 'Absent');

-- Ruwan Perera (Fixed rate)
INSERT INTO attendance (emp_id, job_id, attendance_date, presence, start_time, end_time, remarks) VALUES
(3, 2, '2026-05-25', 1.0, '08:00:00', '17:00:00', 'Installer duty'),
(3, 2, '2026-05-26', 1.0, '08:00:00', '17:00:00', 'Installer duty'),
(3, 2, '2026-05-27', 1.0, '08:00:00', '17:00:00', 'Commissioning'),
(3, 3, '2026-05-28', 1.0, '08:00:00', '17:00:00', 'Electrical backup');

-- 6. Insert Operational Expenses
INSERT INTO operational_expenses (job_id, emp_id, expensed_date, expenses_category, description, expense_amount, paid, remarks, voucher_number) VALUES
(1, 1, '2026-05-26', 'Materials', 'Conduit pipes and brackets purchase', 12500.00, 1, 'Purchased from local hardware', 'VOUCH-1002'),
(2, 3, '2026-05-27', 'Transport', 'Transport charges for AC units delivery', 4500.00, 1, 'Paid to carrier services', 'VOUCH-1003'),
(3, 1, '2026-05-28', 'Tools', 'Industrial crimping tool rental', 3000.00, 0, 'Rental for 3 days', 'VOUCH-1004');

-- 7. Insert Invoice Data
INSERT INTO invoice_data (job_id, invoice_no, invoice_date, invoice_value, receiving_payment, received_amount, payment_received_date) VALUES
(1, 'INV-2026-001', '2026-05-10', 450000.00, 150000.00, 150000.00, '2026-05-15'),
(2, 'INV-2026-002', '2026-05-25', 180000.00, 180000.00, 180000.00, '2026-05-28');

-- 8. Insert Maintenance Schedules
INSERT INTO maintenance_schedule (job_id, cycle_number, scheduled_date, status, description) VALUES
(1, 1, '2026-08-01', 'scheduled', 'Routine solar panel cleaning'),
(2, 1, '2026-11-25', 'scheduled', 'AC filter replacement and performance check');
