$(document).ready(function(){
    var domain = "http://localhost/imsapp/";
    
    // Load initial data
    fetch_reconciliations();
    load_products();
    
    function fetch_reconciliations(){
        $.ajax({
            url: "stock/index.php",
            method: "POST",
            success: function(data){
                if(data != ""){
                    $("#table-data").html(data);
                    
                    // Initialize DataTable with export/print buttons
                    setTimeout(function(){
                        if($("#reconciliation_data").length && !$.fn.DataTable.isDataTable('#reconciliation_data')){
                            $("#reconciliation_data").DataTable({
                                "pageLength": 10,
                                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                "order": [[ 0, "desc" ]],
                                "responsive": true,
                                "searching": true,
                                "search": {
                                    "smart": true,
                                    "regex": false,
                                    "caseInsensitive": true
                                },
                                "language": {
                                    "search": "Search reconciliations:",
                                    "lengthMenu": "Show _MENU_ reconciliations per page",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ reconciliations",
                                    "infoEmpty": "Showing 0 to 0 of 0 reconciliations",
                                    "infoFiltered": "(filtered from _MAX_ total reconciliations)",
                                    "emptyTable": "No reconciliations found",
                                    "zeroRecords": "No matching reconciliations found"
                                },
                                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                       '<"row"<"col-sm-12"B>>' +
                                       '<"row"<"col-sm-12"tr>>' +
                                       '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                                "buttons": [
                                    {
                                        "extend": "excel",
                                        "text": "Export Excel",
                                        "className": "btn btn-success btn-sm",
                                        "title": "Stock Reconciliations - " + new Date().toLocaleDateString()
                                    },
                                    {
                                        "extend": "pdf",
                                        "text": "Export PDF",
                                        "className": "btn btn-danger btn-sm",
                                        "title": "Stock Reconciliations - " + new Date().toLocaleDateString(),
                                        "orientation": "landscape",
                                        "pageSize": "A4"
                                    },
                                    {
                                        "extend": "print",
                                        "text": "Print",
                                        "className": "btn btn-info btn-sm",
                                        "title": "Stock Reconciliations - " + new Date().toLocaleDateString(),
                                        "autoPrint": false
                                    },
                                    {
                                        "extend": "csv",
                                        "text": "Export CSV",
                                        "className": "btn btn-warning btn-sm",
                                        "title": "Stock Reconciliations - " + new Date().toLocaleDateString()
                                    }
                                ],
                                "columnDefs": [
                                    {
                                        "targets": [9, 10, 11], // Action columns (adjust based on user role)
                                        "searchable": false,
                                        "orderable": false
                                    }
                                ]
                            });
                        }
                    }, 100);
                }
            }
        });
    }
    
    function load_products(){
        $.ajax({
            url: "stock/get-products.php",
            method: "POST",
            dataType: "json",
            success: function(data){
                if(data.success){
                    var options = '<option value="">Select Product</option>';
                    $.each(data.products, function(index, product){
                        options += '<option value="' + product.pid + '" data-current-stock="' + product.stock + '">' + 
                                   product.product_name + ' (Current Stock: ' + product.stock + ')</option>';
                    });
                    $("#product_id").html(options);
                }
            }
        });
    }
    
    // Handle product selection to show current stock
    $(document).on('change', '#product_id', function(){
        var selectedOption = $(this).find('option:selected');
        var currentStock = selectedOption.data('current-stock');
        if(currentStock !== undefined){
            $("#physical_count").attr('placeholder', 'Current system stock: ' + currentStock);
        }
    });
    
    // Handle reconciliation form submission
    $("#reconciliation-form").on('submit', function(event){
        event.preventDefault();
        $form = $(this);
        submitForm($form);
    });
    
    function submitForm($form){
        $footer_loader = $('.modal-footer').html('<img src="./images/loader.gif" style="margin-right:250px;">');
        
        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: $form.serialize(),
            success: function(response){
                response = $.parseJSON(response);
                if(response.success){
                    setTimeout(function(){
                        window.location = response.url;
                    }, 100);
                    $("#reconciliation-form")[0].reset();
                    $("#reconciliationModal").modal('hide');
                    fetch_reconciliations();
                } else if(response.error){
                    $footer_loader.html(response.message);
                }
            }
        });
    }
    
    // Handle approval button click
    $(document).on('click', '.approve-btn', function(){
        var reconciliation_id = $(this).attr('reconciliation_id');
        load_reconciliation_details(reconciliation_id);
    });
    
    function load_reconciliation_details(reconciliation_id){
        $.ajax({
            url: "stock/get-reconciliation-details.php",
            method: "POST",
            data: {reconciliation_id: reconciliation_id},
            dataType: "json",
            success: function(data){
                if(data.success){
                    var details = data.reconciliation;
                    var difference = details.difference;
                    var difference_class = difference > 0 ? 'text-success' : (difference < 0 ? 'text-danger' : 'text-info');
                    var difference_text = difference > 0 ? '+' + difference : difference;
                    
                    var html = '<div class="row">' +
                               '<div class="col-md-6"><strong>Product:</strong> ' + details.product_name + '</div>' +
                               '<div class="col-md-6"><strong>System Stock:</strong> ' + details.system_stock + '</div>' +
                               '</div><br>' +
                               '<div class="row">' +
                               '<div class="col-md-6"><strong>Physical Count:</strong> ' + details.physical_count + '</div>' +
                               '<div class="col-md-6"><strong>Difference:</strong> <span class="' + difference_class + '">' + difference_text + '</span></div>' +
                               '</div><br>' +
                               '<div class="row">' +
                               '<div class="col-md-12"><strong>Created By:</strong> ' + details.created_by_name + '</div>' +
                               '</div><br>' +
                               '<div class="row">' +
                               '<div class="col-md-12"><strong>Created Date:</strong> ' + details.created_at + '</div>' +
                               '</div>';
                    
                    if(details.notes){
                        html += '<br><div class="row"><div class="col-md-12"><strong>Notes:</strong> ' + details.notes + '</div></div>';
                    }
                    
                    $("#reconciliation-details").html(html);
                    $("#approval_action").val('');
                }
            }
        });
    }
    
    // Handle approval submission
    $("#submit-approval").on('click', function(){
        var action = $("#approval_action").val();
        var reconciliation_id = $(".approve-btn").attr('reconciliation_id');
        
        if(!action){
            alert('Please select an action');
            return;
        }
        
        if(confirm('Are you sure you want to ' + action + ' this reconciliation?')){
            $.ajax({
                url: "stock/approve.php",
                method: "POST",
                data: {
                    reconciliation_id: reconciliation_id,
                    action: action
                },
                success: function(response){
                    response = $.parseJSON(response);
                    if(response.success){
                        $("#approvalModal").modal('hide');
                        fetch_reconciliations();
                        $("#msg").html(response.message);
                    } else {
                        $("#msg").html(response.message);
                    }
                }
            });
        }
    });
});





