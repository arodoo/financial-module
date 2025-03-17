<?php
require_once 'config/database.php';

echo "<h1>Creating Membres Table</h1>";

try {
    $conn = getDbConnection();
    
    echo "<p>Connected to database successfully.</p>";
    
    // Read the SQL file
    $sqlFile = file_get_contents('sql/create_membres.sql');
    
    // Split into separate statements
    $statements = array_filter(
        array_map('trim', 
            explode(';', $sqlFile)
        )
    );
    
    // Execute each statement
    $successCount = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "<p>Executed: " . htmlspecialchars(substr($statement, 0, 100) . '...') . "</p>";
            $successCount++;
        }
    }
    
    echo "<h2>Membres table created successfully!</h2>";
    echo "<p>Executed $successCount statements.</p>";
    echo "<p><a href='index.php' class='btn btn-primary'>Return to application</a></p>";
    
} catch (PDOException $e) {
    die("<h2>Database Error:</h2><p>" . $e->getMessage() . "</p>");
}
