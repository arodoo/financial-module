<?php
/**
 * Loan Simulator Controller
 */
class LoanSimulatorController {
    public function __construct() {
        // Constructor logic
    }
    
    public function index() {
        // Display loan simulator view
        include_once __DIR__ . '/../views/loan-simulator/index.php';
    }
}
?>