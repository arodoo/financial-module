<?php
// This file displays detailed view of a specific asset
$categoryName = '';
foreach ($categories as $category) {
    if ($category['id'] == $viewAsset['category_id']) {
        $categoryName = $category['name'];
        break;
    }
}

// Find linked loan if exists
$linkedLoan = null;
if (!empty($viewAsset['loan_id'])) {
    $loanController = new LoanController();
    $linkedLoan = $loanController->getLoanById($viewAsset['loan_id']);
}

// Format acquisition and value dates
$acquisitionDate = !empty($viewAsset['acquisition_date']) ? date('d/m/Y', strtotime($viewAsset['acquisition_date'])) : 'N/A';
$lastValuationDate = !empty($viewAsset['valuation_date']) ? date('d/m/Y', strtotime($viewAsset['valuation_date'])) : 'N/A';
?>

<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails de l'Actif: <?php echo htmlspecialchars($viewAsset['name']); ?></h5>
        <a href="?action=asset-management" class="btn btn-sm btn-light">Retour</a>
    </div>
    <div class="card-body">
        <!-- Asset Details Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Nom:</strong> <?php echo htmlspecialchars($viewAsset['name']); ?></p>
                <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($categoryName); ?></p>
                <p><strong>Date d'acquisition:</strong> <?php echo $acquisitionDate; ?></p>
                <p><strong>Prix d'acquisition:</strong> <?php echo number_format($viewAsset['acquisition_value'], 0, ',', ' '); ?>€</p>
            </div>
            <div class="col-md-6">
                <p><strong>Valeur actuelle:</strong> <?php echo number_format($viewAsset['current_value'], 0, ',', ' '); ?>€</p>
                <p><strong>Dernière évaluation:</strong> <?php echo $lastValuationDate; ?></p>
                <?php if ($viewAsset['acquisition_value'] > 0): ?>
                    <?php $valueChange = (($viewAsset['current_value'] - $viewAsset['acquisition_value']) / $viewAsset['acquisition_value']) * 100; ?>
                    <p><strong>Évolution:</strong> 
                        <span class="<?php echo $valueChange >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo ($valueChange >= 0 ? '+' : '') . number_format($valueChange, 2); ?>%
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($viewAsset['location'])): ?>
                    <p><strong>Emplacement:</strong> <?php echo htmlspecialchars($viewAsset['location']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Linked Loan Section (if applicable) -->
        <?php if ($linkedLoan): ?>
        <hr>
        <h6 class="mb-3">Prêt Associé</h6>
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Nom du Prêt:</strong> <?php echo htmlspecialchars($linkedLoan['name']); ?></p>
                <p><strong>Montant Initial:</strong> <?php echo number_format($linkedLoan['amount'], 0, ',', ' '); ?>€</p>
                <p><strong>Taux d'Intérêt:</strong> <?php echo $linkedLoan['interest_rate']; ?>%</p>
            </div>
            <div class="col-md-6">
                <p><strong>Mensualité:</strong> <?php echo number_format($linkedLoan['monthly_payment'], 2, ',', ' '); ?>€</p>
                <p><strong>Date de Début:</strong> <?php echo date('d/m/Y', strtotime($linkedLoan['start_date'])); ?></p>
                <p><strong>Durée:</strong> <?php echo $linkedLoan['term'] * 12; ?> mois (<?php echo $linkedLoan['term']; ?> ans)</p>
            </div>
            <div class="col-12 mt-2">
                <a href="?action=loan-simulator&view_loan=<?php echo $linkedLoan['id']; ?>" class="btn btn-sm btn-primary">
                    Voir les détails du prêt
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Notes Section -->
        <?php if (!empty($viewAsset['notes'])): ?>
        <hr>
        <h6 class="mb-3">Notes</h6>
        <div class="p-3 border rounded bg-light">
            <?php echo nl2br(htmlspecialchars($viewAsset['notes'])); ?>
        </div>
        <?php endif; ?>
        
        <!-- Value Evolution Chart -->
        <hr>
        <h6 class="mb-3">Évolution de la Valeur</h6>
        <div class="mb-4">
            <canvas id="assetValueChart" width="400" height="200"></canvas>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-3 d-flex gap-2">
            <a href="?action=asset-management&edit_asset=<?php echo $viewAsset['id']; ?>" class="btn btn-warning">Modifier</a>
            <form method="POST" class="d-inline">
                <input type="hidden" name="asset_id" value="<?php echo $viewAsset['id']; ?>">
                <button type="submit" name="delete_asset" class="btn btn-danger" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet actif?')">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js initialization for asset value chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('assetValueChart').getContext('2d');
    
    // Sample data - in a real implementation, this would come from the database
    // showing the historical values of the asset
    const labels = ['Acquisition', 'Aujourd\'hui'];
    const values = [<?php echo $viewAsset['acquisition_value']; ?>, <?php echo $viewAsset['current_value']; ?>];
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Valeur de l\'actif',
                data: values,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
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
});
</script>
