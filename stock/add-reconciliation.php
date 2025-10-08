<?php
require_once('../init/init.php');

$response = $stockReconciliation->create_reconciliation($_POST);

if($response === "Reconciliation_Created"){
    echo json_encode([
        "success" => true,
        "message" => '<div class="alert alert-success text-success text-center">Stock reconciliation created successfully!</div>',
        "url" => "stock-reconciliation.php"
    ]);
} else if($response === "Required"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Please fill in all required fields.</div>'
    ]);
} else if($response === "Product_Not_Found"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Product not found.</div>'
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Failed to create reconciliation. Please try again.</div>'
    ]);
}
?>







