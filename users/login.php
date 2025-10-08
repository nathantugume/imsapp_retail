<?php
require_once('../init/init.php');

// Set JSON header
header('Content-Type: application/json');

// debug($dbcon);
//debug($_POST);
$response = $user->User_Login($_POST);

if ($response === "success"){
	echo json_encode([
		'success' => true,
		'message' => 'Login successful!',
		'url' => '../index.php'
	]);

}else if($response === "Required_Fields"){
	echo json_encode([
		"success" => false,
		"message" => "All fields are required. Please fill in both email and password."
	]);

}else if($response === "Not_Exists"){
	echo json_encode([
		"success" => false,
		"message" => "User account not found. Please check your email address."
	]);

}else if($response === "Invalid_Email"){
	echo json_encode([
		"success" => false,
		"message" => "Invalid email format. Please enter a valid email address."
	]);

}else if($response === "Not_Matched"){
	echo json_encode([
		"success" => false,
		"message" => "Invalid email or password. Please check your credentials."
	]);

}else if($response === "Account_Disabled"){
	echo json_encode([
		"success" => false,
		"message" => "Your account has been disabled. Please contact the administrator."
	]);

}else{
	echo json_encode([
		"success" => false,
		"message" => "An unexpected error occurred. Please try again."
	]);
}
?>


