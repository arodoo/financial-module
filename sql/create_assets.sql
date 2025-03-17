-- Create Asset Categories table
CREATE TABLE IF NOT EXISTS asset_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default asset categories
INSERT INTO asset_categories (name, description) VALUES
('Real Estate', 'Properties, land and real estate assets'),
('Vehicles', 'Cars, motorcycles, boats and other vehicles'),
('Investments', 'Stocks, bonds, mutual funds, etc.'),
('Cash & Savings', 'Bank accounts, emergency funds, cash'),
('Retirement Accounts', 'IRA, 401(k), pension plans'),
('Personal Property', 'Valuable items like jewelry, art, collectibles'),
('Business Assets', 'Business ownership, equipment, intellectual property'),
('Other', 'Other types of assets');

-- Create Assets table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membre_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    purchase_value DECIMAL(15, 2) NOT NULL,
    current_value DECIMAL(15, 2) NOT NULL,
    purchase_date DATE NOT NULL,
    last_valuation_date DATE NOT NULL,
    location VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id),
    INDEX idx_membre_assets (membre_id)
);

-- Create Asset Value History table (for tracking value changes over time)
CREATE TABLE IF NOT EXISTS asset_value_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    valuation_date DATE NOT NULL,
    value DECIMAL(15, 2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    INDEX idx_asset_valuation (asset_id, valuation_date)
);
