<?php

require_once("../init/init.php");

if(isset($_POST['fetch_category'])){
	$rows = $data->fetch_all_category($_POST);
	if(!empty($rows)){
		foreach ($rows as $data) {
		//echo $data->category_name;
		// echo json_encode($data);
				echo '<option value='.$data->cat_id.'>'.ucwords($data->category_name).'</option>';
		}
	}else{

		echo '<option>No Category Found </option>';
	}
	exit();
    
}



