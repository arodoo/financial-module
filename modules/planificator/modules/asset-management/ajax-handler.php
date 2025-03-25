<?php
// Start output buffering right away to catch any potential output
ob_start();

// Ensure no errors are displayed in output (store them in error log instead)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Include main configuration files
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_bdd.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_modules.php';

// Include any needed functions
$dir_fonction = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once $_SERVER['DOCUMENT_ROOT'] . '/function/INCLUDE-FUNCTION-HAUT-CMS-CODI-ONE.php';

// Default response - initialize before any potential exit points
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Only proceed if the user is logged in
if (!empty($_SESSION['4M8e7M5b1R2e8s']) || !empty($user)) {
    // Clean any output that might have occurred during includes
    ob_clean();
    
    try {
        // Since this is only accessed via AJAX, we require these files here
        require_once __DIR__ . '/../../controllers/AssetController.php';
        require_once __DIR__ . '/../../models/Asset.php';

        // Initialize the controller
        $assetController = new AssetController();
        
        // Process AJAX requests based on action parameter
        $action = $_REQUEST['action'] ?? '';
        
        switch ($action) {
            case 'get_assets':
                // Get all assets or filter by type/category
                $type = $_REQUEST['type'] ?? null;
                $category = $_REQUEST['category'] ?? null;
                /* $response['data'] = $assetController->getAssets($type, $category); */
                $response['data'] = $assetController->getAssets();
                $response['success'] = true;
                break;
                
            case 'get_asset':
                // Get details of a specific asset
                if (isset($_REQUEST['asset_id'])) {
                    $asset = $assetController->getAssetById($_REQUEST['asset_id']);
                    if ($asset) {
                        $response['data'] = $asset;
                        $response['success'] = true;
                    } else {
                        $response['message'] = 'Actif non trouvé';
                    }
                } else {
                    $response['message'] = 'ID d\'actif manquant';
                }
                break;
                
            case 'save_asset':
                // Add a new asset
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $assetController->saveAsset($_POST);
                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Actif enregistré avec succès';
                        $response['data'] = $result;
                    } else {
                        $response['message'] = 'Échec de l\'enregistrement de l\'actif';
                    }
                } else {
                    $response['message'] = 'Méthode non valide';
                }
                break;
                
            case 'update_asset':
                // Update an existing asset
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['asset_id'])) {
                    $result = $assetController->updateAsset($_POST);
                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Actif mis à jour avec succès';
                        $response['data'] = $result;
                    } else {
                        $response['message'] = 'Échec de la mise à jour de l\'actif';
                    }
                } else {
                    $response['message'] = 'Méthode non valide ou ID manquant';
                }
                break;
                
            case 'delete_asset':
                // Delete an asset
                if (isset($_REQUEST['asset_id'])) {
                    $result = $assetController->deleteAsset($_REQUEST['asset_id']);
                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Actif supprimé avec succès';
                    } else {
                        $response['message'] = 'Échec de la suppression de l\'actif';
                    }
                } else {
                    $response['message'] = 'ID d\'actif manquant';
                }
                break;
                
            case 'get_categories':
                // Get all asset categories
                $response['data'] = $assetController->getCategories();
                $response['success'] = true;
                break;
                
            default:
                $response['message'] = 'Action non reconnue';
                break;
        }
    } catch (Exception $e) {
        // Log the error and provide a generic message
        error_log('AJAX Error: ' . $e->getMessage());
        $response['message'] = 'Une erreur est survenue lors du traitement de la demande.';
    }
} else {
    // Not authenticated
    $response['message'] = 'Non autorisé';
}

// Clear all output buffers to ensure clean response
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Set appropriate headers after clearing buffers but before sending JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Send JSON response
echo json_encode($response);
exit;
?>
