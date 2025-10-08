<?php

require_once("../init/init.php");

$row = $brand->fetch_single_brand($_POST);

echo json_encode($row);




