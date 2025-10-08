$(document).ready(function(){
	var domain = "http://localhost/imsapp/";
	// Fetch brands with pagination
	fetch_all_brands_with_pagination();
	function  fetch_all_brands_with_pagination(page){
		$.ajax({
			url:domain+"brands/index.php",
			method:"POST",
			// dataType:"JSON",
			data:{page:page},
			success:function(data){
				$("#brand-table").html(data);
				//console.log(data);
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
					fetch_all_brands_with_pagination();
				}else if(data.error){
					$("#brand_name").css("border","2px solid red");
					$("#b-error").html("<span style='color:red;'>Please enter category name</span>");
					$(".modal-footer").html(data.message);					

				}
				fetch_all_brands_with_pagination();
				// console.log(data);
			}
		})
	})



	$(document).on("click",".paging-link", function(){
		var page = $(this).attr("id");
		//alert(page);
		fetch_all_brands_with_pagination(page);

	})
	$(document).on("click",".prev-link",function(){
		var previous = $(this).attr("prev-id");
		//alert(previous);
		fetch_all_brands_with_pagination(previous);
	})
	$(document).on("click",".next-link",function(){
		var next = $(this).attr("next-id");
		fetch_all_brands_with_pagination(next);
	})

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
						fetch_all_brands_with_pagination();
					}else if(data.error){
						$("#brand-msg").html(data.message);
					}
					// console.log(data);
				}
			});
		}
	})

})