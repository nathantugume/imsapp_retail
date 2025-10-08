<?php

require_once("../init/init.php");

$product = new Product();
$status = $product->add_stock($_POST);

if($status=="Stock_Added"){
	echo json_encode([
	  	'success'=>'success',
	  	'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The product stock quantity '.$_POST['stock'].' for '.$_POST['product-name-stock'].' has been added</div>',
	  	// 'url'=>'category.php'
	]);
}else if($status=="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The new stock field is required</div>',
	]);

}else if($status=="Not_Added"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The new stock is not added</div>',
	]);

	exit();
}

