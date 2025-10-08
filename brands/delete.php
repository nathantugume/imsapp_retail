<?php
require_once(__DIR__ . '/../init/init.php');

// Set JSON header
header('Content-Type: application/json');

try {
    $status = $brand->delete_brand($_POST);
} catch (Exception $e) {
    error_log("Brand Delete Error: " . $e->getMessage());
    error_log("Brand Delete POST Data: " . json_encode($_POST));
    
    echo json_encode([
        'error' => 'error',
        'message' => 'A server error occurred while deleting the brand. Please try again.'
    ]);
    exit();
}

if($status=="Deleted_Brand"){
	echo json_encode([
		"success"=>'success',
		'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The brand ID : '.$_POST['del_id'].' has been deleted</div>',
	]);
}else if($status=="Connection_Error"){
	echo json_encode([
		"error"=>"error",
		"message"=>'<div class="alert alert-danger text-danger text-center">Database connection failed. Please try again.</div>',
	]);
}else if($status=="Database_Error"){
	echo json_encode([
		"error"=>"error",
		"message"=>'<div class="alert alert-danger text-danger text-center">Database error occurred. Please try again.</div>',
	]);
}else if($status==="Not_Deleted"){
	echo json_encode([
		"error"=>"error",
		"message"=>'<div class="alert alert-warning text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Sorry !!The brand has not been deleted</div>',
	]);
}else {
	echo json_encode([
		"error"=>"error",
		"message"=>'<div class="alert alert-danger text-danger text-center">An unexpected error occurred. Please try again.</div>',
	]);
}
exit();
