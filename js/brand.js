$(document).ready(function(){
	var domain = "http://localhost/imsapp/";
	// Fetch all brands (no pagination - DataTables handles it)
	fetch_all_brands();
	function  fetch_all_brands(){
		$.ajax({
			url:domain+"brands/index.php",
			method:"POST",
			data:{},
			success:function(data){
				$("#brand-table").html(data);
				
				// Destroy existing DataTable if it exists
				if($.fn.DataTable.isDataTable('#brand_data')){
					$('#brand_data').DataTable().destroy();
				}
				
				// Initialize DataTable
				setTimeout(function(){
					if($("#brand_data").length){
						$("#brand_data").DataTable({
							"pageLength": 10,
							"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
							"order": [[ 0, "desc" ]],
							"responsive": true,
							"searching": true,
							"language": {
								"search": "Search brands:",
								"lengthMenu": "Show _MENU_ brands per page",
								"info": "Showing _START_ to _END_ of _TOTAL_ brands",
								"infoEmpty": "Showing 0 to 0 of 0 brands",
								"infoFiltered": "(filtered from _MAX_ total brands)",
								"emptyTable": "No brands found",
								"zeroRecords": "No matching brands found"
							},
							"columnDefs": [
								{
									"targets": [3, 4], // Action columns
									"searchable": false,
									"orderable": false
								}
							]
						});
					}
				}, 100);
			}
		})
	}

	$("#brand_form").on("submit", function(){
		$form = $(this);
		//alert("hello");
		$.ajax({
			url:domain+"brands/add.php",
			method:"POST",
			dataType:"JSON",
			data:$("#brand_form").serialize(),
			success:function(data){
				if(data.success){
					$("#brand_name").css("border","");
					$("#b-error").html("<span style='color:green;'><b> &#10004;</b></span>");
					$(".modal-footer").html(data.message);
					$("#brand-msg").html(data.message);
				fetch_all_brands();
			}else if(data.error){
				$("#brand_name").css("border","2px solid red");
				$("#b-error").html("<span style='color:red;'>Please enter category name</span>");
				$(".modal-footer").html(data.message);					

			}
			fetch_all_brands();
				// console.log(data);
			}
		})
	})



	// Remove custom pagination handlers - DataTables handles it

	$("body").on("click","#edit",function(){
		var edit_bid = $(this).attr("edit_id");
		// alert(edit_id);
		$.ajax({
			url:domain+"brands/edit.php",
			method:"POST",
			dataType:"JSON",
			data:{edit_bid:edit_bid},
			success:function(data){
				$("#bid").val(edit_bid)
				$("#update_brand_name").val(data.brand_name)
				//console.log(data.status);
				if(data.b_status==1){
        			$("#update-status").html('<option value="1">Active</option>'+'<option value="0">Inactive</option>');
        			
        		}else{
        			$("#update-status").html('<option value="0">Inactive</option>'+'<option value="1">Active</option>');
        			
        		}
			}

		});

	});


	$("#UpdateBrandModal").on("submit", function(){
		if($("#update_brand_name").val()==''){
			$("#update_brand_name").css("border",'2px solid red');
			$("#br-error").html('<span class="text-danger">Please enter brand name </span>');
		}else{
			$.ajax({
				url:domain+"brands/update.php",
				method:"POST",
				dataType:"JSON",
				data: $("#update_brand_form").serialize(),
				success:function(data){
					if(data.success){
						$("#update_brand_name").css("border",'');
						$("#br-error").html('');
						$(".modal-footer").html(data.message);
						$("#brand-msg").html(data.message);
						
						// Close modal and reload page after short delay
						setTimeout(function(){
							$("#UpdateBrandModal").modal("hide");
							location.reload(); // Reload page to show updated data
						}, 1500);
					}else if(data.error){
						$(".modal-footer").html(data.message);
					}
					// $(".modal-footer").html(data.message);
					// console.log(data.message);
					
				}
			})
		}
	})

	$("body").on("click","#del",function(){
		var del_id = $(this).attr("del_id");
		if(confirm("Are you sure want to delete this ?")){
			$.ajax({
				url:domain+"brands/delete.php",
				method:"POST",
				dataType:"JSON",
				data:{del_id:del_id},
			success:function(data){
				if(data.success){
					$("#brand-msg").html(data.message);
					$(".modal-footer").html(data.message);
					fetch_all_brands();
				}else if(data.error){
					$("#brand-msg").html(data.message);
				}
				// console.log(data);
			}
			});
		}
	})

})