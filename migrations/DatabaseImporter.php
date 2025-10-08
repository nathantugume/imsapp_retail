<?php
require_once(__DIR__ . "/../init/init.php");

class DatabaseImporter {
    private $dbcon;
    private $config;
    private $importPath;
    private $backupPath;
    
    public function __construct($importPath = null, $backupPath = null) {
        $this->dbcon = getDB();
        $this->config = new Config();
        $this->importPath = $importPath ?: __DIR__ . '/imports/';
        $this->backupPath = $backupPath ?: __DIR__ . '/backups/';
        
        // Create directories if they don't exist
        if (!is_dir($this->importPath)) {
            mkdir($this->importPath, 0755, true);
        }
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }
    
    /**
     * Import database from SQL file with compatibility checks
     */
    public function importDatabase($sqlFile, $options = []) {
        $defaultOptions = [
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
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            // Validate file
            if (!file_exists($sqlFile)) {
                throw new Exception("SQL file not found: $sqlFile");
            }
            
            if (!is_readable($sqlFile)) {
                throw new Exception("SQL file is not readable: $sqlFile");
            }
            
            // Check file size
            $fileSize = filesize($sqlFile);
            if ($fileSize > 100 * 1024 * 1024) { // 100MB limit
                throw new Exception("SQL file too large: " . $this->formatBytes($fileSize) . " (max 100MB)");
            }
            
            $this->info("Starting database import from: " . basename($sqlFile));
            $this->info("File size: " . $this->formatBytes($fileSize));
            
            // Check compatibility if requested
            if ($options['check_compatibility']) {
                $compatibility = $this->checkCompatibility($sqlFile);
                $this->info("Compatibility check: " . $compatibility['status']);
                if ($compatibility['warnings']) {
                    foreach ($compatibility['warnings'] as $warning) {
                        $this->warning($warning);
                    }
                }
            }
            
            // Create backup if requested
            if ($options['backup_before_import'] && !$options['dry_run']) {
                $backupResult = $this->createBackup();
                if ($backupResult['success']) {
                    $this->info("Backup created: " . $backupResult['filename']);
                } else {
                    $this->warning("Backup failed: " . $backupResult['error']);
                }
            }
            
            // Dry run - just parse and validate
            if ($options['dry_run']) {
                return $this->dryRunImport($sqlFile, $options);
            }
            
            // Perform actual import
            return $this->performImport($sqlFile, $options);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check compatibility of SQL file with current database
     */
    private function checkCompatibility($sqlFile) {
        $content = $this->readSqlFile($sqlFile);
        $warnings = [];
        $status = 'compatible';
        
        // Check for MySQL version specific features
        if (strpos($content, 'utf8mb4_0900_ai_ci') !== false) {
            $warnings[] = 'File contains MySQL 8.0 specific collation (utf8mb4_0900_ai_ci)';
            $status = 'warning';
        }
        
        if (strpos($content, 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci') !== false) {
            $warnings[] = 'File contains MySQL 8.0 specific table definitions';
            $status = 'warning';
        }
        
        // Check for MariaDB specific features
        if (strpos($content, 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci') !== false) {
            // This is generally compatible
        }
        
        // Check for large data imports
        $dataSize = $this->estimateDataSize($content);
        if ($dataSize > 50 * 1024 * 1024) { // 50MB
            $warnings[] = 'Large data import detected (' . $this->formatBytes($dataSize) . ') - may take significant time';
            $status = 'warning';
        }
        
        return [
            'status' => $status,
            'warnings' => $warnings,
            'estimated_size' => $dataSize
        ];
    }
    
    /**
     * Perform dry run import (validation only)
     */
    private function dryRunImport($sqlFile, $options) {
        $this->info("Performing dry run import...");
        
        $content = $this->readSqlFile($sqlFile);
        $statements = $this->parseSqlStatements($content);
        
        $stats = [
            'total_statements' => count($statements),
            'create_tables' => 0,
            'insert_statements' => 0,
            'drop_statements' => 0,
            'other_statements' => 0,
            'estimated_rows' => 0
        ];
        
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if (empty($trimmed) || strpos($trimmed, '--') === 0) {
                continue;
            }
            
            $upper = strtoupper($trimmed);
            if (strpos($upper, 'CREATE TABLE') === 0) {
                $stats['create_tables']++;
            } elseif (strpos($upper, 'INSERT INTO') === 0) {
                $stats['insert_statements']++;
                $stats['estimated_rows'] += $this->countInsertRows($trimmed);
            } elseif (strpos($upper, 'DROP TABLE') === 0) {
                $stats['drop_statements']++;
            } else {
                $stats['other_statements']++;
            }
        }
        
        $this->info("Dry run completed successfully");
        $this->info("Total statements: " . $stats['total_statements']);
        $this->info("Create tables: " . $stats['create_tables']);
        $this->info("Insert statements: " . $stats['insert_statements']);
        $this->info("Estimated rows: " . number_format($stats['estimated_rows']));
        
        return [
            'success' => true,
            'dry_run' => true,
            'stats' => $stats,
            'message' => 'Dry run completed successfully - no changes made to database'
        ];
    }
    
    /**
     * Perform actual import
     */
    private function performImport($sqlFile, $options) {
        $this->info("Starting database import...");
        
        $pdo = $this->dbcon->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Set timeout
        $pdo->exec("SET SESSION wait_timeout = " . $options['timeout']);
        
        $content = $this->readSqlFile($sqlFile);
        $statements = $this->parseSqlStatements($content);
        
        $stats = [
            'total_statements' => 0,
            'successful_statements' => 0,
            'failed_statements' => 0,
            'errors' => []
        ];
        
        $startTime = microtime(true);
        
        try {
            $pdo->beginTransaction();
            
            foreach ($statements as $statement) {
                $trimmed = trim($statement);
                if (empty($trimmed) || strpos($trimmed, '--') === 0) {
                    continue;
                }
                
                $stats['total_statements']++;
                
                try {
                    $pdo->exec($trimmed);
                    $stats['successful_statements']++;
                    
                    if ($stats['total_statements'] % 100 === 0) {
                        $this->info("Processed " . $stats['total_statements'] . " statements...");
                    }
                    
                } catch (PDOException $e) {
                    $stats['failed_statements']++;
                    $stats['errors'][] = [
                        'statement' => substr($trimmed, 0, 100) . '...',
                        'error' => $e->getMessage()
                    ];
                    
                    if (!$options['skip_errors']) {
                        throw $e;
                    } else {
                        $this->warning("Skipped statement due to error: " . $e->getMessage());
                    }
                }
            }
            
            $pdo->commit();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("Import completed in {$duration} seconds");
            $this->info("Successful statements: " . $stats['successful_statements']);
            
            if ($stats['failed_statements'] > 0) {
                $this->warning("Failed statements: " . $stats['failed_statements']);
            }
            
            return [
                'success' => true,
                'stats' => $stats,
                'duration' => $duration,
                'message' => 'Database import completed successfully'
            ];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    /**
     * Create backup of current database
     */
    private function createBackup() {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "imsapp_backup_before_import_{$timestamp}.sql";
            $filepath = $this->backupPath . $filename;
            
            // Use mysqldump if available
            $mysqldump = $this->findMysqldump();
            if ($mysqldump) {
                $command = sprintf(
                    '%s -h%s -u%s -p%s %s > %s',
                    $mysqldump,
                    $this->config->host_connect(),
                    $this->config->user_connect(),
                    $this->config->pass_connect(),
                    $this->config->dbname_connect(),
                    $filepath
                );
                
                exec($command, $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($filepath)) {
                    return [
                        'success' => true,
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'size' => filesize($filepath)
                    ];
                }
            }
            
            // Fallback to PHP-based backup
            $exporter = new DatabaseExporter($this->backupPath);
            $result = $exporter->exportFullDatabase([
                'compression' => 'gzip',
                'include_migrations' => true
            ]);
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'filename' => $result['filename'],
                    'filepath' => $result['filepath'],
                    'size' => $result['size']
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
     * Read SQL file content
     */
    private function readSqlFile($sqlFile) {
        // Check if file is compressed
        if (preg_match('/\.gz$/', $sqlFile)) {
            return gzfile($sqlFile, false) ? implode('', gzfile($sqlFile, false)) : '';
        } elseif (preg_match('/\.bz2$/', $sqlFile)) {
            $content = '';
            $fp = bzopen($sqlFile, 'r');
            while (!feof($fp)) {
                $content .= bzread($fp, 8192);
            }
            bzclose($fp);
            return $content;
        } else {
            return file_get_contents($sqlFile);
        }
    }
    
    /**
     * Parse SQL statements from content
     */
    private function parseSqlStatements($content) {
        // Remove comments
        $content = preg_replace('/--.*$/m', '', $content);
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        
        // Split by semicolon, but be careful with strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar) {
                // Check for escaped quotes
                if ($i > 0 && $content[$i-1] === '\\') {
                    // Escaped quote, continue
                } else {
                    $inString = false;
                }
            } elseif (!$inString && $char === ';') {
                $statements[] = $current;
                $current = '';
                continue;
            }
            
            $current .= $char;
        }
        
        if (!empty(trim($current))) {
            $statements[] = $current;
        }
        
        return $statements;
    }
    
    /**
     * Count rows in INSERT statement
     */
    private function countInsertRows($statement) {
        // Simple estimation - count VALUES clauses
        return substr_count(strtoupper($statement), 'VALUES') + 
               substr_count(strtoupper($statement), '),(');
    }
    
    /**
     * Estimate data size from SQL content
     */
    private function estimateDataSize($content) {
        // Rough estimation based on content length
        return strlen($content);
    }
    
    /**
     * Find mysqldump executable
     */
    private function findMysqldump() {
        $paths = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            'C:\\mysql\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe'
        ];
        
        foreach ($paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get import history
     */
    public function getImportHistory() {
        $imports = [];
        $files = glob($this->backupPath . 'imsapp_backup_before_import_*.sql*');
        
        foreach ($files as $file) {
            $imports[] = [
                'filename' => basename($file),
                'filepath' => $file,
                'size' => filesize($file),
                'created' => date('Y-m-d H:i:s', filemtime($file)),
                'compressed' => preg_match('/\.(gz|bz2)$/', $file)
            ];
        }
        
        // Sort by creation time (newest first)
        usort($imports, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });
        
        return $imports;
    }
    
    /**
     * Clean old backups
     */
    public function cleanOldBackups($keepDays = 30) {
        $cutoff = time() - ($keepDays * 24 * 60 * 60);
        $files = glob($this->backupPath . 'imsapp_backup_before_import_*.sql*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        return $deleted;
    }
    
    private function info($message) {
        echo "[INFO] " . date('Y-m-d H:i:s') . " - $message\n";
    }
    
    private function warning($message) {
        echo "[WARNING] " . date('Y-m-d H:i:s') . " - $message\n";
    }
    
    private function error($message) {
        echo "[ERROR] " . date('Y-m-d H:i:s') . " - $message\n";
    }
}
?>
