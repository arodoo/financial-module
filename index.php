<?php
// Entry point for the visualization module
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary configurations and models
require_once 'config/config.php';
require_once 'models/Membre.php';

// Get current membre info
$membreModel = new Membre();
$currentMembre = $membreModel->getMembre($id_oo);

// Global user ID is now available from config.php as $id_oo and CURRENT_USER_ID constant

// Define available modules and their titles
$modules = [
    'dashboard' => 'Tableau de bord',
    'income-expense' => 'Revenus & Dépenses',
    'asset-management' => 'Gestion des actifs',
    'loan-simulator' => 'Simulateur de prêt',
    'school-fee' => 'Frais scolaires'
];

// Route requests based on the action parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Validate action
if (!array_key_exists($action, $modules)) {
    $action = 'dashboard';
}

// HTML header
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Financier - <?php echo $modules[$action]; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .content-card {
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <!-- Header with Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Système de Gestion Financière</a>
            <!-- Membre indicator -->
            <span class="navbar-text me-3 text-white">
                <?php if ($currentMembre): ?>
                    Utilisateur: <?php echo htmlspecialchars($currentMembre['prenom'] . ' ' . $currentMembre['nom']); ?>
                <?php else: ?>
                    ID Membre: <?php echo $id_oo; ?>
                <?php endif; ?>
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($modules as $key => $title): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($action == $key) ? 'active' : ''; ?>" 
                               href="?action=<?php echo $key; ?>">
                                <?php echo $title; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card content-card">
            <div class="card-header bg-light">
                <h2 class="card-title h4 my-2"><?php echo $modules[$action]; ?></h2>
            </div>
            <div class="card-body">
                <?php
                // Display content based on action
                switch ($action) {
                    case 'dashboard':
                        // Include the dashboard module instead of hardcoded content
                        include 'modules/dashboard/index.php';
                        break;
                    case 'income-expense':
                        // Include the income-expense module
                        include 'modules/income-expense/index.php';
                        break;
                    case 'asset-management':
                        // Include the asset-management module
                        include 'modules/asset-management/index.php';
                        break;
                    case 'loan-simulator':
                        // Include the loan simulator module
                        include 'modules/loan-simulator/index.php';
                        break;
                    case 'school-fee':
                        include 'modules/school-fee-simulator/index.php';
                        break;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> Système de Gestion Financière
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>