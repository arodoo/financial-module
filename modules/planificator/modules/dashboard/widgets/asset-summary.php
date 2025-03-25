<!-- Assets Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Aperçu des Actifs</h5>
                <a href="?action=asset-management" class="btn btn-sm btn-outline-primary">Gérer les Actifs</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Catégorie</th>
                                        <th class="text-end">Valeur</th>
                                        <th class="text-end">% du Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($assetsByCategory, 0, 5) as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category']); ?></td>
                                        <td class="text-end">€<?php echo number_format($category['total_value'], 2); ?></td>
                                        <td class="text-end">
                                            <?php 
                                            $percentage = $totalAssetValue > 0 ? ($category['total_value'] / $totalAssetValue) * 100 : 0;
                                            echo number_format($percentage, 1) . '%';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <h6>Répartition des Actifs</h6>
                        </div>
                        <canvas id="assetDistributionMiniChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js script for the mini asset distribution chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data for pie chart
    const categories = <?php echo json_encode(array_column($assetsByCategory, 'category')); ?>;
    const values = <?php echo json_encode(array_column($assetsByCategory, 'total_value')); ?>;
    
    // Mini asset distribution chart
    const ctx = document.getElementById('assetDistributionMiniChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#6f42c1'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    display: false
                }
            }
        }
    });
});
</script>
