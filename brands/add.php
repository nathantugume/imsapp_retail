<?php
require_once("../init/init.php");


// debug($_POST);
$status = $brand->addBrand($_POST);

if($status=="Brand_added"){
	echo json_encode([
	  	'success'=>'success',
	  	'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The brand name '.$_POST['brand_name'].' has been added</div>',
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
		'message'=>'<div class="alert alert-danger text-danger text-center">The name of brand is not valid minimum 3 characters</div>',
	]);

}else if($status=="Brand_Exists"){
	echo json_encode([
		'error'=>'error',
		'exists'=>'exists',
		'message'=>'<div class="alert alert-danger text-dark text-center">This brand name '.$_POST['brand_name'].' is already exists</div>',

	]);
}
exit();
?>