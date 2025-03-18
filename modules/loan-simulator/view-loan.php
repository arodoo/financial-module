<?php
// This file displays the detailed view of a loan with amortization schedule

// Calculate the remaining loan balance and other metrics
$loanController->calculateLoanDetails($viewLoan);
$loanDetails = $loanController->getLoanDetails();

// Current date for calculations
$currentDate = new DateTime();
$startDate = new DateTime($viewLoan['start_date']);
$monthsPassed = (($currentDate->format('Y') - $startDate->format('Y')) * 12) + 
                ($currentDate->format('n') - $startDate->format('n'));
$monthsPassed = max(0, $monthsPassed);

// Get linked asset if available
$linkedAsset = null;
if (!empty($viewLoan['asset_id'])) {
    try {
        $linkedAsset = $assetModel->getAssetById($viewLoan['asset_id']);
    } catch (Exception $e) {
        // Handle silently
    }
}
?>

<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails du Prêt: <?php echo htmlspecialchars($viewLoan['name']); ?></h5>
        <a href="?action=loan-simulator" class="btn btn-sm btn-light">Retour</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Nom du Prêt:</strong> <?php echo htmlspecialchars($viewLoan['name']); ?></p>
                <p><strong>Montant Initial:</strong> <?php echo number_format($viewLoan['amount'], 2, ',', ' '); ?>€</p>
                <p><strong>Taux d'Intérêt:</strong> <?php echo $viewLoan['interest_rate']; ?>%</p>
                <p><strong>Durée du Prêt:</strong> <?php echo $viewLoan['term']; ?> mois (<?php echo number_format($viewLoan['term']/12, 1, ',', ''); ?> ans)</p>
            </div>
            <div class="col-md-6">
                <p><strong>Mensualité:</strong> <?php echo number_format($viewLoan['monthly_payment'], 2, ',', ' '); ?>€</p>
                <p><strong>Date de Début:</strong> <?php echo date('d/m/Y', strtotime($viewLoan['start_date'])); ?></p>
                <p><strong>Mois Écoulés:</strong> <?php echo $monthsPassed; ?> mois</p>
                <?php if ($linkedAsset): ?>
                <p><strong>Actif Lié:</strong> <?php echo htmlspecialchars($linkedAsset['name'] ?? 'Actif #' . $linkedAsset['id']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($loanDetails)): ?>
        <hr>
        <h6 class="mb-3">Résumé du Prêt</h6>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Solde Actuel</h6>
                    <h3 class="text-primary"><?php echo number_format($loanDetails['currentBalance'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Intérêts Déjà Payés</h6>
                    <h3 class="text-danger"><?php echo number_format($loanDetails['interestPaid'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Capital Remboursé</h6>
                    <h3 class="text-success"><?php echo number_format($loanDetails['principalPaid'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
        </div>
        
        <!-- Loan Progress Chart -->
        <h6 class="mb-3">État d'Avancement du Prêt</h6>
        <div class="mb-4">
            <canvas id="loanProgressChart" width="400" height="200"></canvas>
        </div>
        
        <!-- Amortization Schedule -->
        <h6 class="mb-3">Tableau d'Amortissement</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th class="text-end">Capital Payé</th>
                        <th class="text-end">Intérêts Payés</th>
                        <th class="text-end">Solde Restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $balance = $viewLoan['amount'];
                    $term = $viewLoan['term'];
                    $monthlyRate = ($viewLoan['interest_rate'] / 100) / 12;
                    $monthlyPayment = $viewLoan['monthly_payment'];
                    $totalPrincipal = 0;
                    $totalInterest = 0;
                    $currentYear = $startDate->format('Y');
                    
                    for ($year = 1; $year <= min(ceil($term / 12), 30); $year++):
                        $yearlyPrincipal = 0;
                        $yearlyInterest = 0;
                        $yearDisplay = $startDate->format('Y') + $year - 1;
                        
                        // Highlight current year
                        $rowClass = ($yearDisplay == date('Y')) ? 'table-info' : '';
                        
                        for ($month = 1; $month <= 12; $month++) {
                            if (($year - 1) * 12 + $month > $term) break;
                            
                            $interestPayment = $balance * $monthlyRate;
                            $principalPayment = $monthlyPayment - $interestPayment;
                            
                            $yearlyPrincipal += $principalPayment;
                            $yearlyInterest += $interestPayment;
                            $balance -= $principalPayment;
                            
                            if ($balance <= 0) {
                                $balance = 0;
                                break;
                            }
                        }
                        $totalPrincipal += $yearlyPrincipal;
                        $totalInterest += $yearlyInterest;
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo $yearDisplay; ?></td>
                        <td class="text-end"><?php echo number_format($yearlyPrincipal, 2, ',', ' '); ?>€</td>
                        <td class="text-end"><?php echo number_format($yearlyInterest, 2, ',', ' '); ?>€</td>
                        <td class="text-end"><?php echo number_format($balance, 2, ',', ' '); ?>€</td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Chart.js initialization for loan progress -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('loanProgressChart').getContext('2d');
            
            // Data for progress chart
            const data = {
                labels: ['Remboursé', 'Restant'],
                datasets: [{
                    data: [
                        <?php echo $loanDetails['principalPaid']; ?>,
                        <?php echo $loanDetails['currentBalance']; ?>
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(54, 162, 235, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            
            new Chart(ctx, {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
        </script>
        <?php endif; ?>
        
        <div class="mt-3 d-flex gap-2">
            <a href="?action=loan-simulator&edit_loan=<?php echo $viewLoan['id']; ?>" class="btn btn-warning">Modifier</a>
            <form method="POST" class="d-inline">
                <input type="hidden" name="loan_id" value="<?php echo $viewLoan['id']; ?>">
                <button type="submit" name="delete_loan" class="btn btn-danger" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce prêt?')">Supprimer</button>
            </form>
        </div>
    </div>
</div>
