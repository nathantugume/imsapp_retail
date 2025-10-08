<?php
require_once("../init/init.php");

// Set JSON header
header('Content-Type: application/json');

if(isset($_POST['cat_id'])){
    $category_data = $data->fetch_single_category($_POST['cat_id']);
    
    if($category_data){
        echo json_encode($category_data);
    } else {
        echo json_encode([
            'error' => 'error',
            'message' => 'Category not found.'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'error',
        'message' => 'Category ID is required.'
    ]);
}
?>
