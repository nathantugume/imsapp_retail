<?php
/**
 * Programmatic Access Example
 * 
 * This shows how to use the migration tools directly in your PHP code
 */

require_once(__DIR__ . "/DatabaseExporter.php");
require_once(__DIR__ . "/DatabaseImporter.php");
require_once(__DIR__ . "/BackupManager.php");

echo "=== Programmatic Access Examples ===\n\n";

try {
    // Example 1: Export database programmatically
    echo "1. Exporting database...\n";
    $exporter = new DatabaseExporter();
    $exportResult = $exporter->exportFullDatabase([
        'compatibility_mode' => 'mysql5.7',
        'compression' => 'gzip',
        'include_migrations' => true
    ]);
    
    if ($exportResult['success']) {
        echo "✓ Export successful: " . $exportResult['filename'] . "\n";
        echo "  Size: " . formatBytes($exportResult['size']) . "\n";
    } else {
        echo "✗ Export failed: " . $exportResult['error'] . "\n";
    }
    
    echo "\n";
    
    // Example 2: Create backup programmatically
    echo "2. Creating backup...\n";
    $backupManager = new BackupManager();
    $backupResult = $backupManager->createBackup([
        'type' => 'full',
        'compression' => 'gzip',
        'description' => 'Programmatic backup example',
        'retention_days' => 30
    ]);
    
    if ($backupResult['success']) {
        echo "✓ Backup created: " . $backupResult['filename'] . "\n";
        echo "  Version: " . $backupResult['version'] . "\n";
        echo "  Size: " . formatBytes($backupResult['size']) . "\n";
    } else {
        echo "✗ Backup failed: " . $backupResult['error'] . "\n";
    }
    
    echo "\n";
    
    // Example 3: Get history programmatically
    echo "3. Getting history...\n";
    $exportHistory = $exporter->getExportHistory();
    $backupHistory = $backupManager->getBackupHistory();
    
    echo "Recent exports: " . count($exportHistory) . "\n";
    echo "Recent backups: " . count($backupHistory) . "\n";
    
    echo "\n";
    
    // Example 4: Import database programmatically (dry run)
    echo "4. Testing import (dry run)...\n";
    if (!empty($exportHistory)) {
        $latestExport = $exportHistory[0];
        $importer = new DatabaseImporter();
        
        $importResult = $importer->importDatabase($latestExport['filepath'], [
            'dry_run' => true,
            'check_compatibility' => true
        ]);
        
        if ($importResult['success']) {
            echo "✓ Import test successful (dry run)\n";
            if (isset($importResult['stats'])) {
                echo "  Statements: " . $importResult['stats']['total_statements'] . "\n";
            }
        } else {
            echo "✗ Import test failed: " . $importResult['error'] . "\n";
        }
    } else {
        echo "No exports available for import test\n";
    }
    
    echo "\n=== Examples completed successfully! ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>


