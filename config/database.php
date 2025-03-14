<?php
/**
 * Database Configuration
 * This file contains database connection settings for the financial module
 */

// Use different constant names to avoid conflicts with existing config.php
define('FINANCIAL_DB_HOST', 'localhost');
define('FINANCIAL_DB_NAME', 'financial_db');
define('FINANCIAL_DB_USER', 'root');
define('FINANCIAL_DB_PASS', '');

// Create a database connection function
function getDbConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . FINANCIAL_DB_HOST . ";dbname=" . FINANCIAL_DB_NAME,
            FINANCIAL_DB_USER,
            FINANCIAL_DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        return $conn;
    } catch (PDOException $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}
