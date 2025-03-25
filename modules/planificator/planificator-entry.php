<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check - redirect to home if no valid session
if (!isset($_SESSION['4M8e7M5b1R2e8s'])) {
    header('Location: /', true, 301);
    exit();
}

// If we reach this point, user is authenticated

// Get the action from query string
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Define constants and globals the main application would normally set
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

// Set up the environment
$_GET['page'] = 'Planificator'; 
$_GET['action'] = $action;

// Include required files - this effectively runs the application with our parameters
// but preserves the clean URL
chdir($_SERVER['DOCUMENT_ROOT']); // Change directory to document root to avoid path issues
require_once $_SERVER['DOCUMENT_ROOT'] . '/index.php';
exit();
?>