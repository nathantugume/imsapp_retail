<?php
require_once('../init/init.php');

$products = $stockReconciliation->get_products_for_reconciliation();

if(!empty($products)){
    echo json_encode([
        "success" => true,
        "products" => $products
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No products found"
    ]);
}
?>







