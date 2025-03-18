<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] === 'income_added') {
            echo "Revenu ajouté avec succès!";
        } elseif ($_GET['success'] === 'expense_added') {
            echo "Dépense ajoutée avec succès!";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php include 'calculation-results.php'; ?>
