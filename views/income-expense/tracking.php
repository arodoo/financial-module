<?php
// filepath: /financial/modules/visualization/views/income-expense/tracking.php

// Include necessary controllers and models
require_once '../controllers/IncomeExpenseController.php';
require_once '../models/Income.php';
require_once '../models/Expense.php';

// Initialize the IncomeExpenseController
$incomeExpenseController = new IncomeExpenseController();

// Fetch income and expense data
$incomeData = $incomeExpenseController->getIncomeData();
$expenseData = $incomeExpenseController->getExpenseData();

// Calculate totals
$totalIncome = array_sum(array_column($incomeData, 'amount'));
$totalExpense = array_sum(array_column($expenseData, 'amount'));
$balance = $totalIncome - $totalExpense;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/income-expense.js" defer></script>
    <title>Income and Expense Tracking</title>
</head>
<body>
    <div class="container">
        <h1>Income and Expense Tracking</h1>
        <div class="summary">
            <h2>Summary</h2>
            <p>Total Income: <?php echo number_format($totalIncome, 2); ?> USD</p>
            <p>Total Expense: <?php echo number_format($totalExpense, 2); ?> USD</p>
            <p>Balance: <?php echo number_format($balance, 2); ?> USD</p>
        </div>

        <div class="income-section">
            <h2>Income Entries</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incomeData as $income): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($income['date']); ?></td>
                            <td><?php echo htmlspecialchars($income['description']); ?></td>
                            <td><?php echo number_format($income['amount'], 2); ?> USD</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="expense-section">
            <h2>Expense Entries</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenseData as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['date']); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo number_format($expense['amount'], 2); ?> USD</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>