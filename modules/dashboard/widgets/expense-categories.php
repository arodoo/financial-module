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
                                    <td class="text-end">€<?php echo number_format($expense['total'], 2); ?></td>
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
