<?php
require_once(__DIR__ . "/../init/init.php");

class DatabaseExporter {
    private $dbcon;
    private $config;
    private $exportPath;
    
    public function __construct($exportPath = null) {
        $this->dbcon = getDB();
        $this->config = new Config();
        $this->exportPath = $exportPath ?: __DIR__ . '/exports/';
        
        // Create export directory if it doesn't exist
        if (!is_dir($this->exportPath)) {
            mkdir($this->exportPath, 0755, true);
        }
    }
    
    /**
     * Export database with full backward compatibility
     */
    public function exportFullDatabase($options = []) {
        $defaultOptions = [
            'include_data' => true,
            'include_structure' => true,
            'include_migrations' => true,
            'include_triggers' => true,
            'include_views' => true,
            'include_functions' => true,
            'include_procedures' => true,
            'compatibility_mode' => 'mysql5.7', // mysql5.7, mysql8.0, mariadb10.3, mariadb10.4
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'compression' => 'gzip', // none, gzip, bzip2
            'timestamp' => true,
            'version_info' => true
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            $pdo = $this->dbcon->connect();
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "imsapp_export_{$timestamp}.sql";
            $filepath = $this->exportPath . $filename;
            
            $export = $this->generateExportHeader($options);
            
            if ($options['include_structure']) {
                $export .= $this->exportDatabaseStructure($pdo, $options);
            }
            
            if ($options['include_data']) {
                $export .= $this->exportDatabaseData($pdo, $options);
            }
            
            if ($options['include_migrations']) {
                $export .= $this->exportMigrationsTable($pdo, $options);
            }
            
            if ($options['include_triggers']) {
                $export .= $this->exportTriggers($pdo, $options);
            }
            
            if ($options['include_views']) {
                $export .= $this->exportViews($pdo, $options);
            }
            
            if ($options['include_functions']) {
                $export .= $this->exportFunctions($pdo, $options);
            }
            
            if ($options['include_procedures']) {
                $export .= $this->exportProcedures($pdo, $options);
            }
            
            $export .= $this->generateExportFooter($options);
            
            // Write to file
            file_put_contents($filepath, $export);
            
            // Apply compression if requested
            if ($options['compression'] !== 'none') {
                $filepath = $this->compressFile($filepath, $options['compression']);
            }
            
            return [
                'success' => true,
                'filepath' => $filepath,
                'filename' => basename($filepath),
                'size' => filesize($filepath),
                'options' => $options,
                'timestamp' => $timestamp
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Export only database structure (schema)
     */
    public function exportStructure($options = []) {
        $options['include_data'] = false;
        $options['include_migrations'] = false;
        return $this->exportFullDatabase($options);
    }
    
    /**
     * Export only data (no structure)
     */
    public function exportData($options = []) {
        $options['include_structure'] = false;
        $options['include_migrations'] = false;
        return $this->exportFullDatabase($options);
    }
    
    /**
     * Export with specific MySQL version compatibility
     */
    public function exportForMySQLVersion($version, $options = []) {
        $compatibilityMap = [
            '5.6' => 'mysql5.6',
            '5.7' => 'mysql5.7',
            '8.0' => 'mysql8.0',
            'mariadb10.3' => 'mariadb10.3',
            'mariadb10.4' => 'mariadb10.4',
            'mariadb10.5' => 'mariadb10.5'
        ];
        
        if (isset($compatibilityMap[$version])) {
            $options['compatibility_mode'] = $compatibilityMap[$version];
        }
        
        return $this->exportFullDatabase($options);
    }
    
    /**
     * Generate export header with compatibility settings
     */
    private function generateExportHeader($options) {
        $header = "-- IMS App Database Export\n";
        $header .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $header .= "-- Export Version: 1.0\n";
        $header .= "-- Compatibility: " . $options['compatibility_mode'] . "\n";
        $header .= "-- Charset: " . $options['charset'] . "\n";
        $header .= "-- Collation: " . $options['collation'] . "\n\n";
        
        // MySQL version compatibility settings
        switch ($options['compatibility_mode']) {
            case 'mysql5.6':
                $header .= "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
                $header .= "SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, AUTOCOMMIT=0;\n";
                $header .= "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;\n";
                $header .= "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n";
                break;
                
            case 'mysql5.7':
                $header .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
                $header .= "SET AUTOCOMMIT = 0;\n";
                $header .= "SET time_zone = \"+00:00\";\n";
                $header .= "SET FOREIGN_KEY_CHECKS = 0;\n";
                $header .= "SET UNIQUE_CHECKS = 0;\n";
                break;
                
            case 'mysql8.0':
                $header .= "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
                $header .= "SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, AUTOCOMMIT=0;\n";
                $header .= "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;\n";
                $header .= "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n";
                $header .= "SET @OLD_TIME_ZONE=@@TIME_ZONE, TIME_ZONE='+00:00';\n";
                break;
                
            case 'mariadb10.3':
            case 'mariadb10.4':
            case 'mariadb10.5':
                $header .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
                $header .= "SET AUTOCOMMIT = 0;\n";
                $header .= "SET time_zone = \"+00:00\";\n";
                $header .= "SET FOREIGN_KEY_CHECKS = 0;\n";
                $header .= "SET UNIQUE_CHECKS = 0;\n";
                break;
        }
        
        $header .= "\n";
        $header .= "-- Character set and collation settings\n";
        $header .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $header .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $header .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $header .= "/*!40101 SET NAMES {$options['charset']} */;\n";
        $header .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n";
        $header .= "/*!40103 SET TIME_ZONE='+00:00' */;\n";
        $header .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n";
        $header .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
        $header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
        $header .= "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n";
        
        return $header;
    }
    
    /**
     * Export database structure
     */
    private function exportDatabaseStructure($pdo, $options) {
        $export = "-- Database Structure\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        // Get all tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            if ($table === 'migrations' && !$options['include_migrations']) {
                continue;
            }
            
            $export .= "-- Table structure for table `{$table}`\n";
            $export .= "-- --------------------------------------------------------\n\n";
            
            // Drop table if exists
            $export .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Get table structure
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $createTable = $result['Create Table'];
            
            // Modify for compatibility
            $createTable = $this->modifyForCompatibility($createTable, $options);
            
            $export .= $createTable . ";\n\n";
            
            // Get table indexes
            $export .= $this->exportTableIndexes($pdo, $table, $options);
        }
        
        return $export;
    }
    
    /**
     * Export database data
     */
    private function exportDatabaseData($pdo, $options) {
        $export = "-- Database Data\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        // Get all tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            if ($table === 'migrations' && !$options['include_migrations']) {
                continue;
            }
            
            // Check if table has data
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $export .= "-- Dumping data for table `{$table}`\n";
                $export .= "-- --------------------------------------------------------\n\n";
                
                // Lock table for read
                $export .= "LOCK TABLES `{$table}` WRITE;\n";
                $export .= "/*!40000 ALTER TABLE `{$table}` DISABLE KEYS */;\n";
                
                // Get table data in chunks
                $chunkSize = 1000;
                $offset = 0;
                
                do {
                    $stmt = $pdo->prepare("SELECT * FROM `{$table}` LIMIT " . (int)$chunkSize . " OFFSET " . (int)$offset);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($rows)) {
                        $export .= $this->formatInsertStatements($table, $rows, $options);
                    }
                    
                    $offset += $chunkSize;
                } while (count($rows) === $chunkSize);
                
                $export .= "/*!40000 ALTER TABLE `{$table}` ENABLE KEYS */;\n";
                $export .= "UNLOCK TABLES;\n\n";
            }
        }
        
        return $export;
    }
    
    /**
     * Export migrations table separately
     */
    private function exportMigrationsTable($pdo, $options) {
        $export = "-- Migrations Table\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        try {
            // Check if migrations table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
            if ($stmt->rowCount() === 0) {
                $export .= "-- Migrations table does not exist\n\n";
                return $export;
            }
            
            // Export migrations table structure
            $export .= "-- Table structure for table `migrations`\n";
            $export .= "DROP TABLE IF EXISTS `migrations`;\n";
            
            $stmt = $pdo->query("SHOW CREATE TABLE `migrations`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $createTable = $result['Create Table'];
            $createTable = $this->modifyForCompatibility($createTable, $options);
            
            $export .= $createTable . ";\n\n";
            
            // Export migrations data
            $stmt = $pdo->query("SELECT COUNT(*) FROM `migrations`");
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $export .= "-- Dumping data for table `migrations`\n";
                $export .= "LOCK TABLES `migrations` WRITE;\n";
                $export .= "/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;\n";
                
                $stmt = $pdo->query("SELECT * FROM `migrations`");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $export .= $this->formatInsertStatements('migrations', $rows, $options);
                
                $export .= "/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;\n";
                $export .= "UNLOCK TABLES;\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error exporting migrations: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Export triggers
     */
    private function exportTriggers($pdo, $options) {
        $export = "-- Triggers\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        try {
            $stmt = $pdo->query("SHOW TRIGGERS");
            $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($triggers)) {
                $export .= "-- No triggers found\n\n";
                return $export;
            }
            
            foreach ($triggers as $trigger) {
                $export .= "-- Trigger: {$trigger['Trigger']}\n";
                $export .= "DROP TRIGGER IF EXISTS `{$trigger['Trigger']}`;\n";
                
                // Get trigger definition
                $stmt = $pdo->query("SHOW CREATE TRIGGER `{$trigger['Trigger']}`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $createTrigger = $result['SQL Original Statement'];
                
                $export .= $createTrigger . ";\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error exporting triggers: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Export views
     */
    private function exportViews($pdo, $options) {
        $export = "-- Views\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        try {
            $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($views)) {
                $export .= "-- No views found\n\n";
                return $export;
            }
            
            foreach ($views as $view) {
                $export .= "-- View: {$view}\n";
                $export .= "DROP VIEW IF EXISTS `{$view}`;\n";
                
                // Get view definition
                $stmt = $pdo->query("SHOW CREATE VIEW `{$view}`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $createView = $result['Create View'];
                
                $export .= $createView . ";\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error exporting views: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Export functions
     */
    private function exportFunctions($pdo, $options) {
        $export = "-- Functions\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        try {
            $stmt = $pdo->query("SHOW FUNCTION STATUS WHERE Db = DATABASE()");
            $functions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($functions)) {
                $export .= "-- No functions found\n\n";
                return $export;
            }
            
            foreach ($functions as $function) {
                $export .= "-- Function: {$function['Name']}\n";
                $export .= "DROP FUNCTION IF EXISTS `{$function['Name']}`;\n";
                
                // Get function definition
                $stmt = $pdo->query("SHOW CREATE FUNCTION `{$function['Name']}`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $createFunction = $result['Create Function'];
                
                $export .= $createFunction . ";\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error exporting functions: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Export procedures
     */
    private function exportProcedures($pdo, $options) {
        $export = "-- Procedures\n";
        $export .= "-- --------------------------------------------------------\n\n";
        
        try {
            $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE()");
            $procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($procedures)) {
                $export .= "-- No procedures found\n\n";
                return $export;
            }
            
            foreach ($procedures as $procedure) {
                $export .= "-- Procedure: {$procedure['Name']}\n";
                $export .= "DROP PROCEDURE IF EXISTS `{$procedure['Name']}`;\n";
                
                // Get procedure definition
                $stmt = $pdo->query("SHOW CREATE PROCEDURE `{$procedure['Name']}`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $createProcedure = $result['Create Procedure'];
                
                $export .= $createProcedure . ";\n\n";
            }
            
        } catch (Exception $e) {
            $export .= "-- Error exporting procedures: " . $e->getMessage() . "\n\n";
        }
        
        return $export;
    }
    
    /**
     * Export table indexes
     */
    private function exportTableIndexes($pdo, $table, $options) {
        $export = "";
        
        try {
            $stmt = $pdo->query("SHOW INDEX FROM `{$table}`");
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($indexes)) {
                $export .= "-- Indexes for table `{$table}`\n";
                foreach ($indexes as $index) {
                    if ($index['Key_name'] !== 'PRIMARY') {
                        $export .= "-- Index: {$index['Key_name']}\n";
                    }
                }
                $export .= "\n";
            }
            
        } catch (Exception $e) {
            // Ignore errors for indexes
        }
        
        return $export;
    }
    
    /**
     * Format INSERT statements
     */
    private function formatInsertStatements($table, $rows, $options) {
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
     * Modify SQL for compatibility
     */
    private function modifyForCompatibility($sql, $options) {
        switch ($options['compatibility_mode']) {
            case 'mysql5.6':
                // Remove MySQL 5.7+ specific features
                $sql = preg_replace('/AUTO_INCREMENT=\d+/', '', $sql);
                break;
                
            case 'mysql5.7':
                // Ensure MySQL 5.7 compatibility
                $sql = str_replace('utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $sql);
                break;
                
            case 'mysql8.0':
                // Keep MySQL 8.0 features
                break;
                
            case 'mariadb10.3':
            case 'mariadb10.4':
            case 'mariadb10.5':
                // MariaDB compatibility
                $sql = str_replace('utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $sql);
                $sql = preg_replace('/ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci/', 
                                  'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci', $sql);
                break;
        }
        
        return $sql;
    }
    
    /**
     * Generate export footer
     */
    private function generateExportFooter($options) {
        $footer = "-- Export completed\n";
        $footer .= "-- --------------------------------------------------------\n\n";
        
        // Restore settings
        $footer .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
        $footer .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $footer .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $footer .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
        $footer .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n";
        $footer .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n";
        $footer .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
        $footer .= "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n\n";
        
        $footer .= "COMMIT;\n";
        $footer .= "-- End of export\n";
        
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
     * Get export history
     */
    public function getExportHistory() {
        $exports = [];
        $files = glob($this->exportPath . 'imsapp_export_*.sql*');
        
        foreach ($files as $file) {
            $exports[] = [
                'filename' => basename($file),
                'filepath' => $file,
                'size' => filesize($file),
                'created' => date('Y-m-d H:i:s', filemtime($file)),
                'compressed' => preg_match('/\.(gz|bz2)$/', $file)
            ];
        }
        
        // Sort by creation time (newest first)
        usort($exports, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });
        
        return $exports;
    }
    
    /**
     * Clean old exports
     */
    public function cleanOldExports($keepDays = 30) {
        $cutoff = time() - ($keepDays * 24 * 60 * 60);
        $files = glob($this->exportPath . 'imsapp_export_*.sql*');
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
}
?>
