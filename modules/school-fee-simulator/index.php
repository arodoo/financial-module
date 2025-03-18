<?php
// Include necessary controllers and models
require_once __DIR__ . '/../../controllers/SchoolFeeController.php';
require_once __DIR__ . '/../../models/SchoolFee.php';
require_once __DIR__ . '/../../services/CalculationService.php';

// Initialize controller and services
$schoolFeeController = new SchoolFeeController();

// Process form submissions or actions
$schoolFeeController->processRequest();

// Get data for the view
$viewData = $schoolFeeController->getViewData();
$children = $viewData['children'] ?? [];
$selectedChild = $viewData['selectedChild'] ?? null;
$editChild = $viewData['editChild'] ?? null;
$viewChild = $viewData['viewChild'] ?? null;
$results = $viewData['calculationResults'] ?? null;

// Check for success messages
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'child_saved':
            $successMessage = 'Profil enfant enregistré avec succès!';
            break;
        case 'child_updated':
            $successMessage = 'Profil enfant mis à jour avec succès!';
            break;
        case 'child_deleted':
            $successMessage = 'Profil enfant supprimé avec succès!';
            break;
    }
}

// French education system levels with typical ages
$educationLevels = [
    'maternelle' => ['name' => 'Maternelle', 'ages' => '3-5', 'duration' => 3],
    'primaire' => ['name' => 'École primaire', 'ages' => '6-10', 'duration' => 5],
    'college' => ['name' => 'Collège', 'ages' => '11-14', 'duration' => 4],
    'lycee' => ['name' => 'Lycée', 'ages' => '15-17', 'duration' => 3],
    'superieur' => ['name' => 'Études supérieures', 'ages' => '18+', 'duration' => 5]
];

// Pre-fill form with selected child data if available
if ($selectedChild && !$editChild && !$results) {
    // Calculate fees based on the selected child
    $schoolFeeController->calculateFees($selectedChild);
    $results = $schoolFeeController->getCalculationResults();
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
    <!-- School Fee Calculator Form -->
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-<?php echo $editChild ? 'warning' : 'primary'; ?> text-white">
                <h5 class="mb-0"><?php echo $editChild ? 'Modifier le Profil Enfant' : 'Simulateur de Frais de Scolarité'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" id="school-fee-calculator-form">
                    <?php if ($editChild): ?>
                        <input type="hidden" name="child_id" value="<?php echo $editChild['id']; ?>">
                    <?php endif; ?>
                    
                    <h6 class="mb-3">Informations sur l'Enfant</h6>
                    <div class="mb-3">
                        <label for="child_name" class="form-label">Prénom de l'enfant</label>
                        <input type="text" class="form-control" id="child_name" name="child_name" 
                            value="<?php echo $editChild ? htmlspecialchars($editChild['name']) : ($selectedChild ? htmlspecialchars($selectedChild['name']) : ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="child_birthdate" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="child_birthdate" name="child_birthdate" 
                            value="<?php echo $editChild ? $editChild['birthdate'] : ($selectedChild ? $selectedChild['birthdate'] : ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_level" class="form-label">Niveau scolaire actuel</label>
                        <select class="form-select" id="current_level" name="current_level" required>
                            <?php foreach ($educationLevels as $key => $level): ?>
                                <option value="<?php echo $key; ?>" <?php echo (($editChild && $editChild['current_level'] == $key) || ($selectedChild && $selectedChild['current_level'] == $key)) ? 'selected' : ''; ?>>
                                    <?php echo $level['name'] . ' (' . $level['ages'] . ' ans)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <h6 class="mb-3 mt-4">Informations sur les Frais</h6>
                    <div class="mb-3">
                        <label for="school_name" class="form-label">Nom de l'école</label>
                        <input type="text" class="form-control" id="school_name" name="school_name" 
                            value="<?php echo $editChild ? htmlspecialchars($editChild['school_name']) : ($selectedChild ? htmlspecialchars($selectedChild['school_name']) : ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="annual_tuition" class="form-label">Frais de scolarité annuels (€)</label>
                        <input type="text" class="form-control" id="annual_tuition" name="annual_tuition" 
                             
                            value="<?php echo $editChild ? number_format($editChild['annual_tuition'], 0, ',', ' ') : ($selectedChild ? number_format($selectedChild['annual_tuition'], 0, ',', ' ') : ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="additional_expenses" class="form-label">Dépenses supplémentaires annuelles (€)</label>
                        <input type="text" class="form-control" id="additional_expenses" name="additional_expenses" 
                            value="<?php echo $editChild ? number_format($editChild['additional_expenses'], 0, ',', ' ') : ($selectedChild ? number_format($selectedChild['additional_expenses'], 0, ',', ' ') : ''); ?>">
                        <small class="form-text text-muted">Uniformes, livres, activités extrascolaires, etc.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inflation_rate" class="form-label">Taux d'inflation estimé (%)</label>
                        <input type="number" class="form-control" id="inflation_rate" name="inflation_rate" 
                            min="0" step="0.1" 
                            value="<?php echo $editChild ? $editChild['inflation_rate'] : ($selectedChild ? $selectedChild['inflation_rate'] : 2); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="expected_graduation_level" class="form-label">Niveau d'études visé</label>
                        <select class="form-select" id="expected_graduation_level" name="expected_graduation_level" required>
                            <?php foreach ($educationLevels as $key => $level): ?>
                                <option value="<?php echo $key; ?>" <?php echo (($editChild && $editChild['expected_graduation_level'] == $key) || ($selectedChild && $selectedChild['expected_graduation_level'] == $key)) ? 'selected' : ''; ?>>
                                    <?php echo $level['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if ($editChild): ?>
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="update_child" class="btn btn-warning">Mettre à jour</button>
                            <a href="?action=school-fee" class="btn btn-secondary">Annuler</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="calculate_fees" class="btn btn-primary w-100">Calculer</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Calculation Results -->
    <div class="col-md-7">
        <?php if ($viewChild): ?>
            <?php 
            // Force calculation for this child profile
            $schoolFeeController->calculateFees($viewChild);
            $childResults = $schoolFeeController->getCalculationResults();
            include __DIR__ . '/view-child.php'; 
            ?>
        <?php elseif ($results): ?>
            <?php include __DIR__ . '/calculation-results.php'; ?>
        <?php elseif (!empty($children)): ?>
            <?php include __DIR__ . '/list-children.php'; ?>
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <h4>Simulez les frais de scolarité de vos enfants</h4>
                    <p class="text-muted">
                        Remplissez le formulaire pour estimer les coûts d'éducation jusqu'à l'obtention du diplôme.
                    </p>
                    <img src="https://via.placeholder.com/400x200?text=School+Fee+Simulator" alt="School Fee Simulator" class="img-fluid mt-3 mb-3 rounded">
                    <p>
                        Le simulateur de frais de scolarité vous permet de:
                    </p>
                    <ul class="text-start">
                        <li>Estimer les coûts d'éducation futurs pour chaque enfant</li>
                        <li>Prendre en compte l'inflation des frais de scolarité</li>
                        <li>Visualiser les coûts par niveau scolaire (Maternelle, Primaire, Collège, Lycée, Études supérieures)</li>
                        <li>Planifier vos finances en fonction des pics de dépenses scolaires</li>
                        <li>Gérer les profils de plusieurs enfants</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle amount formatting for tuition
    const feeInputs = ['annual_tuition', 'additional_expenses'];
    
    feeInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function(e) {
                // Remove all non-digits
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('fr-FR').format(value);
            });
            
            // Before form submission, clean the input
            input.form.addEventListener('submit', function(e) {
                input.value = input.value.replace(/\D/g, '');
            });
        }
    });
    
    // Calculate child's current age dynamically
    const birthdateInput = document.getElementById('child_birthdate');
    const calculateAge = function() {
        if (birthdateInput.value) {
            const birthDate = new Date(birthdateInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            // Optional: Update a display element with the calculated age
            const ageDisplay = document.getElementById('age_display');
            if (ageDisplay) {
                ageDisplay.textContent = age + ' ans';
            }
        }
    };
    
    if (birthdateInput) {
        birthdateInput.addEventListener('change', calculateAge);
        // Calculate on page load if birthdate is set
        calculateAge();
    }
});
</script>
