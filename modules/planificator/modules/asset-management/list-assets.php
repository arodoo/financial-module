<?php
// This file displays the list of all assets in a table view
$tableId = 'assetManagementTable';
$tableTitle = 'Liste des Actifs';
$nom_fichier_datatable = $tableTitle . "-" . date('d-m-Y', time());
?>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Actifs</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="<?php echo $tableId; ?>" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th class="text-end">Valeur</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="search_table">Nom</th>
                        <th class="search_table">Catégorie</th>
                        <th class="search_table">Valeur</th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($assets as $asset): 
                        $categoryName = '';
                        foreach ($categories as $category) {
                            if ($category['id'] == $asset['category_id']) {
                                $categoryName = $category['name'];
                                break;
                            }
                        }
                        $acquisitionDate = !empty($asset['purchase_date']) ? date('d/m/Y', strtotime($asset['purchase_date'])) : 'N/A';
                    ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($asset['name']); ?>
                            <small class="d-block text-muted">Acquis: <?php echo $acquisitionDate; ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($categoryName); ?></td>
                        <td class="text-end"><?php echo number_format($asset['current_value'], 0, ',', ' '); ?>€</td>
                        <td class="text-center action-column">
                            <!-- Actions buttons using data-id attributes for JavaScript -->
                            <button type="button" class="btn btn-sm btn-info view-asset" data-id="<?php echo $asset['id']; ?>" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning edit-asset" data-id="<?php echo $asset['id']; ?>" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-asset" data-id="<?php echo $asset['id']; ?>" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables
    var dataTable = $('#<?php echo $tableId; ?>').DataTable({
        "order": [],
        responsive: false,
        stateSave: false, 
        dom: 'Bftipr',
        pageLength: 5,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
        buttons: [
            {
                extend: 'print',
                text: "Imprimer",
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'pdf',
                filename: "<?php echo $nom_fichier_datatable; ?>",
                title: "<?php echo $tableTitle; ?>",
                exportOptions: {
                    columns: [0, 1, 2]
                }
            }, 
            {
                extend: 'csv',
                filename: "<?php echo $nom_fichier_datatable; ?>",
                exportOptions: {
                    columns: [0, 1, 2]
                }
            }, 
            {
                extend: 'colvis',
                text: "Colonnes visibles",
                columns: [0, 1, 2]
            }
        ],
        columnDefs: [
            { targets: 0, responsivePriority: 2 },
            { targets: 2, responsivePriority: 3 },
            { 
                targets: 3,
                orderable: false,
                searchable: false,
                className: 'action-column',
                responsivePriority: 1
            }
        ],
        "language": {
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher&nbsp;:",
            "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
            "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;léments",
            "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Pr&eacute;c&eacute;dent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
        }
    });
    
    // Add search functionality for each column
    $('#<?php echo $tableId; ?> tfoot .search_table').each(function() {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="' + title + '" style="width:100%; font-weight: normal;"/>');
    });
    
    // Apply search when typing
    dataTable.columns().every(function() {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });
    
    // Make sure action buttons are visible after DataTable initialization
    setTimeout(function() {
        $('.action-column button, .action-column a').css('display', 'inline-block');
    }, 100);
});
</script>
