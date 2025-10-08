<?php

require_once("../init/init.php");

$product = new Product();
$status = $product->delete_product($_POST);
if($status=="Deleted_Product"){
	echo json_encode([
		"success"=>'success',
		'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The Product ID : '.$_POST['pid'].' has been deleted</div>',

	]);
}else if($status==="Not_Deleted"){
	echo json_encode([
		"error"=>"error",
		"message"=>'<div class="alert alert-warning text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Sorry !!The Product has not been deleted</div>',

	]);
}
exit();
