<?php
require_once __DIR__ . '/../models/Asset.php';
require_once __DIR__ . '/../models/Membre.php';

class AssetController {
    private $assetModel;
    private $currentUser;

    public function __construct() {
        global $id_oo;
        $this->assetModel = new Asset();
        $this->currentUser = ['id' => $id_oo];
    }

    /**
     * Get all asset categories
     */
    public function getCategories() {
        return $this->assetModel->getCategories();
    }

    /**
     * Get all assets for current user
     */
    public function getAssets() {
        if (!$this->currentUser['id']) return [];
        
        return $this->assetModel->getAssetsByMemberId($this->currentUser['id']);
    }

    /**
     * Get asset by ID
     */
    public function getAssetById($id) {
        if (!$this->currentUser['id']) return null;
        
        return $this->assetModel->getAssetById($id);
    }

    /**
     * Save new asset
     */
    public function saveAsset($data) {
        if (!$this->currentUser['id']) return false;
        
        $assetData = [
            'membre_id' => $this->currentUser['id'],
            'name' => $data['asset_name'],
            'category_id' => (int)$data['category_id'],
            'acquisition_date' => !empty($data['acquisition_date']) ? $data['acquisition_date'] : null,
            'acquisition_value' => str_replace(' ', '', $data['acquisition_value']),
            'valuation_date' => !empty($data['valuation_date']) ? $data['valuation_date'] : date('Y-m-d'),
            'current_value' => str_replace(' ', '', $data['current_value']),
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null
        ];
        
        return $this->assetModel->saveAsset($assetData);
    }

    /**
     * Update existing asset
     */
    public function updateAsset($data) {
        if (!$this->currentUser['id']) return false;
        
        $assetId = (int)$data['asset_id'];
        
        $assetData = [
            'membre_id' => $this->currentUser['id'],
            'name' => $data['asset_name'],
            'category_id' => (int)$data['category_id'],
            'acquisition_date' => !empty($data['acquisition_date']) ? $data['acquisition_date'] : null,
            'acquisition_value' => str_replace(' ', '', $data['acquisition_value']),
            'valuation_date' => !empty($data['valuation_date']) ? $data['valuation_date'] : date('Y-m-d'),
            'current_value' => str_replace(' ', '', $data['current_value']),
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null
        ];
        
        return $this->assetModel->updateAsset($assetId, $assetData);
    }

    /**
     * Delete an asset
     */
    public function deleteAsset($assetId) {
        if (!$this->currentUser['id']) return false;
        
        return $this->assetModel->deleteAsset((int)$assetId, $this->currentUser['id']);
    }

    /**
     * Get data for the view
     */
    public function getViewData() {
        $data = [];
        
        // Get all asset categories
        $data['categories'] = $this->getCategories();
        
        // Get all assets for the current user
        $data['assets'] = $this->getAssets();
        
        // Check if a specific asset is requested to view
        if (isset($_GET['view_asset'])) {
            $assetId = (int)$_GET['view_asset'];
            $asset = $this->getAssetById($assetId);
            
            if ($asset) {
                $data['viewAsset'] = $asset;
            }
        }
        
        // Check if a specific asset is requested to edit
        if (isset($_GET['edit_asset'])) {
            $assetId = (int)$_GET['edit_asset'];
            $asset = $this->getAssetById($assetId);
            
            if ($asset) {
                $data['editAsset'] = $asset;
            }
        }
        
        return $data;
    }
}
?>
