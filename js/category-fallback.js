$(document).ready(function(){
    // More reliable domain detection
    var domain = window.location.origin + '/imsapp/';
    
    // Fallback SweetAlert if not available
    if (typeof SweetAlertHandler === 'undefined') {
        window.SweetAlertHandler = {
            success: function(message, title) {
                alert(title + ': ' + message);
            },
            error: function(message, title) {
                alert(title + ': ' + message);
            },
            warning: function(message, title) {
                alert(title + ': ' + message);
            },
            confirm: function(message, title) {
                return new Promise(function(resolve) {
                    resolve({isConfirmed: confirm(title + ': ' + message)});
                });
            }
        };
    }
    
    fetch_category();
    
    function fetch_category(){
        console.log('Fetching categories from:', domain + "category/fetch.php");
        $.ajax({
            url: domain + "category/fetch.php",
            method: "POST",
            data: {fetch_category: 1},
            success: function(data){
                console.log('Categories loaded successfully');
                var main = '<option value="0">Main Category</option>';
                var select = '<option value="">Select Category</option>';
                
                $("#maincategory").html(main + data);
                $("#category_id").html(select + data);
                $("#update-main-category").html(main + data);
            },
            error: function(xhr, status, error) {
                console.error('Fetch category error:', error);
                console.error('URL attempted:', domain + "category/fetch.php");
                console.error('Status:', xhr.status);
                SweetAlertHandler.error('Failed to load categories. Please refresh the page.');
            }
        });
    }
    
    // Load category table with pagination
    function fetch_category_with_pagination(page = 1) {
        console.log('Loading category table from:', domain + "category/index.php");
        $.ajax({
            url: domain + "category/index.php",
            method: "POST",
            data: {page: page},
            success: function(data){
                console.log('Category table loaded successfully');
                $("#category-table").html(data);
            },
            error: function(xhr, status, error) {
                console.error('Load category table error:', error);
                console.error('URL attempted:', domain + "category/index.php");
                console.error('Status:', xhr.status);
                SweetAlertHandler.error('Failed to load category list.');
            }
        });
    }
    
    // Initial load of category table
    fetch_category_with_pagination();
    
    // Debug: Log the detected domain
    console.log('Detected domain:', domain);
});
