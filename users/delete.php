<?php
require_once('../init/init.php');

// debug($_POST);
$status = $user->Delete_User($_POST);


if($status==='Deleted'){
	echo json_encode([
		"success" => "success",
		"message" => '<div class="alert alert-success text-success text-center">User  ID No. : '.$_POST["id"].' has been deleted </div>',

	]);
}else if($status==="Not_Deleted"){
	echo json_encode([
		"error"=>'error',
		"message"=> '<div class="alert alert-danger text-danger text-center">User not deleted </div>',
	]);
}
