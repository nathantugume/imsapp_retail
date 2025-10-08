<?php

require_once("../init/init.php");

if(isset($_POST['user'])){

	$users = $dashboard->count_users();
	$category = $dashboard->count_category();
	$brand = $dashboard->count_brands();
	$product = $dashboard->count_products();
	$value = $dashboard->total_order_value();
	$cash_value = $dashboard->cash_order_value();
	$credit_card = $dashboard->credit_order_value();
		echo json_encode([
			"users"=>$users['total_user'],
			"cat"=>$category['cat'],
			"brand"=>$brand['brands'],
			"item"=>$product['products'],
			"order_value"=>$value['value'],
			"cash_value"=>$cash_value['cash_value'],
			"credit_card"=>$credit_card['credit_value'],

		]);


		exit();	

}



