<?php
// Include necessary controllers and models
require_once __DIR__ . '/../../controllers/LoanController.php';
require_once __DIR__ . '/../../models/Loan.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../models/Membre.php';

// Initialize controller
$loanController = new LoanController();
$assetModel = new Asset();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['calculate_loan'])) {
        $loanController->calculateLoan($_POST);
    } elseif (isset($_POST['save_loan'])) {
        $loanController->saveLoan($_POST);
        header('Location: ?action=loan-simulator&success=loan_saved');
        exit;
    }
}

// Get real estate assets for linking
$realEstateAssets = []; // Initialize as empty array

try {
    // Get all assets and filter for real estate (category 1)
    $allAssets = $assetModel->getAllAssets();
    if (is_array($allAssets)) {
        foreach ($allAssets as $asset) {
            if (isset($asset['category_id']) && $asset['category_id'] == 1) {
                $realEstateAssets[] = $asset;
            }
        }
    }
} catch (Exception $e) {
    // Silently handle any errors
}

// Get saved loans
$savedLoans = $loanController->getSavedLoans();

// Check for success messages
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'loan_saved':
            $successMessage = 'Prêt enregistré avec succès!';
            break;
        case 'loan_deleted':
            $successMessage = 'Prêt supprimé avec succès!';
            break;
    }
}

// Get calculation results if available
$results = $loanController->getCalculationResults();
?>

<!-- Success Message -->
<?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Loan Calculator Form -->
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Calculateur de Prêt</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="loan-calculator-form">
                    <div class="mb-3">
                        <label for="loan_amount" class="form-label">Montant du Prêt (€)</label>
                        <input type="text" class="form-control" id="loan_amount" name="loan_amount" 
                            value="<?php echo isset($_POST['loan_amount']) ? number_format($_POST['loan_amount'], 0, ',', ' ') : '100 000'; ?>" 
                            pattern="[0-9 ]*" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Taux d'Intérêt Annuel (%)</label>
                        <input type="number" class="form-control" id="interest_rate" name="interest_rate" 
                            min="0.1" step="0.01" value="<?php echo $_POST['interest_rate'] ?? 3; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="loan_term" class="form-label">Durée du Prêt (mois)</label>
                        <input type="number" class="form-control" id="loan_term" name="loan_term" 
                            min="1" max="480" value="<?php echo $_POST['loan_term'] ?? 240; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Date de Début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $_POST['start_date'] ?? date('Y-m-d'); ?>">
                    </div>
                    
                    <button type="submit" name="calculate_loan" class="btn btn-primary w-100">Calculer</button>
                </form>
            </div>
        </div>
        
        <!-- Saved Loans -->
        <?php if (!empty($savedLoans)): ?>
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Prêts Enregistrés</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($savedLoans as $loan): ?>
                    <a href="?action=loan-simulator&loan_id=<?php echo $loan['id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?php echo htmlspecialchars($loan['name']); ?></h6>
                            <small><?php echo number_format($loan['monthly_payment'], 2); ?>€/mois</small>
                        </div>
                        <p class="mb-1">
                            <?php echo number_format($loan['amount'], 0, ',', ' '); ?>€ 
                            à <?php echo $loan['interest_rate']; ?>% 
                            sur <?php echo $loan['term']; ?> ans
                        </p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Calculation Results -->
    <div class="col-md-7">
        <?php if ($results): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Résultats du Calcul</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Paiement Mensuel</h6>
                            <h3 class="text-primary"><?php echo number_format($results['monthlyPayment'], 2); ?>€</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Intérêts Totaux</h6>
                            <h3 class="text-danger"><?php echo number_format($results['totalInterest'], 2); ?>€</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Coût Total</h6>
                            <h3><?php echo number_format($results['totalPayment'], 2); ?>€</h3>
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
                            
                            for ($year = 1; $year <= min($term, 30); $year++):
                                $yearlyPrincipal = 0;
                                $yearlyInterest = 0;
                                
                                for ($month = 1; $month <= 12; $month++) {
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
                                <td class="text-end"><?php echo number_format($yearlyPrincipal, 2); ?>€</td>
                                <td class="text-end"><?php echo number_format($yearlyInterest, 2); ?>€</td>
                                <td class="text-end"><?php echo number_format($balance, 2); ?>€</td>
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
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <h4>Calculez votre prêt</h4>
                    <p class="text-muted">
                        Remplissez le formulaire pour calculer vos mensualités de prêt et pour voir un aperçu de votre tableau d'amortissement.
                    </p>
                    <img src="https://via.placeholder.com/400x200?text=Loan+Simulator" alt="Loan Simulator" class="img-fluid mt-3 mb-3 rounded">
                    <p>
                        Le simulateur de prêt vous permet de:
                    </p>
                    <ul class="text-start">
                        <li>Calculer vos mensualités en fonction du montant, du taux et de la durée</li>
                        <li>Estimer le coût total de votre emprunt</li>
                        <li>Visualiser l'amortissement année par année</li>
                        <li>Enregistrer vos simulations pour référence future</li>
                        <li>Lier vos prêts à vos actifs immobiliers</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle loan amount formatting
    const loanAmountInput = document.getElementById('loan_amount');
    if (loanAmountInput) {
        loanAmountInput.addEventListener('input', function(e) {
            // Remove all non-digits
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('fr-FR').format(value);
        });
        
        // Before form submission, clean the input
        loanAmountInput.form.addEventListener('submit', function(e) {
            loanAmountInput.value = loanAmountInput.value.replace(/\D/g, '');
        });
    }
});
</script>
