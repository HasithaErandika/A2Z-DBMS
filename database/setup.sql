-- Create the database
CREATE DATABASE IF NOT EXISTS `operational_db`;
USE `operational_db`;

-- Create Employee table
CREATE TABLE `Employee` (
    `Emp_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Emp_Name` VARCHAR(100) NOT NULL,
    `Emp_NIC` VARCHAR(20) UNIQUE,
    `Date_of_Birth` DATE,
    `Address` TEXT,
    `Date_of_Joined` DATE,
    `Date_of_resigned` DATE,
    `Designation` VARCHAR(50),
    `ETF_No` VARCHAR(20),
    `Daily_Wage` DECIMAL(10, 2),
    `Basic_Salary` DECIMAL(10, 2),
    `NIC_Photo` VARCHAR(255)
);

-- Create Jobs table
CREATE TABLE `Jobs` (
    `Job_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Service_Category` VARCHAR(50),
    `Date_started` DATE,
    `Date_completed` DATE,
    `Customer_ref` VARCHAR(50),
    `Client_ref` VARCHAR(50),
    `Engineer` VARCHAR(100),
    `Location` TEXT,
    `Job_capacity` VARCHAR(50),
    `Remarks` TEXT
);

-- Create Attendance table
CREATE TABLE `Attendance` (
    `Atd_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Emp_ID` INT,
    `Job_ID` INT,
    `Atd_Date` DATE,
    `Presence` ENUM('Present', 'Absent') DEFAULT 'Present',
    `Start_Time` TIME,
    `End_Time` TIME,
    `Remarks_Atd` TEXT,
    FOREIGN KEY (`Emp_ID`) REFERENCES `Employee`(`Emp_ID`) ON DELETE SET NULL,
    FOREIGN KEY (`Job_ID`) REFERENCES `Jobs`(`Job_ID`) ON DELETE SET NULL
);

-- Create Expense table
CREATE TABLE `Expense` (
    `ID_Exp` INT AUTO_INCREMENT PRIMARY KEY,
    `Job_ID` INT,
    `Emp_ID` INT,
    `Expensed_Date` DATE,
    `Expenses_Category` VARCHAR(50),
    `Description` TEXT,
    `Exp_amount` DECIMAL(10, 2),
    `Paid` ENUM('Yes', 'No') DEFAULT 'No',
    `Remarks` TEXT,
    `Voucher_No` VARCHAR(50),
    `Bill` VARCHAR(255),
    FOREIGN KEY (`Job_ID`) REFERENCES `Jobs`(`Job_ID`) ON DELETE SET NULL,
    FOREIGN KEY (`Emp_ID`) REFERENCES `Employee`(`Emp_ID`) ON DELETE SET NULL
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

-- Insert sample data for testing
INSERT INTO `Employee` (`Emp_Name`, `Emp_NIC`, `Date_of_Birth`, `Address`, `Date_of_Joined`, `Designation`, `Daily_Wage`, `Basic_Salary`) VALUES
('John Doe', '123456789V', '1990-01-01', '123 Main St, Colombo', '2023-01-01', 'Engineer', 2000.00, 50000.00),
('Jane Smith', '987654321V', '1992-05-15', '456 High St, Kandy', '2023-02-01', 'Technician', 1500.00, 35000.00);

INSERT INTO `Jobs` (`Service_Category`, `Date_started`, `Customer_ref`, `Engineer`, `Location`, `Job_capacity`) VALUES
('Electrical', '2024-01-01', 'CUST001', 'John Doe', 'Colombo', 'High'),
('AC Maintenance', '2024-01-15', 'CUST002', 'Jane Smith', 'Kandy', 'Medium');

INSERT INTO `Attendance` (`Emp_ID`, `Job_ID`, `Atd_Date`, `Start_Time`, `End_Time`) VALUES
(1, 1, '2024-01-01', '08:00:00', '17:00:00'),
(2, 2, '2024-01-15', '09:00:00', '16:00:00');

INSERT INTO `Expense` (`Job_ID`, `Emp_ID`, `Expensed_Date`, `Expenses_Category`, `Description`, `Exp_amount`, `Paid`) VALUES
(1, 1, '2024-01-01', 'Transport', 'Fuel for site visit', 5000.00, 'Yes'),
(2, 2, '2024-01-15', 'Materials', 'AC parts replacement', 15000.00, 'No'); 