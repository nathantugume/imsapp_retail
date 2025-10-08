// Enhanced Product Form JavaScript
$(document).ready(function() {
    // Initialize form validation and profit calculation
    initializeProductForms();
    
    // Auto-format currency inputs
    formatCurrencyInputs();
    
    // Real-time profit calculation for add form
    $("#buying_price, #price").on('input', calculateProfit);
    
    // Real-time profit calculation for edit form
    $("#update_buying_price, #update_price").on('input', calculateUpdateProfit);
    
    // Real-time stock calculation for stock modal
    $(document).on('input', '#Stock-Modal #stock', calculateStockTotal);
    
    // Form submission handlers
    $("#product_form").on('submit', handleAddProductSubmit);
    $("#update_form").on('submit', handleUpdateProductSubmit);
});

function initializeProductForms() {
    // Add form validation
    $("#product_name, #stock, #buying_price, #price").on('blur', validateField);
    $("#update_product_name, #update_stock, #update_buying_price, #update_price").on('blur', validateUpdateField);
    
    // Auto-format inputs
    $("#stock, #update_stock").on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
}

function formatCurrencyInputs() {
    // Format currency inputs with ugx symbol
    $("input[type='number'][name*='price']").each(function() {
        $(this).on('input', function() {
            let value = parseFloat(this.value) || 0;
            if (value < 0) this.value = 0;
        });
    });
}

function calculateProfit() {
    var buyingPrice = parseFloat($("#buying_price").val()) || 0;
    var sellingPrice = parseFloat($("#price").val()) || 0;

    if (buyingPrice > 0 && sellingPrice > 0) {
        var profit = sellingPrice - buyingPrice;
        var margin = ((profit / sellingPrice) * 100).toFixed(2);

        $("#profit-amount").text("ugx" + profit.toFixed(2));
        $("#profit-margin").text(margin + "%");

        if (profit > 0) {
            $("#profit-preview").removeClass("alert-warning").addClass("alert-success").show();
        } else if (profit < 0) {
            $("#profit-preview").removeClass("alert-success").addClass("alert-warning").show();
        } else {
            $("#profit-preview").hide();
        }
    } else {
        $("#profit-preview").hide();
    }
}

function calculateUpdateProfit() {
    var buyingPrice = parseFloat($("#update_buying_price").val()) || 0;
    var sellingPrice = parseFloat($("#update_price").val()) || 0;

    if (buyingPrice > 0 && sellingPrice > 0) {
        var profit = sellingPrice - buyingPrice;
        var margin = ((profit / sellingPrice) * 100).toFixed(2);

        $("#update-profit-amount").text("ugx" + profit.toFixed(2));
        $("#update-profit-margin").text(margin + "%");

        // Show profit preview and hide placeholder
        $("#update-profit-preview").show();
        $("#update-profit-placeholder").hide();

        // Update profit amount color based on profit/loss
        if (profit > 0) {
            $("#update-profit-amount").removeClass("text-danger").addClass("text-success");
            $("#update-profit-margin").removeClass("text-danger").addClass("text-info");
        } else if (profit < 0) {
            $("#update-profit-amount").removeClass("text-success").addClass("text-danger");
            $("#update-profit-margin").removeClass("text-info").addClass("text-danger");
        } else {
            $("#update-profit-amount").removeClass("text-success text-danger").addClass("text-muted");
            $("#update-profit-margin").removeClass("text-info text-danger").addClass("text-muted");
        }
    } else {
        // Hide profit preview and show placeholder
        $("#update-profit-preview").hide();
        $("#update-profit-placeholder").show();
    }
}

function validateField() {
    var field = $(this);
    var value = field.val().trim();
    var fieldName = field.attr('name');
    
    // Remove existing validation classes
    field.removeClass('is-valid is-invalid');
    
    // Validate based on field type
    if (fieldName === 'product_name') {
        if (value.length < 2) {
            field.addClass('is-invalid');
            showFieldError(field, 'Product name must be at least 2 characters long');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    } else if (fieldName === 'stock') {
        if (value < 0 || !value) {
            field.addClass('is-invalid');
            showFieldError(field, 'Stock quantity must be a positive number');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    } else if (fieldName === 'buying_price' || fieldName === 'price') {
        if (value <= 0 || !value) {
            field.addClass('is-invalid');
            showFieldError(field, 'Price must be greater than 0');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    }
}

function validateUpdateField() {
    var field = $(this);
    var value = field.val().trim();
    var fieldName = field.attr('name');
    
    // Remove existing validation classes
    field.removeClass('is-valid is-invalid');
    
    // Validate based on field type
    if (fieldName === 'update_product_name') {
        if (value.length < 2) {
            field.addClass('is-invalid');
            showFieldError(field, 'Product name must be at least 2 characters long');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    } else if (fieldName === 'update_stock') {
        if (value < 0 || !value) {
            field.addClass('is-invalid');
            showFieldError(field, 'Stock quantity must be a positive number');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    } else if (fieldName === 'update_buying_price' || fieldName === 'update_price') {
        if (value <= 0 || !value) {
            field.addClass('is-invalid');
            showFieldError(field, 'Price must be greater than 0');
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    }
}

function showFieldError(field, message) {
    var errorDiv = field.siblings('.field-error');
    if (errorDiv.length === 0) {
        errorDiv = $('<div class="field-error text-danger small mt-1"></div>');
        field.after(errorDiv);
    }
    errorDiv.text(message);
}

function hideFieldError(field) {
    field.siblings('.field-error').remove();
}

function validateForm() {
    var isValid = true;
    var requiredFields = ['product_name', 'stock', 'buying_price', 'price'];
    
    requiredFields.forEach(function(fieldName) {
        var field = $('#' + fieldName);
        var value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    });
    
    // Validate category and brand selection
    if (!$('#category_id').val()) {
        $('#category_id').addClass('is-invalid');
        showFieldError($('#category_id'), 'Please select a category');
        isValid = false;
    }
    
    if (!$('#brand_id').val()) {
        $('#brand_id').addClass('is-invalid');
        showFieldError($('#brand_id'), 'Please select a brand');
        isValid = false;
    }
    
    return isValid;
}

function validateUpdateForm() {
    var isValid = true;
    // Only require essential fields: product name, stock, and price
    var requiredFields = ['update_product_name', 'update_stock', 'update_price'];
    
    requiredFields.forEach(function(fieldName) {
        var field = $('#' + fieldName);
        var value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            field.addClass('is-valid');
            hideFieldError(field);
        }
    });
    
    // Validate category and brand selection
    if (!$('#update_category_id').val()) {
        $('#update_category_id').addClass('is-invalid');
        showFieldError($('#update_category_id'), 'Please select a category');
        isValid = false;
    }
    
    if (!$('#update_brand_id').val()) {
        $('#update_brand_id').addClass('is-invalid');
        showFieldError($('#update_brand_id'), 'Please select a brand');
        isValid = false;
    }
    
    // Validate buying price if provided
    var buyingPrice = $('#update_buying_price').val().trim();
    if (buyingPrice && (isNaN(buyingPrice) || parseFloat(buyingPrice) < 0)) {
        $('#update_buying_price').addClass('is-invalid');
        showFieldError($('#update_buying_price'), 'Buying price must be a valid positive number');
        isValid = false;
    } else if (buyingPrice) {
        $('#update_buying_price').addClass('is-valid');
        hideFieldError($('#update_buying_price'));
    }
    
    return isValid;
}

function handleAddProductSubmit(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields correctly.',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }
    
    // Show loading state
    var submitBtn = $("#product-btn");
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding Product...').prop('disabled', true);
    
    // Submit form via AJAX
    $.ajax({
        url: 'products/add.php',
        type: 'POST',
        data: $("#product_form").serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Reset form and close modal
                    $("#product_form")[0].reset();
                    $("#productModal").modal('hide');
                    // Refresh product table
                    fetch_all_products();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while adding the product.',
                confirmButtonColor: '#dc3545'
            });
        },
        complete: function() {
            // Restore button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function handleUpdateProductSubmit(e) {
    e.preventDefault();
    
    if (!validateUpdateForm()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields correctly.',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }
    
    // Show loading state
    var submitBtn = $("#update-product-btn");
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating Product...').prop('disabled', true);
    
    // Submit form via AJAX
    $.ajax({
        url: 'products/update.php',
        type: 'POST',
        data: $("#update_form").serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Updated!',
                    text: response.message,
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    // Close modal and reload page
                    $("#UpdateProductModal").modal('hide');
                    location.reload(); // Reload page to show updated data
                });
            } else {
                // Handle different error types
                var errorTitle = 'Update Failed';
                var errorMessage = response.message || 'An error occurred while updating the product.';
                
                // Customize error message based on error code
                if (response.code === 'REQUIRED_FIELDS_MISSING') {
                    errorTitle = 'Missing Required Fields';
                } else if (response.code === 'INVALID_PRODUCT_NAME') {
                    errorTitle = 'Invalid Product Name';
                } else if (response.code === 'UPDATE_FAILED') {
                    errorTitle = 'Update Failed';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr, status, error) {
            var errorMessage = 'An error occurred while updating the product.';
            
            // Try to parse error response if it's JSON
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // If not JSON, use default message
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: errorMessage,
                confirmButtonColor: '#dc3545'
            });
        },
        complete: function() {
            // Restore button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Enhanced profit calculation with better UX
function calculateProfit() {
    var buyingPrice = parseFloat($("#buying_price").val()) || 0;
    var sellingPrice = parseFloat($("#price").val()) || 0;

    if (buyingPrice > 0 && sellingPrice > 0) {
        var profit = sellingPrice - buyingPrice;
        var margin = ((profit / sellingPrice) * 100).toFixed(2);

        $("#profit-amount").text("ugx" + profit.toFixed(2));
        $("#profit-margin").text(margin + "%");

        if (profit > 0) {
            $("#profit-preview").removeClass("alert-warning").addClass("alert-success").show();
        } else if (profit < 0) {
            $("#profit-preview").removeClass("alert-success").addClass("alert-warning").show();
        } else {
            $("#profit-preview").hide();
        }
    } else {
        $("#profit-preview").hide();
    }
}

// Auto-formatting for edit form inputs
$("#update_buying_price, #update_price").on('input', function() {
    var value = parseFloat(this.value) || 0;
    if (value < 0) this.value = 0;
});

$("#update_stock").on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});







// Stock calculation functions
function calculateStockTotal() {
    var currentStock = parseInt($("#inventory").val()) || 0;
    var newQuantity = parseInt($("#Stock-Modal #stock").val()) || 0;
    var totalStock = currentStock + newQuantity;
    
    console.log("Stock Calculation:", {
        currentStock: currentStock,
        newQuantity: newQuantity,
        totalStock: totalStock
    });
    
    // Update stock summary display
    $("#sub-stock strong").text(totalStock);
    
    // Update preview alert
    if (newQuantity > 0) {
        $("#new-quantity").text(newQuantity);
        $("#current-quantity").text(currentStock);
        $("#stock-preview").show();
    } else {
        $("#stock-preview").hide();
    }
}



// Initialize stock modal functionality
$(document).ready(function() {
    // Handle stock button click to populate modal data
    $(document).on('click', '.stock-btn', function(e) {
        e.preventDefault();
        var productId = $(this).attr('stock-id');
        var productName = $(this).closest('tr').find('td:eq(2)').text().trim(); // Product name column
        var currentStock = $(this).closest('tr').find('td:eq(4)').text().trim(); // Stock column
        
        // Populate modal fields
        $("#sid").val(productId);
        $("#product-name-stock").val(productName);
        $("#inventory").val(currentStock);
        
        // Reset calculation displays
        $("#sub-stock strong").text(currentStock);
        $("#stock-preview").hide();
        
        // Show modal
        $("#Stock-Modal").modal('show');
    });
    
    // Reset stock form when modal is closed
    $("#Stock-Modal").on('hidden.bs.modal', function() {
        $("#stock_form")[0].reset();
        $(".stock-modal").html('');
        $("#sub-stock strong").text('0');
        $("#stock-preview").hide();
    });
    
    // Initialize stock modal when shown
    $("#Stock-Modal").on('shown.bs.modal', function() {
        // Focus on quantity input
        $("#Stock-Modal #stock").focus();
        
        // Initialize calculation with current values
        calculateStockTotal();
        
        // Bind input event for real-time calculation
        $("#Stock-Modal #stock").off('input').on('input', calculateStockTotal);
    });
});
