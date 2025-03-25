<?php
// filepath: /financial/modules/visualization/views/income-expense/index.php

// Include necessary controllers
require_once '../controllers/IncomeExpenseController.php';

// Create an instance of the IncomeExpenseController
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/income-expense.js" defer></script>
    <title>Income and Expense Tracking</title>
</head>
<body>
    <div class="container">
        <h1>Income and Expense Tracking</h1>
        <div class="summary">
            <h2>Summary</h2>
            <p>Total Income: <?php echo number_format($totalIncome, 2); ?> </p>
            <p>Total Expense: <?php echo number_format($totalExpense, 2); ?> </p>
            <p>Balance: <?php echo number_format($balance, 2); ?> </p>
        </div>
        <div class="income-expense-list">
            <h2>Income</h2>
            <ul>
                <?php foreach ($incomeData as $income): ?>
                    <li><?php echo htmlspecialchars($income['description']); ?>: <?php echo number_format($income['amount'], 2); ?></li>
                <?php endforeach; ?>
            </ul>
            <h2>Expenses</h2>
            <ul>
                <?php foreach ($expenseData as $expense): ?>
                    <li><?php echo htmlspecialchars($expense['description']); ?>: <?php echo number_format($expense['amount'], 2); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="actions">
            <button onclick="window.location.href='tracking.php'">Track Income/Expense</button>
            <button onclick="window.location.href='reports.php'">View Reports</button>
        </div>
    </div>
</body>
</html>