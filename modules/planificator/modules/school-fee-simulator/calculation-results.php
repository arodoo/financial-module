<?php
// This file displays the calculation results after form submission
?>
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
        
        <?php if (!$editChild && isset($_POST['child_name'])): ?>
        <!-- Save Child Profile Form - only show if not in edit mode -->
        <form method="POST" class="mb-3 border-bottom pb-3">
            <input type="hidden" name="child_name" value="<?php echo htmlspecialchars($_POST['child_name']); ?>">
            <input type="hidden" name="child_birthdate" value="<?php echo $_POST['child_birthdate']; ?>">
            <input type="hidden" name="current_level" value="<?php echo $_POST['current_level']; ?>">
            <input type="hidden" name="school_name" value="<?php echo htmlspecialchars($_POST['school_name'] ?? ''); ?>">
            <input type="hidden" name="annual_tuition" value="<?php echo $_POST['annual_tuition']; ?>">
            <input type="hidden" name="additional_expenses" value="<?php echo $_POST['additional_expenses'] ?? 0; ?>">
            <input type="hidden" name="inflation_rate" value="<?php echo $_POST['inflation_rate']; ?>">
            <input type="hidden" name="expected_graduation_level" value="<?php echo $_POST['expected_graduation_level']; ?>">
            
            <div class="d-flex justify-content-end gap-2">
                <button type="submit" name="save_child" class="btn btn-success">Enregistrer ce Profil</button>
                <a href="?action=school-fee" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
        <?php endif; ?>
        
        <!-- Cost Projection Chart -->
        <h5 class="mb-3">Visualisation des Coûts</h5>
        <div class="mb-4">
            <canvas id="feeProjectionChart" width="400" height="200"></canvas>
        </div>
        
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

<!-- Chart.js initialization for fee projections -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('feeProjectionChart').getContext('2d');
    
    // Extract data from PHP yearlyProjections
    const years = <?php echo json_encode(array_column($results['yearlyProjections'], 'year')); ?>;
    const tuitionFees = <?php echo json_encode(array_column($results['yearlyProjections'], 'tuition')); ?>;
    const additionalFees = <?php echo json_encode(array_column($results['yearlyProjections'], 'additional')); ?>;
    const totalFees = <?php echo json_encode(array_column($results['yearlyProjections'], 'total')); ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: years,
            datasets: [
                {
                    label: 'Frais de Scolarité',
                    data: tuitionFees,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Dépenses Supplémentaires',
                    data: additionalFees,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Coût Total',
                    data: totalFees,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
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
});
</script>
