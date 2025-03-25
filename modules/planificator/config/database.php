<?php
/**
 * Database Configuration
 * This file contains database connection settings for the financial module
 */

// Include the main database configuration file to use its singleton connection
require_once dirname(__FILE__) . '/../../../Configurations_bdd.php';

// Create a database connection function that returns the existing connection
function getDbConnection() {
    global $bdd; // Use the global connection from Configurations_bdd.php
    
    // Check if connection exists
    if (isset($bdd)) {
        return $bdd;
    } else {
        // If for some reason the connection doesn't exist yet
        die("Database Connection Error: The main database connection is not available");
    }
}
