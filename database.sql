-- =========================================================
-- LABORATORY EQUIPMENT INVENTORY SYSTEM
-- Database SQL Hosting / InfinityFree
-- =========================================================

DROP TABLE IF EXISTS `equipment`;

CREATE TABLE `equipment` (
    `equipment_id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `equipment_name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 0,
    `condition` VARCHAR(30) NOT NULL,
    `laboratory` VARCHAR(100) NOT NULL,
    `date_acquired` DATE NOT NULL
);

-- Initial sample records
INSERT INTO `equipment` (`equipment_id`, `equipment_name`, `category`, `quantity`, `condition`, `laboratory`, `date_acquired`) VALUES
('EQ001', 'Desktop Computer', 'Computer', 10, 'Good', 'Computer Laboratory 1', '2025-06-15'),
('EQ002', 'Arduino Uno', 'Electronics', 20, 'Good', 'Electronics Laboratory', '2025-07-20'),
('EQ003', 'Projector', 'Audio Visual', 3, 'For Repair', 'Computer Laboratory 2', '2024-08-10'),
('EQ004', 'Oscilloscope', 'Electronics', 5, 'Good', 'Electronics Laboratory', '2024-09-05'),
('EQ005', 'Printer', 'Office Equipment', 2, 'Damaged', 'Computer Laboratory 1', '2023-11-12');
