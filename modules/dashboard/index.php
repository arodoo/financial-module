<?php
require_once __DIR__ . '/../../models/Dashboard.php';
require_once __DIR__ . '/../../models/Membre.php';
require_once __DIR__ . '/../../models/Asset.php';  // Added Asset model

// Initialize models
$dashboardModel = new Dashboard();
$membreModel = new Membre();
$assetModel = new Asset();  // Initialize Asset model
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

// Get total asset value
$totalAssetValue = $assetModel->getTotalAssetValue();
$assetsByCategory = $assetModel->getAssetsByCategory();
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
    <div class="col-md-3 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Revenu Total</h5>
                <h3 class="card-text">₱<?php echo number_format($totalIncome, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Dépenses Totales</h5>
                <h3 class="card-text">₱<?php echo number_format($totalExpense, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card <?php echo $netBalance >= 0 ? 'text-bg-success' : 'text-bg-warning'; ?> h-100">
            <div class="card-body">
                <h5 class="card-title">Solde Net</h5>
                <h3 class="card-text">₱<?php echo number_format($netBalance, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Valeur des Actifs</h5>
                <h3 class="card-text">₱<?php echo number_format($totalAssetValue, 2); ?></h3>
                <p class="card-text"><small><a href="?action=asset-management" class="text-white">Voir Détails</a></small></p>
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

<!-- Assets Summary (if user has assets) -->
<?php if (!empty($assetsByCategory)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Aperçu des Actifs</h5>
                <a href="?action=asset-management" class="btn btn-sm btn-outline-primary">Gérer les Actifs</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Catégorie</th>
                                        <th class="text-end">Valeur</th>
                                        <th class="text-end">% du Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($assetsByCategory, 0, 5) as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category']); ?></td>
                                        <td class="text-end">₱<?php echo number_format($category['total_value'], 2); ?></td>
                                        <td class="text-end">
                                            <?php 
                                            $percentage = $totalAssetValue > 0 ? ($category['total_value'] / $totalAssetValue) * 100 : 0;
                                            echo number_format($percentage, 1) . '%';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <h6>Répartition des Actifs</h6>
                        </div>
                        <canvas id="assetDistributionMiniChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js script for the mini asset distribution chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data for pie chart
    const categories = <?php echo json_encode(array_column($assetsByCategory, 'category')); ?>;
    const values = <?php echo json_encode(array_column($assetsByCategory, 'total_value')); ?>;
    
    // Mini asset distribution chart
    const ctx = document.getElementById('assetDistributionMiniChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#6f42c1'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    display: false
                }
            }
        }
    });
});
</script>
<?php endif; ?>
