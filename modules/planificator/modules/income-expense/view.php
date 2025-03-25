<?php
// Use the ajax-handler.php file with a new approach that doesn't hit rewrite rules
$ajaxHandlerUrl = '/modules/planificator/modules/income-expense/ajax-handler.php';
?>

<?php if (isset($flashMessage) && isset($flashType)): ?>
    <div class="alert alert-<?php echo $flashType; ?> alert-dismissible fade show" role="alert">
        <?php echo $flashMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['success'])): ?>
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

<!-- JavaScript for handling edit and delete actions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store AJAX handler URL
    const ajaxHandlerUrl = '<?php echo $ajaxHandlerUrl; ?>';
    
    // Function to scroll to form
    function scrollToForm(formId) {
        const formElement = document.getElementById(formId);
        if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Add a highlight effect to draw attention
            formElement.classList.add('highlight-form');
            setTimeout(() => {
                formElement.classList.remove('highlight-form');
            }, 1500);
        }
    }
    
    // Use event delegation for income tab actions
    document.querySelector('#income').addEventListener('click', function(event) {
        // Edit transaction button
        if (event.target.closest('.edit-transaction')) {
            const button = event.target.closest('.edit-transaction');
            const transactionId = button.getAttribute('data-id');
            
            // Fetch transaction details from AJAX handler
            fetch(`${ajaxHandlerUrl}?action=get_income_transaction&id=${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the form
                        const form = document.querySelector('#income form');
                        
                        // Set category value
                        const categorySelect = form.querySelector('[name="category_id"]');
                        categorySelect.value = data.category_id;
                        
                        // Set other form values
                        form.querySelector('[name="amount"]').value = data.amount;
                        form.querySelector('[name="description"]').value = data.description || '';
                        form.querySelector('[name="transaction_date"]').value = data.transaction_date;
                        
                        // Change form submission type
                        const submitButton = form.querySelector('button[type="submit"]');
                        submitButton.name = 'update_income';
                        submitButton.textContent = 'Mettre à jour';
                        
                        // Add hidden field for transaction ID
                        let transactionIdField = form.querySelector('[name="transaction_id"]');
                        if (!transactionIdField) {
                            transactionIdField = document.createElement('input');
                            transactionIdField.type = 'hidden';
                            transactionIdField.name = 'transaction_id';
                            form.appendChild(transactionIdField);
                        }
                        transactionIdField.value = transactionId;
                        
                        // Add a cancel button if it doesn't exist
                        let cancelButton = form.querySelector('.cancel-edit');
                        if (!cancelButton) {
                            cancelButton = document.createElement('button');
                            cancelButton.type = 'button';
                            cancelButton.className = 'btn btn-secondary cancel-edit mt-2 ms-2';
                            cancelButton.textContent = 'Annuler';
                            submitButton.parentNode.appendChild(cancelButton);
                            
                            // Add event listener for cancel button
                            cancelButton.addEventListener('click', function() {
                                resetForm(form, 'add_income', 'Ajouter Revenu');
                            });
                        }
                        
                        // Scroll to the form
                        scrollToForm('income-form');
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction:', error);
                    alert('Erreur lors de la récupération des données: ' + error.message);
                });
        }
        
        // Delete transaction button
        if (event.target.closest('.delete-transaction')) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce revenu ?')) {
                const button = event.target.closest('.delete-transaction');
                const transactionId = button.getAttribute('data-id');
                
                // Create and submit a form to delete the transaction
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'transaction_id';
                inputId.value = transactionId;
                
                const submitButton = document.createElement('input');
                submitButton.type = 'hidden';
                submitButton.name = 'delete_income';
                submitButton.value = '1';
                
                form.appendChild(inputId);
                form.appendChild(submitButton);
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
    
    // Use event delegation for expense tab actions
    document.querySelector('#expense').addEventListener('click', function(event) {
        // Edit transaction button
        if (event.target.closest('.edit-transaction')) {
            const button = event.target.closest('.edit-transaction');
            const transactionId = button.getAttribute('data-id');
            
            fetch(`${ajaxHandlerUrl}?action=get_expense_transaction&id=${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const form = document.querySelector('#expense form');
                        
                        // Set category value
                        const categorySelect = form.querySelector('[name="category_id"]');
                        categorySelect.value = data.category_id;
                        
                        // Set other form values
                        form.querySelector('[name="amount"]').value = data.amount;
                        form.querySelector('[name="description"]').value = data.description || '';
                        form.querySelector('[name="transaction_date"]').value = data.transaction_date;
                        
                        // Change form submission type
                        const submitButton = form.querySelector('button[type="submit"]');
                        submitButton.name = 'update_expense';
                        submitButton.textContent = 'Mettre à jour';
                        
                        // Add hidden field for transaction ID
                        let transactionIdField = form.querySelector('[name="transaction_id"]');
                        if (!transactionIdField) {
                            transactionIdField = document.createElement('input');
                            transactionIdField.type = 'hidden';
                            transactionIdField.name = 'transaction_id';
                            form.appendChild(transactionIdField);
                        }
                        transactionIdField.value = transactionId;
                        
                        // Add a cancel button if it doesn't exist
                        let cancelButton = form.querySelector('.cancel-edit');
                        if (!cancelButton) {
                            cancelButton = document.createElement('button');
                            cancelButton.type = 'button';
                            cancelButton.className = 'btn btn-secondary cancel-edit mt-2 ms-2';
                            cancelButton.textContent = 'Annuler';
                            submitButton.parentNode.appendChild(cancelButton);
                            
                            // Add event listener for cancel button
                            cancelButton.addEventListener('click', function() {
                                resetForm(form, 'add_expense', 'Ajouter Dépense');
                            });
                        }
                        
                        // Scroll to the form
                        scrollToForm('expense-form');
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction:', error);
                    alert('Erreur lors de la récupération des données: ' + error.message);
                });
        }
        
        // Delete transaction button
        if (event.target.closest('.delete-transaction')) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
                const button = event.target.closest('.delete-transaction');
                const transactionId = button.getAttribute('data-id');
                
                // Create and submit a form to delete the transaction
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'transaction_id';
                inputId.value = transactionId;
                
                const submitButton = document.createElement('input');
                submitButton.type = 'hidden';
                submitButton.name = 'delete_expense';
                submitButton.value = '1';
                
                form.appendChild(inputId);
                form.appendChild(submitButton);
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
    
    // Helper function to reset a form
    function resetForm(form, buttonName, buttonText) {
        form.reset();
        
        // Change form submission type back to add
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.name = buttonName;
        submitButton.textContent = buttonText;
        
        // Remove transaction ID field
        const transactionIdField = form.querySelector('[name="transaction_id"]');
        if (transactionIdField) {
            transactionIdField.remove();
        }
        
        // Remove cancel button
        const cancelButton = form.querySelector('.cancel-edit');
        if (cancelButton) {
            cancelButton.remove();
        }
    }
});
</script>

<style>
/* Add highlight effect for the form */
.highlight-form {
    animation: highlight 1.5s ease-out;
}

@keyframes highlight {
    0% { background-color: rgba(255, 255, 0, 0.3); }
    100% { background-color: transparent; }
}
</style>
