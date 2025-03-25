<?php
// filepath: /financial/financial/modules/visualization/views/asset-management/index.php

// Include necessary controllers and models
require_once '../controllers/AssetManagementController.php';
require_once '../models/Asset.php';

// Initialize the Asset Management Controller
$assetController = new AssetManagementController();

// Fetch asset data
$assets = $assetController->getAllAssets();

// Include the header
include '../header.php';
?>

<div class="container">
    <h1>Asset Management</h1>
    <div class="dashboard-summary">
        <h2>Summary of Assets</h2>
        <table>
            <thead>
                <tr>
                    <th>Asset Name</th>
                    <th>Value</th>
                    <th>Date Acquired</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assets as $asset): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asset->name); ?></td>
                        <td><?php echo htmlspecialchars($asset->value); ?></td>
                        <td><?php echo htmlspecialchars($asset->date_acquired); ?></td>
                        <td>
                            <a href="details.php?id=<?php echo $asset->id; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include the footer
include '../footer.php';
?>