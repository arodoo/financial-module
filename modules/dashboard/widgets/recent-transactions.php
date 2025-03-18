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
                                    <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>€<?php echo number_format($transaction['amount'], 2); ?>
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
