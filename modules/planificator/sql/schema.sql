-- Database schema for the Financial Management System

-- Income Categories
CREATE TABLE IF NOT EXISTS income_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default income categories
INSERT INTO income_categories (name, description) VALUES
('Salary', 'Regular employment income'),
('Bonus', 'Performance or holiday bonuses'),
('Real Estate', 'Rental income from properties'),
('Capital Gains', 'Income from investments'),
('Allowances', 'Regular allowances received'),
('Other', 'Miscellaneous income sources');

-- Expense Categories
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default expense categories
INSERT INTO expense_categories (name, description) VALUES
('Mortgage', 'Home or property loan payments'),
('Taxes', 'Income and property taxes'),
('Professional', 'Work-related expenses'),
('School Fees', 'Education expenses'),
('Regular Bills', 'Recurring monthly expenses'),
('Food', 'Groceries and dining'),
('Transportation', 'Fuel, maintenance, and public transit'),
('Other', 'Miscellaneous expenses');

-- Income Transactions
CREATE TABLE IF NOT EXISTS income_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES income_categories(id)
);

-- Expense Transactions
CREATE TABLE IF NOT EXISTS expense_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
);
