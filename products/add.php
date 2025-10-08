<?php

require_once("../init/init.php");

$product = new Product();
$status = $product->add_product($_POST);

if($status=="Product_Added"){
	echo json_encode([
	  	'success'=>'success',
	  	'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The product '.$_POST['product_name'].' has been added</div>',
	  	// 'url'=>'category.php'
	]);
}else if($status=="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">All the fields are required</div>',
	]);

}else if($status=="Invalid_Name"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The name of product is not valid minimum 3 characters</div>',
	]);

}else if($status=="Product_Exists"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-dark text-center">This Product name '.$_POST['product_name'].' is already exists</div>',

	]);
}else if($status=="Not_Added"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-dark text-center">This product addition failed</div>',

	]);
}
exit();
?>