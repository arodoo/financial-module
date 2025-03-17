<?php
require_once __DIR__ . '/../../models/Dashboard.php';
require_once __DIR__ . '/../../models/Membre.php';

// Initialize dashboard model
$dashboardModel = new Dashboard();
$membreModel = new Membre();
$currentMembre = $membreModel->getMembre($id_oo);

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
?>

<!-- Month Summary -->
<div class="alert alert-info">
    Résumé financier pour <?php echo date('F Y'); ?> 
    <?php if ($currentMembre): ?>
        - <?php echo htmlspecialchars($currentMembre['prenom'] . ' ' . $currentMembre['nom']); ?>
    <?php endif; ?>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Revenu Total</h5>
                <h3 class="card-text">₱<?php echo number_format($totalIncome, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Dépenses Totales</h5>
                <h3 class="card-text">₱<?php echo number_format($totalExpense, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card <?php echo $netBalance >= 0 ? 'text-bg-success' : 'text-bg-warning'; ?> h-100">
            <div class="card-body">
                <h5 class="card-title">Solde Net</h5>
                <h3 class="card-text">₱<?php echo number_format($netBalance, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transactions Récentes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentTransactions)): ?>
                    <p class="text-muted">Aucune transaction enregistrée pour le moment.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($recentTransactions as $transaction): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($transaction['category']); ?></h6>
                                    <small><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($transaction['description'] ?: 'Pas de description'); ?></p>
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-muted"><?php echo $transaction['type'] === 'income' ? 'Revenu' : 'Dépense'; ?></small>
                                    <span class="<?php echo $transaction['type'] === 'income' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>₱<?php echo number_format($transaction['amount'], 2); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="?action=income-expense" class="btn btn-sm btn-outline-primary">Voir Toutes les Transactions</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense by Category -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dépenses par Catégorie</h5>
            </div>
            <div class="card-body">
                <?php if (empty($expenseByCategory)): ?>
                    <p class="text-muted">Aucune donnée de dépense disponible.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenseByCategory as $expense): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                        <td class="text-end">₱<?php echo number_format($expense['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="?action=income-expense" class="btn btn-sm btn-outline-primary">Voir Rapport Détaillé</a>
                </div>
            </div>
        </div>
    </div>
</div>
