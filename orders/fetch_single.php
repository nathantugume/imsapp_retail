<?php

require_once("../init/init.php");

$rows = $order->fetch_single_product($_POST);

echo json_encode($rows);