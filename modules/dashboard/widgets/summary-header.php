<!-- Month Summary -->
<div class="alert alert-info">
    Résumé financier pour <?php echo date('F Y'); ?> 
    <?php if ($currentMembre): ?>
        - <?php echo htmlspecialchars($currentMembre['prenom'] . ' ' . $currentMembre['nom']); ?>
    <?php endif; ?>
</div>
