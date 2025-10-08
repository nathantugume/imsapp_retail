<?php

require_once("../init/init.php");

// Set content type to JSON
header('Content-Type: application/json');

try {
    $product = new Product();
    $status = $product->update_product($_POST);

    switch($status) {
        case "Product_Updated":
            echo json_encode([
                'status' => 'success',
                'message' => 'The product "' . htmlspecialchars($_POST['update_product_name']) . '" has been updated successfully.',
                'data' => [
                    'product_name' => $_POST['update_product_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        case "Required":
            echo json_encode([
                'status' => 'error',
                'message' => 'Please fill in all required fields: Product Name, Stock, and Price.',
                'code' => 'REQUIRED_FIELDS_MISSING',
                'details' => 'All marked fields are mandatory for product updates.'
            ]);
            break;
            
        case "Invalid_Name":
            echo json_encode([
                'status' => 'error',
                'message' => 'Product name must be between 3-50 characters and contain only letters, numbers, spaces, and hyphens.',
                'code' => 'INVALID_PRODUCT_NAME',
                'details' => 'Please check the product name format and try again.'
            ]);
            break;
            
        case "Connection_Error":
            echo json_encode([
                'status' => 'error',
                'message' => 'Unable to connect to the database. Please check your connection and try again.',
                'code' => 'DATABASE_CONNECTION_ERROR',
                'details' => 'The system cannot reach the database server. Please contact your administrator if this persists.'
            ]);
            break;
            
        case "Database_Error":
            echo json_encode([
                'status' => 'error',
                'message' => 'A database error occurred while updating the product.',
                'code' => 'DATABASE_ERROR',
                'details' => 'Please try again. If the problem continues, contact support.'
            ]);
            break;
            
        case "Duplicate_Entry":
            echo json_encode([
                'status' => 'error',
                'message' => 'A product with this name already exists.',
                'code' => 'DUPLICATE_ENTRY',
                'details' => 'Please choose a different product name or update the existing product.'
            ]);
            break;
            
        case "Not_Updated":
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update the product. Please try again or contact support if the problem persists.',
                'code' => 'UPDATE_FAILED',
                'details' => 'The update operation did not complete successfully.'
            ]);
            break;
            
        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
                'code' => 'UNKNOWN_ERROR',
                'details' => 'Please contact support if this error continues to occur.'
            ]);
            break;
    }
    
} catch (Exception $e) {
    // Log the full error for debugging
    error_log("Product Update Error: " . $e->getMessage());
    error_log("Product Update Error Trace: " . $e->getTraceAsString());
    error_log("Product Update POST Data: " . json_encode($_POST));
    
    // Determine error type and provide appropriate response
    $errorMessage = 'A server error occurred. Please try again later.';
    $errorCode = 'SERVER_ERROR';
    $errorDetails = 'An unexpected error occurred while processing your request.';
    
    if (strpos($e->getMessage(), 'Connection') !== false) {
        $errorMessage = 'Database connection failed. Please check your connection and try again.';
        $errorCode = 'DATABASE_CONNECTION_ERROR';
        $errorDetails = 'The system cannot connect to the database. Please contact your administrator.';
    } elseif (strpos($e->getMessage(), 'PDO') !== false) {
        $errorMessage = 'Database operation failed. Please try again.';
        $errorCode = 'DATABASE_OPERATION_ERROR';
        $errorDetails = 'There was an issue with the database operation. Please try again.';
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $errorMessage,
        'code' => $errorCode,
        'details' => $errorDetails,
        'debug' => (defined('DEBUG') && DEBUG) ? $e->getMessage() : null
    ]);
}

exit();
?>