<?php
require_once('init/init.php');

if(isset($_SESSION['LOGGEDIN'])){
	$_SESSION = []; // ASSIGN EMPTY SESSION ARRAY
    //Expiring cookie
    setcookie(session_name(), session_id(), time()-1000, "/");
    session_destroy();
    header("location:login.php");
}else{
	header("location:login.php?error=Please login to your account ?");

}




?>