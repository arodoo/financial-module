<?php
// This file displays the list of all child profiles in a table view
?>
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Liste des Profils Enfants</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>École</th>
                        <th>Niveau Actuel</th>
                        <th class="text-end">Frais Annuels</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($children as $child): 
                        // Calculate age
                        $birthdate = new DateTime($child['birthdate']);
                        $today = new DateTime();
                        $age = $birthdate->diff($today)->y;
                    ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($child['name']); ?>
                            <small class="d-block text-muted"><?php echo $age; ?> ans</small>
                        </td>
                        <td><?php echo htmlspecialchars($child['school_name'] ?: 'N/A'); ?></td>
                        <td><?php echo $educationLevels[$child['current_level']]['name']; ?></td>
                        <td class="text-end">€<?php echo number_format($child['annual_tuition'], 2); ?></td>
                        <td class="text-center">
                            <a href="?action=school-fee&view_child=<?php echo $child['id']; ?>" class="btn btn-sm btn-info">Voir</a>
                            <a href="?action=school-fee&edit_child=<?php echo $child['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="?action=school-fee&delete_child=<?php echo $child['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce profil?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
