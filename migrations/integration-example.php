<?php
/**
 * Integration Example - How to add migration tools to your existing IMS App
 */

// Add this to your existing admin panel or create a new admin page

require_once(__DIR__ . "/DatabaseExporter.php");
require_once(__DIR__ . "/BackupManager.php");

// Example: Add to your existing admin menu
function addMigrationToolsToAdminMenu() {
    echo '<li><a href="migrations/web-interface.php">Database Migration Tools</a></li>';
}

// Example: Add backup functionality to your existing backup system
function createScheduledBackup() {
    $backupManager = new BackupManager();
    return $backupManager->createBackup([
        'type' => 'full',
        'compression' => 'gzip',
        'description' => 'Scheduled backup from admin panel'
    ]);
}

// Example: Add export functionality to your existing export system
function exportForMigration() {
    $exporter = new DatabaseExporter();
    return $exporter->exportForMySQLVersion('5.7', [
        'compression' => 'gzip',
        'include_migrations' => true
    ]);
}

// Example: Add to your existing settings page
function showMigrationSettings() {
    echo '<h3>Database Migration Settings</h3>';
    echo '<p><a href="migrations/web-interface.php" class="btn btn-primary">Open Migration Tools</a></p>';
    echo '<p><a href="migrations/export-database.php" class="btn btn-secondary">Export Database</a></p>';
    echo '<p><a href="migrations/backup-database.php" class="btn btn-info">Create Backup</a></p>';
}
?>


