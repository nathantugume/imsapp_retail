<?php

require_once("../init/init.php");

if(isset($_POST['pid'])){
	$pid =$_POST['pid'];
}
$productObj = new Product();
$product = $productObj->fetch_single_product($pid);

echo json_encode($product);


