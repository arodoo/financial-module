// filepath: /financial/financial/modules/visualization/assets/js/asset-management.js
document.addEventListener('DOMContentLoaded', function() {
    const assetTable = document.getElementById('asset-table');
    const addAssetForm = document.getElementById('add-asset-form');
    const assetNameInput = document.getElementById('asset-name');
    const assetValueInput = document.getElementById('asset-value');
    const assetDateInput = document.getElementById('asset-date');

    // Function to add a new asset
    addAssetForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const assetName = assetNameInput.value;
        const assetValue = parseFloat(assetValueInput.value);
        const assetDate = assetDateInput.value;

        if (assetName && !isNaN(assetValue) && assetDate) {
            const newRow = assetTable.insertRow();
            newRow.innerHTML = `<td>${assetName}</td><td>${assetValue.toFixed(2)}</td><td>${assetDate}</td>`;
            resetForm();
        } else {
            alert('Please fill in all fields correctly.');
        }
    });

    // Function to reset the form
    function resetForm() {
        assetNameInput.value = '';
        assetValueInput.value = '';
        assetDateInput.value = '';
    }

    // Function to calculate total assets
    function calculateTotalAssets() {
        let total = 0;
        for (let i = 1; i < assetTable.rows.length; i++) {
            total += parseFloat(assetTable.rows[i].cells[1].innerText);
        }
        document.getElementById('total-assets').innerText = total.toFixed(2);
    }

    // Event listener to recalculate total assets on table update
    assetTable.addEventListener('DOMSubtreeModified', calculateTotalAssets);

    // Initialize any interactive elements or validation
    const purchaseValueField = document.getElementById('purchase_value');
    const currentValueField = document.getElementById('current_value');
    
    // If purchase value changes and current value is empty, set them equal
    if (purchaseValueField && currentValueField) {
        purchaseValueField.addEventListener('change', function() {
            if (currentValueField.value === '') {
                currentValueField.value = purchaseValueField.value;
            }
        });
    }
    
    // Add confirmation for asset deletion
    const deleteButtons = document.querySelectorAll('[data-action="delete-asset"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet actif?')) {
                event.preventDefault();
            }
        });
    });
});

// Function to calculate asset appreciation/depreciation percentage
function calculateAppreciation(purchaseValue, currentValue) {
    const purchase = parseFloat(purchaseValue);
    const current = parseFloat(currentValue);
    
    if (isNaN(purchase) || purchase === 0) {
        return 0;
    }
    
    return ((current - purchase) / purchase) * 100;
}

// Function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(amount);
}