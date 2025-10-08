$(document).ready(function(){
    var domain = "http://localhost/imsapp/";
    
    // Load initial data
    fetch_outstanding_orders();
    load_outstanding_orders();
    load_total_outstanding();
    
    function fetch_outstanding_orders(){
        $.ajax({
            url: "payments/index.php",
            method: "POST",
            success: function(data){
                if(data != ""){
                    $("#table-data").html(data);
                    
                    // Initialize DataTable with export/print buttons
                    setTimeout(function(){
                        if($("#payments_data").length && !$.fn.DataTable.isDataTable('#payments_data')){
                            $("#payments_data").DataTable({
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
                                    "search": "Search payments:",
                                    "lengthMenu": "Show _MENU_ payments per page",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ payments",
                                    "infoEmpty": "Showing 0 to 0 of 0 payments",
                                    "infoFiltered": "(filtered from _MAX_ total payments)",
                                    "emptyTable": "No payments found",
                                    "zeroRecords": "No matching payments found"
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
                                        "title": "Customer Payments - " + new Date().toLocaleDateString()
                                    },
                                    {
                                        "extend": "pdf",
                                        "text": "Export PDF",
                                        "className": "btn btn-danger btn-sm",
                                        "title": "Customer Payments - " + new Date().toLocaleDateString(),
                                        "orientation": "landscape",
                                        "pageSize": "A4"
                                    },
                                    {
                                        "extend": "print",
                                        "text": "Print",
                                        "className": "btn btn-info btn-sm",
                                        "title": "Customer Payments - " + new Date().toLocaleDateString(),
                                        "autoPrint": false
                                    },
                                    {
                                        "extend": "csv",
                                        "text": "Export CSV",
                                        "className": "btn btn-warning btn-sm",
                                        "title": "Customer Payments - " + new Date().toLocaleDateString()
                                    }
                                ],
                                "columnDefs": [
                                    {
                                        "targets": [6], // Action column
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
    
    function load_outstanding_orders(){
        $.ajax({
            url: "payments/get-outstanding-orders.php",
            method: "POST",
            dataType: "json",
            success: function(data){
                if(data.success){
                    var options = '<option value="">Select Order</option>';
                    $.each(data.orders, function(index, order){
                        options += '<option value="' + order.invoice_no + '" data-due="' + order.due + '">' + 
                                   'Invoice #' + order.invoice_no + ' - ' + order.customer_name + 
                                   ' (Due: ugx ' + parseFloat(order.due).toFixed(2) + ')</option>';
                    });
                    $("#order_id").html(options);
                }
            }
        });
    }
    
    function load_total_outstanding(){
        $.ajax({
            url: "payments/get-total-outstanding.php",
            method: "POST",
            dataType: "json",
            success: function(data){
                if(data.success){
                    $("#total-outstanding").text('ugx ' + parseFloat(data.total).toFixed(2));
                }
            }
        });
    }
    
    // Handle order selection to show maximum amount
    $(document).on('change', '#order_id', function(){
        var selectedOption = $(this).find('option:selected');
        var dueAmount = selectedOption.data('due');
        if(dueAmount !== undefined){
            $("#amount_paid").attr('max', dueAmount);
            $("#max-amount").text('ugx ' + parseFloat(dueAmount).toFixed(2));
        } else {
            $("#amount_paid").removeAttr('max');
            $("#max-amount").text('ugx 0');
        }
    });
    
    // Handle payment form submission
    $("#payment-form").on('submit', function(event){
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
                    $("#payment-form")[0].reset();
                    $("#paymentModal").modal('hide');
                    fetch_outstanding_orders();
                    load_outstanding_orders();
                    load_total_outstanding();
                } else if(response.error){
                    $footer_loader.html(response.message);
                }
            }
        });
    }
    
    // Handle view payments button click
    $(document).on('click', '.view-payments-btn', function(){
        var order_id = $(this).attr('order_id');
        load_payment_history(order_id);
    });
    
    function load_payment_history(order_id){
        $.ajax({
            url: "payments/get-payment-history.php",
            method: "POST",
            data: {order_id: order_id},
            dataType: "json",
            success: function(data){
                if(data.success){
                    var html = '<h4>Payment History for Order #' + order_id + '</h4>';
                    if(data.payments.length > 0){
                        html += '<table class="table table-bordered">' +
                                '<thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Recorded By</th><th>Notes</th></tr></thead>' +
                                '<tbody>';
                        
                        $.each(data.payments, function(index, payment){
                            html += '<tr>' +
                                    '<td>' + payment.payment_date + '</td>' +
                                    '<td>ugx ' + parseFloat(payment.amount_paid).toFixed(2) + '</td>' +
                                    '<td>' + payment.payment_method + '</td>' +
                                    '<td>' + payment.created_by_name + '</td>' +
                                    '<td>' + (payment.notes || '-') + '</td>' +
                                    '</tr>';
                        });
                        
                        html += '</tbody></table>';
                    } else {
                        html += '<p class="text-muted">No payments recorded yet.</p>';
                    }
                    
                    $("#payment-history-content").html(html);
                }
            }
        });
    }
});

