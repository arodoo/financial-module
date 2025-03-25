-- Add membre_id column to the transaction tables

-- Add membre_id to income_transactions table
ALTER TABLE income_transactions 
ADD COLUMN membre_id INT NOT NULL DEFAULT 1 AFTER id;

-- Add membre_id to expense_transactions table
ALTER TABLE expense_transactions 
ADD COLUMN membre_id INT NOT NULL DEFAULT 1 AFTER id;

-- Add indexes for better performance
CREATE INDEX idx_income_membre ON income_transactions(membre_id);
CREATE INDEX idx_expense_membre ON expense_transactions(membre_id);
