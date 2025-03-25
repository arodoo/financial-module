<?php
require_once __DIR__ . '/../../models/Dashboard.php';
require_once __DIR__ . '/../../models/Membre.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../models/Loan.php';

// Initialize models
$dashboardModel = new Dashboard();
$membreModel = new Membre();
$assetModel = new Asset();

// Get current month date range
$today = new DateTime();
$startDate = $today->format('Y-m-01'); // First day of current month
$endDate = $today->format('Y-m-t');    // Last day of current month

// Get financial summary
$totalIncome = $dashboardModel->getTotalIncome($startDate, $endDate);
$totalExpense = $dashboardModel->getTotalExpense($startDate, $endDate);
$netBalance = $totalIncome - $totalExpense;

// Get recent transactions
$recentTransactions = $dashboardModel->getRecentTransactions(5);

// Get category totals
$expenseByCategory = $dashboardModel->getCategoryTotals('expense');
$incomeByCategory = $dashboardModel->getCategoryTotals('income');

// Get total asset value
$totalAssetValue = $assetModel->getTotalAssetValue();
$assetsByCategory = $assetModel->getAssetsByCategory();

// Load widget components
include __DIR__ . '/widgets/summary-header.php';
include __DIR__ . '/widgets/summary-cards.php';
include __DIR__ . '/widgets/loan-summary.php';
/* include __DIR__ . '/widgets/school-fee-summary.php'; */
?>

<div class="row">
    <?php 
    include __DIR__ . '/widgets/recent-transactions.php';
    include __DIR__ . '/widgets/expense-categories.php';
    ?>
</div>

<?php 
if (!empty($assetsByCategory)) {
    include __DIR__ . '/widgets/asset-summary.php';
}
?>
