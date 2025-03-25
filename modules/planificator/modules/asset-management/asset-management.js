/**
 * Asset Management Module JavaScript
 * Contains all JavaScript functionality for the asset management module
 */

// Global variables for URLs
let ajaxHandlerUrl = '';
let assetDetailsUrl = '';

/**
 * Initialize the asset management module
 * @param {Object} config Configuration object with URLs
 */
function initAssetManagement(config) {
    // Set global URLs
    ajaxHandlerUrl = config.ajaxHandlerUrl;
    assetDetailsUrl = config.assetDetailsUrl;
    
    // Initialize event listeners
    initEventListeners();
}

/**
 * Initialize event listeners for the table
 */
function initEventListeners() {
    // View asset button
    $(document).on('click', '.view-asset', function() {
        const assetId = $(this).data('id');
        viewAssetDetails(assetId);
    });
    
    // Edit asset button
    $(document).on('click', '.edit-asset', function() {
        const assetId = $(this).data('id');
        editAsset(assetId);
    });
    
    // Delete asset button
    $(document).on('click', '.delete-asset', function() {
        const assetId = $(this).data('id');
        deleteAsset(assetId);
    });
}

/**
 * View asset details in modal
 * @param {number} assetId The asset ID
 */
function viewAssetDetails(assetId) {
    // Show the modal with loading indicator
    $('#assetDetailsModal').modal('show');
    $('#assetModalLoader').show();
    $('#assetModalContent').hide();
    
    // Fetch asset data
    $.ajax({
        url: `${assetDetailsUrl}?asset_id=${assetId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Hide loader
                $('#assetModalLoader').hide();
                
                // Get prepared asset data
                const asset = response.data;
                
                // Update modal title
                $('#assetDetailsModalLabel').text('Détails de l\'Actif: ' + asset.name);
                
                // Fill modal content with asset data
                populateAssetModal(asset);
                
                // Configure action buttons
                configureModalActionButtons(asset);
                
                // Show content
                $('#assetModalContent').show();
            } else {
                alert('Erreur: ' + response.message);
                $('#assetDetailsModal').modal('hide');
            }
        },
        error: function(error) {
            console.error('Error fetching asset details:', error);
            alert('Erreur lors de la récupération des données de l\'actif');
            $('#assetDetailsModal').modal('hide');
        }
    });
}

/**
 * Edit an asset
 * @param {number} assetId The asset ID
 */
function editAsset(assetId) {
    // Fetch asset data via AJAX
    $.ajax({
        url: `${ajaxHandlerUrl}?action=get_asset&asset_id=${assetId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Access the asset data
                const asset = response.data;
                
                // Find the form and populate it
                const form = $('#asset-form');
                
                // Set hidden input for asset ID
                let assetIdField = form.find('[name="asset_id"]');
                if (assetIdField.length === 0) {
                    // Create if it doesn't exist
                    form.append(`<input type="hidden" name="asset_id" value="${asset.id}">`);
                } else {
                    // Update existing field
                    assetIdField.val(asset.id);
                }
                
                // Populate form fields
                form.find('#asset_name').val(asset.name);
                form.find('#category_id').val(asset.category_id);
                
                // Fix date fields - map from database field names to form field names
                form.find('#acquisition_date').val(asset.purchase_date || '');
                form.find('#valuation_date').val(asset.last_valuation_date || '');
                
                // Fix value fields - with proper number formatting
                if (asset.purchase_value) {
                    form.find('#acquisition_value').val(Number(asset.purchase_value).toLocaleString('fr-FR'));
                }
                
                if (asset.current_value) {
                    form.find('#current_value').val(Number(asset.current_value).toLocaleString('fr-FR'));
                }
                
                // Optional fields
                if (asset.location) {
                    form.find('#location').val(asset.location);
                }
                if (asset.notes) {
                    form.find('#notes').val(asset.notes);
                }
                
                // Change form mode to edit
                const submitButton = form.find('button[type="submit"]');
                if (submitButton.attr('name') !== 'update_asset') {
                    submitButton.attr('name', 'update_asset').text('Mettre à jour').removeClass('btn-primary').addClass('btn-warning');
                    
                    // Add cancel button if it doesn't exist
                    if (form.find('.btn-secondary').length === 0) {
                        const cancelButton = $('<button type="button" class="btn btn-secondary ms-2">Annuler</button>');
                        cancelButton.on('click', function() {
                            resetAssetForm(form);
                        });
                        submitButton.after(cancelButton);
                    }
                }
                
                // Close modal if open
                $('#assetDetailsModal').modal('hide');
                
                // Scroll to form
                $('html, body').animate({
                    scrollTop: form.offset().top - 100
                }, 500);
                
                // Highlight form
                form.closest('.card').addClass('border-warning');
                setTimeout(() => {
                    form.closest('.card').removeClass('border-warning');
                }, 2000);
            } else {
                alert('Erreur: ' + response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching asset:', error);
            alert('Erreur lors de la récupération des données de l\'actif');
        }
    });
}

/**
 * Delete an asset
 * @param {number} assetId The asset ID
 */
function deleteAsset(assetId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet actif?')) {
        // Use AJAX to delete the asset
        $.ajax({
            url: `${ajaxHandlerUrl}?action=delete_asset&asset_id=${assetId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Close modal if open
                    $('#assetDetailsModal').modal('hide');
                    // Refresh page with success message
                    window.location.href = '?action=asset-management&success=asset_deleted';
                } else {
                    alert('Erreur: ' + response.message);
                }
            },
            error: function(error) {
                console.error('Error deleting asset:', error);
                alert('Erreur lors de la suppression de l\'actif');
            }
        });
    }
}

/**
 * Configure modal action buttons
 * @param {Object} asset The asset data
 */
function configureModalActionButtons(asset) {
    // Edit button
    $('#modal-edit-asset-btn').off('click').on('click', function() {
        $('#assetDetailsModal').modal('hide');
        editAsset(asset.id);
    });
    
    // Delete button
    $('#modal-delete-asset-btn').off('click').on('click', function() {
        deleteAsset(asset.id);
    });
}

/**
 * Populate the modal with asset data
 * @param {Object} asset The asset data
 */
function populateAssetModal(asset) {
    // Use prepared formatted data from our handler
    $('#modal-asset-name').text(asset.name);
    $('#modal-asset-category').text(asset.category_name);
    $('#modal-asset-acquisition-date').text(asset.acquisition_date_formatted);
    $('#modal-asset-acquisition-value').text(asset.acquisition_value_formatted);
    $('#modal-asset-current-value').text(asset.current_value_formatted);
    $('#modal-asset-valuation-date').text(asset.valuation_date_formatted);
    
    // Set value change with appropriate styling
    const valueChangeClass = asset.value_change_direction === 'positive' ? 'text-success' : 
                          (asset.value_change_direction === 'negative' ? 'text-danger' : '');
    $('#modal-asset-evolution').html(`<span class="${valueChangeClass}">${asset.value_change_formatted}</span>`);
    
    // Optional fields
    if (asset.location) {
        $('#modal-asset-location').text(asset.location);
        $('#modal-asset-location-container').show();
    } else {
        $('#modal-asset-location-container').hide();
    }
    
    if (asset.notes) {
        $('#modal-asset-notes').html(asset.notes.replace(/\n/g, '<br>'));
        $('#modal-asset-notes-section').show();
    } else {
        $('#modal-asset-notes-section').hide();
    }
    
    // Initialize chart
    initializeAssetChart(asset);
}

/**
 * Initialize the asset value chart
 * @param {Object} asset The asset data
 */
function initializeAssetChart(asset) {
    if (document.getElementById('modalAssetValueChart')) {
        // Remove any existing chart
        if (window.assetChart instanceof Chart) {
            window.assetChart.destroy();
        }
        
        const ctx = document.getElementById('modalAssetValueChart').getContext('2d');
        
        window.assetChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: asset.chart_data.labels,
                datasets: [{
                    label: 'Valeur de l\'actif',
                    data: asset.chart_data.values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + '€';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
}

/**
 * Reset form to Add mode
 * @param {jQuery} form The form jQuery object
 */
function resetAssetForm(form) {
    form.trigger('reset');
    
    // Remove asset ID field
    form.find('[name="asset_id"]').remove();
    
    // Reset submit button
    const submitButton = form.find('button[type="submit"]');
    submitButton.attr('name', 'save_asset').text('Enregistrer').removeClass('btn-warning').addClass('btn-primary');
    
    // Remove cancel button
    form.find('.btn-secondary').remove();
    
    // Update date fields to today
    form.find('#valuation_date').val(new Date().toISOString().split('T')[0]);
}
