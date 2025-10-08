$(document).ready(function(){
	var domain = "http://localhost/imsapp/";
	add_new_row();
	$(document).on("click","#add", function(event){
		event.preventDefault();
		add_new_row();
	});

	function add_new_row(){
		$.ajax({
			url:"orders/fetch.php",
			method:"POST",
			// dataType:"JSON",
			data:{add_row:1},
			success:function(data){
				$("#invoice_item").append(data);
				// console.log(data);
				var n =1;
				$(".serial_no").each(function(){
					$(this).html(n++);
				})
				
				// Initialize Select2 on newly added product dropdown
				$(".pid").last().select2({
					placeholder: "Search and select product",
					allowClear: true,
					width: '100%',
					matcher: function(params, data) {
						// If there are no search terms, return all data
						if ($.trim(params.term) === '') {
							return data;
						}
						
						// Do a case-insensitive search
						if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
							return data;
						}
						
						// Return null if no match
						return null;
					}
				});
			}
		})
	}

	$("#remove").click(function(event){
		event.preventDefault();
		$("#invoice_item").children('tr:last').remove();
		calculate(0,0);

	})

	$("#invoice_item").on("change",".pid", function(){
			var pid = $(this).val();
			var tr = $(this).parent().parent();
			$.ajax({
				url:"orders/fetch_single.php",
				method:"POST",
				dataType:"JSON",
				data:{pid:pid},
				success:function(data){
					if(data.stock==0){
						tr.find(".stock").css('border','2px solid red');
					    tr.find(".stock").val(data.stock);
					    alert("This product is out of stock");
					}else{
					    tr.find(".stock").val(data.stock);
					    tr.find(".stock").css('border','');
        				$(".modal-footer").html('');
					}
					tr.find(".order_qty").val(1);
					tr.find(".price").val(data.price);
					tr.find(".total_amount").html(tr.find(".order_qty").val() * tr.find(".price").val());
					tr.find(".product_name").val(data.product_name);
					calculate(0,0);
					// console.log(data);
				}

			})

	})

	$("#invoice_item").on("keyup",".order_qty", function(){
			var order_qty = $(this);
			var tr = $(this).parent().parent();
			if(isNaN(order_qty.val())){
				alert("This is not valid quantity");
				order_qty.val(1);
			}else{
				if((order_qty.val() -0) > (tr.find(".stock").val() -0)){
					alert("Sorry ! That much of quantity not available");
					order_qty.val(1);
				}else{
					tr.find(".total_amount").html(order_qty.val() * tr.find(".price").val());
					calculate(0,0)
				}
			}
	})


	function calculate(dis, paid){
		var subtotal  = 0;
		var gst       = 0;
		var net_total = 0;
		var discount  = dis;
		var paid_amt  = paid;
		var due       = 0;
		$(".total_amount").each(function(){
			subtotal = subtotal + ($(this).html() * 1);
			// alert(subtotal)
		});
	//	gst = Math.round(0.18 * subtotal);
	   // net_total = gst + subtotal;
	    net_total = subtotal - discount;
	    due = net_total - paid_amt;

		$("#subtotal").val(subtotal);
		$("#gst").val(gst);
		$("#discount").val(discount);
		$("#net_total").val(net_total)
		$("#paid").val(paid);
		$("#due").val(due);

	}

	$("#discount").click(function(){
		$("#discount").val('');
	});

	$("#discount").keyup(function(){
		// $("#discount").val(discount);
		var discount = $(this).val();
		if(isNaN(discount)){
			alert("Inavlid discount amount ");
			$("#discount").val(0);
		}else if(discount < 0){
			alert("Inavlid discount amount entered ? ");
			$("#discount").val(0);
			calculate(0,0);
		}else{
			//$("#net_total").val(net_total)
			calculate(discount,0);
		}

	})
	$("#paid").click(function(){
		$("#paid").val('');
	});

	$("#paid").keyup(function(){
		var paid = $(this).val();
		var net_total = $("#net_total").val();
		if(isNaN(paid)){
			alert("Inavlid paid amount entered");
			$("#paid").val(0);
		}else if(paid < 0){
			alert("Inavlid paid amount entered ?");
			$("#paid").val(0);
			calculate(0,0);
		}else if((paid-0) > (net_total-0)){
			alert("paid amount can not be more than net total amount ?");
			$("#paid").val(0);
			calculate(0,0);
		}else{
			var discount = $("#discount").val();
			calculate(discount,paid);
			// alert(discount);
		}
	})

	// Prevent double submission
	var isSubmitting = false;

	$("#order-form-data").on("submit", function(){
		var tr = $(this).parent().parent();
		
		// Prevent double submission
		if (isSubmitting) {
			alert("Order is being processed. Please wait...");
			return false;
		}
		
		if($("#customer_name").val()==''){
		   $("#customer_name").addClass("border-danger");

		}else if($("#address").val()==''){
		   $("#address").addClass("border-danger");
           $('#a_error').html("<span class='text-danger'>Please enter the address ? </span>");

		}else if($('#product').val()==''){
           $('#product').addClass("border-danger");
           alert("Please select product name ?");

        }else if($('#discount').val()==''){
           $('#discount').addClass("border-danger");
           $('#d_error').html("<span class='text-danger'>Please enter discount amount ? </span>");

        }else if($('#paid').val()==''){
           $('#paid').addClass("border-danger");
           $('#paid_error').html("<span class='text-danger'>Please enter paid amount ? </span>");

        }else{
        	// Set submitting flag
        	isSubmitting = true;
        	
        	// Disable submit button
        	$("#order_form").prop("disabled", true).val("Processing...");
        	
        	//var name = $("#customer_name").val();
        	var $form = $("#order-form-data").serialize();
        	$.ajax({
        		url:"orders/add.php",
        		method:"POST",
        		dataType:"JSON",
        		data:$("#order-form-data").serialize(),
        		success:function(data){
        			if(data.success){
        				$(".modal-footer").html(data.message);
        				var id = data.invoice_no;
        				var name = data.name;
        				if(confirm("Do you want to print invoice ?")){
							$.ajax({
					    		url:"orders/invoice.php",
					    		method:"POST",
					    		data:$("#order-form-data").serialize() + "&id="+id,
					    		success:function(data){
									alert("The order invoice has been generated ! check your invoice folder");
									$("#order-msg").html('<div class="alert alert-success text-success text-center alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>The order invoice has been generated ! check your invoice folder</div>')
									window.location.href=domain+"Invoices/invoice_"+name+".pdf";   				  
									// console.log(data);
					    		}
				    		});

						}else{
							alert("No");
							window.location.href=domain+"order.php";
					    }
					$("#order-form-data").trigger("reset");
        			tr.find(".total_amount").html('0');				  

        			}else if(data.error){
        				$(".modal-footer").html(data.message);
        				// Re-enable button on error
        				isSubmitting = false;
        				$("#order_form").prop("disabled", false).val("Save Order and Invoice");
        			}
        			//console.log(data.invoice_no);
        		},
        		error: function(xhr, status, error) {
        			// Re-enable button on AJAX error
        			isSubmitting = false;
        			$("#order_form").prop("disabled", false).val("Save Order and Invoice");
        			alert("Error submitting order. Please try again.");
        			console.error("AJAX Error:", error);
        		}
        	})
        }

	})


// =======================================================
    fetch_all_the_orders();
    function  fetch_all_the_orders(page){
    	console.log("=== FETCHING ORDERS ===");
    	console.log("Page:", page);
    	
    	$.ajax({
    		url:"orders/index.php",
    		method:"POST",
    		// dataType:"JSON",
    		data:{page:page},
    		beforeSend: function() {
    			console.log("Loading orders for page:", page);
    		},
    		success:function(data){
    			console.log("Orders loaded successfully");
    			console.log("Orders HTML length:", data.length);
    			$("#order-table").html(data);
    			
    			// Initialize DataTable with export/print buttons
    			setTimeout(function(){
    				if($("#product_data").length && !$.fn.DataTable.isDataTable('#product_data')){
    					$("#product_data").DataTable({
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
    							"search": "Search orders:",
    							"lengthMenu": "Show _MENU_ orders per page",
    							"info": "Showing _START_ to _END_ of _TOTAL_ orders",
    							"infoEmpty": "Showing 0 to 0 of 0 orders",
    							"infoFiltered": "(filtered from _MAX_ total orders)",
    							"emptyTable": "No orders found",
    							"zeroRecords": "No matching orders found"
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
    								"title": "Orders List - " + new Date().toLocaleDateString()
    							},
    							{
    								"extend": "pdf",
    								"text": "Export PDF",
    								"className": "btn btn-danger btn-sm",
    								"title": "Orders List - " + new Date().toLocaleDateString(),
    								"orientation": "landscape",
    								"pageSize": "A4"
    							},
    							{
    								"extend": "print",
    								"text": "Print",
    								"className": "btn btn-info btn-sm",
    								"title": "Orders List - " + new Date().toLocaleDateString(),
    								"autoPrint": false
    							},
    							{
    								"extend": "csv",
    								"text": "Export CSV",
    								"className": "btn btn-warning btn-sm",
    								"title": "Orders List - " + new Date().toLocaleDateString()
    							}
    						],
    						"columnDefs": [
    							{
    								"targets": [6, 7], // Action columns
    								"searchable": false,
    								"orderable": false
    							}
    						]
    					});
    				}
    			}, 100);
    			
    			// Log how many view buttons are now available
    			var viewButtons = $(".view-btn").length;
    			console.log("Number of view buttons found:", viewButtons);
    			
    			// Log each view button's view-id
    			$(".view-btn").each(function(index) {
    				console.log("View button", index + 1, "view-id:", $(this).attr("view-id"));
    			});
    		},
    		error: function(xhr, status, error) {
    			console.error("Error loading orders:", error);
    		}
    	})
    }

    $(document).on("click",".page_no", function(){
    	var page = $(this).attr("id");
    	// alert(page);
    	fetch_all_the_orders(page);
    })

    $(document).on("click",".prev", function(){
    	var prev = $(this).attr("prev-id");
    	fetch_all_the_orders(prev);

    })

    $(document).on("click",".next", function(){
    	var next = $(this).attr("next-id");
    	fetch_all_the_orders(next);

    })

    $(document).on("click",".view-btn", function(){
    	console.log("=== VIEW BUTTON CLICKED ===");
    	
    	var view_id = $(this).attr("view-id");
    	console.log("View ID:", view_id);
    	
    	$("#Order-View-Modal").modal("show");
    	
    	// Clear all previous data from the modal
    	$("#order-products-tbody").empty();
    	$("#invcno, #cn, #add, #sbt, #gst, #dis, #ntt, #pd, #due, #pm, #od, .total").html('');
    	
    	console.log("Making AJAX request to orders/view.php with view_id:", view_id);
    	
    	$.ajax({
    		url:"orders/view.php",
    		method:"POST",
    		dataType:"JSON",
    		data:{view_id:view_id},
    		beforeSend: function() {
    			console.log("AJAX request started for view_id:", view_id);
    		},
    		success:function(data){
                if(data && data.length > 0){
                	var len = data.length;
                	var list = '';
                	
                	// Set order details from first record (order info is same for all products)
                	$("#invcno").html(data[0].invoice_no);
                	$("#cn").html('<span style="text-transform:capitalize;">'+data[0].customer_name+'</span>');
					$("#add").html('<span style="text-transform:capitalize;">'+data[0].address+'</span>');
					$("#sbt").html(data[0].subtotal+'.00');
					$("#dis").html(data[0].discount+'.00');
					$("#ntt").html(data[0].net_total+'.00');
					$("#pd").html(data[0].paid+'.00');
					$("#due").html(data[0].due+'.00');
					$("#pm").html(data[0].payment_method);
					$("#od").html(data[0].order_date);
					$(".total").html(data[0].net_total+'.00');
                	
                	// Build product list
                	for(var x=0; x<len; x++){
    					if(data[x].id && data[x].product_name){
                        	var row = '<tr><td>'+data[x].id+'</td><td>'+data[x].product_name+'</td><td class="text-center">'+data[x].order_qty+'</td><td class="text-right">'+data[x].price_per_item+'.00</td></tr>';
                        	list += row;
    					}
                	}
                	
                	// Insert product list into table
                	if(list !== ''){
                		$("#order-products-tbody").html(list);
                	} else {
                		// Handle orders with no products
                		$("#order-products-tbody").html('<tr><td colspan="4" class="text-center text-muted">No products found for this order</td></tr>');
                	}
                }
    		},
    		error: function(xhr, status, error) {
    			console.error("AJAX Error:", error);
    		}
    	})
    })

    $(document).on("click",".pdf-btn", function(){
    		var pdf_id = $(this).attr("pdf-id");
    		var name = $(this).attr("name");
    		$.ajax({
    			url:"orders/generate_pdf.php",
    			method:"POST",
    			dataType:"JSON",
    			data:{pdf_id:pdf_id},
    			success:function(response){
    				if(response.success){
    					alert("The invoice has been generated in PDF format");
    					window.location.href = domain + response.url;
    				} else {
    					alert("Error generating PDF: " + response.error);
    				}
    			},
    			error: function(xhr, status, error) {
    				console.error("PDF Generation Error:", xhr.responseText);
    				alert("Error generating PDF. Please check console for details.");
    			}
    		})
    })

		
})

