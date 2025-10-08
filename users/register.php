<?php
require_once('../init/init.php');

// debug($_POST);
$response = $user->UserRegister($_POST);
if($response === "success"){
	echo json_encode([
		'success'=>'success',
		'message'=>'<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$_POST["name"].' : has been registered successfully</div>',
		'logout'=>1,
		// 'url'=>'index.php'
	]);
}else if($response ==="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">All the fields are required ? ? ?</div>',

	]);
}else if($response === "Invalid_Name"){
	echo json_encode([
		"error" => "error",
		"message" => "<div class='alert alert-danger text-danger text-center'>The username is not valid | minimum 3 alphabets </div>",
	]);

}else if($response==='Name_Exists'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The username is already exists ?</div>',

	]);
}else if($response==='Email_Exists'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The email address already exists ?</div>',

	]);
}else if($response==='Weak_Pass'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The password can not be less than 6 digits ?</div>',

	]);
}else if($response==='Mismatch_Pass'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">The confirm password does not match with password</div>',
	]);
}else if($response==='error'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">This is invalid email address ?</div>',
	]);
}else if($response==='Invalid_Email'){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">This is invalid email address ?</div>',
	]);
}

