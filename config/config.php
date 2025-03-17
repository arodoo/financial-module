<?php
// Configuration settings for the visualization module

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_USER', 'zenfamili');
define('DB_PASS', 'gc6Xc91@1');
define('DB_NAME', 'zenfamili');

// Global user ID - this would normally come from a login system
$id_oo = 1; 
define('CURRENT_USER_ID', $id_oo);

// Global membre ID - this will be dynamically set by the web server in production
$id_oo = 1; 

// Other configuration parameters
define('APP_NAME', 'Financial Visualization');
define('APP_VERSION', '1.0.0');
define('ASSET_PATH', '/financial/modules/visualization/assets/');
define('VIEW_PATH', '/financial/modules/visualization/views/');
define('CONTROLLER_PATH', '/financial/modules/visualization/controllers/');
define('MODEL_PATH', '/financial/modules/visualization/models/');
define('SERVICE_PATH', '/financial/modules/visualization/services/');
?>