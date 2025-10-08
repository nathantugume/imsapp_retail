<?php

require_once("../init/init.php");

// Log incoming request
error_log("=== VIEW.PHP REQUEST ===");
error_log("POST data: " . print_r($_POST, true));

if(isset($_POST['view_id'])){
	$id = $_POST['view_id'];
	error_log("Processing view_id: " . $id);
} else {
	error_log("ERROR: No view_id provided");
	echo json_encode(['error' => 'No view_id provided']);
	exit;
}

// Log before database call
error_log("Calling fetch_all_orders_with_invoice for ID: " . $id);

$rows = $order->fetch_all_orders_with_invoice($id);

// Log database result
error_log("Database result: " . print_r($rows, true));
error_log("Number of rows returned: " . (is_array($rows) ? count($rows) : 'null/false'));

// Additional debugging for empty results
if(!$rows || (is_array($rows) && count($rows) == 0)) {
    error_log("DEBUGGING EMPTY RESULT FOR ID: " . $id);
    
    // Check if order exists in orders table
    $check_order_sql = "SELECT * FROM orders WHERE invoice_no = ?";
    $check_stmt = $order->dbcon->connect()->prepare($check_order_sql);
    $check_stmt->bindParam(1, $id, PDO::PARAM_INT);
    $check_stmt->execute();
    $order_exists = $check_stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Order exists check: " . print_r($order_exists, true));
    
    // Check if invoices exist for this order
    $check_invoice_sql = "SELECT * FROM invoices WHERE invoice_no = ?";
    $check_inv_stmt = $order->dbcon->connect()->prepare($check_invoice_sql);
    $check_inv_stmt->bindParam(1, $id, PDO::PARAM_INT);
    $check_inv_stmt->execute();
    $invoice_exists = $check_inv_stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Invoice exists check: " . print_r($invoice_exists, true));
    
    // Check the exact query being used
    $debug_sql = "SELECT * FROM orders OD LEFT JOIN invoices IC ON `OD`.`invoice_no`=`IC`.`invoice_no` WHERE `IC`.invoice_no=?";
    error_log("Query being executed: " . $debug_sql . " with ID: " . $id);
}

// Log final response
error_log("Sending JSON response: " . json_encode($rows));
error_log("=== VIEW.PHP COMPLETE ===");

echo json_encode($rows);

// foreach($rows as $row){
//      echo json_encode($row);

// }


// foreach ($rows as $row) {
	
// 	// echo $row->customer_name;
// 	echo $row->product_name;
	
// }

