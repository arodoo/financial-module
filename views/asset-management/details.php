<?php
// This file provides detailed information about specific assets.

require_once __DIR__ . '/../../../controllers/AssetManagementController.php';

$controller = new AssetManagementController();
$assetId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$assetDetails = $controller->getAssetDetails($assetId);

if (!$assetDetails) {
    echo "<h2>Asset not found</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/financial/modules/visualization/assets/css/style.css">
    <title>Asset Details</title>
</head>
<body>
    <div class="container">
        <h1>Asset Details</h1>
        <div class="asset-info">
            <h2><?php echo htmlspecialchars($assetDetails['name']); ?></h2>
            <p><strong>Value:</strong> <?php echo htmlspecialchars($assetDetails['value']); ?></p>
            <p><strong>Acquisition Date:</strong> <?php echo htmlspecialchars($assetDetails['acquisition_date']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($assetDetails['description']); ?></p>
        </div>
        <a href="/financial/modules/visualization/views/asset-management/index.php">Back to Asset Management</a>
    </div>
</body>
</html>