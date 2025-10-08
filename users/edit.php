<?php


require_once('../init/init.php');

$edit_id = $_POST['edit_id'];

$data = $user->fetch_single_record($edit_id);
echo json_encode($data);
