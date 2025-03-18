<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Transactions de <?php echo $transactionType === 'income' ? 'Revenus' : 'Dépenses'; ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $transactions = $transactionType === 'income' ? $incomeTransactions : $expenseTransactions;
                    if (empty($transactions)): 
                    ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                Aucune transaction <?php echo $transactionType === 'income' ? 'de revenu' : 'de dépense'; ?> trouvée pour cette période.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></td>
                                <td><?php echo $transaction['category_name']; ?></td>
                                <td><?php echo $transaction['description'] ?: 'N/A'; ?></td>
                                <td class="text-end">€<?php echo number_format($transaction['amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
