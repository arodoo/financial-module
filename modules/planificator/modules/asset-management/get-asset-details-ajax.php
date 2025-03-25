<?php
/**
 * Asset Details AJAX Handler
 * 
 * This file prepares asset data for display, separating data preparation from UI rendering.
 * It can return either HTML content or JSON data depending on the request.
 */

// Start output buffering right away to catch any potential output
ob_start();

// Include main configuration files for proper bootstrapping
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_bdd.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Configurations_modules.php';

// Include any needed functions
$dir_fonction = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once $_SERVER['DOCUMENT_ROOT'] . '/function/INCLUDE-FUNCTION-HAUT-CMS-CODI-ONE.php';

// Only proceed if the user is logged in (same check as ajax-handler.php)
if (!empty($_SESSION['4M8e7M5b1R2e8s']) || !empty($user)) {
    // Clean any output that might have occurred during includes
    ob_clean();
    
    // Only process if we have an asset ID
    if (!isset($_GET['asset_id'])) {
        sendErrorResponse('Missing asset ID parameter');
    }

    $assetId = (int)$_GET['asset_id'];
    $outputFormat = isset($_GET['format']) ? $_GET['format'] : 'json';

    // Include required files if they haven't been included yet
    if (!class_exists('AssetController')) {
        require_once __DIR__ . '/../../controllers/AssetController.php';
        require_once __DIR__ . '/../../models/Asset.php';
    }

    // Get the asset data
    $assetController = new AssetController();
    $asset = $assetController->getAssetById($assetId);

    // If asset not found, return error
    if (!$asset) {
        sendErrorResponse('Asset not found');
    }

    // Get categories for reference
    $categories = $assetController->getCategories();

    // Prepare asset data with additional formatted information
    $assetData = prepareAssetData($asset, $categories);

    // Output the data based on the requested format
    if ($outputFormat === 'html') {
        // Include view-asset.php to render HTML
        $viewAsset = $asset; // For backward compatibility with view-asset.php
        ob_start();
        include __DIR__ . '/view-asset.php';
        $html = ob_get_clean();
        echo $html;
    } else {
        // Output JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $assetData
        ]);
    }
} else {
    // Not authenticated
    sendErrorResponse('Not authenticated', 401);
}

exit;

/**
 * Prepare asset data with additional formatted information
 * 
 * @param array $asset The raw asset data from the database
 * @param array $categories List of asset categories
 * @return array The prepared asset data
 */
function prepareAssetData($asset, $categories) {
    // Map database column names to our expected format
    $mappedAsset = [
        'id' => $asset['id'],
        'membre_id' => $asset['membre_id'],
        'category_id' => $asset['category_id'],
        'name' => $asset['name'],
        'description' => $asset['description'],
        'acquisition_value' => $asset['purchase_value'],  // Map purchase_value to acquisition_value
        'current_value' => $asset['current_value'],
        'acquisition_date' => $asset['purchase_date'],    // Map purchase_date to acquisition_date
        'valuation_date' => $asset['last_valuation_date'], // Map last_valuation_date to valuation_date
        'location' => $asset['location'],
        'notes' => $asset['notes'],
        'created_at' => $asset['created_at'],
        'updated_at' => $asset['updated_at'],
        'loan_id' => $asset['loan_id'],
        'loan_amount' => $asset['loan_amount'],
        'loan_monthly_payment' => $asset['loan_monthly_payment']
    ];
    
    // Find category name
    $categoryName = 'Non défini';
    foreach ($categories as $category) {
        if ($category['id'] == $mappedAsset['category_id']) {
            $categoryName = $category['name'];
            break;
        }
    }
    
    // Format dates for display
    $acquisitionDateFormatted = !empty($mappedAsset['acquisition_date']) ? date('d/m/Y', strtotime($mappedAsset['acquisition_date'])) : 'N/A';
    $valuationDateFormatted = !empty($mappedAsset['valuation_date']) ? date('d/m/Y', strtotime($mappedAsset['valuation_date'])) : 'N/A';
    
    // Calculate value change
    $valueChange = 0;
    $valueChangeFormatted = '0%';
    $valueChangeDirection = 'neutral';
    
    if (!empty($mappedAsset['acquisition_value']) && $mappedAsset['acquisition_value'] > 0) {
        $valueChange = (($mappedAsset['current_value'] - $mappedAsset['acquisition_value']) / $mappedAsset['acquisition_value']) * 100;
        $valueChangeDirection = $valueChange >= 0 ? 'positive' : 'negative';
        $valueChangeFormatted = ($valueChange >= 0 ? '+' : '') . number_format($valueChange, 2) . '%';
    }
    
    // Format monetary values
    $acquisitionValueFormatted = number_format($mappedAsset['acquisition_value'], 0, ',', ' ');
    $currentValueFormatted = number_format($mappedAsset['current_value'], 0, ',', ' ');
    
    // Return enriched data
    return array_merge($mappedAsset, [
        'category_name' => $categoryName,
        'acquisition_date_formatted' => $acquisitionDateFormatted,
        'valuation_date_formatted' => $valuationDateFormatted,
        'acquisition_value_formatted' => $acquisitionValueFormatted,
        'current_value_formatted' => $currentValueFormatted,
        'value_change' => $valueChange,
        'value_change_formatted' => $valueChangeFormatted,
        'value_change_direction' => $valueChangeDirection,
        'chart_data' => [
            'labels' => ['Acquisition', 'Aujourd\'hui'],
            'values' => [$mappedAsset['acquisition_value'], $mappedAsset['current_value']]
        ]
    ]);
}

/**
 * Send error response and exit
 * 
 * @param string $message Error message
 * @param int $statusCode HTTP status code
 * @return void
 */
function sendErrorResponse($message, $statusCode = 400) {
    // Clear all output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    http_response_code($statusCode);
    
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    
    exit;
}
?>