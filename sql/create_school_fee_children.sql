-- Create school_fee_children table for the School Fee Simulator module

CREATE TABLE IF NOT EXISTS `school_fee_children` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `birthdate` DATE NOT NULL,
    `current_level` VARCHAR(50) NOT NULL, 
    `school_name` VARCHAR(255),
    `annual_tuition` DECIMAL(10,2) NOT NULL,
    `additional_expenses` DECIMAL(10,2) DEFAULT 0,
    `inflation_rate` DECIMAL(4,2) NOT NULL,
    `expected_graduation_level` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
