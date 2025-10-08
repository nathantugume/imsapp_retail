<?php
require_once('../init/init.php');
require_once('CustomerPayment.php');

$customerPayment = new CustomerPayment();
$response = $customerPayment->record_payment($_POST);

if($response === "Payment_Recorded"){
    echo json_encode([
        "success" => true,
        "message" => '<div class="alert alert-success text-success text-center">Payment recorded successfully!</div>',
        "url" => "customer-payments.php"
    ]);
} else if($response === "Required"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Please fill in all required fields.</div>'
    ]);
} else if($response === "Order_Not_Found"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Order not found.</div>'
    ]);
} else if($response === "Amount_Exceeds_Due"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Payment amount exceeds the outstanding balance.</div>'
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Failed to record payment. Please try again.</div>'
    ]);
}
?>







