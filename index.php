<?php
// Entry point for the visualization module
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary configurations
require_once 'config/config.php';

// Define available modules and their titles
$modules = [
    'dashboard' => 'Dashboard',
    'income-expense' => 'Income & Expenses',
    'asset-management' => 'Asset Management',
    'loan-simulator' => 'Loan Simulator',
    'school-fee' => 'School Fees'
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Module - <?php echo $modules[$action]; ?></title>
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
            <a class="navbar-brand" href="#">Financial Management System</a>
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
                        echo '<div class="alert alert-info">Welcome to your financial dashboard. Here you can see an overview of your finances.</div>';
                        echo '<div class="row mt-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Income</h5>
                                            <h3 class="card-text">$5,250</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card text-white bg-danger">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Expenses</h5>
                                            <h3 class="card-text">$3,420</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card text-white bg-success">
                                        <div class="card-body">
                                            <h5 class="card-title">Net Balance</h5>
                                            <h3 class="card-text">$1,830</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        break;
                    case 'income-expense':
                        // Include the income-expense module
                        include 'modules/income-expense/index.php';
                        break;
                    case 'asset-management':
                        echo '<div class="alert alert-info">Manage your assets and view their performance.</div>';
                        break;
                    case 'loan-simulator':
                        echo '<div class="alert alert-info">Simulate different loan scenarios to make informed decisions.</div>';
                        break;
                    case 'school-fee':
                        echo '<div class="alert alert-info">Plan and track school fees and educational expenses.</div>';
                        break;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> Financial Management System
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>