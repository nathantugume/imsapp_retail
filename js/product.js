$(document).ready(function(){
		var domain = "http://localhost/imsapp/";
		fetch_category();
		function  fetch_category(){
			$.ajax({
				url:domain+"category/fetch.php",
				method:"POST",
				data:{fetch_category:1},
				// dataType:"json",
				success:function(data){			
					var select = '<option value="">Select Category</option>'
					$("#category_id").html(select+data);
					$("#update_category_id").html(select+data);
					console.log("Categories loaded:", data);
				},
				error: function(xhr, status, error) {
					console.error("Error loading categories:", error);
				}
			})
		}

// =============Fetch Brands
		fetch_brands();
		function  fetch_brands(){
			$.ajax({
				url:domain+"products/fetch.php",
				method:"POST",
				// dataType:"json",
				data:{fetch_brand:1},
				success:function(data){
					var select = '<option value="">Select Brand</option>'
					$("#brand_id").html(select+data);
					$("#update_brand_id").html(select+data);
					console.log("Brands loaded:", data);
				},
				error: function(xhr, status, error) {
					console.error("Error loading brands:", error);
				}
			})
		}

// Fetch all products 
		fetch_all_products();
        function fetch_all_products(page){
        	$.ajax({
        		url:"products/index.php",
        		method:"POST",
        		data:{page:page},
        		success:function(data){
        			$("#product-table").html(data);
        			console.log("Product table loaded successfully");
        			
        			// Initialize DataTable with search functionality and export/print buttons
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
        							"search": "Search products:",
        							"lengthMenu": "Show _MENU_ products per page",
        							"info": "Showing _START_ to _END_ of _TOTAL_ products",
        							"infoEmpty": "Showing 0 to 0 of 0 products",
        							"infoFiltered": "(filtered from _MAX_ total products)",
        							"emptyTable": "No products found",
        							"zeroRecords": "No matching products found"
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
        								"title": "Products List - " + new Date().toLocaleDateString()
        							},
        							{
        								"extend": "pdf",
        								"text": "Export PDF",
        								"className": "btn btn-danger btn-sm",
        								"title": "Products List - " + new Date().toLocaleDateString(),
        								"orientation": "landscape",
        								"pageSize": "A4"
        							},
        							{
        								"extend": "print",
        								"text": "Print",
        								"className": "btn btn-info btn-sm",
        								"title": "Products List - " + new Date().toLocaleDateString(),
        								"autoPrint": false
        							},
        							{
        								"extend": "csv",
        								"text": "Export CSV",
        								"className": "btn btn-warning btn-sm",
        								"title": "Products List - " + new Date().toLocaleDateString()
        							}
        						],
        						"columnDefs": [
        							{
        								"targets": [8, 9, 10, 11], // Action columns
        								"searchable": false,
        								"orderable": false
        							}
        						]
        					});
        				}
        			}, 100);
        		},
        		error: function(xhr, status, error) {
        			console.error("Error loading product table:", error);
        			$("#product-table").html('<div class="alert alert-danger">Error loading products. Please try again.</div>');
        		}
        	})
        }

        $(document).on("click",".page-no",function(){
        	var page = $(this).attr("id");
        	// alert(page);
        	fetch_all_products(page);
        })

        $(document).on("click",".prev",function(){
        	var prev_id= $(this).attr("prev-id");
        	fetch_all_products(prev_id);
        })

        $(document).on("click",".next",function(){
        	var next_id = $(this).attr("next-id");
        	fetch_all_products(next_id);
        })

// Fetch Single Product
		$(document).on("click",".view-btn", function(){
			var viewid = $(this).attr("view-id");
			console.log("=== Product View Debug ===");
			console.log("Button clicked - Product ID:", viewid);
			console.log("Button element:", this);
			
			// Clear previous modal data
			$("#pid, #pn, #bn, #pr, #qty, #st, #dt").text('Loading...');
			
			$.ajax({
				url:"products/view.php",
				method:"POST",
				dataType:"JSON",
				data:{viewid:viewid},
				beforeSend: function() {
					console.log("Sending AJAX request to products/view.php");
					console.log("Request data:", {viewid:viewid});
				},
				success:function(data){
					console.log("AJAX Success - Raw response:", data);
					console.log("Response type:", typeof data);
					
					// Check if response contains error
					if(data.error) {
						console.error("Server returned error:", data.error);
						alert("Error: " + data.error);
						return;
					}
					
					// Log each field being set
					console.log("Setting Product ID:", data.pid);
					$("#pid").text(data.pid || 'N/A');
					
					console.log("Setting Product Name:", data.product_name);
					$("#pn").html('<span style="text-transform:capitalize;">'+(data.product_name || 'N/A')+'</span>');
					
					console.log("Setting Brand Name:", data.brand_name);
					$("#bn").html('<span style="text-transform:capitalize;">'+(data.brand_name || 'N/A')+'</span>');
					
					console.log("Setting Price:", data.price);
					$("#pr").text('ugx ' + (data.price ? parseFloat(data.price).toFixed(2) : '0.00'));
					
					console.log("Setting Stock:", data.stock);
					$("#qty").text(data.stock || '0');
					
					console.log("Setting Status:", data.p_status);
					if(data.p_status==1){
						$("#st").html('<span style="color:green;">Active</span>');
					}else{
						$("#st").html('<span style="color:red;">Inactive</span>');
					}
					
					console.log("Setting Created Date:", data.created_at);
					$("#dt").text(data.created_at || 'N/A');
					
					console.log("Modal population completed successfully");
				},
				error: function(xhr, status, error) {
					console.error("=== AJAX Error ===");
					console.error("Status:", status);
					console.error("Error:", error);
					console.error("Response Text:", xhr.responseText);
					console.error("Status Code:", xhr.status);
					
					// Clear loading text and show error
					$("#pid, #pn, #bn, #pr, #qty, #st, #dt").text('Error loading data');
					alert("Failed to load product details. Check console for details.");
				},
				complete: function() {
					console.log("AJAX request completed");
				}
			});
		})

		function selection_change(){
			$(document).on('click',function(){
				var category = $("#category_id").val();
				var brand = $("#brand_id").val();
				if(category==''){
			 		$("#select_cat").find("strong").text('<span style="color:red;">You have not selected category name</span>');
			 		$("#category_id").css('border','2px solid red');
					
			   }else {
			   		$("#select_cat").find("strong").text('<span style="color:green;">Selected <b> &#10004;</b></span>');
			 		$("#category_id").css('border','2px solid green');
			 		$(".modal-footer").find("strong").text('');
			   }

			   if(brand==''){
			   		$("#select_brand").find("strong").text('<span style="color:red;">You have not selected brand name</span>');
			 		$("#brand_id").css('border','2px solid red');
			 		
			   }else{	
			 		$("#select_brand").find("strong").text('<span style="color:green;">Selected <b> &#10004;</b></span>');
			 		$("#brand_id").css('border','2px solid green');
			 		$(".modal-footer").find("strong").text('');
			   }
			})
		}
			
		$("#product_form").on('submit',function(){
			   selection_change();
			   $form = $(this);
				if($("#category_id").val()==''){
			 		$("#category_id").css('border','2px solid red');
			 		$("#select_cat").find("strong").text('<span style="color:red;">Please select category</span>');
			 		$(".modal-footer").find("strong").text('<div class="alert alert-danger text-danger text-center">All the fields are required</div>');
				}else if($("#brand_id").val()==''){
			 		$("#brand_id").css('border','2px solid red');
			 		$("#select_brand").find("strong").text('<span style="color:red;">Please select brand</span>');
			 		$(".modal-footer").find("strong").text('<div class="alert alert-danger text-danger text-center">All the fields are required</div>');
				}else{

					$.ajax({
						url:$form.attr("action"),
						method:$form.attr("method"),
						dataType:"JSON",
						data:$form.serialize(),
						success:function(data){
							if(data.success){
							   $(".add-modal").find("strong").text(data.message);
							   $("#product-msg").find("strong").text(data.message);
							   $("#product_form")[0].reset();
							   $("#product_form").trigger('reset');
							   $("#productModal").modal('hide');
							   fetch_all_products();

							}else{
								$(".add-modal").find("strong").text(data.message);
							}
							//console.log(data);
						}
					})
				}
		})

// Update products
		$(document).on("click",".edit-btn", function(){
			var pid = $(this).attr("edit-id");
			// alert(pid);
			$.ajax({
				url:"products/edit.php",
				method:"POST",
				dataType:"JSON",
				data:{pid:pid},
				success:function(data){

					$("#upid").val(data.pid);
					$("#update_category_id").val(data.cat_id);
					$('#update_category_id').css({'background': 'lightblue','color': 'black','font-weight': 'bolder'});
					$("#update_brand_id").val(data.brand_id);
					$('#update_brand_id').css({'background': 'lightblue','color': 'black','font-weight': 'bolder'});
					$("#update_product_name").val(data.product_name);
					$("#update_stock").val(data.stock);
					$("#update_price").val(data.price);
					$("#update_desc").val(data.description);
					$("#update_expiry_date").val(data.expiry_date);
					if(data.p_status==1){
						$("#update_status").find("strong").text('<option value="1">Active</option>'+'<option value="0">Inactive</option>');
					}else{
						$("#update_status").find("strong").text('<option value="0">Inactive</option>'+'<option value="1">Active</option>');
					}

					// console.log(data.brand_id);
				}
				
			})
		})

		$("#update_form").on("submit",function(){
				$.ajax({
					url:"products/update.php",
					method:"POST",
					dataType:"JSON",
					data:$("#update_form").serialize(),
					success:function(data){
						if(data.status === 'success'){
							$(".update_modal").find("strong").text(data.message);
							$("#product-msg").find("strong").text(data.message);
							$("#UpdateProductModal").trigger('reset');
							$("#UpdateProductModal").modal('hide');
							fetch_all_products();
						}else if(data.status === 'error'){
							$(".modal-footer").find("strong").text(data.message);
						}
						//console.log(data);
					},
					error: function(xhr, status, error) {
						var errorMessage = 'An error occurred while updating the product.';
						if (xhr.responseJSON && xhr.responseJSON.message) {
							errorMessage = xhr.responseJSON.message;
						}
						$(".modal-footer").find("strong").text(errorMessage);
					}
				})
		})

	// Delete products
		$(document).on("click",".del-btn", function(){
			var pid = $(this).attr("del-id");
			if(confirm("Are you sure want to delete this ")){
			$.ajax({
				url:"products/delete.php",
				method:"POST",
				dataType:"JSON",
				data:{pid:pid},
				success:function(data){
					if(data.success){
						$("#product-msg").find("strong").text(data.message);
						fetch_all_products();
					}else{
						$("#product-msg").find("strong").text(data.message);

					}
				}
			})

		   }else{
		   	 alert(" No ");
		   }

		})

// Add stock 
		$(document).on("click",".stock-btn", function(){
			var sid = $(this).attr("stock-id");
			// alert(sid);
			$.ajax({
				url:"products/stock.php",
				method:"POST",
				dataType:"JSON",
				data:{sid:sid},
				success:function(data){
					$("#sid").val(sid);
					$("#product-name-stock").val(data.product_name);
					$("#inventory").val(data.stock);
					$("#stock").val('');
					$("#sub-stock strong").text(data.stock);
					// Show stock preview
					$("#current-quantity").text(data.stock);
					$("#new-quantity").text('0');
					$("#stock-preview").show();
				}
			});
		})

		$("#stock").click(function(){
			$("#stock").val('');
		})

		$("#stock").keyup(function(){
			var stock = $(this);
			if(isNaN(stock.val())){
				alert("Invalid stock quantity ");
				stock.val(0);
			}else{
				var currentStock = parseInt($("#inventory").val()) || 0;
				var newStock = parseInt(stock.val()) || 0;
				var totalStock = currentStock + newStock;
				$("#sub-stock strong").text(totalStock);
				$("#new-quantity").text(newStock);
				$("#stock-preview").show();
			}
		});

		$("#stock_form").on("submit",function(){
				$.ajax({
					url:"products/addStock.php",
					method:"POST",
					dataType:"JSON",
					data:$("#stock_form").serialize(),
					success:function(data){
						if(data.success){
							$(".stock-modal").find("strong").text(data.message);
							$("#product-msg").find("strong").text(data.message);
							$("#stock_form")[0].reset();
							$("#stock_form").trigger('reset');
							$("#Stock-Modal").modal('hide');
							fetch_all_products();

						}else if(data.error){
							$(".stock-modal").find("strong").text(data.message);
						}
						//console.log(data);
					}
					
				})
		})
})
