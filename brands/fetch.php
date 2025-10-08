<?php

require_once("../init/init.php");

if(isset($_POST['fetch_brand'])){
	$brands = $product->fetch_all_brands();
    if(!empty($brands)){
    	foreach ($brands as $brand) {
    	  echo '<option value='.$brand->bid.'>'.ucwords($brand->brand_name).'</option>';
    	}
    }else{

    	echo '<option>No Category Found </option>';
    }
    exit();
}



