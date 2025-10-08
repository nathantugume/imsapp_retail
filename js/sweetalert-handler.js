// Global SweetAlert Error Handler
class SweetAlertHandler {
    
    // Success message
    static success(message, title = 'Success!') {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonColor: '#28a745',
            timer: 2000,
            timerProgressBar: true
        });
    }
    
    // Error message
    static error(message, title = 'Error!') {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonColor: '#dc3545'
        });
    }
    
    // Warning message
    static warning(message, title = 'Warning!') {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            confirmButtonColor: '#ffc107'
        });
    }
    
    // Info message
    static info(message, title = 'Information') {
        return Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            confirmButtonColor: '#17a2b8'
        });
    }
    
    // Confirmation dialog
    static confirm(message, title = 'Are you sure?') {
        return Swal.fire({
            icon: 'question',
            title: title,
            text: message,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        });
    }
    
    // Loading state
    static loading(message = 'Please wait...') {
        return Swal.fire({
            title: message,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // Close loading
    static close() {
        Swal.close();
    }
    
    // AJAX error handler
    static handleAjaxError(xhr, status, error) {
        console.error('AJAX Error:', {xhr, status, error});
        
        let message = 'An unexpected error occurred. Please try again.';
        
        if (xhr.status === 0) {
            message = 'Network error. Please check your internet connection.';
        } else if (xhr.status === 404) {
            message = 'Requested resource not found.';
        } else if (xhr.status === 500) {
            message = 'Server error. Please try again later.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }
        
        this.error(message, 'Connection Error');
    }
    
    // Database connection error
    static databaseError() {
        return this.error('Database connection failed. Please contact administrator.', 'Database Error');
    }
    
    // Session expired
    static sessionExpired() {
        return Swal.fire({
            icon: 'warning',
            title: 'Session Expired',
            text: 'Your session has expired. Please login again.',
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Login'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php';
            }
        });
    }
    
    // Unauthorized access
    static unauthorized() {
        return this.warning('You are not authorized to access this resource.', 'Access Denied');
    }
}

// Global AJAX error handler
$(document).ajaxError(function(event, xhr, settings, error) {
    SweetAlertHandler.handleAjaxError(xhr, xhr.status, error);
});

// Global form validation
function validateForm(formId, requiredFields = []) {
    let errors = [];
    
    requiredFields.forEach(field => {
        const element = $(`#${field}`);
        if (!element.val() || element.val().trim() === '') {
            errors.push(`${field.charAt(0).toUpperCase() + field.slice(1)} is required`);
            element.addClass('is-invalid');
        } else {
            element.removeClass('is-invalid');
        }
    });
    
    if (errors.length > 0) {
        SweetAlertHandler.validationError(errors);
        return false;
    }
    
    return true;
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show loading button
function showLoadingButton(buttonId, originalText = 'Submit') {
    const button = $(`#${buttonId}`);
    button.prop('disabled', true);
    button.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
    return button;
}

// Hide loading button
function hideLoadingButton(buttonId, originalText = 'Submit') {
    const button = $(`#${buttonId}`);
    button.prop('disabled', false);
    button.html(originalText);
    return button;
}
