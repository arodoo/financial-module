<?php
// projections.php

// Include necessary models and services
require_once '../models/SchoolFee.php';
require_once '../services/CalculationService.php';

// Initialize the SchoolFee model and CalculationService
$schoolFeeModel = new SchoolFee();
$calculationService = new CalculationService();

// Fetch school fee data for projections
$projections = $schoolFeeModel->getProjections();

// Calculate future projections based on current data
$futureProjections = $calculationService->calculateFutureProjections($projections);

// Render the projections view
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Fee Projections</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>School Fee Projections</h1>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Projected Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($futureProjections as $year => $fee): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($year); ?></td>
                        <td><?php echo htmlspecialchars(number_format($fee, 2)); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>