<?php

if (!empty($_SESSION['4M8e7M5b1R2e8s']) || !empty($user)) {

    require_once 'config/config.php';
    require_once 'models/Membre.php';

    // Get current membre info
    $membreModel = new Membre();
    $currentMembre = $membreModel->getMembre($id_oo);

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
    ?>

    <!-- Fix CSS path - correct path to match file location -->
    <link rel="stylesheet" href="/modules/planificator/modules/modules.css">

    <nav id="main-nav" class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 border rounded">
        <div class="container">
            <a class="navbar-brand" href="#">Système de Gestion Financière</a>
            <!-- Membre indicator -->
            <span class="navbar-text me-3 text-white">
                <?php if ($currentMembre): ?>
                <?php else: ?>
                    ID Membre: <?php echo $id_oo; ?>
                <?php endif; ?>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class=" content-card">
            <div class="card-header bg-light">
                <h2 class="card-title h4 my-2"><?php echo $modules[$action]; ?></h2>
            </div>
            <div class="card-body">
               <!--  <div class="row">
                    <div class="col-12">
                        
                    </div>
                </div> -->

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

    <?php
} else {
    header("location: /");
}
?>