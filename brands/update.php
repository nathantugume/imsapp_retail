<?php
require_once(__DIR__ . '/../init/init.php');

// Set JSON header
header('Content-Type: application/json');

try {
    $status = $brand->update_brand($_POST);
} catch (Exception $e) {
    error_log("Brand Update Error: " . $e->getMessage());
    error_log("Brand Update POST Data: " . json_encode($_POST));
    
    echo json_encode([
        'error' => 'error',
        'message' => 'A server error occurred while updating the brand. Please try again.'
    ]);
    exit();
}

if($status=="Updated"){
	echo json_encode([
	  	'success'=>'success',
	  	'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The brand name '.$_POST['update_brand_name'].' has been updated</div>',
	  	// 'url'=>'category.php'
	]);
}else if($status=="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">All the fields are required</div>',
	]);
}else if($status=="Connection_Error"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">Database connection failed. Please try again.</div>',
	]);
}else if($status=="Database_Error"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">Database error occurred. Please try again.</div>',
	]);
}else if($status=="Duplicate_Entry"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">A brand with this name already exists.</div>',
	]);
}else if($status=="Not_Updated"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-dark text-center">This brand name not updated</div>',
	]);
}else {
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">An unexpected error occurred. Please try again.</div>',
	]);
}