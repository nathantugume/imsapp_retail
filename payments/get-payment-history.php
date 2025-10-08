<?php
require_once('../init/init.php');

$order_id = $_POST['order_id'];
$payments = $customerPayment->get_payment_history($order_id);

// Format payment dates for display
foreach($payments as $payment) {
    $payment->payment_date = date('M d, Y H:i', strtotime($payment->payment_date));
}

echo json_encode([
    "success" => true,
    "payments" => $payments
]);
?>

