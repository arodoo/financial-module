<?php
namespace Financial\Modules\Visualization\Controllers;

use Financial\Modules\Visualization\Models\Asset;
use PDO;

require_once __DIR__ . '/../models/Asset.php';
require_once __DIR__ . '/../models/Membre.php';

/**
 * Asset Management Controller
 */
class AssetManagementController
{
    private $assetModel;
    private $membreModel;

    public function __construct()
    {
        $this->assetModel = new Asset();
        // Fix: Use the global namespace for the Membre class since it doesn't have a namespace
        $this->membreModel = new \Membre();
    }

    /**
     * Process actions based on request
     */
    public function processRequest() {
        global $id_oo;
        
        // Check for form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Add new asset
            if (isset($_POST['add_asset'])) {
                $this->addAsset($_POST);
                header('Location: ?action=asset-management&success=asset_added');
                exit;
            }
            
            // Update asset
            if (isset($_POST['update_asset'])) {
                $assetId = intval($_POST['asset_id']);
                $this->updateAsset($assetId, $_POST);
                header('Location: ?action=asset-management&success=asset_updated');
                exit;
            }
        }
        
        // Handle asset deletion
        if (isset($_GET['delete_asset'])) {
            $assetId = intval($_GET['delete_asset']);
            $this->deleteAsset($assetId);
            header('Location: ?action=asset-management&success=asset_deleted');
            exit;
        }
    }

    /**
     * Get data needed for the asset management module
     */
    public function getViewData() {
        $viewData = [];
        
        // Get assets and related data
        $viewData['assets'] = $this->assetModel->getAllAssets();
        $viewData['totalValue'] = $this->assetModel->getTotalAssetValue();
        $viewData['assetsByCategory'] = $this->assetModel->getAssetsByCategory();
        $viewData['categories'] = $this->assetModel->getAllCategories();
        
        // Check if we need to edit an asset
        if (isset($_GET['edit_asset'])) {
            $assetId = intval($_GET['edit_asset']);
            $viewData['editAsset'] = $this->assetModel->getAsset($assetId);
        }
        
        // Check if we need to show asset details
        if (isset($_GET['view_asset'])) {
            $assetId = intval($_GET['view_asset']);
            $viewData['viewAsset'] = $this->assetModel->getAsset($assetId);
            $viewData['assetHistory'] = $this->assetModel->getAssetValueHistory($assetId);
        }
        
        return $viewData;
    }
    
    /**
     * Add a new asset
     */
    private function addAsset($data) {
        return $this->assetModel->addAsset([
            'category_id' => intval($data['category_id']),
            'name' => trim($data['name']),
            'description' => trim($data['description'] ?? ''),
            'purchase_value' => floatval($data['purchase_value']),
            'current_value' => floatval($data['current_value']),
            'purchase_date' => $data['purchase_date'],
            'last_valuation_date' => $data['last_valuation_date'] ?? date('Y-m-d'),
            'location' => trim($data['location'] ?? ''),
            'notes' => trim($data['notes'] ?? '')
        ]);
    }
    
    /**
     * Update an existing asset
     */
    private function updateAsset($assetId, $data) {
        return $this->assetModel->updateAsset($assetId, [
            'category_id' => intval($data['category_id']),
            'name' => trim($data['name']),
            'description' => trim($data['description'] ?? ''),
            'purchase_value' => floatval($data['purchase_value']),
            'current_value' => floatval($data['current_value']),
            'purchase_date' => $data['purchase_date'],
            'last_valuation_date' => $data['last_valuation_date'] ?? date('Y-m-d'),
            'location' => trim($data['location'] ?? ''),
            'notes' => trim($data['notes'] ?? '')
        ]);
    }
    
    /**
     * Delete an asset
     */
    private function deleteAsset($assetId) {
        return $this->assetModel->deleteAsset($assetId);
    }
}
?>