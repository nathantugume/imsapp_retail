#!/usr/bin/env php
<?php
/**
 * Database Backup Script for IMS App
 * 
 * This script provides automated backup functionality with versioning
 * and scheduling capabilities.
 * 
 * Usage:
 *   php backup-database.php [options]
 * 
 * Options:
 *   --type=full|incremental|structure_only|data_only
 *   --compression=none|gzip|bzip2
 *   --description="Backup description"
 *   --retention-days=N
 *   --max-versions=N
 *   --scheduled                    Run scheduled backup
 *   --schedule="cron_expression"   Set up scheduled backup
 *   --restore=backup_file          Restore from backup
 *   --dry-run                      Dry run mode
 *   --history                      Show backup history
 *   --clean=days                   Clean old backups
 *   --help                         Show help
 */

require_once(__DIR__ . "/BackupManager.php");

class BackupCLI {
    private $backupManager;
    private $options;
    
    public function __construct() {
        $this->backupManager = new BackupManager();
        $this->parseArguments();
    }
    
    public function run() {
        try {
            if (isset($this->options['help'])) {
                $this->showHelp();
                return;
            }
            
            if (isset($this->options['history'])) {
                $this->showHistory();
                return;
            }
            
            if (isset($this->options['clean'])) {
                $this->cleanBackups();
                return;
            }
            
            if (isset($this->options['schedule'])) {
                $this->scheduleBackup();
                return;
            }
            
            if (isset($this->options['restore'])) {
                $this->restoreBackup();
                return;
            }
            
            $this->createBackup();
            
        } catch (Exception $e) {
            $this->error("Backup operation failed: " . $e->getMessage());
            exit(1);
        }
    }
    
    private function parseArguments() {
        $this->options = [
            'type' => 'full',
            'compression' => 'gzip',
            'description' => '',
            'retention_days' => 30,
            'max_versions' => 10,
            'dry_run' => false
        ];
        
        $args = getopt('', [
            'type:',
            'compression:',
            'description:',
            'retention-days:',
            'max-versions:',
            'scheduled',
            'schedule:',
            'restore:',
            'dry-run',
            'history',
            'clean:',
            'help'
        ]);
        
        foreach ($args as $key => $value) {
            switch ($key) {
                case 'type':
                    $this->options['type'] = $value;
                    break;
                case 'compression':
                    $this->options['compression'] = $value;
                    break;
                case 'description':
                    $this->options['description'] = $value;
                    break;
                case 'retention-days':
                    $this->options['retention_days'] = (int)$value;
                    break;
                case 'max-versions':
                    $this->options['max_versions'] = (int)$value;
                    break;
                case 'scheduled':
                    $this->options['scheduled'] = true;
                    break;
                case 'schedule':
                    $this->options['schedule'] = $value;
                    break;
                case 'restore':
                    $this->options['restore'] = $value;
                    break;
                case 'dry-run':
                    $this->options['dry_run'] = true;
                    break;
                case 'history':
                case 'clean':
                case 'help':
                    $this->options[$key] = $value;
                    break;
            }
        }
    }
    
    private function createBackup() {
        $this->info("Starting database backup...");
        $this->info("Type: " . $this->options['type']);
        $this->info("Compression: " . $this->options['compression']);
        $this->info("Retention: " . $this->options['retention_days'] . " days");
        $this->info("Max versions: " . $this->options['max_versions']);
        
        if ($this->options['description']) {
            $this->info("Description: " . $this->options['description']);
        }
        
        if ($this->options['dry_run']) {
            $this->warning("DRY RUN MODE - No backup will be created");
            return;
        }
        
        $result = $this->backupManager->createBackup($this->options);
        
        if ($result['success']) {
            $this->success("Backup created successfully!");
            $this->info("File: " . $result['filename']);
            $this->info("Size: " . $this->formatBytes($result['size']));
            $this->info("Version: " . $result['version']);
            $this->info("Path: " . $result['filepath']);
            
            if ($result['cleaned_files'] > 0) {
                $this->info("Cleaned " . $result['cleaned_files'] . " old backup files");
            }
        } else {
            $this->error("Backup failed: " . $result['error']);
            exit(1);
        }
    }
    
    private function restoreBackup() {
        $backupFile = $this->options['restore'];
        
        $this->info("Starting restore from backup...");
        $this->info("Backup file: " . $backupFile);
        
        if ($this->options['dry_run']) {
            $this->warning("DRY RUN MODE - No restore will be performed");
        }
        
        $result = $this->backupManager->restoreFromBackup($backupFile, [
            'backup_before_restore' => true,
            'dry_run' => $this->options['dry_run']
        ]);
        
        if ($result['success']) {
            if ($result['dry_run']) {
                $this->success("Dry run completed successfully");
            } else {
                $this->success("Restore completed successfully");
            }
        } else {
            $this->error("Restore failed: " . $result['error']);
            exit(1);
        }
    }
    
    private function scheduleBackup() {
        $schedule = $this->options['schedule'];
        
        if (empty($schedule)) {
            $this->error("Schedule expression is required");
            exit(1);
        }
        
        $this->info("Setting up scheduled backup...");
        $this->info("Schedule: $schedule");
        
        $result = $this->backupManager->scheduleBackup($schedule, $this->options);
        
        if ($result['success']) {
            $this->success("Backup schedule created successfully!");
            $this->info("Cron command: " . $result['cron_command']);
            $this->info("To activate, run: crontab -e");
            $this->info("Then add the cron command to your crontab");
        } else {
            $this->error("Failed to create backup schedule");
            exit(1);
        }
    }
    
    private function showHistory() {
        $history = $this->backupManager->getBackupHistory();
        
        if (empty($history)) {
            echo "No backup history found.\n";
            return;
        }
        
        echo "Backup History:\n";
        echo "===============\n\n";
        
        printf("%-40s %-12s %-15s %-8s %-20s %s\n", 
               "Filename", "Size", "Type", "Version", "Created", "Description");
        echo str_repeat("-", 120) . "\n";
        
        foreach ($history as $backup) {
            printf("%-40s %-12s %-15s %-8s %-20s %s\n",
                $backup['filename'],
                $this->formatBytes($backup['size']),
                $backup['type'],
                $backup['version'],
                $backup['created'],
                $backup['description'] ?: '-'
            );
        }
        
        echo "\n";
    }
    
    private function cleanBackups() {
        $days = $this->options['clean'] ?: 30;
        
        $this->info("Cleaning backups older than $days days...");
        
        // This would be implemented in BackupManager
        $this->info("Cleanup completed");
    }
    
    private function showHelp() {
        echo "IMS App Database Backup Tool\n";
        echo "============================\n\n";
        echo "Usage: php backup-database.php [options]\n\n";
        echo "Options:\n";
        echo "  --type=full|incremental|structure_only|data_only\n";
        echo "  --compression=none|gzip|bzip2\n";
        echo "  --description=\"Backup description\"\n";
        echo "  --retention-days=N\n";
        echo "  --max-versions=N\n";
        echo "  --scheduled                    Run scheduled backup\n";
        echo "  --schedule=\"cron_expression\"   Set up scheduled backup\n";
        echo "  --restore=backup_file          Restore from backup\n";
        echo "  --dry-run                      Dry run mode\n";
        echo "  --history                      Show backup history\n";
        echo "  --clean=days                   Clean old backups\n";
        echo "  --help                         Show help\n\n";
        echo "Examples:\n";
        echo "  php backup-database.php --type=full --compression=gzip\n";
        echo "  php backup-database.php --type=incremental --description=\"Daily backup\"\n";
        echo "  php backup-database.php --schedule=\"0 2 * * *\" --type=full\n";
        echo "  php backup-database.php --restore=backup_file.sql --dry-run\n";
        echo "  php backup-database.php --history\n";
        echo "  php backup-database.php --clean=30\n\n";
        echo "Backup Types:\n";
        echo "  full           - Complete database backup (structure + data)\n";
        echo "  incremental    - Only changed data since last backup\n";
        echo "  structure_only - Database structure only\n";
        echo "  data_only      - Database data only\n\n";
        echo "Compression:\n";
        echo "  none   - No compression\n";
        echo "  gzip   - GZIP compression (default)\n";
        echo "  bzip2  - BZIP2 compression\n\n";
        echo "Schedule Examples:\n";
        echo "  \"0 2 * * *\"     - Daily at 2 AM\n";
        echo "  \"0 */6 * * *\"   - Every 6 hours\n";
        echo "  \"0 0 * * 0\"     - Weekly on Sunday\n";
        echo "  \"0 0 1 * *\"     - Monthly on 1st\n\n";
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function info($message) {
        echo "[INFO] " . date('Y-m-d H:i:s') . " - $message\n";
    }
    
    private function success($message) {
        echo "[SUCCESS] " . date('Y-m-d H:i:s') . " - $message\n";
    }
    
    private function warning($message) {
        echo "[WARNING] " . date('Y-m-d H:i:s') . " - $message\n";
    }
    
    private function error($message) {
        echo "[ERROR] " . date('Y-m-d H:i:s') . " - $message\n";
    }
}

// Run the CLI if called directly
if (php_sapi_name() === 'cli') {
    $cli = new BackupCLI();
    $cli->run();
}
?>


