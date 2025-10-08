$(document).ready(function(){
    // Dynamic domain detection instead of hardcoded
    var domain = window.location.origin + '/imsapp/';
    
    fetch_category();
    
    function fetch_category(){
        $.ajax({
            url: domain + "category/fetch.php",
            method: "POST",
            data: {fetch_category: 1},
            success: function(data){
                var main = '<option value="0">Main Category</option>';
                var select = '<option value="">Select Category</option>';
                
                $("#maincategory").html(main + data);
                $("#category_id").html(select + data);
                $("#update-main-category").html(main + data);
            },
            error: function(xhr, status, error) {
                console.error('Fetch category error:', error);
                SweetAlertHandler.error('Failed to load categories. Please refresh the page.');
            }
        });
    }
    
    // Add Category Form Submission
    $("#category-form").on("submit", function(e){
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $("#category-btn");
        var originalText = submitBtn.text();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        
        // Clear previous errors
        $(".cat_error, .st").html('');
        $("input, select").removeClass('is-invalid');
        
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            dataType: "json",
            data: formData,
            success: function(data){
                if(data.success == 'success'){
                    SweetAlertHandler.success('Category added successfully!');
                    
                    // Reset form
                    $("#category-form")[0].reset();
                    $("#categoryModal").modal("hide");
                    
                    // Refresh category list
                    fetch_category();
                    
                    // Show success message in main area
                    $("#msg").html(data.message);
                    
                } else if(data.error == 'error'){
                    // Handle validation errors
                    if($("#category_name").val() == ""){
                        $("#category_name").addClass('is-invalid');
                        $("#cat_error").html('<span class="text-danger">Please enter category name</span>');
                    }
                    
                    if($("#status").val() == ""){
                        $("#status").addClass('is-invalid');
                        $("#e-status").html('<span class="text-danger">Please select status</span>');
                    }
                    
                    SweetAlertHandler.error(data.message);
                    
                } else if(data.exists == 'exists'){
                    $("#category_name").addClass('is-invalid');
                    $("#cat_error").html('<span class="text-danger">This category already exists! Please enter another one</span>');
                    SweetAlertHandler.warning(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Add category error:', error);
                SweetAlertHandler.error('Failed to add category. Please try again.');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // View Category
    $(document).on("click", ".view_category", function(){
        var cat_id = $(this).data("id");
        
        $.ajax({
            url: domain + "category/view.php",
            method: "POST",
            data: {cat_id: cat_id},
            dataType: "json",
            success: function(data){
                $("#main-id").text(data.main_cat);
                $("#main").text(data.main_category_name);
                $("#sub-id").text(data.cat_id);
                $("#sub").text(data.category_name);
                $("#st").text(data.status == 1 ? 'Active' : 'Inactive');
                $("#dt").text(data.created_at);
                $("#Cat-View-Modal").modal("show");
            },
            error: function(xhr, status, error) {
                console.error('View category error:', error);
                SweetAlertHandler.error('Failed to load category details.');
            }
        });
    });
    
    // Edit Category
    $(document).on("click", ".edit_category", function(){
        var cat_id = $(this).data("id");
        
        $.ajax({
            url: domain + "category/edit.php",
            method: "POST",
            data: {cat_id: cat_id},
            dataType: "json",
            success: function(data){
                $("#cat_id").val(data.cat_id);
                $("#update-main-category").html('<option value="'+data.main_cat+'">'+data.category_name+'</option>');
                $("#update-category-name").val(data.category_name);
                
                if(data.status == 1){
                    $("#update-status").html('<option value="1">Active</option><option value="0">Inactive</option>');
                } else {
                    $("#update-status").html('<option value="0">Inactive</option><option value="1">Active</option>');
                }
                
                $("#UpdatecategoryModal").modal("show");
            },
            error: function(xhr, status, error) {
                console.error('Edit category error:', error);
                SweetAlertHandler.error('Failed to load category for editing.');
            }
        });
    });
    
    // Update Category Form Submission
    $("#update-category-form").on("submit", function(e){
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find("#category-btn");
        var originalText = submitBtn.text();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        // Clear previous errors
        $("#up_error, #up-status").html('');
        $("input, select").removeClass('is-invalid');
        
        $.ajax({
            url: domain + "category/update.php",
            method: "POST",
            dataType: "json",
            data: formData,
            success: function(data){
                if(data.success == 'success'){
                    SweetAlertHandler.success('Category updated successfully!');
                    
                    // Reset form and close modal
                    $("#update-category-form")[0].reset();
                    $("#UpdatecategoryModal").modal("hide");
                    
                    // Refresh category list
                    fetch_category();
                    
                } else if(data.error == 'error'){
                    if($("#update-category-name").val() == ""){
                        $("#update-category-name").addClass('is-invalid');
                        $("#up_error").html('<span class="text-danger">Please enter category name</span>');
                    }
                    
                    SweetAlertHandler.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Update category error:', error);
                SweetAlertHandler.error('Failed to update category. Please try again.');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Delete Category
    $(document).on("click", ".delete_category", function(){
        var cat_id = $(this).data("id");
        var category_name = $(this).data("name");
        
        SweetAlertHandler.confirm(
            'Are you sure you want to delete the category "' + category_name + '"?',
            'Delete Category'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: domain + "category/delete.php",
                    method: "POST",
                    data: {cat_id: cat_id},
                    dataType: "json",
                    success: function(data){
                        if(data.success == 'success'){
                            SweetAlertHandler.success('Category deleted successfully!');
                            // Refresh category list
                            fetch_category();
                        } else {
                            SweetAlertHandler.error(data.message || 'Failed to delete category.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete category error:', error);
                        SweetAlertHandler.error('Failed to delete category. Please try again.');
                    }
                });
            }
        });
    });
    
    // Load category table with pagination
    function fetch_category_with_pagination(page = 1) {
        $.ajax({
            url: domain + "category/index.php",
            method: "POST",
            data: {page: page},
            success: function(data){
                $("#category-table").html(data);
            },
            error: function(xhr, status, error) {
                console.error('Load category table error:', error);
                SweetAlertHandler.error('Failed to load category list.');
            }
        });
    }
    
    // Initial load of category table
    fetch_category_with_pagination();
});
