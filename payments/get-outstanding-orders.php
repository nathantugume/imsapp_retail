<?php
require_once('../init/init.php');

$orders = $customerPayment->get_outstanding_orders();

if(!empty($orders)){
    echo json_encode([
        "success" => true,
        "orders" => $orders
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No outstanding orders found"
    ]);
}
?>







