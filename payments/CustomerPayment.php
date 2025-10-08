<?php

class CustomerPayment{
    private $dbcon;
    private $table = "customer_payments";

    public function __construct(){
        $this->dbcon = getDB();
    }

    public function record_payment($data){
        if(empty($data['order_id']) || empty($data['amount_paid']) || empty($data['payment_method'])){
            return "Required";
        }

        $amount_paid = (float)$data['amount_paid'];
        $created_by = isset($_SESSION['LOGGEDIN']['id']) ? $_SESSION['LOGGEDIN']['id'] : 1;

        // Check if order exists and get current due amount
        $sql = "SELECT due, paid FROM orders WHERE invoice_no = ?";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->bindParam(1, $data['order_id'], PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if(!$order){
            return "Order_Not_Found";
        }

        if($amount_paid > $order->due){
            return "Amount_Exceeds_Due";
        }

        // Start transaction
        $this->dbcon->connect()->beginTransaction();

        try {
            // Record the payment
            $sql = "INSERT INTO {$this->table} (invoice_no, amount_paid, payment_method, notes, created_by) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $data['order_id'], PDO::PARAM_INT);
            $stmt->bindParam(2, $amount_paid, PDO::PARAM_STR);
            $stmt->bindParam(3, $data['payment_method'], PDO::PARAM_STR);
            $stmt->bindParam(4, $data['notes'], PDO::PARAM_STR);
            $stmt->bindParam(5, $created_by, PDO::PARAM_INT);
            $stmt->execute();

            // Update order paid and due amounts
            $new_paid = $order->paid + $amount_paid; // Add to existing paid amount
            $new_due = $order->due - $amount_paid; // Subtract from due amount

            $sql = "UPDATE orders SET paid = ?, due = ? WHERE invoice_no = ?";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $new_paid, PDO::PARAM_STR);
            $stmt->bindParam(2, $new_due, PDO::PARAM_STR);
            $stmt->bindParam(3, $data['order_id'], PDO::PARAM_INT);
            $stmt->execute();

            $this->dbcon->connect()->commit();
            return "Payment_Recorded";

        } catch (Exception $e) {
            $this->dbcon->connect()->rollback();
            return "Payment_Failed";
        }
    }

    public function get_outstanding_orders(){
        $sql = "SELECT invoice_no, customer_name, net_total, paid, due, order_date 
                FROM orders 
                WHERE due > 0 
                ORDER BY order_date DESC";
        
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_payment_history($order_id = null){
        if($order_id){
            $sql = "SELECT cp.*, u.name as created_by_name 
                    FROM {$this->table} cp 
                    JOIN users u ON cp.created_by = u.id 
                    WHERE cp.invoice_no = ? 
                    ORDER BY cp.payment_date DESC";
            $stmt = $this->dbcon->connect()->prepare($sql);
            $stmt->bindParam(1, $order_id, PDO::PARAM_INT);
        } else {
            $sql = "SELECT cp.*, u.name as created_by_name, o.customer_name 
                    FROM {$this->table} cp 
                    JOIN users u ON cp.created_by = u.id 
                    JOIN orders o ON cp.invoice_no = o.invoice_no 
                    ORDER BY cp.payment_date DESC 
                    LIMIT 100";
            $stmt = $this->dbcon->connect()->prepare($sql);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_total_outstanding(){
        $sql = "SELECT SUM(due) as total_outstanding FROM orders WHERE due > 0";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total_outstanding ?? 0;
    }

    public function get_total_cash_received(){
        $sql = "SELECT SUM(amount_paid) as total_cash FROM {$this->table} WHERE payment_method = 'Cash'";
        $stmt = $this->dbcon->connect()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total_cash ?? 0;
    }
}

$customerPayment = new CustomerPayment();

