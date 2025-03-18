<?php
// Include necessary controllers and models
require_once __DIR__ . '/../../controllers/SchoolFeeController.php';
require_once __DIR__ . '/../../models/SchoolFee.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../services/CalculationService.php';

// Initialize controller and services
$schoolFeeController = new SchoolFeeController();
$calculationService = new CalculationService();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['calculate_fees'])) {
        $schoolFeeController->calculateFees($_POST);
    } elseif (isset($_POST['save_child'])) {
        $schoolFeeController->saveChild($_POST);
        header('Location: ?action=school-fee-simulator&success=child_saved');
        exit;
    } elseif (isset($_POST['delete_child'])) {
        $schoolFeeController->deleteChild($_POST['child_id']);
        header('Location: ?action=school-fee-simulator&success=child_deleted');
        exit;
    }
}

// Get saved children profiles
$children = $schoolFeeController->getSavedChildren();

// Check for success messages
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'child_saved':
            $successMessage = 'Profil enfant enregistré avec succès!';
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

// Get calculation results if available
$results = $schoolFeeController->getCalculationResults();
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Simulateur de Frais de Scolarité</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="school-fee-calculator-form">
                    <h6 class="mb-3">Informations sur l'Enfant</h6>
                    <div class="mb-3">
                        <label for="child_name" class="form-label">Prénom de l'enfant</label>
                        <input type="text" class="form-control" id="child_name" name="child_name" 
                            value="<?php echo $_POST['child_name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="child_birthdate" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="child_birthdate" name="child_birthdate" 
                            value="<?php echo $_POST['child_birthdate'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_level" class="form-label">Niveau scolaire actuel</label>
                        <select class="form-select" id="current_level" name="current_level" required>
                            <?php foreach ($educationLevels as $key => $level): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($_POST['current_level']) && $_POST['current_level'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $level['name'] . ' (' . $level['ages'] . ' ans)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <h6 class="mb-3 mt-4">Informations sur les Frais</h6>
                    <div class="mb-3">
                        <label for="school_name" class="form-label">Nom de l'école</label>
                        <input type="text" class="form-control" id="school_name" name="school_name" 
                            value="<?php echo $_POST['school_name'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="annual_tuition" class="form-label">Frais de scolarité annuels (€)</label>
                        <input type="text" class="form-control" id="annual_tuition" name="annual_tuition" 
                            pattern="[0-9 ]*" value="<?php echo isset($_POST['annual_tuition']) ? number_format($_POST['annual_tuition'], 0, ',', ' ') : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="additional_expenses" class="form-label">Dépenses supplémentaires annuelles (€)</label>
                        <input type="text" class="form-control" id="additional_expenses" name="additional_expenses" 
                            value="<?php echo isset($_POST['additional_expenses']) ? number_format($_POST['additional_expenses'], 0, ',', ' ') : ''; ?>">
                        <small class="form-text text-muted">Uniformes, livres, activités extrascolaires, etc.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inflation_rate" class="form-label">Taux d'inflation estimé (%)</label>
                        <input type="number" class="form-control" id="inflation_rate" name="inflation_rate" 
                            min="0" step="0.1" value="<?php echo $_POST['inflation_rate'] ?? 2; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="expected_graduation_level" class="form-label">Niveau d'études visé</label>
                        <select class="form-select" id="expected_graduation_level" name="expected_graduation_level" required>
                            <?php foreach ($educationLevels as $key => $level): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($_POST['expected_graduation_level']) && $_POST['expected_graduation_level'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $level['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="calculate_fees" class="btn btn-primary w-100">Calculer</button>
                </form>
            </div>
        </div>
        
        <!-- Saved Children Profiles -->
        <?php if (!empty($children)): ?>
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Profils d'Enfants Enregistrés</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($children as $child): ?>
                    <a href="?action=school-fee-simulator&child_id=<?php echo $child['id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?php echo htmlspecialchars($child['name']); ?></h6>
                            <small><?php echo date('d/m/Y', strtotime($child['birthdate'])); ?></small>
                        </div>
                        <p class="mb-1">
                            École: <?php echo htmlspecialchars($child['school_name']); ?> | 
                            Niveau: <?php echo htmlspecialchars($educationLevels[$child['current_level']]['name']); ?>
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
                <h5 class="mb-0">Projection des Frais de Scolarité</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Coût Annuel Moyen</h6>
                            <h3 class="text-primary"><?php echo number_format($results['averageAnnualCost'], 2); ?>€</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Années Restantes</h6>
                            <h3 class="text-info"><?php echo number_format($results['yearsRemaining'], 0); ?> ans</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6>Coût Total Estimé</h6>
                            <h3><?php echo number_format($results['totalCost'], 2); ?>€</h3>
                        </div>
                    </div>
                </div>
                
                <!-- Save Child Profile Form -->
                <form method="POST" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="child_name" value="<?php echo $_POST['child_name']; ?>">
                    <input type="hidden" name="child_birthdate" value="<?php echo $_POST['child_birthdate']; ?>">
                    <input type="hidden" name="current_level" value="<?php echo $_POST['current_level']; ?>">
                    <input type="hidden" name="school_name" value="<?php echo $_POST['school_name']; ?>">
                    <input type="hidden" name="annual_tuition" value="<?php echo $_POST['annual_tuition']; ?>">
                    <input type="hidden" name="additional_expenses" value="<?php echo $_POST['additional_expenses'] ?? 0; ?>">
                    <input type="hidden" name="inflation_rate" value="<?php echo $_POST['inflation_rate']; ?>">
                    <input type="hidden" name="expected_graduation_level" value="<?php echo $_POST['expected_graduation_level']; ?>">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="save_child" class="btn btn-success">Enregistrer ce Profil</button>
                        <a href="?action=school-fee-simulator" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                
                <!-- Yearly Cost Projection -->
                <h5 class="mb-3">Projection Annuelle des Coûts</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Année</th>
                                <th>Âge</th>
                                <th>Niveau</th>
                                <th class="text-end">Frais de Scolarité</th>
                                <th class="text-end">Dépenses Suppl.</th>
                                <th class="text-end">Total Annuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results['yearlyProjections'] as $projection): ?>
                            <tr>
                                <td><?php echo $projection['year']; ?></td>
                                <td><?php echo $projection['age']; ?> ans</td>
                                <td><?php echo $educationLevels[$projection['level']]['name']; ?></td>
                                <td class="text-end"><?php echo number_format($projection['tuition'], 2); ?>€</td>
                                <td class="text-end"><?php echo number_format($projection['additional'], 2); ?>€</td>
                                <td class="text-end"><?php echo number_format($projection['total'], 2); ?>€</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
