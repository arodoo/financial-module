<?php
use Financial\Modules\Visualization\Controllers\AssetManagementController;

// Adjust paths to match actual file structure
require_once __DIR__ . '/../../controllers/AssetManagementController.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../models/Membre.php';

// Initialize controller
$assetController = new AssetManagementController();

// Process any form submissions or actions
$assetController->processRequest();

// Get data for the view
$viewData = $assetController->getViewData();
$assets = $viewData['assets'] ?? [];
$totalValue = $viewData['totalValue'] ?? 0;
$assetsByCategory = $viewData['assetsByCategory'] ?? [];
$categories = $viewData['categories'] ?? [];
$editAsset = $viewData['editAsset'] ?? null;
$viewAsset = $viewData['viewAsset'] ?? null;
$assetHistory = $viewData['assetHistory'] ?? [];

// Calculate total appreciation/depreciation
$totalPurchase = 0;
$totalAppreciation = 0;

foreach ($assets as $asset) {
    $totalPurchase += $asset['purchase_value'];
    $totalAppreciation += ($asset['current_value'] - $asset['purchase_value']);
}
?>

<!-- Success Messages -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] === 'asset_added') {
            echo "Actif ajouté avec succès!";
        } elseif ($_GET['success'] === 'asset_updated') {
            echo "Actif mis à jour avec succès!";
        } elseif ($_GET['success'] === 'asset_deleted') {
            echo "Actif supprimé avec succès!";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Asset Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Valeur Totale des Actifs</h5>
                <h3 class="card-text"><?php echo '₱' . number_format($totalValue, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body text-center">
                <h5 class="card-title text-info">Valeur d'Achat Totale</h5>
                <h3 class="card-text"><?php echo '₱' . number_format($totalPurchase, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card <?php echo $totalAppreciation >= 0 ? 'border-success' : 'border-danger'; ?>">
            <div class="card-body text-center">
                <h5 class="card-title <?php echo $totalAppreciation >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $totalAppreciation >= 0 ? 'Appréciation' : 'Dépréciation'; ?> Totale
                </h5>
                <h3 class="card-text"><?php echo ($totalAppreciation >= 0 ? '+' : '') . '₱' . number_format($totalAppreciation, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Assets by Category -->
<?php if (!empty($assetsByCategory)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Actifs par Catégorie</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th class="text-center">Nombre d'Actifs</th>
                                <th class="text-end">Valeur Totale</th>
                                <th class="text-end">% du Portefeuille</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assetsByCategory as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['category']); ?></td>
                                    <td class="text-center"><?php echo $category['count']; ?></td>
                                    <td class="text-end">₱<?php echo number_format($category['total_value'], 2); ?></td>
                                    <td class="text-end">
<?php 
                                        $percentage = $totalValue > 0 ? ($category['total_value'] / $totalValue) * 100 : 0;
                                        echo number_format($percentage, 1) . '%';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <!-- Asset Form (Add/Edit) -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-<?php echo $editAsset ? 'warning' : 'primary'; ?> text-white">
                <h5 class="mb-0"><?php echo $editAsset ? 'Modifier l\'Actif' : 'Ajouter un Nouvel Actif'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($editAsset): ?>
                        <input type="hidden" name="asset_id" value="<?php echo $editAsset['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($editAsset && $editAsset['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de l'Actif</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $editAsset ? htmlspecialchars($editAsset['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"><?php echo $editAsset ? htmlspecialchars($editAsset['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_value" class="form-label">Valeur d'Achat (₱)</label>
                            <input type="number" class="form-control" id="purchase_value" name="purchase_value" step="0.01" min="0" value="<?php echo $editAsset ? $editAsset['purchase_value'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="current_value" class="form-label">Valeur Actuelle (₱)</label>
                            <input type="number" class="form-control" id="current_value" name="current_value" step="0.01" min="0" value="<?php echo $editAsset ? $editAsset['current_value'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_date" class="form-label">Date d'Achat</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $editAsset ? $editAsset['purchase_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_valuation_date" class="form-label">Date de Dernière Évaluation</label>
                            <input type="date" class="form-control" id="last_valuation_date" name="last_valuation_date" value="<?php echo $editAsset ? $editAsset['last_valuation_date'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Emplacement</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo $editAsset ? htmlspecialchars($editAsset['location']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"><?php echo $editAsset ? htmlspecialchars($editAsset['notes']) : ''; ?></textarea>
                    </div>
                    
                    <?php if ($editAsset): ?>
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="update_asset" class="btn btn-warning">Mettre à jour l'Actif</button>
                            <a href="?action=asset-management" class="btn btn-secondary">Annuler</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="add_asset" class="btn btn-primary w-100">Ajouter l'Actif</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Asset List -->
    <div class="col-md-8">
        <?php if ($viewAsset): ?>
            <!-- Asset Details View -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'Actif: <?php echo htmlspecialchars($viewAsset['name']); ?></h5>
                    <a href="?action=asset-management" class="btn btn-sm btn-light">Retour</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($viewAsset['category_name']); ?></p>
                            <p><strong>Valeur d'Achat:</strong> ₱<?php echo number_format($viewAsset['purchase_value'], 2); ?></p>
                            <p><strong>Valeur Actuelle:</strong> ₱<?php echo number_format($viewAsset['current_value'], 2); ?></p>
                            <p><strong>Changement:</strong> 
                                <span class="<?php echo ($viewAsset['current_value'] >= $viewAsset['purchase_value']) ? 'text-success' : 'text-danger'; ?>">
                                    <?php 
                                    $change = $viewAsset['current_value'] - $viewAsset['purchase_value'];
                                    $changePercentage = ($viewAsset['purchase_value'] > 0) ? ($change / $viewAsset['purchase_value']) * 100 : 0;
                                    echo ($change >= 0 ? '+' : '') . '₱' . number_format($change, 2) . ' (' . number_format($changePercentage, 1) . '%)';
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date d'Achat:</strong> <?php echo !empty($viewAsset['purchase_date']) ? date('d/m/Y', strtotime($viewAsset['purchase_date'])) : 'N/A'; ?></p>
                            <p><strong>Date de Dernière Évaluation:</strong> <?php echo !empty($viewAsset['last_valuation_date']) ? date('d/m/Y', strtotime($viewAsset['last_valuation_date'])) : 'N/A'; ?></p>
                            <p><strong>Emplacement:</strong> <?php echo !empty($viewAsset['location']) ? htmlspecialchars($viewAsset['location']) : 'N/A'; ?></p>
                            <p><strong>Description:</strong> <?php echo !empty($viewAsset['description']) ? htmlspecialchars($viewAsset['description']) : 'N/A'; ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($viewAsset['notes'])): ?>
                    <div class="mb-4">
                        <h6>Notes:</h6>
                        <p><?php echo nl2br(htmlspecialchars($viewAsset['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Asset Value History -->
                    <?php if (!empty($assetHistory)): ?>
                    <div class="mt-4">
                        <h6>Historique des Valeurs</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date d'Évaluation</th>
                                        <th class="text-end">Valeur</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assetHistory as $record): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($record['valuation_date'])); ?></td>
                                        <td class="text-end">₱<?php echo number_format($record['value'], 2); ?></td>
                                        <td><?php echo !empty($record['notes']) ? htmlspecialchars($record['notes']) : ''; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-3 d-flex gap-2">
                        <a href="?action=asset-management&edit_asset=<?php echo $viewAsset['id']; ?>" class="btn btn-warning">Modifier</a>
                        <a href="?action=asset-management&delete_asset=<?php echo $viewAsset['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet actif?')">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Assets List -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Liste des Actifs</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assets)): ?>
                        <div class="alert alert-info">
                            Vous n'avez pas encore d'actifs. Utilisez le formulaire pour ajouter votre premier actif.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Catégorie</th>
                                        <th class="text-end">Valeur Actuelle</th>
                                        <th class="text-end">Appréciation</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assets as $asset): ?>
                                        <?php 
                                        $appreciation = $asset['current_value'] - $asset['purchase_value'];
                                        $appreciationPercentage = ($asset['purchase_value'] > 0) ? ($appreciation / $asset['purchase_value'] * 100) : 0;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($asset['name']); ?></td>
                                            <td><?php echo htmlspecialchars($asset['category_name']); ?></td>
                                            <td class="text-end">₱<?php echo number_format($asset['current_value'], 2); ?></td>
                                            <td class="text-end <?php echo $appreciation >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo ($appreciation >= 0 ? '+' : '') . '₱' . number_format($appreciation, 2); ?>
                                                <small>(<?php echo number_format($appreciationPercentage, 1); ?>%)</small>
                                            </td>
                                            <td class="text-center">
                                                <a href="?action=asset-management&view_asset=<?php echo $asset['id']; ?>" class="btn btn-sm btn-info">Voir</a>
                                                <a href="?action=asset-management&edit_asset=<?php echo $asset['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="?action=asset-management&delete_asset=<?php echo $asset['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet actif?')">Supprimer</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Charts for asset visualization (using Chart.js) -->
<?php if (!empty($assetsByCategory)): ?>
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Répartition des Actifs</h5>
            </div>
            <div class="card-body">
                <canvas id="assetDistributionChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Valeur des Actifs par Catégorie</h5>
            </div>
            <div class="card-body">
                <canvas id="assetValueChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js initialization script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data for pie chart
    const categories = <?php echo json_encode(array_column($assetsByCategory, 'category')); ?>;
    const values = <?php echo json_encode(array_column($assetsByCategory, 'total_value')); ?>;
    
    // Distribution chart
    const distributionCtx = document.getElementById('assetDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#6f42c1', '#20c9a6', '#f8f9fc'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Value chart
    const valueCtx = document.getElementById('assetValueChart').getContext('2d');
    new Chart(valueCtx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Valeur Totale',
                data: values,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
<?php endif; ?>
