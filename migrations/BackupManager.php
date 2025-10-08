<?php
require_once(__DIR__ . "/../init/init.php");
require_once(__DIR__ . "/DatabaseExporter.php");

class BackupManager {
    private $dbcon;
    private $config;
    private $backupPath;
    private $retentionDays;
    private $suppressOutput;
    
    public function __construct($backupPath = null, $retentionDays = 30, $suppressOutput = false) {
        $this->dbcon = getDB();
        $this->config = new Config();
        $this->backupPath = $backupPath ?: __DIR__ . '/backups/';
        $this->retentionDays = $retentionDays;
        $this->suppressOutput = $suppressOutput;
        
        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }
    
    /**
     * Create automated backup with versioning
     */
    public function createBackup($options = []) {
        $defaultOptions = [
            'type' => 'full', // full, incremental, structure_only, data_only
            'compression' => 'gzip', // none, gzip, bzip2
            'include_migrations' => true,
            'versioning' => true,
            'description' => '',
            'retention_days' => $this->retentionDays,
            'max_versions' => 10
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $version = $this->getNextVersion($options['type']);
            $filename = "imsapp_backup_{$options['type']}_v{$version}_{$timestamp}.sql";
            $filepath = $this->backupPath . $filename;
            
            $this->info("Creating {$options['type']} backup (version $version)...");
            
            // Create backup based on type
            switch ($options['type']) {
                case 'full':
                    $result = $this->createFullBackup($filepath, $options);
                    break;
                case 'incremental':
                    $result = $this->createIncrementalBackup($filepath, $options);
                    break;
                case 'structure_only':
                    $result = $this->createStructureBackup($filepath, $options);
                    break;
                case 'data_only':
                    $result = $this->createDataBackup($filepath, $options);
                    break;
                default:
                    throw new Exception("Invalid backup type: " . $options['type']);
            }
            
            if ($result['success']) {
                // Apply compression if requested
                if ($options['compression'] !== 'none') {
                    $filepath = $this->compressFile($filepath, $options['compression']);
                    $filename = basename($filepath);
                }
                
                // Record backup metadata
                $this->recordBackupMetadata($filename, $options, $result);
                
                // Clean old backups
                $cleaned = $this->cleanOldBackups($options['retention_days'], $options['max_versions']);
                
                $this->success("Backup created successfully: $filename");
                if ($cleaned > 0) {
                    $this->info("Cleaned $cleaned old backup files");
                }
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                    'version' => $version,
                    'type' => $options['type'],
                    'compression' => $options['compression'],
                    'cleaned_files' => $cleaned
                ];
            } else {
                throw new Exception($result['error']);
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create full backup
     */
    private function createFullBackup($filepath, $options) {
        $exporter = new DatabaseExporter($this->backupPath);
        $result = $exporter->exportFullDatabase([
            'include_data' => true,
            'include_structure' => true,
            'include_migrations' => $options['include_migrations'],
            'compression' => 'none', // We'll handle compression separately
            'compatibility_mode' => 'mysql5.7'
        ]);
        
        if ($result['success']) {
            // Move the file to the expected location
            $sourceFile = $result['filepath'];
            if (file_exists($sourceFile) && $sourceFile !== $filepath) {
                rename($sourceFile, $filepath);
                $result['filepath'] = $filepath;
            }
        }
        
        return $result;
    }
    
    /**
     * Create incremental backup (only changed data since last backup)
     */
    private function createIncrementalBackup($filepath, $options) {
        $pdo = $this->dbcon->connect();
        $lastBackup = $this->getLastBackupTime();
        
        $export = $this->generateBackupHeader($options);
        $export .= "-- Incremental backup since: " . ($lastBackup ?: 'beginning') . "\n\n";
        
        // Get all tables with timestamps
        $tables = $this->getTablesWithTimestamps($pdo);
        
        foreach ($tables as $table) {
            $export .= $this->exportIncrementalTableData($pdo, $table, $lastBackup);
        }
        
        $export .= $this->generateBackupFooter();
        
        file_put_contents($filepath, $export);
        
        return [
            'success' => true,
            'size' => filesize($filepath)
        ];
    }
    
    /**
     * Create structure-only backup
     */
    private function createStructureBackup($filepath, $options) {
        $exporter = new DatabaseExporter($this->backupPath);
        return $exporter->exportStructure([
            'include_migrations' => $options['include_migrations'],
            'compression' => 'none',
            'compatibility_mode' => 'mysql5.7'
        ]);
    }
    
    /**
     * Create data-only backup
     */
    private function createDataBackup($filepath, $options) {
        $exporter = new DatabaseExporter($this->backupPath);
        return $exporter->exportData([
            'include_migrations' => $options['include_migrations'],
            'compression' => 'none',
            'compatibility_mode' => 'mysql5.7'
        ]);
    }
    
    /**
     * Get next version number for backup type
     */
    private function getNextVersion($type) {
        $pattern = $this->backupPath . "imsapp_backup_{$type}_v*_*.sql*";
        $files = glob($pattern);
        
        $maxVersion = 0;
        foreach ($files as $file) {
            if (preg_match('/_v(\d+)_/', basename($file), $matches)) {
                $version = (int)$matches[1];
                if ($version > $maxVersion) {
                    $maxVersion = $version;
                }
            }
        }
        
        return $maxVersion + 1;
    }
    
    /**
     * Get tables with timestamp columns
     */
    private function getTablesWithTimestamps($pdo) {
        $tables = [];
        
        // Get all tables
        $stmt = $pdo->query("SHOW TABLES");
        $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($allTables as $table) {
            // Check for timestamp columns
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE '%_at'");
            $timestampColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($timestampColumns)) {
                $tables[] = [
                    'name' => $table,
                    'timestamp_column' => $timestampColumns[0] // Use first timestamp column
                ];
            }
        }
        
        return $tables;
    }
    
    /**
     * Export incremental data for a table
     */
    private function exportIncrementalTableData($pdo, $table, $lastBackup) {
        $export = "-- Incremental data for table `{$table['name']}`\n";
        
        try {
            $whereClause = '';
            if ($lastBackup) {
                $whereClause = "WHERE `{$table['timestamp_column']}` > '{$lastBackup}'";
            }
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table['name']}` {$whereClause}");
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $export .= "-- Found $count new/updated records\n";
                $export .= "LOCK TABLES `{$table['name']}` WRITE;\n";
                $export .= "/*!40000 ALTER TABLE `{$table['name']}` DISABLE KEYS */;\n";
                
                // Get data in chunks
                $chunkSize = 1000;
                $offset = 0;
                
                do {
                    $stmt = $pdo->prepare("SELECT * FROM `{$table['name']}` {$whereClause} LIMIT " . (int)$chunkSize . " OFFSET " . (int)$offset);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($rows)) {
                        $export .= $this->formatInsertStatements($table['name'], $rows);
                    }
                    
                    $offset += $chunkSize;
                } while (count($rows) === $chunkSize);
                
                $export .= "/*!40000 ALTER TABLE `{$table['name']}` ENABLE KEYS */;\n";
                $export .= "UNLOCK TABLES;\n\n";
            } else {
                $export .= "-- No new/updated records found\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Format INSERT statements
     */
    private function formatInsertStatements($table, $rows) {
        if (empty($rows)) {
            return "";
        }
        
        $export = "";
        $columns = array_keys($rows[0]);
        $columnList = '`' . implode('`, `', $columns) . '`';
        
        $export .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
        
        $values = [];
        foreach ($rows as $row) {
            $rowValues = [];
            foreach ($row as $value) {
                if ($value === null) {
                    $rowValues[] = 'NULL';
                } elseif (is_numeric($value)) {
                    $rowValues[] = $value;
                } else {
                    $rowValues[] = "'" . addslashes($value) . "'";
                }
            }
            $values[] = '(' . implode(', ', $rowValues) . ')';
        }
        
        $export .= implode(",\n", $values) . ";\n\n";
        
        return $export;
    }
    
    /**
     * Get last backup time
     */
    private function getLastBackupTime() {
        $metadataFile = $this->backupPath . 'backup_metadata.json';
        
        if (!file_exists($metadataFile)) {
            return null;
        }
        
        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        if (empty($metadata)) {
            return null;
        }
        
        // Get the most recent backup time
        $lastBackup = null;
        foreach ($metadata as $backup) {
            if ($backup['type'] === 'full' || $backup['type'] === 'incremental') {
                $backupTime = strtotime($backup['created']);
                if ($lastBackup === null || $backupTime > $lastBackup) {
                    $lastBackup = $backup['created'];
                }
            }
        }
        
        return $lastBackup;
    }
    
    /**
     * Record backup metadata
     */
    private function recordBackupMetadata($filename, $options, $result) {
        $metadataFile = $this->backupPath . 'backup_metadata.json';
        
        $metadata = [];
        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true) ?: [];
        }
        
        $backupInfo = [
            'filename' => $filename,
            'type' => $options['type'],
            'version' => $this->getNextVersion($options['type']) - 1,
            'size' => $result['size'],
            'compression' => $options['compression'],
            'created' => date('Y-m-d H:i:s'),
            'description' => $options['description'],
            'include_migrations' => $options['include_migrations']
        ];
        
        $metadata[] = $backupInfo;
        
        // Keep only last 100 backup records
        if (count($metadata) > 100) {
            $metadata = array_slice($metadata, -100);
        }
        
        file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    }
    
    /**
     * Clean old backups
     */
    private function cleanOldBackups($retentionDays, $maxVersions) {
        $cutoff = time() - ($retentionDays * 24 * 60 * 60);
        $deleted = 0;
        
        // Clean by age
        $files = glob($this->backupPath . 'imsapp_backup_*.sql*');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        // Clean by version count
        $this->cleanByVersionCount($maxVersions);
        
        return $deleted;
    }
    
    /**
     * Clean backups by version count
     */
    private function cleanByVersionCount($maxVersions) {
        $types = ['full', 'incremental', 'structure_only', 'data_only'];
        
        foreach ($types as $type) {
            $pattern = $this->backupPath . "imsapp_backup_{$type}_v*_*.sql*";
            $files = glob($pattern);
            
            if (count($files) > $maxVersions) {
                // Sort by version number
                usort($files, function($a, $b) {
                    preg_match('/_v(\d+)_/', basename($a), $matchesA);
                    preg_match('/_v(\d+)_/', basename($b), $matchesB);
                    return (int)$matchesA[1] - (int)$matchesB[1];
                });
                
                // Delete oldest files
                $toDelete = array_slice($files, 0, count($files) - $maxVersions);
                foreach ($toDelete as $file) {
                    unlink($file);
                }
            }
        }
    }
    
    /**
     * Generate backup header
     */
    private function generateBackupHeader($options) {
        $header = "-- IMS App Database Backup\n";
        $header .= "-- Type: " . $options['type'] . "\n";
        $header .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $header .= "-- Version: " . $this->getNextVersion($options['type']) . "\n";
        
        if ($options['description']) {
            $header .= "-- Description: " . $options['description'] . "\n";
        }
        
        $header .= "\n";
        $header .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $header .= "SET AUTOCOMMIT = 0;\n";
        $header .= "SET time_zone = \"+00:00\";\n";
        $header .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $header .= "SET UNIQUE_CHECKS = 0;\n\n";
        
        return $header;
    }
    
    /**
     * Generate backup footer
     */
    private function generateBackupFooter() {
        $footer = "-- Backup completed\n";
        $footer .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $footer .= "SET UNIQUE_CHECKS = 1;\n";
        $footer .= "COMMIT;\n";
        $footer .= "-- End of backup\n";
        
        return $footer;
    }
    
    /**
     * Compress file
     */
    private function compressFile($filepath, $compression) {
        $compressedPath = $filepath;
        
        switch ($compression) {
            case 'gzip':
                $compressedPath .= '.gz';
                $fp_out = gzopen($compressedPath, 'wb9');
                if ($fp_out === false) {
                    throw new Exception("Failed to create gzip file: $compressedPath");
                }
                
                $fp_in = fopen($filepath, 'rb');
                if ($fp_in === false) {
                    gzclose($fp_out);
                    throw new Exception("Failed to open source file: $filepath");
                }
                
                while (!feof($fp_in)) {
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in);
                gzclose($fp_out);
                unlink($filepath);
                break;
                
            case 'bzip2':
                $compressedPath .= '.bz2';
                $fp_out = bzopen($compressedPath, 'w');
                if ($fp_out === false) {
                    throw new Exception("Failed to create bzip2 file: $compressedPath");
                }
                
                $fp_in = fopen($filepath, 'r');
                if ($fp_in === false) {
                    bzclose($fp_out);
                    throw new Exception("Failed to open source file: $filepath");
                }
                
                while (!feof($fp_in)) {
                    bzwrite($fp_out, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in);
                bzclose($fp_out);
                unlink($filepath);
                break;
        }
        
        return $compressedPath;
    }
    
    /**
     * Get backup history
     */
    public function getBackupHistory() {
        $metadataFile = $this->backupPath . 'backup_metadata.json';
        
        if (!file_exists($metadataFile)) {
            return [];
        }
        
        $metadata = json_decode(file_get_contents($metadataFile), true) ?: [];
        
        // Sort by creation time (newest first)
        usort($metadata, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });
        
        return $metadata;
    }
    
    /**
     * Restore from backup
     */
    public function restoreFromBackup($backupFile, $options = []) {
        $defaultOptions = [
            'backup_before_restore' => true,
            'dry_run' => false
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            if (!file_exists($backupFile)) {
                throw new Exception("Backup file not found: $backupFile");
            }
            
            $this->info("Restoring from backup: " . basename($backupFile));
            
            if ($options['backup_before_restore'] && !$options['dry_run']) {
                $this->info("Creating backup before restore...");
                $backupResult = $this->createBackup(['type' => 'full', 'description' => 'Pre-restore backup']);
                if ($backupResult['success']) {
                    $this->info("Pre-restore backup created: " . $backupResult['filename']);
                }
            }
            
            if ($options['dry_run']) {
                $this->info("Dry run mode - no changes will be made");
                return [
                    'success' => true,
                    'dry_run' => true,
                    'message' => 'Dry run completed successfully'
                ];
            }
            
            // Use DatabaseImporter for restoration
            $importer = new DatabaseImporter();
            $result = $importer->importDatabase($backupFile, [
                'backup_before_import' => false, // We already created backup
                'check_compatibility' => true,
                'skip_errors' => false
            ]);
            
            if ($result['success']) {
                $this->success("Restore completed successfully");
            } else {
                $this->error("Restore failed: " . $result['error']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Schedule automated backups
     */
    public function scheduleBackup($schedule, $options = []) {
        $cronFile = $this->backupPath . 'backup_schedule.json';
        
        $scheduleData = [
            'schedule' => $schedule,
            'options' => $options,
            'created' => date('Y-m-d H:i:s'),
            'active' => true
        ];
        
        file_put_contents($cronFile, json_encode($scheduleData, JSON_PRETTY_PRINT));
        
        $this->info("Backup schedule created: $schedule");
        $this->info("To activate, add this to your crontab:");
        $this->info("$schedule php " . __DIR__ . "/backup-database.php --scheduled");
        
        return [
            'success' => true,
            'schedule' => $schedule,
            'cron_command' => "$schedule php " . __DIR__ . "/backup-database.php --scheduled"
        ];
    }
    
    private function info($message) {
        if (!$this->suppressOutput) {
            echo "[INFO] " . date('Y-m-d H:i:s') . " - $message\n";
        }
    }
    
    private function success($message) {
        if (!$this->suppressOutput) {
            echo "[SUCCESS] " . date('Y-m-d H:i:s') . " - $message\n";
        }
    }
    
    private function error($message) {
        if (!$this->suppressOutput) {
            echo "[ERROR] " . date('Y-m-d H:i:s') . " - $message\n";
        }
    }
}
?>
