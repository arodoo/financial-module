<?php
/**
 * Income and Expense Controller
 */
class IncomeExpenseController {
    private $incomeModel;
    private $expenseModel;

    public function __construct() {
        // Constructor logic
        $this->incomeModel = new Income();
        $this->expenseModel = new Expense();
    }

    public function index() {
        // Display income-expense view
        include_once __DIR__ . '/../views/income-expense/index.php';
    }

    public function trackIncome($data) {
        // Validate and track income
        if ($this->incomeModel->validate($data)) {
            $this->incomeModel->save($data);
            // Redirect or return success response
        } else {
            // Handle validation errors
        }
    }

    public function trackExpense($data) {
        // Validate and track expense
        if ($this->expenseModel->validate($data)) {
            $this->expenseModel->save($data);
            // Redirect or return success response
        } else {
            // Handle validation errors
        }
    }

    public function generateReports() {
        // Generate reports based on income and expense data
        $reports = $this->incomeModel->getReports();
        $reports = array_merge($reports, $this->expenseModel->getReports());
        include '../views/income-expense/reports.php';
    }
}
?>