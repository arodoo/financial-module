-- Create Loans table
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membre_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    interest_rate DECIMAL(5, 3) NOT NULL,
    term INT NOT NULL COMMENT 'Loan term in years',
    monthly_payment DECIMAL(15, 2) NOT NULL,
    start_date DATE NOT NULL,
    asset_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_membre_loans (membre_id),
    INDEX idx_asset_loan (asset_id)
);

-- Add loan-related columns to assets table if they don't exist
ALTER TABLE assets 
ADD COLUMN loan_id INT NULL,
ADD COLUMN loan_amount DECIMAL(15, 2) NULL,
ADD COLUMN loan_monthly_payment DECIMAL(15, 2) NULL,
ADD INDEX idx_loan_asset (loan_id);
