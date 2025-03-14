<?php
namespace Financial\Modules\Visualization\Controllers;

use Financial\Modules\Visualization\Models\Asset;
use Financial\Modules\Visualization\Services\VisualizationService;

/**
 * Asset Management Controller
 */
class AssetManagementController
{
    private $assetModel;
    private $visualizationService;

    public function __construct()
    {
        $this->assetModel = new Asset();
        $this->visualizationService = new VisualizationService();
    }

    public function index()
    {
        // Display asset management view
        include_once __DIR__ . '/../views/asset-management/index.php';
    }

    public function viewAsset($id)
    {
        $asset = $this->assetModel->getAssetById($id);
        // Render the asset details view with the asset data
        include_once __DIR__ . '/../views/asset-management/details.php';
    }

    public function addAsset($data)
    {
        $this->assetModel->createAsset($data);
        // Redirect to the asset management index
        header('Location: /financial/visualization/asset-management');
    }

    public function updateAsset($id, $data)
    {
        $this->assetModel->updateAsset($id, $data);
        // Redirect to the asset management index
        header('Location: /financial/visualization/asset-management');
    }

    public function deleteAsset($id)
    {
        $this->assetModel->deleteAsset($id);
        // Redirect to the asset management index
        header('Location: /financial/visualization/asset-management');
    }
}
?>