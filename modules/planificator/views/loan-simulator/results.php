<?php
// filepath: /financial/financial/modules/visualization/views/loan-simulator/results.php

// Assuming that the loan simulation results are passed to this view as an associative array
// Example: $simulationResults = ['monthlyPayment' => 500, 'totalPayment' => 60000, 'totalInterest' => 10000];

$simulationResults = isset($simulationResults) ? $simulationResults : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Simulation Results</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Loan Simulation Results</h1>
        <?php if (!empty($simulationResults)): ?>
            <div class="results">
                <h2>Summary</h2>
                <p><strong>Monthly Payment:</strong> $<?php echo number_format($simulationResults['monthlyPayment'], 2); ?></p>
                <p><strong>Total Payment:</strong> $<?php echo number_format($simulationResults['totalPayment'], 2); ?></p>
                <p><strong>Total Interest:</strong> $<?php echo number_format($simulationResults['totalInterest'], 2); ?></p>
            </div>
        <?php else: ?>
            <p>No simulation results available. Please try again.</p>
        <?php endif; ?>
        <a href="index.php" class="btn">Back to Loan Simulator</a>
    </div>
    <script src="../../assets/js/loan-simulator.js"></script>
</body>
</html>