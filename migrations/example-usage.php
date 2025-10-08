<?php
/**
 * Example Usage Script for IMS App Database Migration Tools
 * 
 * This script demonstrates how to use the migration tools programmatically
 * and provides examples for common migration scenarios.
 */

require_once(__DIR__ . "/DatabaseExporter.php");
require_once(__DIR__ . "/DatabaseImporter.php");
require_once(__DIR__ . "/BackupManager.php");

echo "IMS App Database Migration Tools - Example Usage\n";
echo "================================================\n\n";

try {
    // Example 1: Export database with different compatibility modes
    echo "Example 1: Export Database with Compatibility Options\n";
    echo "----------------------------------------------------\n";
    
    $exporter = new DatabaseExporter();
    
    // Export for MySQL 5.7 compatibility
    echo "Exporting for MySQL 5.7 compatibility...\n";
    $result = $exporter->exportForMySQLVersion('5.7', [
        'compression' => 'gzip',
        'include_migrations' => true
    ]);
    
    if ($result['success']) {
        echo "✓ Export successful: " . $result['filename'] . "\n";
        echo "  Size: " . formatBytes($result['size']) . "\n";
    } else {
        echo "✗ Export failed: " . $result['error'] . "\n";
    }
    
    echo "\n";
    
    // Example 2: Create automated backup
    echo "Example 2: Create Automated Backup\n";
    echo "----------------------------------\n";
    
    $backupManager = new BackupManager();
    
    echo "Creating full backup with versioning...\n";
    $backupResult = $backupManager->createBackup([
        'type' => 'full',
        'compression' => 'gzip',
        'description' => 'Example backup',
        'retention_days' => 30,
        'max_versions' => 5
    ]);
    
    if ($backupResult['success']) {
        echo "✓ Backup created: " . $backupResult['filename'] . "\n";
        echo "  Version: " . $backupResult['version'] . "\n";
        echo "  Size: " . formatBytes($backupResult['size']) . "\n";
    } else {
        echo "✗ Backup failed: " . $backupResult['error'] . "\n";
    }
    
    echo "\n";
    
    // Example 3: Show export history
    echo "Example 3: Export History\n";
    echo "-------------------------\n";
    
    $history = $exporter->getExportHistory();
    if (!empty($history)) {
        echo "Recent exports:\n";
        foreach (array_slice($history, 0, 3) as $export) {
            echo "  - " . $export['filename'] . " (" . formatBytes($export['size']) . ") - " . $export['created'] . "\n";
        }
    } else {
        echo "No export history found.\n";
    }
    
    echo "\n";
    
    // Example 4: Backup history
    echo "Example 4: Backup History\n";
    echo "-------------------------\n";
    
    $backupHistory = $backupManager->getBackupHistory();
    if (!empty($backupHistory)) {
        echo "Recent backups:\n";
        foreach (array_slice($backupHistory, 0, 3) as $backup) {
            echo "  - " . $backup['filename'] . " (" . $backup['type'] . ", v" . $backup['version'] . ") - " . $backup['created'] . "\n";
        }
    } else {
        echo "No backup history found.\n";
    }
    
    echo "\n";
    
    // Example 5: Compatibility check (simulated)
    echo "Example 5: Compatibility Information\n";
    echo "------------------------------------\n";
    
    echo "Supported compatibility modes:\n";
    echo "  - MySQL 5.7: Full compatibility\n";
    echo "  - MySQL 8.0: Full compatibility\n";
    echo "  - MariaDB 10.3: Full compatibility\n";
    echo "  - MariaDB 10.4: Full compatibility\n";
    echo "  - MariaDB 10.5: Full compatibility\n";
    
    echo "\n";
    
    // Example 6: Command-line usage examples
    echo "Example 6: Command-Line Usage Examples\n";
    echo "--------------------------------------\n";
    
    echo "Export commands:\n";
    echo "  php export-database.php --type=full --compatibility=mysql5.7\n";
    echo "  php export-database.php --type=structure --compression=none\n";
    echo "  php export-database.php --type=data --compression=gzip\n";
    
    echo "\nImport commands:\n";
    echo "  php import-database.php backup.sql --dry-run\n";
    echo "  php import-database.php backup.sql --backup-before-import\n";
    echo "  php import-database.php backup.sql --skip-errors\n";
    
    echo "\nBackup commands:\n";
    echo "  php backup-database.php --type=full --compression=gzip\n";
    echo "  php backup-database.php --type=incremental --description=\"Daily backup\"\n";
    echo "  php backup-database.php --schedule=\"0 2 * * *\" --type=full\n";
    
    echo "\n";
    
    echo "✓ All examples completed successfully!\n";
    echo "\nFor more information, see the README.md file.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>


