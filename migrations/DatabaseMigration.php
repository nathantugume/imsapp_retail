<?php
require_once(__DIR__ . "/../init/init.php");

class DatabaseMigration {
    private $dbcon;
    private $migrations = [];
    
    public function __construct() {
        $this->dbcon = getDB();
        $this->initializeMigrations();
    }
    
    private function initializeMigrations() {
        $this->migrations = [
            '001_add_expiry_date_to_products' => [
                'description' => 'Add expiry_date field to products table',
                'up' => "ALTER TABLE products ADD COLUMN expiry_date DATE NULL AFTER created_at",
                'down' => "ALTER TABLE products DROP COLUMN expiry_date",
                'check' => "SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'expiry_date'"
            ],
            '002_add_status_to_users' => [
                'description' => 'Add status field to users table (already exists - skip)',
                'up' => "-- Status field already exists in users table",
                'down' => "-- Status field already exists in users table",
                'check' => "SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status'"
            ],
            '003_create_customer_payments_table' => [
                'description' => 'Create customer_payments table',
                'up' => "CREATE TABLE IF NOT EXISTS customer_payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    invoice_no INT NOT NULL,
                    amount_paid DECIMAL(10,2) NOT NULL,
                    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    payment_method VARCHAR(50) DEFAULT 'Cash',
                    notes TEXT,
                    created_by INT,
                    FOREIGN KEY (invoice_no) REFERENCES orders(invoice_no) ON DELETE CASCADE,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
                )",
                'down' => "DROP TABLE IF EXISTS customer_payments",
                'check' => "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'customer_payments'"
            ],
            '004_create_stock_reconciliations_table' => [
                'description' => 'Create stock_reconciliations table',
                'up' => "CREATE TABLE IF NOT EXISTS stock_reconciliations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    product_id INT NOT NULL,
                    system_stock INT NOT NULL,
                    physical_count INT NOT NULL,
                    difference INT NOT NULL,
                    reconciliation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('pending','approved','rejected') DEFAULT 'pending',
                    notes TEXT,
                    created_by INT,
                    approved_by INT NULL,
                    approved_at TIMESTAMP NULL,
                    FOREIGN KEY (product_id) REFERENCES products(pid) ON DELETE CASCADE,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
                )",
                'down' => "DROP TABLE IF EXISTS stock_reconciliations",
                'check' => "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'stock_reconciliations'"
            ],
            '005_add_created_at_to_products' => [
                'description' => 'Add created_at timestamp to products table (already exists - skip)',
                'up' => "-- Created_at field already exists in products table",
                'down' => "-- Created_at field already exists in products table",
                'check' => "SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'created_at'"
            ],
            '006_update_orders_payment_fields' => [
                'description' => 'Ensure orders table has proper payment fields',
                'up' => "ALTER TABLE orders MODIFY COLUMN paid DECIMAL(10,2) NOT NULL DEFAULT 0, MODIFY COLUMN due DECIMAL(10,2) NOT NULL DEFAULT 0",
                'down' => "ALTER TABLE orders MODIFY COLUMN paid DOUBLE NOT NULL, MODIFY COLUMN due DOUBLE NOT NULL",
                'check' => "SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'paid' AND DATA_TYPE = 'decimal'"
            ]
        ];
    }
    
    public function runMigration($migrationId) {
        try {
            if (!isset($this->migrations[$migrationId])) {
                throw new Exception("Migration not found: $migrationId");
            }
            
            $migration = $this->migrations[$migrationId];
            
            // Check if migration is already applied
            if ($this->isMigrationApplied($migrationId)) {
                return [
                    'success' => true,
                    'message' => "Migration $migrationId is already applied",
                    'skipped' => true
                ];
            }
            
            // Check if the migration is already applied by examining the database structure
            if ($this->isMigrationAlreadyApplied($migrationId)) {
                // Record the migration as applied
                $this->recordMigration($migrationId);
                return [
                    'success' => true,
                    'message' => "Migration $migrationId was already applied (detected and recorded)",
                    'skipped' => true
                ];
            }
            
            // Execute the migration
            $pdo = $this->dbcon->connect();
            $pdo->exec($migration['up']);
            
            // Record the migration
            $this->recordMigration($migrationId);
            
            return [
                'success' => true,
                'message' => "Migration $migrationId applied successfully: " . $migration['description']
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Migration $migrationId failed: " . $e->getMessage()
            ];
        }
    }
    
    private function isMigrationAlreadyApplied($migrationId) {
        try {
            $pdo = $this->dbcon->connect();
            
            switch($migrationId) {
                case '001_add_expiry_date_to_products':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'expiry_date'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                case '002_add_status_to_users':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                case '003_create_customer_payments_table':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'customer_payments'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                case '004_create_stock_reconciliations_table':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'stock_reconciliations'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                case '005_add_created_at_to_products':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'created_at'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                case '006_update_orders_payment_fields':
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'paid' AND DATA_TYPE = 'decimal'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result['count'] > 0;
                    
                default:
                    return false;
            }
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function runAllMigrations() {
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($this->migrations as $migrationId => $migration) {
            $result = $this->runMigration($migrationId);
            $results[] = [
                'id' => $migrationId,
                'description' => $migration['description'],
                'result' => $result
            ];
            
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        return [
            'success' => $errorCount === 0,
            'message' => "Migration completed. Success: $successCount, Errors: $errorCount",
            'results' => $results
        ];
    }
    
    public function rollbackMigration($migrationId) {
        try {
            if (!isset($this->migrations[$migrationId])) {
                throw new Exception("Migration not found: $migrationId");
            }
            
            $migration = $this->migrations[$migrationId];
            
            // Check if migration is applied
            if (!$this->isMigrationApplied($migrationId)) {
                return [
                    'success' => true,
                    'message' => "Migration $migrationId is not applied",
                    'skipped' => true
                ];
            }
            
            // Execute the rollback
            $pdo = $this->dbcon->connect();
            $pdo->exec($migration['down']);
            
            // Remove the migration record
            $this->removeMigration($migrationId);
            
            return [
                'success' => true,
                'message' => "Migration $migrationId rolled back successfully"
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Migration $migrationId rollback failed: " . $e->getMessage()
            ];
        }
    }
    
    private function isMigrationApplied($migrationId) {
        try {
            $pdo = $this->dbcon->connect();
            
            // Check if migrations table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
            if ($stmt->rowCount() === 0) {
                // Create migrations table
                $pdo->exec("CREATE TABLE migrations (
                    id VARCHAR(255) PRIMARY KEY,
                    description TEXT,
                    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                
                // Check if migrations are already applied by examining the database structure
                $this->checkExistingMigrations($pdo);
                return false;
            }
            
            // Check if migration is recorded
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM migrations WHERE id = ?");
            $stmt->execute([$migrationId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function checkExistingMigrations($pdo) {
        try {
            // Check for existing expiry_date column in products table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'expiry_date'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $this->recordMigration('001_add_expiry_date_to_products');
            }
            
            // Check for existing status column in users table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $this->recordMigration('002_add_status_to_users');
            }
            
            // Check for existing customer_payments table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'customer_payments'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $this->recordMigration('003_create_customer_payments_table');
            }
            
            // Check for existing stock_reconciliations table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'stock_reconciliations'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $this->recordMigration('004_create_stock_reconciliations_table');
            }
            
            // Check for existing created_at column in products table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'imsapp' AND TABLE_NAME = 'products' AND COLUMN_NAME = 'created_at'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $this->recordMigration('005_add_created_at_to_products');
            }
            
        } catch (Exception $e) {
            // Log error but don't fail
            error_log("Error checking existing migrations: " . $e->getMessage());
        }
    }
    
    private function recordMigration($migrationId) {
        try {
            $pdo = $this->dbcon->connect();
            $migration = $this->migrations[$migrationId];
            
            $stmt = $pdo->prepare("INSERT INTO migrations (id, description) VALUES (?, ?)");
            $stmt->execute([$migrationId, $migration['description']]);
            
        } catch (Exception $e) {
            // Log error but don't fail the migration
            error_log("Failed to record migration $migrationId: " . $e->getMessage());
        }
    }
    
    private function removeMigration($migrationId) {
        try {
            $pdo = $this->dbcon->connect();
            $stmt = $pdo->prepare("DELETE FROM migrations WHERE id = ?");
            $stmt->execute([$migrationId]);
            
        } catch (Exception $e) {
            // Log error but don't fail the rollback
            error_log("Failed to remove migration record $migrationId: " . $e->getMessage());
        }
    }
    
    public function getMigrationStatus() {
        $status = [];
        
        foreach ($this->migrations as $migrationId => $migration) {
            $status[] = [
                'id' => $migrationId,
                'description' => $migration['description'],
                'applied' => $this->isMigrationApplied($migrationId)
            ];
        }
        
        return $status;
    }
    
    public function getAppliedMigrations() {
        try {
            $pdo = $this->dbcon->connect();
            
            // Check if migrations table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
            if ($stmt->rowCount() === 0) {
                return [];
            }
            
            $stmt = $pdo->query("SELECT * FROM migrations ORDER BY applied_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
