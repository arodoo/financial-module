<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Revenu Total</h5>
                <h3 class="card-text">€<?php echo number_format($totalIncome, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Dépenses Totales</h5>
                <h3 class="card-text">€<?php echo number_format($totalExpense, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card <?php echo $netBalance >= 0 ? 'text-bg-success' : 'text-bg-warning'; ?> h-100">
            <div class="card-body">
                <h5 class="card-title">Solde Net</h5>
                <h3 class="card-text">€<?php echo number_format($netBalance, 2); ?></h3>
                <p class="card-text"><small>Mois Courant</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Valeur des Actifs</h5>
                <h3 class="card-text">€<?php echo number_format($totalAssetValue, 2); ?></h3>
                <p class="card-text"><small><a href="?action=asset-management" class="text-white">Voir Détails</a></small></p>
            </div>
        </div>
    </div>
</div>
