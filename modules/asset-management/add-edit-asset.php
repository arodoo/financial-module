<?php
// This file contains the form for adding or editing an asset
$isEditMode = isset($editAsset) && $editAsset;
$formTitle = $isEditMode ? 'Modifier l\'Actif' : 'Ajouter un Actif';
$asset = $isEditMode ? $editAsset : null;
?>

<div class="card-body">
    <form method="POST" id="asset-form">
        <?php if ($isEditMode): ?>
            <input type="hidden" name="asset_id" value="<?php echo $asset['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="asset_name" class="form-label">Nom de l'actif</label>
            <input type="text" class="form-control" id="asset_name" name="asset_name" 
                value="<?php echo $isEditMode ? htmlspecialchars($asset['name']) : ''; ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="category_id" class="form-label">Catégorie</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Choisir une catégorie</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($isEditMode && $asset['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="acquisition_date" class="form-label">Date d'acquisition</label>
                <input type="date" class="form-control" id="acquisition_date" name="acquisition_date" 
                    value="<?php echo $isEditMode && !empty($asset['acquisition_date']) ? $asset['acquisition_date'] : ''; ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="acquisition_value" class="form-label">Prix d'acquisition (€)</label>
                <input type="text" class="form-control currency-input" id="acquisition_value" name="acquisition_value" 
                    value="<?php echo $isEditMode ? number_format($asset['acquisition_value'], 0, ',', ' ') : ''; ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="valuation_date" class="form-label">Date de dernière évaluation</label>
                <input type="date" class="form-control" id="valuation_date" name="valuation_date" 
                    value="<?php echo $isEditMode && !empty($asset['valuation_date']) ? $asset['valuation_date'] : date('Y-m-d'); ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="current_value" class="form-label">Valeur actuelle (€)</label>
                <input type="text" class="form-control currency-input" id="current_value" name="current_value" 
                    value="<?php echo $isEditMode ? number_format($asset['current_value'], 0, ',', ' ') : ''; ?>" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="location" class="form-label">Emplacement</label>
            <input type="text" class="form-control" id="location" name="location" 
                value="<?php echo $isEditMode && !empty($asset['location']) ? htmlspecialchars($asset['location']) : ''; ?>" 
                placeholder="Adresse ou localisation (optionnel)">
        </div>
        
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" 
                placeholder="Informations supplémentaires (optionnel)"><?php echo $isEditMode && !empty($asset['notes']) ? htmlspecialchars($asset['notes']) : ''; ?></textarea>
        </div>
        
        <div class="d-flex justify-content-between">
            <?php if ($isEditMode): ?>
                <button type="submit" name="update_asset" class="btn btn-warning">Mettre à jour</button>
                <a href="?action=asset-management" class="btn btn-secondary">Annuler</a>
            <?php else: ?>
                <button type="submit" name="save_asset" class="btn btn-primary">Enregistrer</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(input => {
        // Format on page load
        if (input.value) {
            let value = input.value.replace(/\D/g, '');
            input.value = new Intl.NumberFormat('fr-FR').format(value);
        }
        
        // Format when typing
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('fr-FR').format(value);
        });
        
        // Clean before form submission
        input.form.addEventListener('submit', function() {
            currencyInputs.forEach(inp => {
                inp.value = inp.value.replace(/\D/g, '');
            });
        });
    });
    
    // Show/hide additional fields based on category
    const categorySelect = document.getElementById('category_id');
    const locationField = document.getElementById('location').closest('.mb-3');
    
    function updateVisibility() {
        // Category ID 1 is typically real estate
        if (categorySelect.value === '1') {
            locationField.style.display = 'block';
        } else {
            locationField.style.display = 'none';
        }
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', updateVisibility);
        updateVisibility(); // Run on page load
    }
});
</script>
