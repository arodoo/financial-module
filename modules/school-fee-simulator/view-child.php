<?php
// This file displays the detailed view of a child profile with financial projections

// Force recalculation of the child's fee projections - this is critical!
$schoolFeeController->calculateFees($viewChild);
$childResults = $schoolFeeController->getCalculationResults();

// Debug information - uncomment if needed
// echo '<pre>Debug: ' . (empty($childResults) ? 'No results' : 'Results found') . '</pre>';
?>
<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails du Profil: <?php echo htmlspecialchars($viewChild['name']); ?></h5>
        <a href="?action=school-fee" class="btn btn-sm btn-light">Retour</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Prénom:</strong> <?php echo htmlspecialchars($viewChild['name']); ?></p>
                <p><strong>Date de Naissance:</strong> <?php echo date('d/m/Y', strtotime($viewChild['birthdate'])); ?></p>
                <p><strong>Âge:</strong> 
                    <?php 
                    $birthdate = new DateTime($viewChild['birthdate']);
                    $today = new DateTime();
                    $age = $birthdate->diff($today)->y;
                    echo $age . ' ans';
                    ?>
                </p>
                <p><strong>Niveau Actuel:</strong> <?php echo $educationLevels[$viewChild['current_level']]['name']; ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>École:</strong> <?php echo htmlspecialchars($viewChild['school_name']); ?></p>
                <p><strong>Frais Annuels:</strong> €<?php echo number_format($viewChild['annual_tuition'], 2); ?></p>
                <p><strong>Dépenses Supplémentaires:</strong> €<?php echo number_format($viewChild['additional_expenses'], 2); ?></p>
                <p><strong>Taux d'Inflation:</strong> <?php echo $viewChild['inflation_rate']; ?>%</p>
                <p><strong>Niveau Visé:</strong> <?php echo $educationLevels[$viewChild['expected_graduation_level']]['name']; ?></p>
            </div>
        </div>
        
        <?php if (!empty($childResults)): ?>
        <hr>
        <h6 class="mb-3">Résumé Financier</h6>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Coût Annuel Moyen</h6>
                    <h3 class="text-primary"><?php echo number_format($childResults['averageAnnualCost'], 2); ?>€</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Années Restantes</h6>
                    <h3 class="text-info"><?php echo number_format($childResults['yearsRemaining'], 0); ?> ans</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6>Coût Total Estimé</h6>
                    <h3><?php echo number_format($childResults['totalCost'], 2); ?>€</h3>
                </div>
            </div>
        </div>
        
        <!-- Fee Projection Chart -->
        <h6 class="mb-3">Visualisation des Coûts</h6>
        <div class="mb-4">
            <canvas id="childFeeProjectionChart" width="400" height="200"></canvas>
        </div>
        
        <!-- Yearly Cost Projection Table -->
        <h6 class="mb-3">Projection Annuelle des Coûts</h6>
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
                    <?php foreach ($childResults['yearlyProjections'] as $projection): ?>
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
        
        <!-- Chart.js initialization for child profile -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('childFeeProjectionChart').getContext('2d');
            
            // Extract data from PHP yearlyProjections
            const years = <?php echo json_encode(array_column($childResults['yearlyProjections'], 'year')); ?>;
            const tuitionFees = <?php echo json_encode(array_column($childResults['yearlyProjections'], 'tuition')); ?>;
            const additionalFees = <?php echo json_encode(array_column($childResults['yearlyProjections'], 'additional')); ?>;
            const totalFees = <?php echo json_encode(array_column($childResults['yearlyProjections'], 'total')); ?>;
            
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
        <?php endif; ?>
        
        <div class="mt-3 d-flex gap-2">
            <a href="?action=school-fee&edit_child=<?php echo $viewChild['id']; ?>" class="btn btn-warning">Modifier</a>
            <a href="?action=school-fee&delete_child=<?php echo $viewChild['id']; ?>" class="btn btn-danger" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce profil?')">Supprimer</a>
        </div>
    </div>
</div>
