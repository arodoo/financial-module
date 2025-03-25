<?php
// filepath: /financial/financial/modules/visualization/views/dashboard/summary.php

// Include necessary models and services
require_once '../models/Dashboard.php';
require_once '../services/VisualizationService.php';

// Initialize the VisualizationService
$visualizationService = new VisualizationService();

// Fetch the summary data
$summaryData = $visualizationService->getDashboardSummary();

// Render the summary view
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Dashboard Summary</title>
</head>
<body>
    <div class="dashboard-summary">
        <h1>Dashboard Summary</h1>
        <div class="summary-data">
            <h2>Income Overview</h2>
            <p>Total Income: <?php echo htmlspecialchars($summaryData['totalIncome']); ?></p>
            <p>Average Monthly Income: <?php echo htmlspecialchars($summaryData['averageMonthlyIncome']); ?></p>

            <h2>Expense Overview</h2>
            <p>Total Expenses: <?php echo htmlspecialchars($summaryData['totalExpenses']); ?></p>
            <p>Average Monthly Expenses: <?php echo htmlspecialchars($summaryData['averageMonthlyExpenses']); ?></p>

            <h2>Net Savings</h2>
            <p>Total Savings: <?php echo htmlspecialchars($summaryData['totalSavings']); ?></p>
        </div>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>