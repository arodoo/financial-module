<?php
// This file displays the calculation results after form submission
?>
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Résultats du Calcul</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Paiement Mensuel</h6>
                    <h3 class="text-primary"><?php echo number_format($results['monthlyPayment'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Intérêts Totaux</h6>
                    <h3 class="text-danger"><?php echo number_format($results['totalInterest'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Coût Total</h6>
                    <h3><?php echo number_format($results['totalPayment'], 2, ',', ' '); ?>€</h3>
                </div>
            </div>
        </div>
        
        <!-- Save Loan Form -->
        <form method="POST" class="mb-3 border-bottom pb-3">
            <input type="hidden" name="loan_amount" value="<?php echo $_POST['loan_amount']; ?>">
            <input type="hidden" name="interest_rate" value="<?php echo $_POST['interest_rate']; ?>">
            <input type="hidden" name="loan_term" value="<?php echo $_POST['loan_term']; ?>">
            <input type="hidden" name="monthly_payment" value="<?php echo $results['monthlyPayment']; ?>">
            <input type="hidden" name="start_date" value="<?php echo $_POST['start_date'] ?? date('Y-m-d'); ?>">
            
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <input type="text" class="form-control" name="loan_name" placeholder="Nom du prêt (ex: Maison Paris)" required>
                </div>
                <div class="col-12 col-sm-6">
                    <select class="form-select" name="asset_id">
                        <option value="">Lier à un actif immobilier</option>
                        <?php if (!empty($realEstateAssets)): ?>
                            <?php foreach ($realEstateAssets as $asset): ?>
                                <option value="<?php echo htmlspecialchars($asset['id']); ?>">
                                    <?php echo htmlspecialchars(isset($asset['name']) ? $asset['name'] : 'Actif #' . $asset['id']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" name="save_loan" class="btn btn-success">Enregistrer</button>
                        <a href="?action=loan-simulator" class="btn btn-secondary">Annuler</a>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Amortization Schedule -->
        <h5 class="mb-3">Tableau d'Amortissement</h5>
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
                    $balance = $_POST['loan_amount'];
                    $term = $_POST['loan_term'];
                    $monthlyRate = ($_POST['interest_rate'] / 100) / 12;
                    $monthlyPayment = $results['monthlyPayment'];
                    $totalPrincipal = 0;
                    $totalInterest = 0;
                    
                    for ($year = 1; $year <= min(ceil($term / 12), 30); $year++):
                        $yearlyPrincipal = 0;
                        $yearlyInterest = 0;
                        
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
                    <tr>
                        <td><?php echo $year; ?></td>
                        <td class="text-end"><?php echo number_format($yearlyPrincipal, 2, ',', ' '); ?>€</td>
                        <td class="text-end"><?php echo number_format($yearlyInterest, 2, ',', ' '); ?>€</td>
                        <td class="text-end"><?php echo number_format($balance, 2, ',', ' '); ?>€</td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Integration with other modules -->
        <div class="mt-3">
            <h5 class="mb-3">Intégration avec d'autres modules</h5>
            <div class="d-flex gap-2">
                <a href="?action=income-expense" class="btn btn-outline-primary">
                    Ajouter aux Dépenses Mensuelles
                </a>
                <a href="?action=asset-management" class="btn btn-outline-primary">
                    Gérer les Actifs Immobiliers
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js initialization for fee projections -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('feeProjectionChart');
    
    if (ctx) {
        // Extract data for years
        const years = [];
        const principalData = [];
        const interestData = [];
        const balanceData = [];
        
        // Extract data from the table
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length === 4) {
                years.push(cells[0].textContent);
                principalData.push(parseFloat(cells[1].textContent.replace(/[€\s.]/g, '').replace(',', '.')));
                interestData.push(parseFloat(cells[2].textContent.replace(/[€\s.]/g, '').replace(',', '.')));
                balanceData.push(parseFloat(cells[3].textContent.replace(/[€\s.]/g, '').replace(',', '.')));
            }
        });
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Capital Payé',
                        data: principalData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Intérêts Payés',
                        data: interestData,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Solde Restant',
                        data: balanceData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + '€';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
