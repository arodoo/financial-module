<?php
// Include necessary controllers and models
require_once __DIR__ . '/../../controllers/AssetController.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../models/Membre.php';

// Initialize controller
$assetController = new AssetController();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_asset'])) {
        $assetController->saveAsset($_POST);
        header('Location: ?action=asset-management&success=asset_saved');
        exit;
    } elseif (isset($_POST['update_asset'])) {
        $assetController->updateAsset($_POST);
        header('Location: ?action=asset-management&success=asset_updated');
        exit;
    } elseif (isset($_POST['delete_asset'])) {
        $assetController->deleteAsset($_POST['asset_id']);
        header('Location: ?action=asset-management&success=asset_deleted');
        exit;
    }
}

// Get data for the view
$viewData = $assetController->getViewData();
$assets = $viewData['assets'] ?? [];
$categories = $viewData['categories'] ?? [];
$selectedAsset = $viewData['selectedAsset'] ?? null;
$viewAsset = $viewData['viewAsset'] ?? null;
$editAsset = $viewData['editAsset'] ?? null;

// Check for success messages
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'asset_saved':
            $successMessage = 'Actif enregistré avec succès!';
            break;
        case 'asset_updated':
            $successMessage = 'Actif mis à jour avec succès!';
            break;
        case 'asset_deleted':
            $successMessage = 'Actif supprimé avec succès!';
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
    <!-- Left Column: Form or Asset List -->
    <div class="col-md-5">
        <?php if ($editAsset): ?>
            <?php include __DIR__ . '/add-edit-asset.php'; ?>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ajouter un Actif</h5>
                </div>
                <?php include __DIR__ . '/add-edit-asset.php'; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Right Column: Asset Details or Assets List -->
    <div class="col-md-7">
        <?php if ($viewAsset): ?>
            <?php include __DIR__ . '/view-asset.php'; ?>
        <?php elseif (!empty($assets)): ?>
            <?php include __DIR__ . '/list-assets.php'; ?>
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <h4>Gérez vos actifs</h4>
                    <p class="text-muted">
                        Utilisez le formulaire pour ajouter des actifs à votre portefeuille.
                    </p>
                    <img src="https://via.placeholder.com/400x200?text=Asset+Management" alt="Asset Management" class="img-fluid mt-3 mb-3 rounded">
                    <p>
                        Le module de gestion d'actifs vous permet de:
                    </p>
                    <ul class="text-start">
                        <li>Suivre tous vos actifs financiers et immobiliers</li>
                        <li>Enregistrer les détails importants de chaque actif</li>
                        <li>Associer des prêts à vos actifs immobiliers</li>
                        <li>Visualiser l'évolution de la valeur de vos actifs</li>
                        <li>Analyser votre patrimoine global</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
