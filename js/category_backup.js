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
					var main = '<option value="0">Main Category</option>';
					var select = '<option value="">Select Category</option>';
					$("#maincategory").html(main+data);
					$("#category_id").html(select+data);
					
				}
			});
		}
		$("#category-form").on("submit", function(){
			var	$form = $(this);
				$.ajax({
					// url: domain+"category/add.php",
					url: $form.attr("action"),
					method:$form.attr("method"),
					dataType:"json",
					data: $form.serialize(),
					success:function(data){
						if(data.error=='error'){
							if($("#category_name").val()==""){
							   $("#category_name").css("border","2px solid red");
							   $("#cat_error").html("<span style='color:red;'>Please enter category name</span>");
							   $("#status").css("border","2px solid red");
							   $("#e-status").html("<span style='color:red;'>Please select status</span>");
							   $(".modal-footer").html(data.message);	
							}else if(data.exists=='exists'){
							   $("#cat_error").html("<span style='color:red;'>This category already exists ! Please enter another one</span>");
							   $(".modal-footer").html(data.message);	
							}
							
						}else if(data.success=='success'){
							$(".modal-footer").html(data.message);
							$("#category_name").css("border","");
							$("#status").css("border","");
							$("#cat_error").html("<span class='text-success' style='color:green;'>The category has been added <b> &#10004;</b></span>");
							$("#e-status").html("<span style='color:green;'>Active Enable<b> &#10004;</b></span>");
							setTimeout(function(){
								$("#category-form")[0].reset();
								$("#categoryModal").modal("hide");
							}, 3000);
							$("#msg").html(data.message);
							fetch_category();
						}

						//console.log(data);
					}
				});
			
		})
// fetch categories with pagination 
		fetch_category_with_pagination();
		function fetch_category_with_pagination(page){
			$.ajax({
				url:domain+"category/index.php",
				method:"POST",
				data:{page:page},
				success:function(data){
					$("#category-table").html(data);
					//console.log(data);
				}
			});
		}

		$("body").on("click",".paging", function(){
			var page = $(this).attr("id");
			// alert(page);
			fetch_category_with_pagination(page);
		})
		$("body").on("click",".previous", function(){
			var previous = $(this).attr("prev-id");
			fetch_category_with_pagination(previous);
		})
		$("body").on("click",".next", function(){
			var next = $(this).attr("nxt-id");
			fetch_category_with_pagination(next);
		})
// =======View Single Category
        $(document).on("click",".v-id", function(){
        	var viewid = $(this).attr("view-id");
        	// alert(viewid);
        	$("#Cat-View-Modal").modal("show");

        	$.ajax({
        		url:domain+"category/view.php",
        		method: "POST",
        		dataType:"json",
        		data:{viewid:viewid},
        		success:function(data){
        			// console.log(data);

        		    if(data.main_cat==0){
        				$("#main-id").html(data.main_cat);
        				$("#main").html(data.category_name);
        				$("#sub-id").html(data.cat_id);
        				$(".subcat").hide();
        			}
        			else if(data.main_cat>0){
        		    	$("#main-id").html(data.main_cat);
        		    	$(".maincat").hide();
        		    	$("#sub-id").html(data.cat_id);
        		    	$(".subcat").show();
        		    	$("#sub").html(data.category_name);
        		    }
        			
        			if(data.status==1){
        				$("#st").html('<span style="color:green;">Active</span>');
        			}else{
        				$("#st").html('<span style="color:red;">Inactive</span>');
        			}
        			$("#dt").html(data.created_at);
        		}
        	});
        })
// Delete Category
		$(document).on("click","#del", function(){
			var del_id = $(this).attr("del-id");
			//alert(del_id);
		    if(confirm("Are you sure want to delete this ?")){
				$.ajax({
					url:domain+"category/delete.php",
					method:"POST",
					dataType:"json",
					data:{del_id:del_id},
					success:function(data){
						if(data.success){
							$("#msg").html(data.message);
							alert("The category has been deleted");
							fetch_category_with_pagination();
						}else if(data.error){
							$("#msg").html(data.message);
							alert("This is dependent category ! It can not delete");	
						}
						//alert(data);
					}
				});
		    }else{
		    	Alert("No, I do not want to delete ");
		    }
		})

        $(document).on("click",".edit-btn",function(){
        	$("#UpdatecategoryModal").modal("show");
          	var edit_id = $(this).attr("edit-id");
			$(".modal-footer").html('');
            $.ajax({
            	url: domain+"category/edit.php",
            	method: "POST",
            	dataType: "json",
            	data:{edit_id:edit_id},
            	success:function(data){
            		$("#cat_id").val(data.cat_id);
            		$("#update-main-category").html('<option value="'+data.main_cat+'">'+data.category_name+'</option>');
            		$("#update-category-name").val(data.category_name);
            		if(data.status==1){
            			$("#update-status").html('<option value="1">Active</option>'+'<option value="0">Inactive</option>');
            			
            		}else{
            			$("#update-status").html('<option value="0">Inactive</option>'+'<option value="1">Active</option>');
            			
            		}
            		
            	}
            });
  		
        });

        $("#update-category-form").on("submit",function(){
        	$.ajax({
        		url: domain+"category/update.php",
        		method:"POST",
        		dataType:"json",
        		data:$("#update-category-form").serialize(),
        		success:function(data){
        			if(data.success){
        				// $("#up_error").html("<span style='color:green;'>The category has been updated <b>&#10004;</b></span>");
						$("#update-category-name").css("border","");
						$("#update-status").css("border","");
        				setTimeout(function(){
							$("#UpdatecategoryModal").modal("hide");
						}, 2000);
						// $("#UpdatecategoryModal").trigger("reset");
						$("#update-category-form")[0].reset();
						$(".modal-footer").html(data.message);
						fetch_category_with_pagination();
					}else if(data.error){
						if($("#update-category-name").val()==""){
						   $("#update-category-name").css("border","2px solid red");
						   $("#update-status").css("border","");
						   $("#up_error").html("<span style='color:red;'>Please enter category name </span>");
						   
						}
						$(".modal-footer").html(data.message);	
					}
        			//console.log(data);
        		}
        	})
        });

        

})