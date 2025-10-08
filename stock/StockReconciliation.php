<?php

class StockReconciliation{
    private $dbcon;
    private $table = "stock_reconciliations";

    public function __construct(){
        $this->dbcon = getDB();
    }

    public function create_reconciliation($data){
        if(empty($data['product_id']) || empty($data['physical_count'])){
            return "Required";
        }

        // Get current system stock
        $sql = "SELECT stock FROM products WHERE pid = ?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $data['product_id'], PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_OBJ);

        if(!$product){
            return "Product_Not_Found";
        }

        $system_stock = $product->stock;
        $physical_count = (int)$data['physical_count'];
        $difference = $physical_count - $system_stock;
        $created_by = $_SESSION['LOGGEDIN']['id'];

        $sql = "INSERT INTO {$this->table} (product_id, system_stock, physical_count, difference, created_by, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(2, $system_stock, PDO::PARAM_INT);
        $stmt->bindParam(3, $physical_count, PDO::PARAM_INT);
        $stmt->bindParam(4, $difference, PDO::PARAM_INT);
        $stmt->bindParam(5, $created_by, PDO::PARAM_INT);
        $stmt->bindParam(6, $data['notes'], PDO::PARAM_STR);

        if($stmt->execute()){
            return "Reconciliation_Created";
        }
        return "Failed";
    }

    public function get_pending_reconciliations(){
        $sql = "SELECT sr.*, p.product_name, u.name as created_by_name 
                FROM {$this->table} sr 
                JOIN products p ON sr.product_id = p.pid 
                JOIN users u ON sr.created_by = u.id 
                WHERE sr.status = 'pending' 
                ORDER BY sr.reconciliation_date DESC";
        
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_all_reconciliations($limit = 50){
        $sql = "SELECT sr.*, p.product_name, u.name as created_by_name, 
                approver.name as approved_by_name
                FROM {$this->table} sr 
                JOIN products p ON sr.product_id = p.pid 
                JOIN users u ON sr.created_by = u.id 
                LEFT JOIN users approver ON sr.approved_by = approver.id
                ORDER BY sr.reconciliation_date DESC 
                LIMIT ?";
        
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function approve_reconciliation($reconciliation_id, $action){
        if(!in_array($action, ['approved', 'rejected'])){
            return "Invalid_Action";
        }

        $approved_by = $_SESSION['LOGGEDIN']['id'];
        $approved_at = date('Y-m-d H:i:s');

        $sql = "UPDATE {$this->table} SET status = ?, approved_by = ?, approved_at = ? WHERE id = ?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $action, PDO::PARAM_STR);
        $stmt->bindParam(2, $approved_by, PDO::PARAM_INT);
        $stmt->bindParam(3, $approved_at, PDO::PARAM_STR);
        $stmt->bindParam(4, $reconciliation_id, PDO::PARAM_INT);

        if($stmt->execute()){
            // If approved, update the product stock
            if($action === 'approved'){
                $this->update_product_stock($reconciliation_id);
            }
            return "Reconciliation_{$action}";
        }
        return "Failed";
    }

    private function update_product_stock($reconciliation_id){
        // Get reconciliation details
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $reconciliation_id, PDO::PARAM_INT);
        $stmt->execute();
        $reconciliation = $stmt->fetch(PDO::FETCH_OBJ);

        if($reconciliation){
            // Update product stock to physical count
            $sql = "UPDATE products SET stock = ? WHERE pid = ?";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $reconciliation->physical_count, PDO::PARAM_INT);
            $stmt->bindParam(2, $reconciliation->product_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function get_products_for_reconciliation(){
        $sql = "SELECT pid, product_name, stock FROM products WHERE p_status = '1' ORDER BY product_name";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}

$stockReconciliation = new StockReconciliation();







