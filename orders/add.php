<?php

require_once("../init/init.php");
include("../fpdf/fpdf.php");


$status = $order->create_customer_order($_POST);

// debug($status);

if($status=='Out_of_Stock'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center w-100">The product is out of stock </div>',
	]);

}else if($status['Added']=="Order_Added"){
	echo json_encode([
	  	'success'=>'success',
	  	'message'=>'<div class="alert alert-success text-success text-center w-100">The order has been created</div>',
	  	'invoice_no'=>$status['id'],
	  	'name'=>$status['name'],
	  	// 'url'=>'orders/generate_invoice.php'
	]);

	
}else if($status=="Not_Added"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-dark text-center w-100">This order has not been created</div>',

	]);
}

/*if(isset($_POST['order_date'])){

		

}*/

