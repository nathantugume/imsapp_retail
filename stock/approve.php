<?php
require_once('../init/init.php');

$reconciliation_id = $_POST['reconciliation_id'];
$action = $_POST['action'];

$response = $stockReconciliation->approve_reconciliation($reconciliation_id, $action);

if($response === "Reconciliation_approved" || $response === "Reconciliation_rejected"){
    echo json_encode([
        "success" => true,
        "message" => '<div class="alert alert-success text-success text-center">Reconciliation ' . $action . ' successfully!</div>',
        "url" => "stock-reconciliation.php"
    ]);
} else if($response === "Invalid_Action"){
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Invalid action selected.</div>'
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => '<div class="alert alert-danger text-danger text-center">Failed to process reconciliation. Please try again.</div>'
    ]);
}
?>







