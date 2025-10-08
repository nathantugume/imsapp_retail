<?php


function debug($args){
	echo "<pre>";
	print_r($args);
	echo "</pre>";
	exit;

}

function generateCode(){
	$str = "$10AbzxsdertyqwopukmnbvchgfhMAZX&MNLKPRTY1234567890#$&%";
    $token_str = str_shuffle($str);
    $token = substr($token_str, 0, 15);
    return $token;
}

?>