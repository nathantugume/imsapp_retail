<?php
require_once('../init/init.php');

$response = $user->reset_password($_POST);

if($response === "Pass_Updated"){
	echo json_encode([
		'success'=>'success',
		'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The password has been reset successfully</div>',
		'url' => 'logout.php',
	]);
}else if($response ==="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">All the fields are required ? ? ?</div>',

	]);
}else if($response === "Not_Updated"){
	echo json_encode([
		"error" => "error",
		"message" => "<div class='alert alert-danger text-danger text-center'>The password is not updated </div>",
	]);

}else if($response === "Not_Matched"){
	echo json_encode([
		"error" => "error",
		"mismatch"=>"mismatch",
		"message" => "<div class='alert alert-danger text-danger text-center'>The old password does not match </div>",

	]);

}else if($response === "Matched"){
	echo json_encode([
		"error" => "error",
		"matched"=>"matched",
		"message" => "<div class='alert alert-success text-success text-center'>The old password is matched </div>"
	]);

}

exit();