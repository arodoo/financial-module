<?php
require_once __DIR__ . '/../../models/Income.php';
require_once __DIR__ . '/../../models/Expense.php';

// Initialize models
$incomeModel = new Income();
$expenseModel = new Expense();

// Set default date range to current month
$today = new DateTime();
$startDate = $today->format('Y-m-01'); 
$endDate = $today->format('Y-m-t');    

// Process filter form if submitted
if (isset($_GET['filter'])) {
    $startDate = $_GET['start_date'] ?? $startDate;
    $endDate = $_GET['end_date'] ?? $endDate;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_income'])) {
        $incomeModel->addIncome(
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'],
            $_POST['transaction_date']
        );
        header("Location: ?action=income-expense&success=income_added");
        exit;
    }
    
    if (isset($_POST['add_expense'])) {
        $expenseModel->addExpense(
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'],
            $_POST['transaction_date']
        );
        header("Location: ?action=income-expense&success=expense_added");
        exit;
    }
}

// Get data for the dashboard
$incomeCategories = $incomeModel->getAllCategories();
$expenseCategories = $expenseModel->getAllCategories();
$incomeTransactions = $incomeModel->getIncomeTransactions($startDate, $endDate);
$expenseTransactions = $expenseModel->getExpenseTransactions($startDate, $endDate);

$totalIncome = $incomeModel->getTotalIncome($startDate, $endDate);
$totalExpense = $expenseModel->getTotalExpense($startDate, $endDate);
$netBalance = $totalIncome - $totalExpense;

include 'view.php';
