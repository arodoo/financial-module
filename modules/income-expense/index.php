<?php
require_once __DIR__ . '/../../models/Income.php';
require_once __DIR__ . '/../../models/Expense.php';

// Initialize models
$incomeModel = new Income();
$expenseModel = new Expense();

// Set default date range to current month
$today = new DateTime();
$startDate = $today->format('Y-m-01'); // First day of current month
$endDate = $today->format('Y-m-t');    // Last day of current month

// Process filter form if submitted
if (isset($_GET['filter'])) {
    $startDate = $_GET['start_date'] ?? $startDate;
    $endDate = $_GET['end_date'] ?? $endDate;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_income'])) {
        $incomeModel->addIncome(
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'],
            $_POST['transaction_date']
        );
        // Redirect to prevent form resubmission
        header("Location: ?action=income-expense&success=income_added");
        exit;
    }
    
    if (isset($_POST['add_expense'])) {
        $expenseModel->addExpense(
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'],
            $_POST['transaction_date']
        );
        // Redirect to prevent form resubmission
        header("Location: ?action=income-expense&success=expense_added");
        exit;
    }
}

// Get data for the dashboard
$incomeCategories = $incomeModel->getAllCategories();
$expenseCategories = $expenseModel->getAllCategories();
$incomeTransactions = $incomeModel->getIncomeTransactions($startDate, $endDate);
$expenseTransactions = $expenseModel->getExpenseTransactions($startDate, $endDate);

$totalIncome = $incomeModel->getTotalIncome($startDate, $endDate);
$totalExpense = $expenseModel->getTotalExpense($startDate, $endDate);
$netBalance = $totalIncome - $totalExpense;
?>

<!-- Success Messages -->
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

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Revenu Total</h5>
                <h3 class="card-text"><?php echo '₱' . number_format($totalIncome, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h5 class="card-title text-danger">Dépenses Totales</h5>
                <h3 class="card-text"><?php echo '₱' . number_format($totalExpense, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card <?php echo $netBalance >= 0 ? 'border-success' : 'border-warning'; ?>">
            <div class="card-body text-center">
                <h5 class="card-title <?php echo $netBalance >= 0 ? 'text-success' : 'text-warning'; ?>">Solde Net</h5>
                <h3 class="card-text"><?php echo '₱' . number_format($netBalance, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Date Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="action" value="income-expense">
            <input type="hidden" name="filter" value="1">
            
            <div class="col-md-4">
                <label for="start_date" class="form-label">Date de Début</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Date de Fin</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Appliquer Filtre</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabs for Income/Expense Management -->
<ul class="nav nav-tabs mb-3" id="financeTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button">
            Gestion des Revenus
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense" type="button">
            Gestion des Dépenses
        </button>
    </li>
</ul>

<div class="tab-content" id="financeTabContent">
    <!-- Income Tab -->
    <div class="tab-pane fade show active" id="income" role="tabpanel">
        <div class="row">
            <!-- Income Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Ajouter Nouveau Revenu</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="income_category" class="form-label">Catégorie</label>
                                <select class="form-select" id="income_category" name="category_id" required>
                                    <option value="">Sélectionner Catégorie</option>
                                    <?php foreach ($incomeCategories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="income_amount" class="form-label">Montant (₱)</label>
                                <input type="number" class="form-control" id="income_amount" name="amount" step="0.01" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="income_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_description" class="form-label">Description</label>
                                <textarea class="form-control" id="income_description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_income" class="btn btn-primary w-100">Ajouter Revenu</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Income Transactions List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Transactions de Revenus</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Catégorie</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($incomeTransactions)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune transaction de revenu trouvée pour cette période.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($incomeTransactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></td>
                                                <td><?php echo $transaction['category_name']; ?></td>
                                                <td><?php echo $transaction['description'] ?: 'N/A'; ?></td>
                                                <td class="text-end">₱<?php echo number_format($transaction['amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Expense Tab -->
    <div class="tab-pane fade" id="expense" role="tabpanel">
        <div class="row">
            <!-- Expense Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Ajouter Nouvelle Dépense</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="expense_category" class="form-label">Catégorie</label>
                                <select class="form-select" id="expense_category" name="category_id" required>
                                    <option value="">Sélectionner Catégorie</option>
                                    <?php foreach ($expenseCategories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="expense_amount" class="form-label">Montant (₱)</label>
                                <input type="number" class="form-control" id="expense_amount" name="amount" step="0.01" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expense_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="expense_description" class="form-label">Description</label>
                                <textarea class="form-control" id="expense_description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_expense" class="btn btn-danger w-100">Ajouter Dépense</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Expense Transactions List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Transactions de Dépenses</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Catégorie</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($expenseTransactions)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune transaction de dépense trouvée pour cette période.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($expenseTransactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></td>
                                                <td><?php echo $transaction['category_name']; ?></td>
                                                <td><?php echo $transaction['description'] ?: 'N/A'; ?></td>
                                                <td class="text-end">₱<?php echo number_format($transaction['amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
