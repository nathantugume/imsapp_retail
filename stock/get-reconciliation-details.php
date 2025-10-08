<?php
require_once('../init/init.php');

$reconciliation_id = $_POST['reconciliation_id'];

$sql = "SELECT sr.*, p.product_name, u.name as created_by_name 
        FROM stock_reconciliations sr 
        JOIN products p ON sr.product_id = p.pid 
        JOIN users u ON sr.created_by = u.id 
        WHERE sr.id = ?";

$stmt = $stockReconciliation->dbcon->connect()->prepare($sql);
$stmt->bindParam(1, $reconciliation_id, PDO::PARAM_INT);
$stmt->execute();
$reconciliation = $stmt->fetch(PDO::FETCH_OBJ);

if($reconciliation){
    echo json_encode([
        "success" => true,
        "reconciliation" => $reconciliation
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Reconciliation not found"
    ]);
}
?>







