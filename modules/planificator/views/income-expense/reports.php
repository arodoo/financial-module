<?php
// filepath: /financial/modules/visualization/views/income-expense/reports.php

require_once __DIR__ . '/../../../controllers/IncomeExpenseController.php';

$controller = new IncomeExpenseController();
$reportsData = $controller->getReportsData(); // Fetch reports data

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/modules/visualization/assets/css/style.css">
    <title>Income and Expense Reports</title>
</head>
<body>
    <div class="container">
        <h1>Income and Expense Reports</h1>
        
        <div class="reports-summary">
            <h2>Summary</h2>
            <p>Total Income: <?php echo number_format($reportsData['total_income'], 2); ?> </p>
            <p>Total Expenses: <?php echo number_format($reportsData['total_expenses'], 2); ?> </p>
            <p>Net Income: <?php echo number_format($reportsData['net_income'], 2); ?> </p>
        </div>

        <div class="reports-details">
            <h2>Detailed Reports</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Income</th>
                        <th>Expense</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportsData['details'] as $report): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['date']); ?></td>
                            <td><?php echo htmlspecialchars($report['description']); ?></td>
                            <td><?php echo number_format($report['income'], 2); ?></td>
                            <td><?php echo number_format($report['expense'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="/modules/visualization/assets/js/income-expense.js"></script>
</body>
</html>