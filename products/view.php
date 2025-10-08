<?php

require_once("../init/init.php");

// Enable error logging
error_log("=== Product View Debug - " . date('Y-m-d H:i:s') . " ===");

if(isset($_POST['viewid'])){
	$pid = $_POST['viewid'];
	error_log("Product view request - Product ID: " . $pid);
} else {
	error_log("ERROR: No viewid parameter received in POST request");
	echo json_encode(['error' => 'No product ID provided']);
	exit;
}

// Validate product ID
if(empty($pid) || !is_numeric($pid)) {
	error_log("ERROR: Invalid product ID: " . $pid);
	echo json_encode(['error' => 'Invalid product ID']);
	exit;
}

try {
	error_log("Fetching product data for ID: " . $pid);
	$product = new Product();
	$datas = $product->fetch_single_product($pid);
	
	if($datas) {
		error_log("Product data found: " . json_encode($datas));
		echo json_encode($datas);
	} else {
		error_log("ERROR: No product found with ID: " . $pid);
		echo json_encode(['error' => 'Product not found']);
	}
} catch (Exception $e) {
	error_log("ERROR: Exception in product view: " . $e->getMessage());
	echo json_encode(['error' => 'Database error occurred']);
}
