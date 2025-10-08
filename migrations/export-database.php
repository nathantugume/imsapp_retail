#!/usr/bin/env php
<?php
/**
 * Database Export Script for IMS App
 * 
 * This script provides command-line interface for exporting the database
 * with various compatibility options for easy migration to other MySQL instances.
 * 
 * Usage:
 *   php export-database.php [options]
 * 
 * Options:
 *   --type=full|structure|data     Export type (default: full)
 *   --compatibility=mysql5.7|mysql8.0|mariadb10.3|mariadb10.4|mariadb10.5
 *   --compression=none|gzip|bzip2  Compression type (default: gzip)
 *   --output=path                  Output directory (default: ./exports/)
 *   --no-data                      Skip data export (structure only)
 *   --no-structure                 Skip structure export (data only)
 *   --no-migrations                Skip migrations table
 *   --help                         Show this help message
 *   --list                         List available export types and compatibility modes
 *   --history                      Show export history
 *   --clean=days                   Clean exports older than specified days
 */

require_once(__DIR__ . "/DatabaseExporter.php");

class DatabaseExportCLI {
    private $exporter;
    private $options;
    
    public function __construct() {
        $this->exporter = new DatabaseExporter();
        $this->parseArguments();
    }
    
    public function run() {
        try {
            if (isset($this->options['help'])) {
                $this->showHelp();
                return;
            }
            
            if (isset($this->options['list'])) {
                $this->showAvailableOptions();
                return;
            }
            
            if (isset($this->options['history'])) {
                $this->showHistory();
                return;
            }
            
            if (isset($this->options['clean'])) {
                $this->cleanExports();
                return;
            }
            
            $this->performExport();
            
        } catch (Exception $e) {
            $this->error("Export failed: " . $e->getMessage());
            exit(1);
        }
    }
    
    private function parseArguments() {
        $this->options = [
            'type' => 'full',
            'compatibility' => 'mysql5.7',
            'compression' => 'gzip',
            'output' => null,
            'include_data' => true,
            'include_structure' => true,
            'include_migrations' => true
        ];
        
        $args = getopt('', [
            'type:',
            'compatibility:',
            'compression:',
            'output:',
            'no-data',
            'no-structure',
            'no-migrations',
            'help',
            'list',
            'history',
            'clean:'
        ]);
        
        foreach ($args as $key => $value) {
            switch ($key) {
                case 'type':
                    $this->options['type'] = $value;
                    break;
                case 'compatibility':
                    $this->options['compatibility'] = $value;
                    break;
                case 'compression':
                    $this->options['compression'] = $value;
                    break;
                case 'output':
                    $this->options['output'] = $value;
                    break;
                case 'no-data':
                    $this->options['include_data'] = false;
                    break;
                case 'no-structure':
                    $this->options['include_structure'] = false;
                    break;
                case 'no-migrations':
                    $this->options['include_migrations'] = false;
                    break;
                case 'help':
                case 'list':
                case 'history':
                case 'clean':
                    $this->options[$key] = $value;
                    break;
            }
        }
    }
    
    private function performExport() {
        $this->info("Starting database export...");
        $this->info("Type: " . $this->options['type']);
        $this->info("Compatibility: " . $this->options['compatibility']);
        $this->info("Compression: " . $this->options['compression']);
        
        // Set output directory if specified
        if ($this->options['output']) {
            $this->exporter = new DatabaseExporter($this->options['output']);
        }
        
        // Prepare export options
        $exportOptions = [
            'compatibility_mode' => $this->options['compatibility'],
            'compression' => $this->options['compression'],
            'include_data' => $this->options['include_data'],
            'include_structure' => $this->options['include_structure'],
            'include_migrations' => $this->options['include_migrations']
        ];
        
        // Perform export based on type
        switch ($this->options['type']) {
            case 'structure':
                $result = $this->exporter->exportStructure($exportOptions);
                break;
            case 'data':
                $result = $this->exporter->exportData($exportOptions);
                break;
            case 'full':
            default:
                $result = $this->exporter->exportFullDatabase($exportOptions);
                break;
        }
        
        if ($result['success']) {
            $this->success("Export completed successfully!");
            $this->info("File: " . $result['filename']);
            $this->info("Size: " . $this->formatBytes($result['size']));
            $this->info("Path: " . $result['filepath']);
            
            if ($result['options']['compression'] !== 'none') {
                $this->info("Compression: " . $result['options']['compression']);
            }
        } else {
            $this->error("Export failed: " . $result['error']);
            exit(1);
        }
    }
    
    private function showHelp() {
        echo "IMS App Database Export Tool\n";
        echo "============================\n\n";
        echo "Usage: php export-database.php [options]\n\n";
        echo "Options:\n";
        echo "  --type=full|structure|data     Export type (default: full)\n";
        echo "  --compatibility=mysql5.7|mysql8.0|mariadb10.3|mariadb10.4|mariadb10.5\n";
        echo "  --compression=none|gzip|bzip2  Compression type (default: gzip)\n";
        echo "  --output=path                  Output directory (default: ./exports/)\n";
        echo "  --no-data                      Skip data export (structure only)\n";
        echo "  --no-structure                 Skip structure export (data only)\n";
        echo "  --no-migrations                Skip migrations table\n";
        echo "  --help                         Show this help message\n";
        echo "  --list                         List available export types and compatibility modes\n";
        echo "  --history                      Show export history\n";
        echo "  --clean=days                   Clean exports older than specified days\n\n";
        echo "Examples:\n";
        echo "  php export-database.php --type=full --compatibility=mysql5.7\n";
        echo "  php export-database.php --type=structure --compression=none\n";
        echo "  php export-database.php --no-data --output=/backup/exports/\n";
        echo "  php export-database.php --history\n";
        echo "  php export-database.php --clean=30\n\n";
    }
    
    private function showAvailableOptions() {
        echo "Available Export Types:\n";
        echo "  full       - Complete database export (structure + data)\n";
        echo "  structure  - Database structure only (no data)\n";
        echo "  data       - Database data only (no structure)\n\n";
        
        echo "Available Compatibility Modes:\n";
        echo "  mysql5.7     - MySQL 5.7 compatibility\n";
        echo "  mysql8.0     - MySQL 8.0 compatibility\n";
        echo "  mariadb10.3  - MariaDB 10.3 compatibility\n";
        echo "  mariadb10.4  - MariaDB 10.4 compatibility\n";
        echo "  mariadb10.5  - MariaDB 10.5 compatibility\n\n";
        
        echo "Available Compression Types:\n";
        echo "  none   - No compression\n";
        echo "  gzip   - GZIP compression (default)\n";
        echo "  bzip2  - BZIP2 compression\n\n";
    }
    
    private function showHistory() {
        $history = $this->exporter->getExportHistory();
        
        if (empty($history)) {
            echo "No export history found.\n";
            return;
        }
        
        echo "Export History:\n";
        echo "===============\n\n";
        
        printf("%-30s %-12s %-20s %s\n", "Filename", "Size", "Created", "Compressed");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($history as $export) {
            printf("%-30s %-12s %-20s %s\n",
                $export['filename'],
                $this->formatBytes($export['size']),
                $export['created'],
                $export['compressed'] ? 'Yes' : 'No'
            );
        }
        
        echo "\n";
    }
    
    private function cleanExports() {
        $days = $this->options['clean'] ?: 30;
        $deleted = $this->exporter->cleanOldExports($days);
        
        if ($deleted > 0) {
            $this->success("Cleaned $deleted old export files (older than $days days)");
        } else {
            $this->info("No old export files found to clean");
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
        echo "[INFO] $message\n";
    }
    
    private function success($message) {
        echo "[SUCCESS] $message\n";
    }
    
    private function error($message) {
        echo "[ERROR] $message\n";
    }
}

// Run the CLI if called directly
if (php_sapi_name() === 'cli') {
    $cli = new DatabaseExportCLI();
    $cli->run();
}
?>


