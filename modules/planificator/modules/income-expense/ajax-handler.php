<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Include main configuration files - this is key
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_bdd.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_modules.php';

// Include any needed functions
$dir_fonction = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once $_SERVER['DOCUMENT_ROOT'] . '/function/INCLUDE-FUNCTION-HAUT-CMS-CODI-ONE.php';

// Now we should have access to the $id_oo variable from the included Configurations.php

// Only proceed if the user is logged in (like in the Notifications file)
if (isset($user)) {
    // Clear any previous output that might have occurred during includes
    ob_clean();
    
    // Require necessary model files
    require_once __DIR__ . '/../../models/Income.php';
    require_once __DIR__ . '/../../models/Expense.php';
    
    // Initialize models
    $incomeModel = new Income();
    $expenseModel = new Expense();
    
    // Set appropriate headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');

    // Handle AJAX requests
    if (isset($_GET['action'])) {
        $response = ['success' => false, 'error' => 'Invalid action'];
        
        if ($_GET['action'] === 'get_income_transaction' && isset($_GET['id'])) {
            $transaction = $incomeModel->getTransactionById($_GET['id']);
            if ($transaction) {
                $response = $transaction;
                $response['success'] = true;
            } else {
                $response['error'] = 'Income transaction not found';
            }
        } 
        elseif ($_GET['action'] === 'get_expense_transaction' && isset($_GET['id'])) {
            $transaction = $expenseModel->getTransactionById($_GET['id']);
            if ($transaction) {
                $response = $transaction;
                $response['success'] = true;
            } else {
                $response['error'] = 'Expense transaction not found';
            }
        }
        
        // Output the JSON response
        echo json_encode($response);
    } 
    else {
        // If no action is specified, return an error
        echo json_encode(['success' => false, 'error' => 'No action specified']);
    }
} else {
    // User not logged in or session expired
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
}

// End output buffering and send the response
ob_end_flush();
?>