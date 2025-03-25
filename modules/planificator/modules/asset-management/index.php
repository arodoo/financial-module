<?php
// Include necessary controllers and models
require_once __DIR__ . '/../../controllers/AssetController.php';
require_once __DIR__ . '/../../models/Asset.php';
require_once __DIR__ . '/../../models/Membre.php';

// Start output buffering at the beginning
ob_start();

// Check for AJAX request before anything else
$isAjaxRequest = isset($_GET['ajax']);
if ($isAjaxRequest) {
    // For AJAX requests, we should redirect to the dedicated ajax-handler.php
    ob_end_clean();
    include __DIR__ . '/ajax-handler.php';
    exit;
}

// If not an AJAX request, continue with normal page processing
ob_end_clean(); // Clear buffer but continue with normal page load

// Initialize controller
$assetController = new AssetController();

// Handle AJAX partials for view and list
if (isset($_GET['ajax_view']) && isset($_GET['asset_id'])) {
    // For AJAX view requests, we'll just include the view-asset.php file directly
    $viewAsset = $assetController->getAssetById($_GET['asset_id']);
    if ($viewAsset) {
        // Fetch categories for the view
        $categories = $assetController->getCategories();
        include __DIR__ . '/view-asset.php';
        exit;
    } else {
        echo '<div class="alert alert-danger">Actif non trouvé</div>';
        exit;
    }
}

if (isset($_GET['ajax_list'])) {
    // For AJAX list requests, we'll just include the list-assets.php file directly
    $assets = $assetController->getAssets();
    $categories = $assetController->getCategories();
    include __DIR__ . '/list-assets.php';
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_asset'])) {
        $assetController->saveAsset($_POST);
        // Instead of redirecting, set a flash message
        $successMessage = 'Actif enregistré avec succès!';
    } elseif (isset($_POST['update_asset'])) {
        $assetController->updateAsset($_POST);
        // Instead of redirecting, set a flash message
        $successMessage = 'Actif mis à jour avec succès!';
    } elseif (isset($_POST['delete_asset'])) {
        $assetController->deleteAsset($_POST['asset_id']);
        // Instead of redirecting, set a flash message
        $successMessage = 'Actif supprimé avec succès!';
    }
}

// Get data for the view - this needs to be after form processing
$viewData = $assetController->getViewData();
$assets = $viewData['assets'] ?? [];
$categories = $viewData['categories'] ?? [];
$selectedAsset = $viewData['selectedAsset'] ?? null;
$viewAsset = $viewData['viewAsset'] ?? null;
$editAsset = $viewData['editAsset'] ?? null;

// Instead of using the GET parameter for success messages, use the variable we set above
// if (isset($_GET['success'])) {
//     switch ($_GET['success']) {
//         ...
//     }
// }

// Define AJAX handler URLs for JavaScript
$ajaxHandlerUrl = '/modules/planificator/modules/asset-management/ajax-handler.php';
$assetDetailsUrl = '/modules/planificator/modules/asset-management/get-asset-details-ajax.php';
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

<!-- Include modal for asset details -->
<?php include __DIR__ . '/modal-asset-view.php'; ?>

<!-- Include asset management JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo '/modules/planificator/modules/asset-management/asset-management.js'; ?>"></script>
<script>
    // Initialize Asset Management module
    $(document).ready(function() {
        // Pass configuration object with URLs
        initAssetManagement({
            ajaxHandlerUrl: '<?php echo $ajaxHandlerUrl; ?>',
            assetDetailsUrl: '<?php echo $assetDetailsUrl; ?>'
        });
    });
</script>

<?php
// TEMP: Debug section to test AJAX functionality - only show in development mode
// Remove this section once the UI is fully implemented
if (true) {
?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>AJAX Testing Interface (Debug Mode)</span>
                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#ajaxTestingPanel">Toggle</button>
                </h5>
            </div>
            <div id="ajaxTestingPanel" class="card-body collapse show">
                <div class="row g-3">
                    <!-- Test get_categories -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Get Categories</div>
                            <div class="card-body">
                                <button id="btn-get-categories" class="btn btn-primary">Get All Categories</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test get_assets -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Get Assets</div>
                            <div class="card-body">
                                <button id="btn-get-assets" class="btn btn-primary">Get All Assets</button>
                                <div class="mt-2">
                                    <label>Filter by Category:</label>
                                    <select id="category-filter" class="form-select">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test get_asset -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Get Single Asset</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="asset-id" class="form-label">Asset ID:</label>
                                    <input type="number" id="asset-id" class="form-control">
                                </div>
                                <button id="btn-get-asset" class="btn btn-primary">Get Asset Details</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test save_asset -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Add New Asset</div>
                            <div class="card-body">
                                <form id="form-add-asset">
                                    <div class="mb-2">
                                        <label for="add-asset-name" class="form-label">Name:</label>
                                        <input type="text" id="add-asset-name" class="form-control" name="asset_name">
                                    </div>
                                    <div class="mb-2">
                                        <label for="add-asset-category" class="form-label">Category:</label>
                                        <select id="add-asset-category" class="form-select" name="category_id">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label for="add-asset-value" class="form-label">Current Value:</label>
                                        <input type="number" id="add-asset-value" class="form-control" name="current_value">
                                    </div>
                                    <button type="button" id="btn-add-asset" class="btn btn-success">Save New Asset</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test update_asset -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Update Asset</div>
                            <div class="card-body">
                                <form id="form-update-asset">
                                    <div class="mb-2">
                                        <label for="update-asset-id" class="form-label">Asset ID to Update:</label>
                                        <input type="number" id="update-asset-id" class="form-control" name="asset_id">
                                    </div>
                                    <div class="mb-2">
                                        <label for="update-asset-name" class="form-label">Name:</label>
                                        <input type="text" id="update-asset-name" class="form-control" name="asset_name">
                                    </div>
                                    <div class="mb-2">
                                        <label for="update-asset-value" class="form-label">Current Value:</label>
                                        <input type="number" id="update-asset-value" class="form-control" name="current_value">
                                    </div>
                                    <button type="button" id="btn-update-asset" class="btn btn-warning">Update Asset</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test delete_asset -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">Delete Asset</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="delete-asset-id" class="form-label">Asset ID to Delete:</label>
                                    <input type="number" id="delete-asset-id" class="form-control">
                                </div>
                                <button id="btn-delete-asset" class="btn btn-danger">Delete Asset</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results panel -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">Results</div>
                            <div class="card-body">
                                <pre id="ajax-results" class="bg-light p-3 border rounded" style="min-height: 200px; max-height: 400px; overflow: auto;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- AJAX Testing JavaScript -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Display results function
                    function displayResults(data) {
                        document.getElementById('ajax-results').textContent = 
                            JSON.stringify(data, null, 2);
                    }
                    
                    // Generic AJAX function - FIXED URL PATH FORMAT
                    function makeAjaxRequest(action, params, method = 'GET') {
                        // Direct path to AJAX handler - FIXED to match income-expense format
                        const url = '/modules/planificator/modules/asset-management/ajax-handler.php';
                        
                        // Prepare request options
                        const options = {
                            method: method,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        };
                        
                        // Add params to URL for GET requests or to body for POST
                        let fullUrl = url;
                        let body = '';
                        
                        params = params || {};
                        params.action = action;
                        
                        if (method === 'GET') {
                            const searchParams = new URLSearchParams(params);
                            fullUrl += '?' + searchParams.toString();
                        } else {
                            const searchParams = new URLSearchParams(params);
                            body = searchParams.toString();
                            options.body = body;
                        }
                        
                        console.log('Making request to:', fullUrl, 'with options:', options);
                        
                        // Make the request with better error handling
                        return fetch(fullUrl, options)
                            .then(response => {
                                console.log('Response status:', response.status);
                                return response.text();
                            })
                            .then(text => {
                                console.log('Raw response text:', text.substring(0, 200)); // Show first 200 chars for debugging
                                try {
                                    const data = JSON.parse(text);
                                    displayResults(data);
                                    return data;
                                } catch (error) {
                                    console.error('JSON parse error:', error);
                                    displayResults({error: 'Invalid JSON response', raw: text.substring(0, 500)});
                                    throw new Error('Invalid JSON: ' + text.substring(0, 100) + '...');
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                displayResults({error: error.message});
                            });
                    }
                    
                    // Get Categories
                    document.getElementById('btn-get-categories').addEventListener('click', function() {
                        makeAjaxRequest('get_categories');
                    });
                    
                    // Get Assets
                    document.getElementById('btn-get-assets').addEventListener('click', function() {
                        const categoryId = document.getElementById('category-filter').value;
                        const params = categoryId ? {category: categoryId} : {};
                        makeAjaxRequest('get_assets', params);
                    });
                    
                    // Get Single Asset
                    document.getElementById('btn-get-asset').addEventListener('click', function() {
                        const assetId = document.getElementById('asset-id').value;
                        if (!assetId) {
                            alert('Please enter an asset ID');
                            return;
                        }
                        makeAjaxRequest('get_asset', {asset_id: assetId});
                    });
                    
                    // Add New Asset
                    document.getElementById('btn-add-asset').addEventListener('click', function() {
                        const form = document.getElementById('form-add-asset');
                        const formData = new FormData(form);
                        const params = Object.fromEntries(formData.entries());
                        
                        // Add required fields for testing
                        if (!params.acquisition_date) {
                            params.acquisition_date = new Date().toISOString().split('T')[0];
                        }
                        if (!params.acquisition_value) {
                            params.acquisition_value = params.current_value;
                        }
                        if (!params.valuation_date) {
                            params.valuation_date = new Date().toISOString().split('T')[0];
                        }
                        
                        makeAjaxRequest('save_asset', params, 'POST');
                    });
                    
                    // Update Asset
                    document.getElementById('btn-update-asset').addEventListener('click', function() {
                        const form = document.getElementById('form-update-asset');
                        const formData = new FormData(form);
                        const params = Object.fromEntries(formData.entries());
                        
                        if (!params.asset_id) {
                            alert('Please enter an asset ID to update');
                            return;
                        }
                        
                        // Add required fields for testing
                        if (!params.category_id) {
                            params.category_id = document.getElementById('add-asset-category').value;
                        }
                        if (!params.acquisition_date) {
                            params.acquisition_date = new Date().toISOString().split('T')[0];
                        }
                        if (!params.acquisition_value) {
                            params.acquisition_value = params.current_value;
                        }
                        if (!params.valuation_date) {
                            params.valuation_date = new Date().toISOString().split('T')[0];
                        }
                        
                        makeAjaxRequest('update_asset', params, 'POST');
                    });
                    
                    // Delete Asset
                    document.getElementById('btn-delete-asset').addEventListener('click', function() {
                        const assetId = document.getElementById('delete-asset-id').value;
                        if (!assetId) {
                            alert('Please enter an asset ID to delete');
                            return;
                        }
                        
                        if (confirm('Are you sure you want to delete asset ID ' + assetId + '?')) {
                            makeAjaxRequest('delete_asset', {asset_id: assetId});
                        }
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>
<?php
} // End of debug panel
?>
