<?php
// filepath: /financial/financial/modules/visualization/views/dashboard/index.php

// Include necessary controllers and models
require_once '../controllers/DashboardController.php';
require_once '../models/Dashboard.php';

// Initialize the DashboardController
$dashboardController = new DashboardController();
$summaryData = $dashboardController->getSummaryData();

// Render the dashboard view
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/dashboard.js" defer></script>
    <title>Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <h1>Financial Dashboard</h1>
        <div class="summary">
            <h2>Summary</h2>
            <p>Total Income: <?php echo htmlspecialchars($summaryData['total_income']); ?></p>
            <p>Total Expenses: <?php echo htmlspecialchars($summaryData['total_expenses']); ?></p>
            <p>Net Balance: <?php echo htmlspecialchars($summaryData['net_balance']); ?></p>
        </div>
        <div class="charts">
            <h2>Income and Expenses Over Time</h2>
            <canvas id="incomeExpenseChart"></canvas>
        </div>
        <div class="actions">
            <a href="../income-expense/index.php">Track Income and Expenses</a>
            <a href="../asset-management/index.php">Manage Assets</a>
            <a href="../loan-simulator/index.php">Simulate Loans</a>
            <a href="../school-fee/index.php">Simulate School Fees</a>
        </div>
    </div>
</body>
</html>