<?php
// Database Migration Script
session_start();
$pageTitle = "Database Migration";

// Include database connection
require_once 'config/database.php';

// Function to run SQL files using the existing database connection
function executeSQLFile($conn, $file) {
    $success = true;
    $errorMessages = [];

    if (file_exists($file)) {
        $sql = file_get_contents($file);
        
        // Split SQL commands on semicolons
        $commands = explode(';', $sql);
        
        foreach ($commands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                try {
                    $conn->exec($command);
                } catch (PDOException $e) {
                    $success = false;
                    $errorMessages[] = "Error executing: " . htmlspecialchars($command) . " - " . $e->getMessage();
                }
            }
        }
    } else {
        $success = false;
        $errorMessages[] = "SQL file not found: " . $file;
    }
    
    return ['success' => $success, 'errors' => $errorMessages];
}

// Process migration if requested
$migrationDone = false;
$testDataDone = false;
$migrationErrors = [];

// Check database connection
try {
    $conn = getDbConnection();
    $dbExists = true;
} catch (PDOException $e) {
    // If the database doesn't exist yet, connect to MySQL without selecting a database
    try {
        $conn = new PDO(
            "mysql:host=" . FINANCIAL_DB_HOST,
            FINANCIAL_DB_USER,
            FINANCIAL_DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        $dbExists = false;
    } catch (PDOException $e2) {
        $migrationErrors[] = "Failed to connect to database server: " . $e2->getMessage();
        $conn = null;
    }
}

if (isset($_POST['migrate']) && $conn !== null) {
    // Create database if not exists - using plain SQL since we may not have the database yet
    $result = executeSQLFile($conn, 'sql/schema.sql');
    if (!$result['success']) {
        $migrationErrors = array_merge($migrationErrors, $result['errors']);
    }
    
    // Get a fresh connection that's connected to the financial_db
    if (!$dbExists) {
        $conn = getDbConnection();
    }
    
    // Execute SQL files in proper order
    $sqlFiles = [
        'sql/create_membres.sql',
        'sql/create_school_fee_children.sql',
        'sql/create_assets.sql',
        'sql/create_loans.sql',
        'sql/update_schema.sql'
    ];
    
    foreach ($sqlFiles as $file) {
        $result = executeSQLFile($conn, $file);
        if (!$result['success']) {
            $migrationErrors = array_merge($migrationErrors, $result['errors']);
        }
    }
    
    $migrationDone = true;
}

// Insert test data if requested
if (isset($_POST['test_data']) && $conn !== null) {
    // Get user ID parameter or use default
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 1;
    
    // Execute test data file with user ID parameter
    try {
        // Prepare and set user_id parameter
        $stmt = $conn->prepare("SET @user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = executeSQLFile($conn, 'sql/insert_test_data.sql');
        
        if (!$result['success']) {
            $migrationErrors = array_merge($migrationErrors, $result['errors']);
        } else {
            $testDataDone = true;
        }
    } catch (PDOException $e) {
        $migrationErrors[] = "Error setting user ID: " . $e->getMessage();
    }
}

// Check if tables exist
$tablesExist = false;
if ($conn !== null) {
    try {
        $result = $conn->query("SHOW TABLES");
        $tablesExist = ($result && $result->rowCount() > 0);
    } catch (PDOException $e) {
        // Database or tables might not exist yet
        $tablesExist = false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Database Migration Tool</h2>
            </div>
            <div class="card-body">
                <?php if ($migrationDone && empty($migrationErrors)): ?>
                    <div class="alert alert-success">
                        <h4>Migration Successful!</h4>
                        <p>All database tables have been created successfully.</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($testDataDone && empty($migrationErrors)): ?>
                    <div class="alert alert-success">
                        <h4>Test Data Inserted!</h4>
                        <p>Test data has been successfully inserted for user ID: <?php echo isset($_POST['user_id']) ? intval($_POST['user_id']) : 1; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($migrationErrors)): ?>
                    <div class="alert alert-danger">
                        <h4>Migration Errors</h4>
                        <ul>
                            <?php foreach ($migrationErrors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h3>Step 1: Create Database Structure</h3>
                    <p>This will create all necessary tables in the database.</p>
                    <form method="post" action="">
                        <button type="submit" name="migrate" class="btn btn-primary" <?php echo $tablesExist ? 'onclick="return confirm(\'Tables already exist. Running this may reset your data. Continue?\');"' : ''; ?>>
                            Create Database Structure
                        </button>
                    </form>
                </div>
                
                <div class="mb-4">
                    <h3>Step 2: Insert Test Data</h3>
                    <p>This will insert test data for a specific user.</p>
                    <form method="post" action="" class="row g-3">
                        <div class="col-auto">
                            <label for="user_id" class="visually-hidden">User ID</label>
                            <input type="number" class="form-control" id="user_id" name="user_id" value="1" min="1" placeholder="User ID">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="test_data" class="btn btn-success">
                                Insert Test Data
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mb-4">
                    <h3>Database Status</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Database Connection</td>
                                <td>
                                    <?php if ($conn): ?>
                                        <span class="badge bg-success">Connected</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Not Connected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Database "<?php echo FINANCIAL_DB_NAME; ?>"</td>
                                <td>
                                    <?php if ($dbExists): ?>
                                        <span class="badge bg-success">Exists</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Not Created</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Tables</td>
                                <td>
                                    <?php if ($tablesExist): ?>
                                        <span class="badge bg-success">Created</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Not Created</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Return to Application</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if ($conn !== null) {
    $conn = null; // Close the connection
}
?>
