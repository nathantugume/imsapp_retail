<?php

require_once('../init/init.php');


$status = $user->Update_User($_POST);

if($status==="updated"){
	echo json_encode([
		'success'=>'success',
		'message'=>'<div class="alert alert-success text-success text-center">'.$_POST["edit-name"].' has been updated </div>',
		'logout'=>1,
		// 'url'=>'index.php'
	]);
}else if($status==="Required"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">All the fields are required</div>',

	]);
}else if($status==="user_exists"){
	echo json_encode([
		'error'=>'error',
		'message'=>'<div class="alert alert-danger text-danger text-center">User already exists ?</div>',

	]);
}