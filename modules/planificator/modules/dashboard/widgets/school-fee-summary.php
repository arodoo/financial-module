<?php
// Include necessary models
require_once __DIR__ . '/../../../models/SchoolFee.php';
require_once __DIR__ . '/../../../controllers/SchoolFeeController.php';

// Initialize the SchoolFee model and controller
$schoolFeeModel = new SchoolFee();
$schoolFeeController = new SchoolFeeController();

// Get children profiles
$childProfiles = $schoolFeeModel->getChildProfiles();

// Calculate total upcoming expenses (for current year)
$currentYear = date('Y');
$totalUpcomingExpenses = 0;
$childrenWithUpcomingExpenses = [];

if (!empty($childProfiles)) {
    foreach ($childProfiles as $child) {
        // Instead of calculating all projections, just get the one for the current year
        $currentYearProjection = $schoolFeeController->getProjectionForYear($child, $currentYear);
        
        if ($currentYearProjection) {
            $totalUpcomingExpenses += $currentYearProjection['total'];
            $childrenWithUpcomingExpenses[] = [
                'name' => $child['name'],
                'expense' => $currentYearProjection['total']
            ];
        }
    }
}
?>

<!-- School Fee Summary Widget -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Frais de Scolarité</h5>
                <a href="?action=school-fee" class="btn btn-sm btn-outline-primary">Gérer les Profils</a>
            </div>
            <div class="card-body">
                <?php if (empty($childProfiles)): ?>
                    <p class="text-muted mb-0">Aucun profil d'enfant enregistré.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Enfant</th>
                                            <th>Niveau</th>
                                            <th class="text-end">Frais <?php echo $currentYear; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Define education levels mapping for display
                                        $educationLevels = [
                                            'maternelle' => ['name' => 'Maternelle'],
                                            'primaire' => ['name' => 'École primaire'],
                                            'college' => ['name' => 'Collège'],
                                            'lycee' => ['name' => 'Lycée'],
                                            'superieur' => ['name' => 'Études supérieures']
                                        ];
                                        
                                        foreach (array_slice($childProfiles, 0, 3) as $child): 
                                            $expense = 0;
                                            foreach ($childrenWithUpcomingExpenses as $childExpense) {
                                                if ($childExpense['name'] == $child['name']) {
                                                    $expense = $childExpense['expense'];
                                                    break;
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($child['name']); ?></td>
                                                <td><?php echo $educationLevels[$child['current_level']]['name']; ?></td>
                                                <td class="text-end"><?php echo number_format($expense, 2); ?>€</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h6>Dépenses <?php echo $currentYear; ?></h6>
                                <h3 class="text-primary mb-0"><?php echo number_format($totalUpcomingExpenses, 2); ?>€</h3>
                                <small class="text-muted"><?php echo count($childProfiles); ?> enfant(s)</small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
