<?php
// This file displays the list of all assets in a table view
?>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Actifs</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th class="text-end">Valeur</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): 
                        $categoryName = '';
                        foreach ($categories as $category) {
                            if ($category['id'] == $asset['category_id']) {
                                $categoryName = $category['name'];
                                break;
                            }
                        }
                        $acquisitionDate = !empty($asset['acquisition_date']) ? date('d/m/Y', strtotime($asset['acquisition_date'])) : 'N/A';
                    ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($asset['name']); ?>
                            <small class="d-block text-muted">Acquis: <?php echo $acquisitionDate; ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($categoryName); ?></td>
                        <td class="text-end"><?php echo number_format($asset['current_value'], 0, ',', ' '); ?>€</td>
                        <td class="text-center">
                            <a href="?action=asset-management&view_asset=<?php echo $asset['id']; ?>" class="btn btn-sm btn-info">Voir</a>
                            <a href="?action=asset-management&edit_asset=<?php echo $asset['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="asset_id" value="<?php echo $asset['id']; ?>">
                                <button type="submit" name="delete_asset" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet actif?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
