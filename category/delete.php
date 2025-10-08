<?php
require_once(__DIR__ . '/../init/init.php');

// Set JSON header
header('Content-Type: application/json');

if(isset($_POST['cat_id'])){
    try {
        $status = $data->deleteCategory($_POST['cat_id']);
    } catch (Exception $e) {
        error_log("Category Delete Error: " . $e->getMessage());
        error_log("Category Delete POST Data: " . json_encode($_POST));
        
        echo json_encode([
            'error' => 'error',
            'message' => 'A server error occurred while deleting the category. Please try again.'
        ]);
        exit();
    }
    
    if($status == "Deleted_Category"){
        echo json_encode([
            'success' => 'success',
            'message' => 'Category deleted successfully!'
        ]);
    } else if($status == "Dependent_Category"){
        echo json_encode([
            'error' => 'error',
            'message' => 'Cannot delete category. It has dependent subcategories or products.'
        ]);
    } else if($status == "Connection_Error"){
        echo json_encode([
            'error' => 'error',
            'message' => 'Database connection failed. Please try again.'
        ]);
    } else if($status == "Database_Error"){
        echo json_encode([
            'error' => 'error',
            'message' => 'Database error occurred. Please try again.'
        ]);
    } else {
        echo json_encode([
            'error' => 'error',
            'message' => 'Failed to delete category. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'error',
        'message' => 'Category ID is required.'
    ]);
}
?>
