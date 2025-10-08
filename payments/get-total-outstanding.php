<?php
require_once('../init/init.php');

$total = $customerPayment->get_total_outstanding();

echo json_encode([
    "success" => true,
    "total" => $total
]);
?>







