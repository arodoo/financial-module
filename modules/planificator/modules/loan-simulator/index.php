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
    } elseif (isset($_POST['update_loan'])) {
        $loanController->updateLoan($_POST);
        header('Location: ?action=loan-simulator&success=loan_updated');
        exit;
    } elseif (isset($_POST['delete_loan'])) {
        $loanController->deleteLoan($_POST['loan_id']);
        header('Location: ?action=loan-simulator&success=loan_deleted');
        exit;
    }
}

// Get data for the view
$viewData = $loanController->getViewData();
$loans = $viewData['loans'] ?? [];
$selectedLoan = $viewData['selectedLoan'] ?? null;
$viewLoan = $viewData['viewLoan'] ?? null;
$editLoan = $viewData['editLoan'] ?? null;
$results = $viewData['calculationResults'] ?? null;

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

// Check for success messages
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'loan_saved':
            $successMessage = 'Prêt enregistré avec succès!';
            break;
        case 'loan_updated':
            $successMessage = 'Prêt mis à jour avec succès!';
            break;
        case 'loan_deleted':
            $successMessage = 'Prêt supprimé avec succès!';
            break;
    }
}
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
            <div class="card-header bg-<?php echo $editLoan ? 'warning' : 'primary'; ?> text-white">
                <h5 class="mb-0"><?php echo $editLoan ? 'Modifier le Prêt' : 'Calculateur de Prêt'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" id="loan-calculator-form">
                    <?php if ($editLoan): ?>
                        <input type="hidden" name="loan_id" value="<?php echo $editLoan['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <?php if ($editLoan): ?>
                            <label for="loan_name" class="form-label">Nom du Prêt</label>
                            <input type="text" class="form-control" id="loan_name" name="loan_name" 
                                value="<?php echo htmlspecialchars($editLoan['name']); ?>" required>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="loan_amount" class="form-label">Montant du Prêt (€)</label>
                        <input type="text" class="form-control" id="loan_amount" name="loan_amount" 
                            value="<?php echo isset($_POST['loan_amount']) ? number_format($_POST['loan_amount'], 0, ',', ' ') : 
                                ($editLoan ? number_format($editLoan['amount'], 0, ',', ' ') : '100 000'); ?>" 
                             required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Taux d'Intérêt Annuel (%)</label>
                        <input type="number" class="form-control" id="interest_rate" name="interest_rate" 
                            min="0.1" step="0.01" value="<?php echo $_POST['interest_rate'] ?? ($editLoan ? $editLoan['interest_rate'] : 3); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="loan_term" class="form-label">Durée du Prêt (mois)</label>
                        <input type="number" class="form-control" id="loan_term" name="loan_term" 
                            min="1" max="480" value="<?php echo $_POST['loan_term'] ?? ($editLoan ? $editLoan['term'] : 240); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Date de Début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                            value="<?php echo $_POST['start_date'] ?? ($editLoan ? $editLoan['start_date'] : date('Y-m-d')); ?>">
                    </div>
                    
                    <?php if ($editLoan && !empty($realEstateAssets)): ?>
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Actif Immobilier Lié</label>
                        <select class="form-select" id="asset_id" name="asset_id">
                            <option value="">Aucun</option>
                            <?php foreach ($realEstateAssets as $asset): ?>
                                <option value="<?php echo $asset['id']; ?>" <?php echo ($editLoan['asset_id'] == $asset['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(isset($asset['name']) ? $asset['name'] : 'Actif #' . $asset['id']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($editLoan): ?>
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="update_loan" class="btn btn-warning">Mettre à jour</button>
                            <a href="?action=loan-simulator" class="btn btn-secondary">Annuler</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="calculate_loan" class="btn btn-primary w-100">Calculer</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Saved Loans, Calculation Results, or Loan Details -->
    <div class="col-md-7">
        <?php if ($viewLoan): ?>
            <?php include __DIR__ . '/view-loan.php'; ?>
        <?php elseif ($results): ?>
            <?php include __DIR__ . '/calculation-results.php'; ?>
        <?php elseif (!empty($loans)): ?>
            <?php include __DIR__ . '/list-loans.php'; ?>
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
