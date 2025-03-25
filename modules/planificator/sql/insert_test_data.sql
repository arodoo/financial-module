-- Test data insertion for financial management system
-- This script uses @user_id variable which should be set before execution

-- Make sure we have the user in membres table
-- Insert test income transactions for the user
INSERT INTO income_transactions (membre_id, category_id, amount, description, transaction_date) VALUES
(@user_id, 1, 3500.00, 'Monthly Salary', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 1, 3500.00, 'Monthly Salary', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 1, 3500.00, 'Monthly Salary', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 2, 1000.00, 'Year-end bonus', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 3, 750.00, 'Apartment rental income', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 3, 750.00, 'Apartment rental income', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 3, 750.00, 'Apartment rental income', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 4, 350.00, 'Stock dividends', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 5, 200.00, 'Transportation allowance', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 5, 200.00, 'Transportation allowance', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 5, 200.00, 'Transportation allowance', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 6, 120.00, 'Freelance project', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH));

-- Insert test expense transactions for the user
INSERT INTO expense_transactions (membre_id, category_id, amount, description, transaction_date) VALUES
(@user_id, 1, 1200.00, 'Monthly mortgage payment', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 1, 1200.00, 'Monthly mortgage payment', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 1, 1200.00, 'Monthly mortgage payment', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 2, 650.00, 'Quarterly taxes', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 3, 75.00, 'Office supplies', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 4, 500.00, 'Tuition fees', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 5, 120.00, 'Electricity bill', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 5, 120.00, 'Electricity bill', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 5, 120.00, 'Electricity bill', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 5, 45.00, 'Water bill', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 5, 45.00, 'Water bill', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 5, 45.00, 'Water bill', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 5, 60.00, 'Internet subscription', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 5, 60.00, 'Internet subscription', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 5, 60.00, 'Internet subscription', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 6, 450.00, 'Groceries', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 6, 475.00, 'Groceries', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 6, 425.00, 'Groceries', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 6, 120.00, 'Restaurant dinner', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 7, 80.00, 'Gasoline', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)),
(@user_id, 7, 85.00, 'Gasoline', DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)),
(@user_id, 7, 75.00, 'Gasoline', DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)),
(@user_id, 8, 99.00, 'Mobile phone case', DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH));

-- Insert test children for school fee simulator
INSERT INTO school_fee_children 
(name, birthdate, current_level, school_name, annual_tuition, additional_expenses, inflation_rate, expected_graduation_level)
VALUES
(CONCAT('Child 1 of User ', @user_id), DATE_SUB(CURRENT_DATE, INTERVAL 5 YEAR), 'Primary School', 'International School', 6000.00, 1500.00, 3.50, 'University'),
(CONCAT('Child 2 of User ', @user_id), DATE_SUB(CURRENT_DATE, INTERVAL 10 YEAR), 'Middle School', 'St. Mary School', 8500.00, 2000.00, 3.50, 'University');

-- Insert test assets for the user
INSERT INTO assets 
(membre_id, category_id, name, description, purchase_value, current_value, purchase_date, last_valuation_date, location, notes)
VALUES
(@user_id, 1, 'Primary Residence', 'Main family home', 350000.00, 380000.00, DATE_SUB(CURRENT_DATE, INTERVAL 5 YEAR), CURRENT_DATE, '123 Main Street', 'Primary residence with 3 bedrooms'),
(@user_id, 1, 'Rental Apartment', 'Investment property', 180000.00, 210000.00, DATE_SUB(CURRENT_DATE, INTERVAL 7 YEAR), CURRENT_DATE, '45 Oak Avenue', 'Currently rented for 750€ per month'),
(@user_id, 2, 'Family Car', '2019 Toyota SUV', 28000.00, 19000.00, DATE_SUB(CURRENT_DATE, INTERVAL 3 YEAR), CURRENT_DATE, 'Home garage', 'Family vehicle in good condition'),
(@user_id, 3, 'Stock Portfolio', 'Mixed stocks and bonds', 50000.00, 64500.00, DATE_SUB(CURRENT_DATE, INTERVAL 4 YEAR), CURRENT_DATE, 'ABC Broker', 'Diversified investment portfolio'),
(@user_id, 4, 'Savings Account', 'Emergency fund', 15000.00, 15450.00, DATE_SUB(CURRENT_DATE, INTERVAL 2 YEAR), CURRENT_DATE, 'National Bank', '3% annual interest rate'),
(@user_id, 5, '401(k)', 'Retirement account', 120000.00, 142000.00, DATE_SUB(CURRENT_DATE, INTERVAL 10 YEAR), CURRENT_DATE, 'Fidelity Investments', 'Target date fund 2045'),
(@user_id, 6, 'Jewelry Collection', 'Family heirlooms', 8000.00, 9500.00, DATE_SUB(CURRENT_DATE, INTERVAL 15 YEAR), CURRENT_DATE, 'Home safe', 'Includes wedding rings and watches');

-- Insert asset value history for sample asset tracking
INSERT INTO asset_value_history (asset_id, valuation_date, value, notes)
SELECT id, DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH), current_value * 0.95, 'Bi-annual valuation'
FROM assets WHERE membre_id = @user_id;

INSERT INTO asset_value_history (asset_id, valuation_date, value, notes)
SELECT id, DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH), current_value * 0.9, 'Annual valuation'
FROM assets WHERE membre_id = @user_id;

-- Insert test loans for the user
INSERT INTO loans 
(membre_id, name, amount, interest_rate, term, monthly_payment, start_date, asset_id)
SELECT 
    @user_id, 
    'Mortgage for Primary Residence', 
    280000.00, 
    2.75, 
    25, 
    1285.35, 
    DATE_SUB(CURRENT_DATE, INTERVAL 5 YEAR),
    id
FROM assets 
WHERE membre_id = @user_id AND name = 'Primary Residence';

INSERT INTO loans 
(membre_id, name, amount, interest_rate, term, monthly_payment, start_date, asset_id)
SELECT 
    @user_id, 
    'Mortgage for Rental Property', 
    144000.00, 
    3.25, 
    20, 
    815.42, 
    DATE_SUB(CURRENT_DATE, INTERVAL 7 YEAR),
    id
FROM assets 
WHERE membre_id = @user_id AND name = 'Rental Apartment';

INSERT INTO loans 
(membre_id, name, amount, interest_rate, term, monthly_payment, start_date, asset_id)
SELECT 
    @user_id, 
    'Car Loan', 
    22400.00, 
    4.5, 
    5, 
    417.56, 
    DATE_SUB(CURRENT_DATE, INTERVAL 3 YEAR),
    id
FROM assets 
WHERE membre_id = @user_id AND name = 'Family Car';

-- Update assets with loan information
UPDATE assets a
JOIN loans l ON a.id = l.asset_id
SET 
    a.loan_id = l.id, 
    a.loan_amount = l.amount, 
    a.loan_monthly_payment = l.monthly_payment
WHERE a.membre_id = @user_id;
