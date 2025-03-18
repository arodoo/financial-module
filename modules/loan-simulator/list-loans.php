<?php
// This file displays the list of all saved loans in a table view
?>
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Liste des Prêts</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Montant</th>
                        <th>Taux</th>
                        <th class="text-end">Mensualité</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): 
                        // Calculate remaining months/years - term is already in years in the database
                        $startDate = new DateTime($loan['start_date']);
                        $today = new DateTime();
                        $monthsPassed = (($today->format('Y') - $startDate->format('Y')) * 12) + 
                                        ($today->format('n') - $startDate->format('n'));
                        $termInMonths = $loan['term'] * 12;
                        $monthsRemaining = max(0, $termInMonths - $monthsPassed);
                        $yearsRemaining = round($monthsRemaining / 12, 1);
                    ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($loan['name']); ?>
                            <small class="d-block text-muted">Début: <?php echo date('d/m/Y', strtotime($loan['start_date'])); ?></small>
                        </td>
                        <td><?php echo number_format($loan['amount'], 0, ',', ' '); ?>€</td>
                        <td><?php echo $loan['interest_rate']; ?>%</td>
                        <td class="text-end"><?php echo number_format($loan['monthly_payment'], 2, ',', ' '); ?>€</td>
                        <td class="text-center">
                            <a href="?action=loan-simulator&view_loan=<?php echo $loan['id']; ?>" class="btn btn-sm btn-info">Voir</a>
                            <a href="?action=loan-simulator&edit_loan=<?php echo $loan['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                <button type="submit" name="delete_loan" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce prêt?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
