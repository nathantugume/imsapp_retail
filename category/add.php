<?php
require_once("../init/init.php");

// Set JSON header
header('Content-Type: application/json');

$status = $data->addCategory($_POST);

if($status == "MainCategory"){
    echo json_encode([
        'success' => 'success',
        'message' => 'Main category has been added successfully!'
    ]);
} else if($status == "Subcategory"){
    echo json_encode([
        'success' => 'success',
        'message' => 'Subcategory has been added successfully!'
    ]);
} else if($status == "Required"){
    echo json_encode([
        'error' => 'error',
        'message' => 'All fields are required. Please fill in all the fields.'
    ]);
} else if($status == "Invalid_Name"){
    echo json_encode([
        'error' => 'error',
        'message' => 'Category name is not valid. Minimum 3 characters required.'
    ]);
} else if($status == "Cat_Exists"){
    echo json_encode([
        'error' => 'error',
        'exists' => 'exists',
        'message' => 'Category name "' . $_POST['category_name'] . '" already exists.'
    ]);
} else {
    echo json_encode([
        'error' => 'error',
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
}
?>
