<?php
require_once(__DIR__ . '/../init/init.php');

// Set JSON header
header('Content-Type: application/json');

try {
    $status = $data->updateCategory($_POST);
} catch (Exception $e) {
    error_log("Category Update Error: " . $e->getMessage());
    error_log("Category Update POST Data: " . json_encode($_POST));
    
    echo json_encode([
        'error' => 'error',
        'message' => 'A server error occurred while updating the category. Please try again.'
    ]);
    exit();
}

if($status == "Updated"){
    echo json_encode([
        'success' => 'success',
        'message' => 'Category updated successfully!'
    ]);
} else if($status == "Required"){
    echo json_encode([
        'error' => 'error',
        'message' => 'Category name is required.'
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
} else if($status == "Duplicate_Entry"){
    echo json_encode([
        'error' => 'error',
        'message' => 'A category with this name already exists.'
    ]);
} else if($status == "failed"){
    echo json_encode([
        'error' => 'error',
        'message' => 'Failed to update category. Please try again.'
    ]);
} else {
    echo json_encode([
        'error' => 'error',
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
}
?>
