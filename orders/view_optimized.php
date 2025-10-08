<?php
// Optimized view endpoint with separate order and products queries
require_once("../init/init.php");

if(isset($_POST['invoice_no'])) {
    $invoice_no = $_POST['invoice_no'];
    
    try {
        // Single query for order details
        $sql_order = "SELECT invoice_no, customer_name, address, subtotal, discount, net_total, paid, due, payment_method, order_date FROM orders WHERE invoice_no = ?";
        $stmt_order = $order->dbcon->connect()->prepare($sql_order);
        $stmt_order->bindParam(1, $invoice_no, PDO::PARAM_INT);
        $stmt_order->execute();
        
        // Single query for products
        $sql_products = "SELECT id, product_name, order_qty, price_per_item FROM invoices WHERE invoice_no = ?";
        $stmt_products = $order->dbcon->connect()->prepare($sql_products);
        $stmt_products->bindParam(1, $invoice_no, PDO::PARAM_INT);
        $stmt_products->execute();
        
        if($stmt_order->rowCount() > 0 && $stmt_products->rowCount() > 0) {
            $order_data = $stmt_order->fetch(PDO::FETCH_ASSOC);
            $products_data = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'order' => $order_data,
                'products' => $products_data
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invoice number required']);
}
?>
