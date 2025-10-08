#!/usr/bin/env php
<?php
/**
 * Database Import Script for IMS App
 * 
 * This script provides command-line interface for importing database
 * with compatibility checks and safety features.
 * 
 * Usage:
 *   php import-database.php <sql_file> [options]
 * 
 * Options:
 *   --dry-run                    Perform dry run (validation only)
 *   --no-backup                  Skip backup creation before import
 *   --skip-errors                Continue import even if errors occur
 *   --no-compatibility-check     Skip compatibility checks
 *   --create-database            Create database if it doesn't exist
 *   --drop-existing              Drop existing tables before import
 *   --preserve-migrations        Preserve existing migrations table
 *   --chunk-size=N               Process N statements at a time (default: 1000)
 *   --timeout=N                  Set timeout in seconds (default: 300)
 *   --help                       Show this help message
 *   --history                    Show import history
 *   --clean=days                 Clean backups older than specified days
 */

require_once(__DIR__ . "/DatabaseImporter.php");

class DatabaseImportCLI {
    private $importer;
    private $options;
    private $sqlFile;
    
    public function __construct() {
        $this->importer = new DatabaseImporter();
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
            
            if (empty($this->sqlFile)) {
                $this->error("SQL file is required");
                $this->showHelp();
                exit(1);
            }
            
            $this->performImport();
            
        } catch (Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            exit(1);
        }
    }
    
    private function parseArguments() {
        $this->options = [
            'backup_before_import' => true,
            'check_compatibility' => true,
            'skip_errors' => false,
            'dry_run' => false,
            'create_database' => false,
            'drop_existing' => false,
            'preserve_migrations' => true,
            'chunk_size' => 1000,
            'timeout' => 300
        ];
        
        // Get SQL file from command line arguments first
        global $argv;
        if (isset($argv[1]) && strpos($argv[1], '--') !== 0) {
            $this->sqlFile = $argv[1];
        }
        
        // Manual argument parsing
        $args = [];
        for ($i = 2; $i < count($argv); $i++) {
            if (strpos($argv[$i], '--') === 0) {
                $arg = substr($argv[$i], 2);
                if (strpos($arg, '=') !== false) {
                    list($key, $value) = explode('=', $arg, 2);
                    $args[$key] = $value;
                } else {
                    $args[$arg] = true;
                }
            }
        }
        
        // Debug: Print all arguments (remove in production)
        // echo "DEBUG: All arguments: " . print_r($args, true) . "\n";
        
        foreach ($args as $key => $value) {
            switch ($key) {
                case 'dry-run':
                    $this->options['dry_run'] = true;
                    break;
                case 'no-backup':
                    $this->options['backup_before_import'] = false;
                    break;
                case 'skip-errors':
                    $this->options['skip_errors'] = true;
                    break;
                case 'no-compatibility-check':
                    $this->options['check_compatibility'] = false;
                    break;
                case 'create-database':
                    $this->options['create_database'] = true;
                    break;
                case 'drop-existing':
                    $this->options['drop_existing'] = true;
                    break;
                case 'preserve-migrations':
                    $this->options['preserve_migrations'] = true;
                    break;
                case 'chunk-size':
                    $this->options['chunk_size'] = (int)$value;
                    break;
                case 'timeout':
                    $this->options['timeout'] = (int)$value;
                    break;
                case 'help':
                case 'history':
                case 'clean':
                    $this->options[$key] = $value;
                    break;
            }
        }
    }
    
    private function performImport() {
        $this->info("Starting database import...");
        $this->info("SQL file: " . $this->sqlFile);
        $this->info("Dry run: " . ($this->options['dry_run'] ? 'Yes' : 'No'));
        $this->info("Backup before import: " . ($this->options['backup_before_import'] ? 'Yes' : 'No'));
        $this->info("Compatibility check: " . ($this->options['check_compatibility'] ? 'Yes' : 'No'));
        $this->info("Skip errors: " . ($this->options['skip_errors'] ? 'Yes' : 'No'));
        
        if ($this->options['dry_run']) {
            $this->warning("DRY RUN MODE - No changes will be made to the database");
        }
        
        $result = $this->importer->importDatabase($this->sqlFile, $this->options);
        
        if ($result['success']) {
            $this->success("Import completed successfully!");
            
            if (isset($result['stats'])) {
                $stats = $result['stats'];
                $this->info("Total statements: " . $stats['total_statements']);
                
                if (isset($stats['successful_statements'])) {
                    $this->info("Successful statements: " . $stats['successful_statements']);
                }
                
                if (isset($stats['failed_statements']) && $stats['failed_statements'] > 0) {
                    $this->warning("Failed statements: " . $stats['failed_statements']);
                }
            }
            
            if (isset($result['duration'])) {
                $this->info("Duration: " . $result['duration'] . " seconds");
            }
            
            if (isset($result['message'])) {
                $this->info($result['message']);
            }
            
        } else {
            $this->error("Import failed: " . $result['error']);
            exit(1);
        }
    }
    
    private function showHelp() {
        echo "IMS App Database Import Tool\n";
        echo "============================\n\n";
        echo "Usage: php import-database.php <sql_file> [options]\n\n";
        echo "Options:\n";
        echo "  --dry-run                    Perform dry run (validation only)\n";
        echo "  --no-backup                  Skip backup creation before import\n";
        echo "  --skip-errors                Continue import even if errors occur\n";
        echo "  --no-compatibility-check     Skip compatibility checks\n";
        echo "  --create-database            Create database if it doesn't exist\n";
        echo "  --drop-existing              Drop existing tables before import\n";
        echo "  --preserve-migrations        Preserve existing migrations table\n";
        echo "  --chunk-size=N               Process N statements at a time (default: 1000)\n";
        echo "  --timeout=N                  Set timeout in seconds (default: 300)\n";
        echo "  --help                       Show this help message\n";
        echo "  --history                    Show import history\n";
        echo "  --clean=days                 Clean backups older than specified days\n\n";
        echo "Examples:\n";
        echo "  php import-database.php backup.sql --dry-run\n";
        echo "  php import-database.php backup.sql --no-backup\n";
        echo "  php import-database.php backup.sql --skip-errors\n";
        echo "  php import-database.php backup.sql --create-database --drop-existing\n";
        echo "  php import-database.php --history\n";
        echo "  php import-database.php --clean=30\n\n";
        echo "Supported file formats:\n";
        echo "  .sql    - Plain SQL file\n";
        echo "  .sql.gz - GZIP compressed SQL file\n";
        echo "  .sql.bz2 - BZIP2 compressed SQL file\n\n";
    }
    
    private function showHistory() {
        $history = $this->importer->getImportHistory();
        
        if (empty($history)) {
            echo "No import history found.\n";
            return;
        }
        
        echo "Import History (Backups):\n";
        echo "========================\n\n";
        
        printf("%-40s %-12s %-20s %s\n", "Filename", "Size", "Created", "Compressed");
        echo str_repeat("-", 90) . "\n";
        
        foreach ($history as $backup) {
            printf("%-40s %-12s %-20s %s\n",
                $backup['filename'],
                $this->formatBytes($backup['size']),
                $backup['created'],
                $backup['compressed'] ? 'Yes' : 'No'
            );
        }
        
        echo "\n";
    }
    
    private function cleanBackups() {
        $days = $this->options['clean'] ?: 30;
        $deleted = $this->importer->cleanOldBackups($days);
        
        if ($deleted > 0) {
            $this->success("Cleaned $deleted old backup files (older than $days days)");
        } else {
            $this->info("No old backup files found to clean");
        }
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
    $cli = new DatabaseImportCLI();
    $cli->run();
}
?>
