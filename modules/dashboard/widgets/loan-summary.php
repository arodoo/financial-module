<!-- Loan Summary Widget -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Aperçu des Prêts</h5>
                <a href="?action=loan-simulator" class="btn btn-sm btn-outline-primary">Gérer les Prêts</a>
            </div>
            <div class="card-body">
                <?php
                $loanModel = new Loan();
                $activeLoans = $loanModel->getLoans($id_oo);
                $totalMonthlyPayments = 0;
                
                if (empty($activeLoans)): ?>
                    <p class="text-muted mb-0">Aucun prêt actif.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Prêt</th>
                                            <th class="text-end">Mensualité</th>
                                            <th class="text-end">Capital Restant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($activeLoans, 0, 3) as $loan): 
                                            $totalMonthlyPayments += $loan['monthly_payment'];
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($loan['name']); ?></td>
                                                <td class="text-end"><?php echo number_format($loan['monthly_payment'], 2); ?>€</td>
                                                <td class="text-end"><?php echo number_format($loan['amount'], 2); ?>€</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h6>Total Mensualités</h6>
                                <h3 class="text-primary mb-0"><?php echo number_format($totalMonthlyPayments, 2); ?>€</h3>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
