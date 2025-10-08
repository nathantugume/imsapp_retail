<?php
require_once(__DIR__ . "/../init/init.php");

// Check if user is logged in and is Master
if(!isset($_SESSION['LOGGEDIN']) || $_SESSION['LOGGEDIN']['role'] !== 'Master'){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once("DatabaseMigration.php");

header('Content-Type: application/json');

try {
    $migration = new DatabaseMigration();
    
    if (isset($_POST['migration_id'])) {
        $migrationId = $_POST['migration_id'];
        
        if ($migrationId === 'all') {
            $result = $migration->runAllMigrations();
        } else {
            $result = $migration->runMigration($migrationId);
        }
        
        echo json_encode($result);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Migration ID not provided'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
